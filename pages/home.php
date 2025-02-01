<?php

require_once "includes/GameEngine.php";

$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Fetch user stats
$stmt = $conn->prepare('SELECT strength, agility, defense, dexterity FROM users WHERE userid = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($strength, $agility, $defense, $dexterity);
$stmt->fetch();
$stmt->close();

// Fetch working stats and money
$stmt = $conn->prepare('SELECT intelligence, manual_labor, endurance, money FROM users WHERE userid = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($intelligence, $manualLabor, $endurance, $money);
$stmt->fetch();
$stmt->close();

// Fetch the name and max happiness of the moved-in property
$stmt = $conn->prepare('
    SELECT p.name, p.max_happiness 
    FROM user_properties up
    JOIN properties p ON up.property_id = p.property_id
    WHERE up.userid = ? AND up.is_moved_in = TRUE
');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($propertyName, $propertyMaxHappiness);
$stmt->fetch();
$stmt->close();

$conn->close();
?>

<h2>Welcome to Kalpanapur</h2>

<!-- Grid Layout for Stats -->
<div class="content-grid">
    <!-- Battle Stats -->
    <div class="stats-container">
        <h3>Battle Stats</h3>
        <p>💪 <strong>Strength:</strong> <?= $strength ?></p>
        <p>🏃 <strong>Agility:</strong> <?= $agility ?></p>
        <p>🛡️ <strong>Defense:</strong> <?= $defense ?></p>
        <p>🎯 <strong>Dexterity:</strong> <?= $dexterity ?></p>
    </div>

    <!-- Moved-In Property -->
    <div class="stats-container">
        <h3>Moved-In Property</h3>
        <p>🏠 <strong>Property:</strong> <?= htmlspecialchars($propertyName ?? 'None') ?></p>
        <p>😊 <strong>Max Happiness:</strong> <?= $propertyMaxHappiness ?? 'N/A' ?></p>
    </div>

    <!-- Working Stats -->
    <div class="stats-container">
        <h3>Working Stats</h3>
        <p>🧠 Intelligence: <?= $intelligence ?></p>
        <p>💪 Manual Labor: <?= $manualLabor ?></p>
        <p>🏃 Endurance: <?= $endurance ?></p>
    </div>

    <!-- Money -->
    <div class="stats-container">
        <h3>Money</h3>
        <p>💰 Balance: ₹<?= $money ?></p>
    </div>
</div>
