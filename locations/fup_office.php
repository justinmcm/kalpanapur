<?php
require_once "includes/GameEngine.php";

// Establish a single database connection
$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$message = ''; // Default message

// Handle donation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donation_amount'])) {
    $donationAmount = (int)$_POST['donation_amount'];
    if ($donationAmount > 0) {
        $stmt = $conn->prepare('
            UPDATE users 
            SET money = money - ? 
            WHERE userid = ? AND money >= ?
        ');
        if (!$stmt) {
            die('Query preparation failed: ' . $conn->error);
        }
        $stmt->bind_param('iii', $donationAmount, $_SESSION['user_id'], $donationAmount);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $message = "Thank you for your donation of ₹{$donationAmount}!";
        } else {
            $message = "You don't have enough money to donate ₹{$donationAmount}.";
        }
        $stmt->close();
    } else {
        $message = "Invalid donation amount.";
    }
}

// Fetch available missions
$missions = [];
$stmt = $conn->prepare('SELECT mission_id, mission_name, mission_description FROM missions WHERE location = "fup_office"');
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $missions[] = $row;
    }
    $stmt->close();
}


// Close the database connection
$conn->close();
?>

<?php require_once "includes/header.php"; ?>

<div class="location-page">
    <h1>Freedom United Party Office</h1>
    <p>Welcome to the main office of the Freedom United Party. Here, you can interact with party leaders, participate in missions, and contribute to the cause.</p>

    <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Donations -->
    <div class="donations">
        <h2>Support the Party</h2>
        <form method="POST">
            <label for="donation-amount">Enter Donation Amount:</label>
            <input type="number" id="donation-amount" name="donation_amount" min="1" placeholder="1000" required>
            <button type="submit">Donate</button>
        </form>
    </div>
     <!-- Back Button -->
     <div class="back-button">
        <a href="index.php?page=city">← Back to City</a>
    </div>
</div>


<?php //require_once "includes/footer.php"; ?>

