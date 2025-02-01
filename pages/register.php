<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }

        // Insert new user
        $stmt = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->bind_param('ss', $username, $hashedPassword);

        if ($stmt->execute()) {
            $userid = $conn->insert_id; // Get the newly created user's ID

            // Function to initialize default standings
            function createDefaultStandings($userid, $conn) {
                $factionsResult = $conn->query("SELECT faction_id FROM factions");
                if ($factionsResult->num_rows > 0) {
                    $stmt = $conn->prepare("INSERT INTO standings (userid, faction_id, value) VALUES (?, ?, 0.00)");
                    while ($row = $factionsResult->fetch_assoc()) {
                        $stmt->bind_param('ii', $userid, $row['faction_id']);
                        $stmt->execute();
                    }
                    $stmt->close();
                }
            }

            // Initialize standings for the new user
            createDefaultStandings($userid, $conn);

            echo '<p>Registration successful. <a href="?page=login">Login here</a>.</p>';
        } else {
            echo '<p>Error: ' . $stmt->error . '</p>';
        }

        $stmt->close();
        $conn->close();
    } else {
        echo '<p>Please fill in all fields.</p>';
    }
}
?>

<h2>Register</h2>
<form method="POST">
    <label>Username:</label>
    <input type="text" name="username" required>
    <br>
    <label>Password:</label>
    <input type="password" name="password" required>
    <br>
    <button type="submit">Register</button>
</form>
