<?php
require_once "includes/GameEngine.php";

// Establish a single database connection
$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Fetch current happiness and max happiness
$currentHappiness = 0; // Default fallback
$maxHappiness = 500;   // Default fallback
$stmt = $conn->prepare('
    SELECT u.happiness, p.max_happiness 
    FROM users u
    JOIN user_properties up ON u.userid = up.userid
    JOIN properties p ON up.property_id = p.property_id
    WHERE u.userid = ? AND up.is_moved_in = TRUE
');
if (!$stmt) {
    die('Query preparation failed: ' . $conn->error);
}
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($currentHappiness, $maxHappiness);
if (!$stmt->fetch()) {
    $currentHappiness = 0;
    $maxHappiness = 500;
}
$stmt->close();

// Check if the user is currently boosted
$additionalGain = 0; // Default additional gain
$currentTime = time();
$stmt = $conn->prepare('
    SELECT boost_end_time 
    FROM npc_boosts 
    WHERE userid = ? AND boost_end_time > ?
');
if (!$stmt) {
    die('Query preparation failed: ' . $conn->error);
}
$stmt->bind_param('ii', $_SESSION['user_id'], $currentTime);
$stmt->execute();
$stmt->bind_result($boostEndTime);
if ($stmt->fetch() && $boostEndTime > $currentTime) {
    $random = mt_rand(1, 100);
    if ($random <= 3) {
        $additionalGain = 3;
    } elseif ($random <= 13) {
        $additionalGain = 1.5;
    } elseif ($random <= 33) {
        $additionalGain = 1;
    } elseif ($random <= 63) {
        $additionalGain = 0.5;
    }
}
$stmt->close();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stat'])) {
    $stat = $_POST['stat'];
    $energyCost = 10;

    $stmt = $conn->prepare('SELECT energy FROM users WHERE userid = ?');
    if (!$stmt) {
        die('Query preparation failed: ' . $conn->error);
    }
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($currentEnergy);
    $stmt->fetch();
    $stmt->close();

    if ($currentEnergy < $energyCost) {
        $message = "Not enough energy to train!";
    } else {
        $happinessMultiplier = ($currentHappiness / $maxHappiness) * 5;
        $baseGain = 1;
        $boostedGain = $baseGain * $additionalGain;
        $totalGain = ceil($baseGain * (1 + $happinessMultiplier) + $boostedGain);

        $stmt = $conn->prepare('
            UPDATE users 
            SET energy = energy - ?, 
                happiness = happiness - 10, 
                ' . $stat . ' = ' . $stat . ' + ? 
            WHERE userid = ?
        ');
        if (!$stmt) {
            die('Query preparation failed: ' . $conn->error);
        }
        $stmt->bind_param('iii', $energyCost, $totalGain, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $message = ucfirst($stat) . " increased by $totalGain!";
            if ($additionalGain > 0) {
                $message .= " (Boost applied: +{$boostedGain}!)";
            }
        } else {
            $message = "Failed to update stats: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>


<div class="gym-container">
    <?php if (!empty($message)): ?>
        <p class="gym-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Gym Buttons Grid -->
    <div class="gym-grid">
        <form method="POST">
            <button type="submit" name="stat" value="strength">Train Strength</button>
            <button type="submit" name="stat" value="agility">Train Agility</button>
            <button type="submit" name="stat" value="defense">Train Defense</button>
            <button type="submit" name="stat" value="dexterity">Train Dexterity</button>
        </form>
    </div>
</div>

<!-- Gym Page Styles -->
<style>
    .gym-container {
        padding: 20px;
    }

    .gym-message {
        font-size: 1rem;
        margin-bottom: 20px;
        color: #fff;
        background-color: #444;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
    }

    .gym-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }

    .gym-grid button {
        background-color: #333;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        font-size: 0.9rem;
        transition: background-color 0.3s;
    }

    .gym-grid button:hover {
        background-color: #555;
    }
</style>
