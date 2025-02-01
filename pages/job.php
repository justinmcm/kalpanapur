<?php
require_once "includes/GameEngine.php";

$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$userid = $_SESSION['user_id'];

// Fetch user's working stats
$stmt = $conn->prepare('SELECT intelligence, manual_labor, endurance FROM users WHERE userid = ?');
$stmt->bind_param('i', $userid);
$stmt->execute();
$userStats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch user's current job
$stmt = $conn->prepare('
    SELECT uj.job_id, uj.position_id, uj.last_pay_date, jp.title, jp.salary, 
           jp.intelligence_reward, jp.manual_labor_reward, jp.endurance_reward, 
           j.name AS job_name
    FROM user_jobs uj
    JOIN job_positions jp ON uj.position_id = jp.position_id
    JOIN jobs j ON uj.job_id = j.job_id
    WHERE uj.userid = ?
');
$stmt->bind_param('i', $userid);
$stmt->execute();
$currentJob = $stmt->get_result()->fetch_assoc();
$stmt->close();

$message = '';

// Calculate pay and stat rewards if the user has a job
if ($currentJob) {
    $lastPayDate = new DateTime($currentJob['last_pay_date']);
    $now = new DateTime();
    $interval = $lastPayDate->diff($now);
    $daysElapsed = $interval->days;

    if ($daysElapsed > 0) {
        $payout = $daysElapsed * $currentJob['salary'];
        $intelligenceReward = $daysElapsed * $currentJob['intelligence_reward'];
        $manualLaborReward = $daysElapsed * $currentJob['manual_labor_reward'];
        $enduranceReward = $daysElapsed * $currentJob['endurance_reward'];

        // Update user's money and working stats
        $stmt = $conn->prepare('
            UPDATE users 
            SET money = money + ?, intelligence = intelligence + ?, manual_labor = manual_labor + ?, endurance = endurance + ? 
            WHERE userid = ?
        ');
        $stmt->bind_param('iiiii', $payout, $intelligenceReward, $manualLaborReward, $enduranceReward, $userid);
        $stmt->execute();
        $stmt->close();

        // Reset last pay date
        $stmt = $conn->prepare('UPDATE user_jobs SET last_pay_date = NOW() WHERE userid = ?');
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $stmt->close();

        $message = "You received ₹$payout for $daysElapsed days of work! 
                    Additionally, you gained $intelligenceReward intelligence, $manualLaborReward manual labor, and $enduranceReward endurance.";
    }
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'apply') {
        $jobId = (int)$_POST['job_id'];

        // Fetch job prerequisites
        $stmt = $conn->prepare('
            SELECT min_intelligence, min_manual_labor, min_endurance
            FROM job_positions
            WHERE job_id = ? AND position_id = 1
        ');
        $stmt->bind_param('i', $jobId);
        $stmt->execute();
        $requirements = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Check if the user meets the prerequisites
        if (
            $userStats['intelligence'] >= $requirements['min_intelligence'] &&
            $userStats['manual_labor'] >= $requirements['min_manual_labor'] &&
            $userStats['endurance'] >= $requirements['min_endurance']
        ) {
            // Assign the user to the job
            $stmt = $conn->prepare('INSERT INTO user_jobs (userid, job_id, position_id, last_pay_date) VALUES (?, ?, 1, NOW())');
            $stmt->bind_param('ii', $userid, $jobId);
            if ($stmt->execute()) {
                $message = "You have joined the job!";
            } else {
                $message = "Failed to join the job.";
            }
            $stmt->close();
        } else {
            $message = "You do not meet the prerequisites for this job.";
        }
    } elseif ($_POST['action'] === 'promote') {
        // Fetch the next position
        $stmt = $conn->prepare('
            SELECT position_id, title, min_intelligence, min_manual_labor, min_endurance, salary
            FROM job_positions
            WHERE job_id = ? AND position_id > ?
            ORDER BY position_id ASC LIMIT 1
        ');
        $stmt->bind_param('ii', $currentJob['job_id'], $currentJob['position_id']);
        $stmt->execute();
        $nextPosition = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Check if the user meets the promotion requirements
        if ($nextPosition &&
            $userStats['intelligence'] >= $nextPosition['min_intelligence'] &&
            $userStats['manual_labor'] >= $nextPosition['min_manual_labor'] &&
            $userStats['endurance'] >= $nextPosition['min_endurance']
        ) {
            // Promote the user
            $stmt = $conn->prepare('UPDATE user_jobs SET position_id = ? WHERE userid = ?');
            $stmt->bind_param('ii', $nextPosition['position_id'], $userid);
            $stmt->execute();
            $stmt->close();

            $message = "Congratulations! You have been promoted to " . htmlspecialchars($nextPosition['title']) . "!";
        } else {
            $message = "You do not meet the requirements for promotion.";
        }
    } elseif ($_POST['action'] === 'quit') {
        // Remove the user's job
        $stmt = $conn->prepare('DELETE FROM user_jobs WHERE userid = ?');
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $stmt->close();

        $message = "You have quit your job.";
    }
}

// Fetch available jobs for users without a job
if (!$currentJob) {
    $stmt = $conn->prepare('SELECT * FROM jobs');
    $stmt->execute();
    $availableJobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<h1>Jobs</h1>
<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($currentJob): ?>
    <h2>Current Job: <?= htmlspecialchars($currentJob['job_name']) ?></h2>
    <p>Position: <?= htmlspecialchars($currentJob['title']) ?></p>
    <p>Daily Salary: ₹<?= htmlspecialchars($currentJob['salary']) ?></p>
    <form method="POST">
        <input type="hidden" name="action" value="promote">
        <button type="submit">Request Promotion</button>
    </form>
    <form method="POST">
        <input type="hidden" name="action" value="quit">
        <button type="submit" style="background-color: red; color: white;">Quit Job</button>
    </form>
<?php else: ?>
    <h2>Available Jobs</h2>
    <?php foreach ($availableJobs as $job): ?>
        <form method="POST">
            <input type="hidden" name="action" value="apply">
            <input type="hidden" name="job_id" value="<?= $job['job_id'] ?>">
            <p><?= htmlspecialchars($job['name']) ?> - <?= htmlspecialchars($job['description']) ?></p>
            <button type="submit">Apply</button>
        </form>
    <?php endforeach; ?>
<?php endif; ?>