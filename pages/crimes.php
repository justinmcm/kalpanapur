<?php
require_once "includes/GameEngine.php";

$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Fetch crimes grouped by category
$query = "
    SELECT cc.category_name, c.crime_id, c.name AS crime_name, c.storyline, c.nerve_cost
    FROM crime_categories cc
    LEFT JOIN crimes c ON cc.category_id = c.category_id
    ORDER BY cc.category_name, c.nerve_cost
";
$result = $conn->query($query);

$crimeData = [];
while ($row = $result->fetch_assoc()) {
    $crimeData[$row['category_name']][] = $row;
}

// Initialize a variable for the result message
$resultMessage = '';

if (isset($_GET['attempt'])) {
    $crimeId = (int)$_GET['attempt'];

    // Fetch crime details
    $stmt = $conn->prepare("SELECT name, nerve_cost, success_rate, reward_min, reward_max, critical_failure_rate FROM crimes WHERE crime_id = ?");
    $stmt->bind_param('i', $crimeId);
    $stmt->execute();
    $stmt->bind_result($crimeName, $nerveCost, $baseSuccessRate, $rewardMin, $rewardMax, $criticalFailureRate);
    $stmt->fetch();
    $stmt->close();

    // Fetch user's current nerve and crime experience
    $stmt = $conn->prepare("SELECT nerve, crime_experience FROM users WHERE userid = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($currentNerve, $crimeExperience);
    $stmt->fetch();
    $stmt->close();

    // Check if user has enough nerve
    if ($currentNerve < $nerveCost) {
        $resultMessage = "<p class='result-failure'>You don't have enough nerve to attempt this crime.</p>";
    } else {
        // Calculate effective success rate
        $effectiveSuccessRate = getEffectiveSuccessRate($baseSuccessRate, $crimeExperience);

        // Roll for success
        $roll = rand(1, 100);
        if ($roll <= $effectiveSuccessRate) {
            // Success: Calculate reward and update stats
            $reward = rand($rewardMin, $rewardMax);

            $stmt = $conn->prepare("UPDATE users SET money = money + ?, crime_experience = crime_experience + 1, nerve = nerve - ? WHERE userid = ?");
            $stmt->bind_param('iii', $reward, $nerveCost, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();

            $resultMessage = "<p class='result-success'>Success! You earned ₹$reward.</p>";
        } elseif ($roll > (100 - $criticalFailureRate)) {
            // Critical Failure: Busted by police, sent to jail
            $reason = 'Caught while attempting ' . $crimeName;
            $jailTime = rand(1, 3); // Random jail time in minutes
            $stmt = $conn->prepare("INSERT INTO jail (userid, reason, jail_time_remaining) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE reason = ?, jail_time_remaining = ?");
            $stmt->bind_param('isisi', $_SESSION['user_id'], $reason, $jailTime, $reason, $jailTime);
            $stmt->execute();
            $stmt->close();

            $resultMessage = "<p class='result-critical-failure'>Busted by police! You are now in jail for $jailTime minutes.</p>";
        } else {
            // Failure: Deduct nerve only
            $resultMessage = "<p class='result-failure'>Failure! You gained no reward.</p>";
            $stmt = $conn->prepare("UPDATE users SET nerve = nerve - ? WHERE userid = ?");
            $stmt->bind_param('ii', $nerveCost, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <title>Crimes</title>
</head>
<body>
    <div class="crime-container">
        <h1>Crimes</h1>
        <?php if (!empty($resultMessage)): ?>
            <div class="notification">
                <?= $resultMessage ?>
            </div>
        <?php endif; ?>

        <!-- Crime Categories -->
        <div id="categories">
            <?php foreach ($crimeData as $categoryName => $crimes): ?>
                <div class="crime-category">
                    <button class="crime-category-header" data-category="<?= htmlspecialchars($categoryName) ?>">
                        <?= htmlspecialchars($categoryName) ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Crime Details -->
        <?php foreach ($crimeData as $categoryName => $crimes): ?>
            <div class="crime-details" id="<?= htmlspecialchars($categoryName) ?>" style="display: none;">
                <button class="back-button">⬅️ Back</button>
                <h3><?= htmlspecialchars($categoryName) ?></h3>
                <table class="crime-table compact">
                    <thead>
                        <tr>
                            <th>Crime</th>
                            <th>Nerve</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($crimes as $crime): ?>
                            <tr>
                                <td><?= htmlspecialchars($crime['crime_name']) ?></td>
                                <td><?= htmlspecialchars($crime['nerve_cost']) ?></td>
                                <td>
                                    <form method="POST" action="?page=crimes&attempt=<?= $crime['crime_id'] ?>">
                                        <button type="submit" class="attempt-button">Attempt</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Show crimes in a category
        document.querySelectorAll('.crime-category-header').forEach(button => {
            button.addEventListener('click', () => {
                const category = button.dataset.category;

                // Hide all categories and show selected category details
                document.getElementById('categories').style.display = 'none';
                document.querySelectorAll('.crime-details').forEach(detail => detail.style.display = 'none');
                document.getElementById(category).style.display = 'block';
            });
        });

        // Back button functionality
        document.querySelectorAll('.back-button').forEach(button => {
            button.addEventListener('click', () => {
                // Hide all crime details and show categories
                document.querySelectorAll('.crime-details').forEach(detail => detail.style.display = 'none');
                document.getElementById('categories').style.display = 'block';
            });
        });
    </script>
</body>
</html>
