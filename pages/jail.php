<?php
$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');

$stmt = $conn->prepare("SELECT reason, jail_time_remaining FROM jail WHERE userid = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($reason, $jailTime);
$isInJail = false;

if ($stmt->fetch() && $jailTime > 0) {
    $isInJail = true;
    echo "<h3 style='color: #F98B85; text-align: left;'>You are in Jail</h3>";
    echo "<p style='text-align: left;'>$reason</p>";
    echo "<p style='text-align: left;'>Time Remaining: $jailTime minutes</p>";

    // Attempt Escape
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['escape'])) {
        $stmt->close();
        $stmt = $conn->prepare("SELECT nerve FROM users WHERE userid = ?");
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($currentNerve);
        $stmt->fetch();

        if ($currentNerve >= 7) {
            $stmt->close();
            $stmt = $conn->prepare("UPDATE users SET nerve = nerve - 7 WHERE userid = ?");
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();

            $roll = rand(1, 100);
            if ($roll <= 50) {
                $stmt = $conn->prepare("DELETE FROM jail WHERE userid = ?");
                $stmt->bind_param('i', $_SESSION['user_id']);
                $stmt->execute();
                echo "<p style='text-align: left;'>Success! You escaped jail.</p>";
            } else {
                echo "<p style='text-align: left;'>Failed! You remain in jail.</p>";
            }
        } else {
            echo "<p style='text-align: left;'>Not enough nerve to attempt an escape!</p>";
        }
    }

    echo '<form method="POST" style="text-align: left;">
        <button type="submit" name="escape" class="jail-action-btn">Attempt Escape (Costs 7 Nerve)</button>
    </form>';

    // Pay Bail
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bail'])) {
        $stmt->close();
        $stmt = $conn->prepare("SELECT money FROM users WHERE userid = ?");
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($currentMoney);
        $stmt->fetch();

        $bailCost = $jailTime * 1000;
        if ($currentMoney >= $bailCost) {
            $stmt->close();
            $stmt = $conn->prepare("UPDATE users SET money = money - ? WHERE userid = ?");
            $stmt->bind_param('ii', $bailCost, $_SESSION['user_id']);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM jail WHERE userid = ?");
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            echo "<p style='text-align: left;'>You paid ₹$bailCost and were released from jail.</p>";
        } else {
            echo "<p style='text-align: left;'>You don't have enough money to pay the bail!</p>";
        }
    }

    echo '<form method="POST" style="text-align: left;">
        <button type="submit" name="bail" class="jail-action-btn">Pay Bail (₹1,000 per minute)</button>
    </form>';
} else {
    echo "<h3 style='color: #F98B85; text-align: left;'>Jail</h3>";
    echo "<p style='text-align: left;'>You are not in jail.</p>";
}

$stmt->close();
$conn->close();
?>
