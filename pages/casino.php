<?php
require_once "includes/GameEngine.php";

$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$userid = $_SESSION['user_id'];

// Fetch user's money
$stmt = $conn->prepare('SELECT money FROM users WHERE userid = ?');
$stmt->bind_param('i', $userid);
$stmt->execute();
$stmt->bind_result($money);
$stmt->fetch();
$stmt->close();

// Initialize variables
$message = '';
$result = '';
$bet = $_POST['bet'] ?? ($_SESSION['last_bet'] ?? 0); // Default to session or 0
$game = $_POST['game'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $bet > 0 && $bet <= $money) {
    if ($game === 'slots') {
        // Slots logic
        $icons = ['🍒', '⭐', '💎'];
        $slot1 = $icons[array_rand($icons)];
        $slot2 = $icons[array_rand($icons)];
        $slot3 = $icons[array_rand($icons)];

        $result = "$slot1 | $slot2 | $slot3";

        // Check for matches
        if ($slot1 === $slot2 && $slot2 === $slot3) {
            $winnings = $bet * 5;
            $message = "Jackpot! You won ₹$winnings!";
        } elseif ($slot1 === $slot2 || $slot2 === $slot3 || $slot1 === $slot3) {
            $winnings = $bet * 2;
            $message = "You won ₹$winnings!";
        } else {
            $winnings = -$bet;
            $message = "You lost ₹$bet.";
        }
    } elseif ($game === 'dice') {
        // Dice roll logic
        $playerGuess = (int)$_POST['guess'];
        $diceRoll = rand(1, 6);
        $result = "Dice rolled: $diceRoll";

        if ($playerGuess === $diceRoll) {
            $winnings = $bet * 6;
            $message = "You guessed correctly and won ₹$winnings!";
        } else {
            $winnings = -$bet;
            $message = "You lost ₹$bet.";
        }
    } elseif ($game === 'coin') {
        // Coin flip logic
        $playerGuess = $_POST['guess'];
        $coinFlip = rand(0, 1) ? 'heads' : 'tails';
        $result = "Coin flipped: $coinFlip";

        if ($playerGuess === $coinFlip) {
            $winnings = $bet * 2;
            $message = "You guessed correctly and won ₹$winnings!";
        } else {
            $winnings = -$bet;
            $message = "You lost ₹$bet.";
        }
    }

    // Update user's money
    $stmt = $conn->prepare('UPDATE users SET money = money + ? WHERE userid = ?');
    $stmt->bind_param('ii', $winnings, $userid);
    $stmt->execute();
    $stmt->close();

    $money += $winnings;

    // Save the current bet as the last bet in the session
    $_SESSION['last_bet'] = $bet;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = "Invalid bet amount.";
}

$conn->close();
?>

<h1>Casino</h1>
<p>Your balance: ₹<?= $money ?></p>
<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>
<?php if ($result): ?>
    <p><?= htmlspecialchars($result) ?></p>
<?php endif; ?>

<!-- Slots -->
<h2>Slots</h2>
<form method="POST">
    <input type="hidden" name="game" value="slots">
    <input type="number" name="bet" placeholder="Bet Amount" value="<?= htmlspecialchars($bet) ?>" required>
    <button type="submit">Spin</button>
</form>

<!-- Dice Roll -->
<h2>Dice Roll</h2>
<form method="POST">
    <input type="hidden" name="game" value="dice">
    <input type="number" name="bet" placeholder="Bet Amount" value="<?= htmlspecialchars($bet) ?>" required>
    <input type="number" name="guess" placeholder="Your Guess (1-6)" required>
    <button type="submit">Roll</button>
</form>

<!-- Coin Flip -->
<h2>Coin Flip</h2>
<form method="POST">
    <input type="hidden" name="game" value="coin">
    <input type="number" name="bet" placeholder="Bet Amount" value="<?= htmlspecialchars($bet) ?>" required>
    <select name="guess">
        <option value="heads">Heads</option>
        <option value="tails">Tails</option>
    </select>
    <button type="submit">Flip</button>
</form>
