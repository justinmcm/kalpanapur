<?php


// Connect to the database
function getDatabaseConnection() {
    $servername = "localhost";
    $username = "root";
    $password = ""; // Update this if your MySQL root user has a password
    $dbname = "kalpanapurdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    return $conn;
}




//UPDATE ENERGY
function updateEnergy($userid) {
    $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }

    // Fetch current energy and last update time
    $stmt = $conn->prepare('SELECT energy, last_energy_update FROM users WHERE userid = ?');
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $stmt->bind_result($currentEnergy, $lastUpdate);
    $stmt->fetch();
    $stmt->close();

    // Calculate energy refill
    $maxEnergy = 100;
    $refillRate = 5; // Energy per interval
    $intervalMinutes = 5; // Interval duration in minutes

    $lastUpdateTime = new DateTime($lastUpdate);
    $now = new DateTime();
    $timeDiff = $lastUpdateTime->diff($now);
    $minutesPassed = ($timeDiff->days * 24 * 60) + ($timeDiff->h * 60) + $timeDiff->i;

    // Calculate how much energy to add
    $energyToAdd = floor($minutesPassed / $intervalMinutes) * $refillRate;

    if ($energyToAdd > 0) {
        $newEnergy = min($currentEnergy + $energyToAdd, $maxEnergy); // Cap at max energy

        // Update energy and last update time
        $stmt = $conn->prepare('UPDATE users SET energy = ?, last_energy_update = ? WHERE userid = ?');
        $newUpdateTime = $now->format('Y-m-d H:i:s');
        $stmt->bind_param('isi', $newEnergy, $newUpdateTime, $userid);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
}

// UPDATE NERVE
function updateNerve($userid) {
    $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }

    // Fetch current nerve and last update time
    $stmt = $conn->prepare('SELECT nerve, last_nerve_update FROM users WHERE userid = ?');
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $stmt->bind_result($currentNerve, $lastUpdate);
    $stmt->fetch();
    $stmt->close();

    // Define nerve regeneration parameters
    $maxNerve = 35; // Maximum nerve capacity
    $refillRate = 7; // Nerve points regenerated per interval
    $intervalMinutes = 1; // Interval duration in minutes

    // Calculate time elapsed since last nerve update
    $lastUpdateTime = new DateTime($lastUpdate);
    $now = new DateTime();
    $timeDiff = $lastUpdateTime->diff($now);
    $minutesPassed = ($timeDiff->days * 24 * 60) + ($timeDiff->h * 60) + $timeDiff->i;

    // Calculate how much nerve to add
    $nerveToAdd = floor($minutesPassed / $intervalMinutes) * $refillRate;

    if ($nerveToAdd > 0) {
        $newNerve = min($currentNerve + $nerveToAdd, $maxNerve); // Cap at max nerve

        // Update nerve and last update time
        $stmt = $conn->prepare('UPDATE users SET nerve = ?, last_nerve_update = ? WHERE userid = ?');
        $newUpdateTime = $now->format('Y-m-d H:i:s');
        $stmt->bind_param('isi', $newNerve, $newUpdateTime, $userid);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
}

// Calculate Effective Crime Success Rate
function getEffectiveSuccessRate($baseRate, $crimeExperience) {
    $experienceBonus = $crimeExperience * 0.1; // Add 0.1% per CE point
    $effectiveRate = $baseRate + $experienceBonus;

    // Cap the success rate at 95%
    return min($effectiveRate, 95.00);
}


// JAIL TIME
function updateJailTime($userid) {
    $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }

    // Fetch jail time and last update time
    $stmt = $conn->prepare("SELECT jail_time_remaining, last_update FROM jail WHERE userid = ?");
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $stmt->bind_result($jailTime, $lastUpdate);

    if ($stmt->fetch()) {
        error_log("Fetched jail time: $jailTime");
        error_log("Last update time: $lastUpdate");

        if ($jailTime > 0) {
            // Handle timezone explicitly (Indian Standard Time)
            $timezone = new DateTimeZone('Asia/Kolkata');
            $lastUpdateTime = new DateTime($lastUpdate, $timezone);
            $now = new DateTime('now', $timezone);

            $timeDiff = $lastUpdateTime->diff($now);
            $minutesPassed = ($timeDiff->days * 24 * 60) + ($timeDiff->h * 60) + $timeDiff->i;

            // Calculate new jail time
            $newJailTime = max(0, $jailTime - $minutesPassed);
            error_log("Minutes passed since last update: $minutesPassed");
            error_log("New jail time calculated: $newJailTime");

            if ($newJailTime > 0) {
                // Update jail time and last update
                $stmt->close();
                $stmt = $conn->prepare("UPDATE jail SET jail_time_remaining = ?, last_update = ? WHERE userid = ?");
                $nowFormatted = $now->format('Y-m-d H:i:s');
                $stmt->bind_param('isi', $newJailTime, $nowFormatted, $userid);
                $stmt->execute();
                error_log("Jail time updated successfully.");
            } elseif ($newJailTime === 0 && $jailTime > 0) {
                // Release the player if time is up
                error_log("Releasing player from jail. Jail time remaining: $newJailTime");
                $stmt->close();
                $stmt = $conn->prepare("DELETE FROM jail WHERE userid = ?");
                $stmt->bind_param('i', $userid);
                $stmt->execute();
            }
        } else {
            error_log("Jail time is not greater than 0. Skipping update.");
        }
    } else {
        error_log("No jail record found for userid $userid.");
    }

    $stmt->close();
    $conn->close();
}


//MAX HAPPINESS
function getMaxHappiness($userid) {
    $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }

    // Fetch max happiness from the moved-in property
    $stmt = $conn->prepare('
        SELECT p.max_happiness 
        FROM user_properties up
        JOIN properties p ON up.property_id = p.property_id
        WHERE up.userid = ? AND up.is_moved_in = TRUE
    ');
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $stmt->bind_result($maxHappiness);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    return $maxHappiness ?? 500; // Default to 500 if no property is moved into
}

//Update Happiness
function updateHappiness($userid) {
    $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }

    $stmt = $conn->prepare("
        SELECT u.happiness, u.last_happiness_update, p.max_happiness
        FROM users u
        JOIN user_properties up ON u.userid = up.userid
        JOIN properties p ON up.property_id = p.property_id
        WHERE up.is_moved_in = 1 AND u.userid = ?
    ");
    if (!$stmt) {
        die("SQL Error: " . $conn->error); // Error handling for failed prepare
    }
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $stmt->bind_result($currentHappiness, $lastUpdate, $maxHappiness);
    if (!$stmt->fetch()) {
        $stmt->close();
        $conn->close();
        return; // No moved-in property found, exit the function
    }
    $stmt->close();

    // Calculate time passed since last update
    $now = new DateTime();
    $lastUpdate = $lastUpdate ? new DateTime($lastUpdate) : new DateTime('now - 5 minutes');
    $timeDiff = $lastUpdate->diff($now);
    $minutesPassed = ($timeDiff->days * 24 * 60) + ($timeDiff->h * 60) + $timeDiff->i;

    if ($minutesPassed > 0) {
        $regenRate = 10;
        $happinessGain = floor($minutesPassed / 5) * $regenRate;
        $newHappiness = min($currentHappiness + $happinessGain, $maxHappiness);

        $stmt = $conn->prepare("UPDATE users SET happiness = ?, last_happiness_update = NOW() WHERE userid = ?");
        $stmt->bind_param('ii', $newHappiness, $userid);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
}





//GET CRIMES BY CATEGORY
function getCrimesByCategory() {
    $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
    $query = "
        SELECT cc.category_name, cc.description, c.crime_id, c.crime_name, c.nerve_cost
        FROM crime_categories cc
        LEFT JOIN crimes c ON cc.category_id = c.category_id
        ORDER BY cc.category_name, c.nerve_cost;
    ";
    $result = $conn->query($query);

    $crimes = [];
    while ($row = $result->fetch_assoc()) {
        $crimes[$row['category_name']]['description'] = $row['description'];
        $crimes[$row['category_name']]['crimes'][] = [
            'crime_id' => $row['crime_id'],
            'crime_name' => $row['crime_name'],
            'nerve_cost' => $row['nerve_cost']
        ];
    }
    $conn->close();
    return $crimes;
}
















// EDUCATION

/**
 * Get all education categories with their courses.
 */
function getEducationCategories($conn, $userid) {
    $query = "SELECT c.category_id, c.name AS category_name, c.description, 
                     e.course_id, e.name AS course_name, e.length, e.reward, e.is_master,
                     IFNULL(ue.completed, 0) AS completed
              FROM education_categories c
              LEFT JOIN education_courses e ON c.category_id = e.category_id
              LEFT JOIN user_education ue ON e.course_id = ue.course_id AND ue.userid = ?
              ORDER BY c.category_id, e.is_master, e.course_id";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[$row['category_id']]['category_name'] = $row['category_name'];
        $categories[$row['category_id']]['description'] = $row['description'];
        $categories[$row['category_id']]['courses'][] = $row;
    }
    return $categories;
}




/**
 * Check if a user has completed all courses in a category.
 */
function hasCompletedCategory($conn, $userid, $category_id) {
    $query = "SELECT COUNT(*) AS incomplete_count
              FROM education_courses ec
              LEFT JOIN user_education ue 
              ON ec.course_id = ue.course_id AND ue.userid = ?
              WHERE ec.category_id = ? AND (ue.completed IS NULL OR ue.completed = 0)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userid, $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['incomplete_count'] == 0;
}

/**
 * Get the user's active course.
 */
function getActiveCourse($conn, $userid) {
    $query = "SELECT ue.course_id, ec.name, ec.length, ue.start_time, ue.end_time, 
                     TIMESTAMPDIFF(MINUTE, ue.start_time, NOW()) AS elapsed_minutes 
              FROM user_education ue
              JOIN education_courses ec ON ue.course_id = ec.course_id
              WHERE ue.userid = ? AND ue.completed = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Enroll the user in a course.
 */
function enrollInCourse($conn, $userid, $courseId) {
    // Check if the user already has an active course
    $activeCourse = getActiveCourse($conn, $userid);
    if ($activeCourse) {
        return "You are already enrolled in a course!";
    }

    // Enroll in the course
    $query = "INSERT INTO user_education (userid, course_id, progress, completed, start_time, end_time) 
              VALUES (?, ?, 0, 0, NOW(), DATE_ADD(NOW(), INTERVAL (SELECT length FROM education_courses WHERE course_id = ?) MINUTE))
              ON DUPLICATE KEY UPDATE progress = 0, completed = 0, start_time = NOW(), 
                                      end_time = DATE_ADD(NOW(), INTERVAL (SELECT length FROM education_courses WHERE course_id = ?) MINUTE)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $userid, $courseId, $courseId, $courseId);
    if ($stmt->execute()) {
        return "Successfully enrolled in the course!";
    } else {
        return "Failed to enroll in the course.";
    }
}

/**
 * Mark the course as completed and collect the reward.
 */
function collectCourseCertificate($conn, $userid, $courseId) {
    // Validate course progress
    $query = "SELECT completed, start_time, end_time FROM user_education WHERE userid = ? AND course_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return "Error: Failed to prepare query.";
    }

    $stmt->bind_param("ii", $userid, $courseId);
    $stmt->execute();

    // Bind result variables
    $completed = 0;
    $startTime = null;
    $endTime = null;
    $stmt->bind_result($completed, $startTime, $endTime);

    if (!$stmt->fetch()) {
        $stmt->close();
        return "Course not found or no progress recorded.";
    }
    $stmt->close();

    // Check if the course is completed
    if ($completed == 1) {
        return "Certificate already collected for this course.";
    }

    // Validate end_time
    if (is_null($endTime)) {
        return "Error: Course end time is missing. Please contact support.";
    }

    // Check if the end time has passed
    $currentTime = new DateTime();
    $endDateTime = new DateTime($endTime);
    if ($currentTime < $endDateTime) {
        return "Failed to collect certificate. Course is not yet completed.";
    }

    // Mark course as completed
    $query = "UPDATE user_education SET completed = 1 WHERE userid = ? AND course_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userid, $courseId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();
        return "Course certificate collected successfully!";
    } else {
        $stmt->close();
        return "Error: Failed to update course completion.";
    }
}





?>
