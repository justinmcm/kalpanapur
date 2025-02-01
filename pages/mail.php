<?php

require_once "includes/GameEngine.php";

// Establish a single database connection
$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$message = ''; // Default message
$activeTab = $_GET['tab'] ?? 'inbox'; // Active tab (inbox/sent)

// Handle sending messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver'], $_POST['subject'], $_POST['message'])) {
    $receiver = trim($_POST['receiver']);
    $subject = trim($_POST['subject']);
    $content = trim($_POST['message']);

    if (empty($receiver) || empty($subject) || empty($content)) {
        $message = "All fields are required.";
    } else {
        // Fetch receiver's user ID
        $stmt = $conn->prepare('SELECT userid FROM users WHERE username = ?');
        if (!$stmt) {
            die('Query preparation failed: ' . $conn->error);
        }
        $stmt->bind_param('s', $receiver);
        $stmt->execute();
        $stmt->bind_result($receiverId);
        if ($stmt->fetch()) {
            $stmt->close();

            // Insert the message into the mail table
            $stmt = $conn->prepare('
                INSERT INTO mail (sender_id, receiver_id, subject, message) 
                VALUES (?, ?, ?, ?)
            ');
            if (!$stmt) {
                die('Query preparation failed: ' . $conn->error);
            }
            $stmt->bind_param('iiss', $_SESSION['user_id'], $receiverId, $subject, $content);
            if ($stmt->execute()) {
                $message = "Message sent successfully!";
            } else {
                $message = "Failed to send the message: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Recipient not found.";
        }
    }
}

// Handle marking messages as read
if (isset($_POST['mark_as_read'])) {
    $stmt = $conn->prepare('UPDATE mail SET `read` = 1 WHERE mail_id = ? AND receiver_id = ?');
    $stmt->bind_param('ii', $_POST['mark_as_read'], $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

// Handle deleting messages
if (isset($_POST['delete'])) {
    $stmt = $conn->prepare('DELETE FROM mail WHERE mail_id = ? AND (receiver_id = ? OR sender_id = ?)');
    $stmt->bind_param('iii', $_POST['delete'], $_SESSION['user_id'], $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

// Fetch messages
$inboxMessages = [];
$sentMessages = [];

if ($activeTab === 'inbox') {
    $stmt = $conn->prepare('
        SELECT m.mail_id, m.subject, m.message, m.sent_at, m.read, u.username AS sender 
        FROM mail m
        JOIN users u ON m.sender_id = u.userid
        WHERE m.receiver_id = ?
        ORDER BY m.sent_at DESC
    ');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $inboxMessages = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} elseif ($activeTab === 'sent') {
    $stmt = $conn->prepare('
        SELECT m.mail_id, m.subject, m.message, m.sent_at, u.username AS recipient 
        FROM mail m
        JOIN users u ON m.receiver_id = u.userid
        WHERE m.sender_id = ?
        ORDER BY m.sent_at DESC
    ');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $sentMessages = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<h2>Mailbox</h2>
<?php if (!empty($message)): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<div>
    <a href="?tab=inbox">Inbox</a> | <a href="?tab=sent">Sent Messages</a>
</div>

<?php if ($activeTab === 'inbox'): ?>
    <h3>Inbox</h3>
    <?php if (empty($inboxMessages)): ?>
        <p>No messages in your inbox.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inboxMessages as $message): ?>
                    <tr>
                        <td><?= htmlspecialchars($message['sender']) ?></td>
                        <td><?= htmlspecialchars($message['subject']) ?></td>
                        <td><?= htmlspecialchars($message['sent_at']) ?></td>
                        <td>
                            <?php if (!$message['read']): ?>
                                <form method="POST" style="display:inline;">
                                    <button type="submit" name="mark_as_read" value="<?= $message['mail_id'] ?>">Mark as Read</button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" style="display:inline;">
                                <button type="submit" name="delete" value="<?= $message['mail_id'] ?>">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php elseif ($activeTab === 'sent'): ?>
    <h3>Sent Messages</h3>
    <?php if (empty($sentMessages)): ?>
        <p>No sent messages.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>To</th>
                    <th>Subject</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sentMessages as $message): ?>
                    <tr>
                        <td><?= htmlspecialchars($message['recipient']) ?></td>
                        <td><?= htmlspecialchars($message['subject']) ?></td>
                        <td><?= htmlspecialchars($message['sent_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<h3>Send a Message</h3>
<form method="POST">
    <label for="receiver">To:</label>
    <input type="text" id="receiver" name="receiver" required>
    
    <label for="subject">Subject:</label>
    <input type="text" id="subject" name="subject" required>
    
    <label for="message">Message:</label>
    <textarea id="message" name="message" rows="5" required></textarea>
    
    <button type="submit">Send</button>
</form>
