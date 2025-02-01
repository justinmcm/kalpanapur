<?php
require_once "includes/GameEngine.php";

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debug: Check script execution
echo "Debug: Script is running.<br>";

// Establish a database connection
$conn = getDatabaseConnection();
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
echo "Debug: Database connection successful.<br>";

// Debug: Check session and user ID
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("Debug: User not logged in. Redirecting...");
    header('Location: index.php?page=login');
    exit;
}
echo "Debug: User ID is $user_id.<br>";

// Fetch user money
$user_query = $conn->prepare("SELECT money FROM users WHERE userid = ?");
if (!$user_query) {
    die("Debug: User query preparation failed: " . $conn->error);
}
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_data = $user_query->get_result()->fetch_assoc();
$user_money = $user_data['money'] ?? 0;
echo "Debug: User money fetched: $user_money.<br>";

// Fetch available properties
$properties_query = $conn->prepare("SELECT * FROM properties WHERE availability = TRUE");
if (!$properties_query) {
    die("Debug: Properties query preparation failed: " . $conn->error);
}
$properties_query->execute();
$available_properties = $properties_query->get_result();
if (!$available_properties) {
    die("Debug: Failed to fetch available properties: " . $conn->error);
}
echo "Debug: Properties fetched successfully.<br>";

// Debug: Output fetched properties
while ($property = $available_properties->fetch_assoc()) {
    echo "Debug: Property - " . htmlspecialchars($property['name']) . " | Price: " . $property['price'] . "<br>";
}
$available_properties->data_seek(0); // Reset pointer for rendering

// Handle property purchase
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
    $property_id = intval($_POST['property_id']);
    echo "Debug: Attempting to purchase property ID $property_id.<br>";

    // Check property price
    $property_query = $conn->prepare("SELECT price FROM properties WHERE property_id = ?");
    if (!$property_query) {
        die("Debug: Property query preparation failed: " . $conn->error);
    }
    $property_query->bind_param("i", $property_id);
    $property_query->execute();
    $property_data = $property_query->get_result()->fetch_assoc();

    if ($property_data && $user_money >= $property_data['price']) {
        echo "Debug: User has enough money to purchase.<br>";
        // Deduct money and add property
        $conn->begin_transaction();
        try {
            $deduct_money = $conn->prepare("UPDATE users SET money = money - ? WHERE userid = ?");
            if (!$deduct_money) {
                throw new Exception("Debug: Deduct money query preparation failed: " . $conn->error);
            }
            $deduct_money->bind_param("ii", $property_data['price'], $user_id);
            $deduct_money->execute();

            $add_property = $conn->prepare("INSERT INTO user_properties (user_id, property_id) VALUES (?, ?)");
            if (!$add_property) {
                throw new Exception("Debug: Add property query preparation failed: " . $conn->error);
            }
            $add_property->bind_param("ii", $user_id, $property_id);
            $add_property->execute();

            $conn->commit();
            $message = "You successfully purchased the property!";
            echo "Debug: Transaction committed successfully.<br>";
        } catch (Exception $e) {
            $conn->rollback();
            $message = "Transaction failed: " . $e->getMessage();
            echo "Debug: Transaction rolled back.<br>";
        }
    } else {
        $message = "You don't have enough money to purchase this property.";
        echo "Debug: User does not have enough money.<br>";
    }
}

// Close database connection
$conn->close();
?>

<h2>Real Estate Broker</h2>
<?php if (!empty($message)): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<h3>Available Properties</h3>
<?php while ($property = $available_properties->fetch_assoc()): ?>
    <div style="margin-bottom: 15px; padding: 10px; border: 1px solid #ccc;">
        <strong><?= htmlspecialchars($property['name']) ?></strong>
        <p>Price: $<?= number_format($property['price']) ?></p>
        <p>Happiness Boost: +<?= $property['max_happiness'] ?></p>
        <form method="POST">
            <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">
            <button type="submit" style="padding: 5px 10px;">Buy</button>
        </form>
    </div>
<?php endwhile; ?>
