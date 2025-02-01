<?php
require_once "includes/GameEngine.php";

// Establish a single database connection
$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$message = ''; // Default message

// Handle item purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $itemId = (int)$_POST['item_id'];
    $stmt = $conn->prepare('SELECT item_name, item_price, item_quantity FROM items WHERE item_id = ? AND location = "chor_bazaar"');
    $stmt->bind_param('i', $itemId);
    $stmt->execute();
    $stmt->bind_result($itemName, $itemPrice, $itemQuantity);
    if ($stmt->fetch() && $itemQuantity > 0) {
        // Fetch user balance
        $stmt->close();
        $stmt = $conn->prepare('SELECT money FROM users WHERE userid = ?');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($currentMoney);
        $stmt->fetch();
        $stmt->close();

        if ($currentMoney >= $itemPrice) {
            // Deduct balance and update item quantity
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare('UPDATE users SET money = money - ? WHERE userid = ?');
                $stmt->bind_param('ii', $itemPrice, $_SESSION['user_id']);
                $stmt->execute();

                $stmt = $conn->prepare('UPDATE items SET item_quantity = item_quantity - 1 WHERE item_id = ?');
                $stmt->bind_param('i', $itemId);
                $stmt->execute();

                $conn->commit();
                $message = "You purchased {$itemName} for ₹{$itemPrice}!";
            } catch (Exception $e) {
                $conn->rollback();
                $message = "Transaction failed: " . $e->getMessage();
            }
        } else {
            $message = "You don't have enough money to purchase {$itemName}.";
        }
    } else {
        $message = "This item is not available.";
    }
    $stmt->close();
}

// Fetch available items
$items = [];
$stmt = $conn->prepare('SELECT item_id, item_name, item_price, item_quantity FROM items WHERE location = "chor_bazaar"');
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<div class="location-page">
    <h1>Chor Bazaar</h1>
    <p>Welcome to Chor Bazaar, the black market where rare and illegal items can be found. Spend wisely!</p>

    <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Available Items -->
    <div class="items-for-sale">
        <h2>Items for Sale</h2>
        <?php if (!empty($items)): ?>
            <ul>
                <?php foreach ($items as $item): ?>
                    <li>
                        <strong><?= htmlspecialchars($item['item_name']) ?></strong><br>
                        Price: ₹<?= number_format($item['item_price']) ?><br>
                        Available: <?= $item['item_quantity'] ?><br>
                        <?php if ($item['item_quantity'] > 0): ?>
                            <form method="POST" style="margin-top: 10px;">
                                <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                <button type="submit">Buy Now</button>
                            </form>
                        <?php else: ?>
                            <p>Out of Stock</p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No items available at the moment. Check back later!</p>
        <?php endif; ?>
    </div>
     <!-- Back Button -->
     <div class="back-button">
        <a href="index.php?page=city">← Back to City</a>
    </div>
</div>

<style>
    .location-page {
        padding: 20px;
        color: #fff;
    }

    .message {
        color: #32cd32;
        margin-bottom: 15px;
    }

    .items-for-sale ul {
        list-style: none;
        padding: 0;
    }

    .items-for-sale li {
        background: #2c2c2c;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 8px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
    }

    .items-for-sale button {
        padding: 8px 15px;
        border: none;
        background: #444;
        color: #fff;
        border-radius: 5px;
        cursor: pointer;
    }

    .items-for-sale button:hover {
        background: #555;
    }
</style>
