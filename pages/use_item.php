<?php
require_once 'includes/GameEngine.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION['user_id'];
    $itemId = $_POST['item_id'];

    $conn = getDatabaseConnection();

    // Fetch the item details and user quantity
    $stmt = $conn->prepare("
        SELECT is_consumable, attributes, ui.quantity 
        FROM items i
        JOIN user_items ui ON i.item_id = ui.item_id
        WHERE i.item_id = ? AND ui.userid = ?
    ");
    $stmt->bind_param("ii", $itemId, $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if (!$item || !$item['is_consumable']) {
        echo "This item cannot be used.";
        exit;
    }

    // Decode the item's attributes
    $attributes = json_decode($item['attributes'], true);
    $quantity = $item['quantity'];

    // Prevent usage if quantity is 0 or less
    if ($quantity <= 0) {
        echo "You do not have enough of this item to use.";
        exit;
    }

    // Apply energy restoration
    if (isset($attributes['energy']) && $attributes['energy'] > 0) {
        $energyRestored = $attributes['energy'];
        $stmt = $conn->prepare("
            UPDATE users 
            SET energy = LEAST(energy + ?, 100) 
            WHERE userid = ?
        ");
        $stmt->bind_param("ii", $energyRestored, $userid);
        $stmt->execute();
        echo "Energy restored by {$energyRestored} points!";
    }

    // Apply nerve restoration
    if (isset($attributes['nerve']) && $attributes['nerve'] > 0) {
        $nerveRestored = $attributes['nerve'];
        $stmt = $conn->prepare("
            UPDATE users 
            SET nerve = LEAST(nerve + ?, 30) 
            WHERE userid = ?
        ");
        $stmt->bind_param("ii", $nerveRestored, $userid);
        $stmt->execute();
        echo "Nerve restored by {$nerveRestored} points!";
    }

    // Reduce item quantity
    $newQuantity = $quantity - 1;
    if ($newQuantity > 0) {
        $stmt = $conn->prepare("
            UPDATE user_items 
            SET quantity = ? 
            WHERE userid = ? AND item_id = ?
        ");
        $stmt->bind_param("iii", $newQuantity, $userid, $itemId);
        $stmt->execute();
    } else {
        // Remove item from inventory if quantity reaches 0
        $stmt = $conn->prepare("
            DELETE FROM user_items 
            WHERE userid = ? AND item_id = ?
        ");
        $stmt->bind_param("ii", $userid, $itemId);
        $stmt->execute();
        echo "Item removed from inventory!";
    }

    // Redirect back to inventory
    header('Location: index.php?page=inventory');
    exit;
}
?>
