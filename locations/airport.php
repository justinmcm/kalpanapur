<?php
require_once "includes/GameEngine.php";

// Establish a single database connection
$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$message = ''; // Default message

// Handle flight booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['destination'])) {
    $destination = $_POST['destination'];
    $ticketCost = 5000; // Fixed cost for simplicity

    $stmt = $conn->prepare('SELECT money FROM users WHERE userid = ?');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($currentMoney);
    $stmt->fetch();
    $stmt->close();

    if ($currentMoney >= $ticketCost) {
        // Deduct money and update location (future functionality placeholder)
        $stmt = $conn->prepare('
            UPDATE users 
            SET money = money - ? 
            WHERE userid = ?
        ');
        if (!$stmt) {
            die('Query preparation failed: ' . $conn->error);
        }
        $stmt->bind_param('ii', $ticketCost, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $message = "Flight to {$destination} booked successfully! ₹{$ticketCost} has been deducted.";
        } else {
            $message = "Failed to book flight.";
        }
        $stmt->close();
    } else {
        $message = "You don't have enough money to book this flight. ₹{$ticketCost} is required.";
    }
}

// Close the database connection
$conn->close();
?>

<div class="location-page">
    <h1>Kalpanapur International Airport</h1>
    <p>Welcome to the Kalpanapur International Airport. From here, you can book flights to explore new areas and continue your journey.</p>

    <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Flight Booking -->
    <div class="flight-booking">
        <h2>Book a Flight</h2>
        <form method="POST">
            <label for="destination">Select Destination:</label>
            <select id="destination" name="destination" required>
                <option value="New City">New City</option>
                <option value="Tropical Island">Tropical Island</option>
                <option value="Mountain Base">Mountain Base</option>
            </select>
            <p>Cost: ₹5000 per flight</p>
            <button type="submit">Book Flight</button>
        </form>
    </div>
</div>
 <!-- Back Button -->
 <div class="back-button">
        <a href="index.php?page=city">← Back to City</a>
    </div>
<style>
    .location-page {
        padding: 20px;
        color: #fff;
    }

    .message {
        color: #32cd32;
        margin-bottom: 15px;
    }

    .flight-booking {
        margin-top: 20px;
    }

    .flight-booking form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .flight-booking select,
    .flight-booking button {
        padding: 10px;
        border: 1px solid #444;
        border-radius: 5px;
        background-color: #333;
        color: #fff;
    }

    .flight-booking button {
        cursor: pointer;
    }

    .flight-booking button:hover {
        background-color: #444;
    }
</style>
