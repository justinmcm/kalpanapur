<?php
// Establish a local database connection for this file
$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$page = $_GET['page'] ?? 'home'; // Default to 'home' if no page is specified

// Fetch NPCs for the current page
$stmt = $conn->prepare('SELECT name, description, file FROM npcs WHERE page = ?');
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param('s', $page);
$stmt->execute();
$stmt->bind_result($npc_name, $npc_description, $npc_file);
$has_npcs = false;

// Buffer output to isolate rendering issues
ob_start();

while ($stmt->fetch()) {
    $has_npcs = true;
    ?>
    <div class="npc">
        <div class="npc-photo">😊</div>
        <div class="npc-details">
            <p class="npc-name"><?= htmlspecialchars($npc_name) ?></p>
            <p class="npc-description"><?= htmlspecialchars($npc_description) ?></p>
        </div>
        <form method="POST" action="npc/johnf.php">
    <input type="hidden" name="npc" value="John Flex">
    <button type="submit" name="choice" value="yes">Yes, Let's do it</button>
    <button type="submit" name="choice" value="no">Maybe another time</button>
</form>

    </div>
    <?php
}

$npc_output = ob_get_clean(); // Store the buffered output
$stmt->close();
$conn->close(); // Close the connection here

// Render NPC section only if NPCs exist
if ($has_npcs): ?>
    <div class="npc-section">
        <?= $npc_output ?>
    </div>
<?php endif;
?>
