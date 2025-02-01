<?php
session_start(); // Assuming the user is logged in
$userid = $_SESSION['userid']; // Get the logged-in user's ID
$npc_id = $_GET['npc_id']; // NPC being interacted with
$action = $_GET['action']; // Action type (e.g., 'help', 'ignore')

// Database connection
$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Get the NPC and their faction
$stmt = $conn->prepare("SELECT faction_id FROM npcs WHERE npc_id = ?");
$stmt->bind_param('i', $npc_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $npc = $result->fetch_assoc();
    $faction_id = $npc['faction_id'];

    // Determine standings change based on action
    $change = 0.05; // Example: helping adds 0.05 to the faction
    if ($action === 'ignore') {
        $change = -0.02; // Ignoring reduces by 0.02
    }

    // Update standings for the player's interaction
    $stmt = $conn->prepare("UPDATE standings SET value = LEAST(GREATEST(value + ?, -10.00), 10.00) WHERE userid = ? AND faction_id = ?");
    $stmt->bind_param('dii', $change, $userid, $faction_id);
    $stmt->execute();

    echo "Your standings with faction ID $faction_id have been updated.";
} else {
    echo "NPC not found.";
}

$stmt->close();
$conn->close();
?>
