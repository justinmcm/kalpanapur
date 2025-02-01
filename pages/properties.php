<?php
require_once "includes/GameEngine.php";

// Verify user session
if (!isset($_SESSION['user_id'])) {
    die('Error: User is not logged in. Please log in to access properties.');
}
$userid = $_SESSION['user_id'];

// Establish database connection
$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Initialize message
$message = '';

// Fetch user's properties
$userProperties = [];
$stmt = $conn->prepare('
    SELECT up.property_id, up.is_moved_in, up.quantity, p.name, p.max_happiness
    FROM user_properties up
    JOIN properties p ON up.property_id = p.property_id
    WHERE up.userid = ?
');
if (!$stmt) {
    die('Error preparing fetch user properties query: ' . $conn->error);
}
$stmt->bind_param('i', $userid);
$stmt->execute();
$result = $stmt->get_result();
$userProperties = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch available properties
$availableProperties = [];
$stmt = $conn->prepare('SELECT * FROM properties WHERE availability = TRUE');
if (!$stmt) {
    die('Error preparing fetch available properties query: ' . $conn->error);
}
$stmt->execute();
$availableProperties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle property-related actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $propertyId = intval($_POST['property_id'] ?? 0);

    if (isset($_POST['move_in'])) {
        $stmt = $conn->prepare('UPDATE user_properties SET is_moved_in = 0 WHERE userid = ?');
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare('UPDATE user_properties SET is_moved_in = 1 WHERE userid = ? AND property_id = ? AND quantity > 0 LIMIT 1');
        $stmt->bind_param('ii', $userid, $propertyId);
        $stmt->execute();
        $stmt->close();

        $message = "You have moved into the selected property.";
    } elseif (isset($_POST['move_out'])) {
        $stmt = $conn->prepare('UPDATE user_properties SET is_moved_in = 0 WHERE userid = ?');
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $stmt->close();

        $message = "You have vacated your current property.";
    } elseif (isset($_POST['buy_property'])) {
        $stmt = $conn->prepare('SELECT money FROM users WHERE userid = ?');
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $stmt->bind_result($userMoney);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare('SELECT price FROM properties WHERE property_id = ? AND availability = TRUE');
        $stmt->bind_param('i', $propertyId);
        $stmt->execute();
        $stmt->bind_result($propertyPrice);
        if (!$stmt->fetch()) {
            $message = "The selected property is not available.";
        } else {
            $stmt->close();

            if ($userMoney >= $propertyPrice) {
                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare('UPDATE users SET money = money - ? WHERE userid = ?');
                    $stmt->bind_param('ii', $propertyPrice, $userid);
                    $stmt->execute();
                    $stmt->close();

                    $stmt = $conn->prepare('INSERT INTO user_properties (userid, property_id, quantity) VALUES (?, ?, 1)
                                            ON DUPLICATE KEY UPDATE quantity = quantity + 1');
                    $stmt->bind_param('ii', $userid, $propertyId);
                    $stmt->execute();
                    $stmt->close();

                    $conn->commit();
                    $message = "You successfully purchased the property!";
                } catch (Exception $e) {
                    $conn->rollback();
                    $message = "Transaction failed: " . $e->getMessage();
                }
            } else {
                $message = "You don't have enough money to purchase this property.";
            }
        }
    }
}

$conn->close();
?>

<h1 class="page-title">Properties</h1>
<?php if (!empty($message)) : ?>
    <p class="message-box"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<section class="properties-section">
    <div class="user-properties">
        <h2>Your Properties</h2>
        <table class="properties-table">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Max Happiness</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userProperties as $property) : ?>
                    <tr>
                        <td><?= htmlspecialchars($property['name']) ?></td>
                        <td><?= htmlspecialchars($property['max_happiness']) ?></td>
                        <td><?= htmlspecialchars($property['quantity']) ?></td>
                        <td>
                            <?php if ($property['is_moved_in']) : ?>
                                <form method="POST">
                                    <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">
                                    <button type="submit" name="move_out" class="action-button vacate">Vacate</button>
                                </form>
                            <?php else : ?>
                                <form method="POST">
                                    <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">
                                    <button type="submit" name="move_in" class="action-button move-in">Move In</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="available-properties">
        <h2>Available Properties</h2>
        <table class="properties-table">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Price (₹)</th>
                    <th>Max Happiness</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($availableProperties as $property) : ?>
                    <tr>
                        <td><?= htmlspecialchars($property['name']) ?></td>
                        <td>₹<?= number_format($property['price']) ?></td>
                        <td><?= htmlspecialchars($property['max_happiness']) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">
                                <button type="submit" name="buy_property" class="action-button buy">Buy</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
