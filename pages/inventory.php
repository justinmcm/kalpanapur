<?php
// Include database connection
require_once 'includes/GameEngine.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$userid = $_SESSION['user_id'];

// Fetch user inventory
$conn = getDatabaseConnection();
$stmt = $conn->prepare("
    SELECT i.item_id, i.name, i.description, i.is_consumable, ui.quantity
    FROM user_items ui
    JOIN items i ON ui.item_id = i.item_id
    WHERE ui.userid = ?
");
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Your Inventory</h2>
<table>
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>
                <?php if (!empty($row['is_consumable']) && $row['is_consumable']): ?>
                    <form method="POST" action="index.php?page=use_item">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($row['item_id']) ?>">
                        <button type="submit">Use</button>
                    </form>
                <?php else: ?>
                    Not Usable
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
