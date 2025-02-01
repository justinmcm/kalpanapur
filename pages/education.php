<?php

require_once "includes/GameEngine.php";

// Establish database connection
$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$userid = $_SESSION['user_id'];

// Dynamically mark courses as completed if end time has passed
$query = "UPDATE user_education 
          SET completed = 1 
          WHERE userid = ? AND completed = 0 AND end_time <= NOW()";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userid);
$stmt->execute();
$stmt->close();

// Get the user's active course
$activeCourse = getActiveCourse($conn, $userid);

// Handle course enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $courseId = intval($_POST['course_id']);
    $message = enrollInCourse($conn, $userid, $courseId);
}

// Handle certificate collection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['collect_certificate'])) {
    $courseId = intval($_POST['collect_certificate']);
    $message = collectCourseCertificate($conn, $userid, $courseId);
}

// Handle leaving a course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_course'])) {
    $courseId = $activeCourse['course_id'];
    $query = "DELETE FROM user_education WHERE userid = ? AND course_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userid, $courseId);
    $stmt->execute();
    $stmt->close();
    $message = "You have left the course.";
    $activeCourse = null; // Reset active course
}

// Fetch all education categories and courses
$categories = getEducationCategories($conn, $userid);
?>

<h1>Education</h1>
<?php if (isset($message)): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($activeCourse): ?>
    <div style="display: flex; align-items: center; gap: 15px;">
        <h2 style="margin: 0;">Current Course: <?= htmlspecialchars($activeCourse['name']) ?></h2>
        <div style="width: 300px; height: 15px; background-color: #ddd; border: 1px solid #000; border-radius: 5px; position: relative;">
            <div style="width: <?= min(100, ($activeCourse['elapsed_minutes'] / $activeCourse['length']) * 100) ?>%; 
                        background-color: #4caf50; height: 100%; border-radius: 5px;"></div>
        </div>
        <span><?= min(100, round(($activeCourse['elapsed_minutes'] / $activeCourse['length']) * 100)) ?>%</span>
    </div>
    <form method="POST" style="margin-top: 10px;">
        <button type="submit" name="leave_course" style="background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;">Leave Course</button>
    </form>
    <?php if ($activeCourse['elapsed_minutes'] >= $activeCourse['length']): ?>
        <form method="POST" style="margin-top: 10px;">
            <input type="hidden" name="collect_certificate" value="<?= $activeCourse['course_id'] ?>">
            <button type="submit">Collect Course Certificate</button>
        </form>
    <?php endif; ?>
<?php else: ?>
    <p>You are not enrolled in any course.</p>
    <h2>Available Courses</h2>
    <?php foreach ($categories as $category_id => $category): ?>
        <div class="category">
            <h3><?= htmlspecialchars($category['category_name']) ?></h3>
            <ul>
            <?php foreach ($category['courses'] as $course): ?>
    <li>
        <?= htmlspecialchars($course['course_name']) ?> (<?= $course['length'] ?> hours) - <?= htmlspecialchars($course['reward']) ?>
        <?php if ($course['completed'] == 1): ?>
            <span style="color: green;">(Completed)</span>
        <?php elseif (!$activeCourse): ?>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                <button type="submit">Enroll</button>
            </form>
        <?php else: ?>
            <span style="color: gray;">(Currently unavailable)</span>
        <?php endif; ?>
    </li>
<?php endforeach; ?>

            </ul>
        </div>
    <?php endforeach; ?>
<?php endif; ?>


