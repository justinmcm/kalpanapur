<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You must be logged in to interact with NPCs.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Enable PHP error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// If a multiplier calculation request is made
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['calculate_multiplier'])) {
    $stmt = $conn->prepare('SELECT boost_end_time FROM npc_boosts WHERE userid = ? AND npc = ?');
    $npc = 'John Flex'; // Replace with the correct NPC name
    $stmt->bind_param('is', $user_id, $npc);
    $stmt->execute();
    $stmt->bind_result($boost_end_time);
    if ($stmt->fetch() && $boost_end_time > time()) {
        $random = mt_rand(1, 100);
        $multiplier = 0;
        if ($random <= 3) {
            $multiplier = 3;
        } elseif ($random <= 13) {
            $multiplier = 1.5;
        } elseif ($random <= 33) {
            $multiplier = 1;
        } elseif ($random <= 63) {
            $multiplier = 0.5;
        }
        echo json_encode(['multiplier' => $multiplier]);
    } else {
        echo json_encode(['multiplier' => 0]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// Boost activation logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['npc'], $_POST['choice'])) {
    $npc = $_POST['npc'];
    $choice = $_POST['choice'];

    if ($choice === 'yes') {
        $cost = 15000;

        $stmt = $conn->prepare('SELECT money FROM users WHERE userid = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($current_money);
        $stmt->fetch();
        $stmt->close();

        if ($current_money >= $cost) {
            $new_money = $current_money - $cost;
            $boost_end_time = time() + (7 * 24 * 60 * 60);

            $stmt = $conn->prepare('UPDATE users SET money = ? WHERE userid = ?');
            $stmt->bind_param('ii', $new_money, $user_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare('
                INSERT INTO npc_boosts (userid, npc, boost_end_time)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE boost_end_time = VALUES(boost_end_time)
            ');
            $stmt->bind_param('isi', $user_id, $npc, $boost_end_time);
            $stmt->execute();
            $stmt->close();

            echo "<p>Boost activated for $npc. You now have enhanced training gains!</p>";
        } else {
            echo "<p>Not enough money! You need ₹$cost but only have ₹$current_money.</p>";
        }
    } elseif ($choice === 'no') {
        echo "<p>John Flex: Alright, come back when you're ready!</p>";
    }
}

$conn->close();
?>

<h2>John Flex</h2>
<p>The energetic and motivational gym trainer.</p>
<form method="POST">
    <button type="submit" name="action" value="train">Train with John Flex</button>
    <button type="submit" name="action" value="ignore">Ignore John Flex</button>
</form>

<h3>Activate Boost</h3>
<form method="POST">
    <input type="hidden" name="npc" value="John Flex">
    <button type="submit" name="choice" value="yes">Activate Boost</button>
    <button type="submit" name="choice" value="no">Decline</button>
</form>
