<?php
require_once "includes/admin_auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Management</title>
</head>
<body>
    <h1>Money Management</h1>
    <form method="POST">
        <label>Player ID: <input type="number" name="userid" required></label><br>
        <label>Amount: <input type="number" name="amount" required></label><br>
        <label>Action: 
            <select name="action" required>
                <option value="add">Add Money</option>
                <option value="subtract">Subtract Money</option>
            </select>
        </label><br>
        <button type="submit" name="update_money">Submit</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_money'])) {
        $userid = intval($_POST['userid']);
        $amount = intval($_POST['amount']);
        $action = $_POST['action'];

        $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }

        if ($action === 'add') {
            $stmt = $conn->prepare('UPDATE users SET money = money + ? WHERE userid = ?');
        } elseif ($action === 'subtract') {
            $stmt = $conn->prepare('UPDATE users SET money = money - ? WHERE userid = ?');
        } else {
            die('Invalid action.');
        }

        $stmt->bind_param('ii', $amount, $userid);
        if ($stmt->execute()) {
            echo "<p>Money updated successfully!</p>";
        } else {
            echo "<p>Error updating money: " . $stmt->error . "</p>";
        }
        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
