<?php
require_once "includes/admin_auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Management</title>
    <style>
        .error-message { color: red; font-size: 14px; }
        .info-message { color: blue; font-size: 14px; }
    </style>
</head>
<body>
    <h1>Item Management</h1>
    <form method="POST" onsubmit="return validateForm();">
        <h2>Add Item</h2>
        <label>Item Name: <input type="text" name="item_name" required></label><br>
        <label>Item Description: <textarea name="item_description" required></textarea></label><br>

        <label>Effect (optional): 
            <select name="item_effect" id="item_effect" onchange="handleEffectChange()">
                <option value="">-- Select an Effect --</option>
                <option value="heal_50">Heal 50 HP</option>
                <option value="boost_strength">Boost Strength by 10%</option>
                <option value="boost_agility">Boost Agility by 10%</option>
                <option value="boost_energy">Restore 20 Energy</option>
                <option value="nerve_restore">Restore 5 Nerve</option>
                <option value="custom">Custom Effect (Type Below)</option>
            </select>
        </label><br>
        
        <label>Custom Effect (only if "Custom" is selected): 
            <input type="text" name="custom_effect" id="custom_effect" disabled placeholder="effect_name:value">
        </label><br>
        <p class="info-message">Example Formats:
            <ul>
                <li><b>heal:50</b> (Heals 50 HP)</li>
                <li><b>boost_strength:10</b> (Boosts Strength by 10%)</li>
                <li><b>restore_energy:20</b> (Restores 20 Energy)</li>
            </ul>
        </p>
        <p id="error-message" class="error-message"></p>

        <button type="submit" name="add_item">Add Item</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
        $item_name = htmlspecialchars($_POST['item_name']);
        $item_description = htmlspecialchars($_POST['item_description']);
        $item_effect = htmlspecialchars($_POST['item_effect']);
        
        // Use custom effect if "custom" is selected
        if ($item_effect === "custom" && !empty($_POST['custom_effect'])) {
            $item_effect = htmlspecialchars($_POST['custom_effect']);
        }

        // Validate custom effect format (PHP side validation)
        if ($item_effect === "custom" && !preg_match('/^[a-zA-Z0-9_]+:\d+$/', $item_effect)) {
            echo "<p class='error-message'>Invalid custom effect format. Use effect_name:value (e.g., heal:50).</p>";
        } else {
            $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
            if ($conn->connect_error) {
                die('Database connection failed: ' . $conn->connect_error);
            }

            $stmt = $conn->prepare('INSERT INTO items (name, description, effect) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $item_name, $item_description, $item_effect);
            if ($stmt->execute()) {
                echo "<p>Item added successfully!</p>";
            } else {
                echo "<p>Error adding item: " . $stmt->error . "</p>";
            }
            $stmt->close();
            $conn->close();
        }
    }
    ?>

    <script>
        function handleEffectChange() {
            const effectSelect = document.getElementById("item_effect");
            const customEffectInput = document.getElementById("custom_effect");

            if (effectSelect.value === "custom") {
                customEffectInput.disabled = false;
                customEffectInput.required = true;
            } else {
                customEffectInput.disabled = true;
                customEffectInput.value = "";
                customEffectInput.required = false;
            }
        }

        function validateForm() {
            const effectSelect = document.getElementById("item_effect");
            const customEffectInput = document.getElementById("custom_effect");
            const errorMessage = document.getElementById("error-message");

            // Clear previous error messages
            errorMessage.textContent = "";

            if (effectSelect.value === "custom") {
                const customValue = customEffectInput.value.trim();
                const regex = /^[a-zA-Z0-9_]+:\d+$/;

                if (!regex.test(customValue)) {
                    errorMessage.textContent = "Invalid custom effect format. Use effect_name:value (e.g., heal:50).";
                    return false; // Prevent form submission
                }
            }

            return true; // Allow form submission
        }
    </script>
</body>
</html>
