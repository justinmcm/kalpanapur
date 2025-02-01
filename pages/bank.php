<?php
require_once "includes/GameEngine.php";

$conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$userid = $_SESSION['user_id'];

// Fetch user's balances
$stmt = $conn->prepare('SELECT money, bank_balance FROM users WHERE userid = ?');
$stmt->bind_param('i', $userid);
$stmt->execute();
$userBalances = $stmt->get_result()->fetch_assoc();
$stmt->close();

$wallet = $userBalances['money'];
$bank = $userBalances['bank_balance'];

$message = '';

// Fetch active loan
$stmt = $conn->prepare('SELECT * FROM loans WHERE userid = ? AND remaining_balance > 0');
$stmt->bind_param('i', $userid);
$stmt->execute();
$activeLoan = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Apply Weekly Compounding Interest
if ($activeLoan) {
    $today = new DateTime();
    $lastInterestDate = new DateTime($activeLoan['last_interest_date']);
    $interval = $lastInterestDate->diff($today)->days;

    if ($interval >= 7) {
        $weeksElapsed = floor($interval / 7);
        $remainingBalance = $activeLoan['remaining_balance'];

        // Apply 2.5% interest for each elapsed week
        for ($i = 0; $i < $weeksElapsed; $i++) {
            $remainingBalance += $remainingBalance * 0.025;
        }

        // Update loan with new balance and last_interest_date
        $stmt = $conn->prepare('
            UPDATE loans 
            SET remaining_balance = ?, last_interest_date = ?
            WHERE loan_id = ?
        ');
        $newDate = $today->format('Y-m-d');
        $stmt->bind_param('dsi', $remainingBalance, $newDate, $activeLoan['loan_id']);
        $stmt->execute();
        $stmt->close();

        $activeLoan['remaining_balance'] = $remainingBalance;
        $activeLoan['last_interest_date'] = $newDate;

        $message = "Weekly interest has been applied to your loan.";
    }
}

// Handle Bank and Loan Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $amount = (int)$_POST['amount'];

    if ($action === 'deposit' && $amount > 0 && $amount <= $wallet) {
        // Deposit money into the bank
        $stmt = $conn->prepare('
            UPDATE users 
            SET money = money - ?, bank_balance = bank_balance + ?
            WHERE userid = ?
        ');
        $stmt->bind_param('iii', $amount, $amount, $userid);
        $stmt->execute();
        $stmt->close();

        $message = "You deposited ₹$amount into your bank account.";
        $bank += $amount;
        $wallet -= $amount;
    } elseif ($action === 'withdraw' && $amount > 0 && $amount <= $bank) {
        // Withdraw money from the bank
        $stmt = $conn->prepare('
            UPDATE users 
            SET money = money + ?, bank_balance = bank_balance - ?
            WHERE userid = ?
        ');
        $stmt->bind_param('iii', $amount, $amount, $userid);
        $stmt->execute();
        $stmt->close();

        $message = "You withdrew ₹$amount from your bank account.";
        $bank -= $amount;
        $wallet += $amount;
    } elseif ($action === 'borrow' && !$activeLoan) {
        // Borrow money
        if ($amount > 0 && $amount <= 500000) {
            // Insert loan details into the database
            $stmt = $conn->prepare('
                INSERT INTO loans (userid, amount_borrowed, remaining_balance, daily_payment, start_date, due_date, last_interest_date)
                VALUES (?, ?, ?, 0, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 10 DAY), CURDATE())
            ');
            $stmt->bind_param('iii', $userid, $amount, $amount);
            if ($stmt->execute()) {
                $stmt->close(); // Close the first statement here
    
                // Add loaned amount to the user's wallet
                $stmt2 = $conn->prepare('
                    UPDATE users SET money = money + ? WHERE userid = ?
                ');
                $stmt2->bind_param('ii', $amount, $userid);
                $stmt2->execute();
                $stmt2->close(); // Ensure the second statement is closed properly
    
                $message = "You borrowed ₹$amount. Interest will be applied weekly.";
                $wallet += $amount; // Update wallet balance
            } else {
                $message = "Failed to borrow money.";
                $stmt->close(); // Ensure the statement is closed even if an error occurs
            }
        } else {
            $message = "Invalid loan amount. Maximum loan is ₹5,00,000.";
        }
    
    
    } elseif ($action === 'repay' && $activeLoan) {
        // Repay loan
        if ($amount > 0 && $amount <= $wallet) {
            $remainingBalance = $activeLoan['remaining_balance'] - $amount;

            // Update loan balance
            $stmt = $conn->prepare('
                UPDATE loans SET remaining_balance = ? WHERE loan_id = ?
            ');
            $stmt->bind_param('di', $remainingBalance, $activeLoan['loan_id']);
            $stmt->execute();
            $stmt->close();

            // Deduct repayment amount from user's wallet
            $stmt = $conn->prepare('
                UPDATE users SET money = money - ? WHERE userid = ?
            ');
            $stmt->bind_param('ii', $amount, $userid);
            $stmt->execute();
            $stmt->close();

            $message = "You repaid ₹$amount. Remaining loan balance: ₹$remainingBalance.";

            $activeLoan['remaining_balance'] = $remainingBalance;
            $wallet -= $amount;

            if ($remainingBalance <= 0) {
                $message .= " Your loan has been fully repaid!";
            }
        } else {
            $message = "Invalid repayment amount.";
        }
    }
}

$conn->close();
?>

<h1>Bank</h1>

<p>Your Wallet Balance: ₹<?= $wallet ?></p>
<p>Your Bank Balance: ₹<?= $bank ?></p>

<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<h2>Deposit Money</h2>
<form method="POST">
    <input type="hidden" name="action" value="deposit">
    <input type="number" name="amount" placeholder="Enter amount to deposit" required>
    <button type="submit">Deposit</button>
</form>

<h2>Withdraw Money</h2>
<form method="POST">
    <input type="hidden" name="action" value="withdraw">
    <input type="number" name="amount" placeholder="Enter amount to withdraw" required>
    <button type="submit">Withdraw</button>
</form>

<h2>Loans</h2>
<?php if ($activeLoan): ?>
    <h3>Active Loan</h3>
    <p>Amount Borrowed: ₹<?= $activeLoan['amount_borrowed'] ?></p>
    <p>Remaining Balance: ₹<?= round($activeLoan['remaining_balance']) ?></p>
    <p>Last Interest Date: <?= $activeLoan['last_interest_date'] ?></p>
    <form method="POST">
        <input type="hidden" name="action" value="repay">
        <input type="number" name="amount" placeholder="Enter amount to repay" required>
        <button type="submit">Repay</button>
    </form>
<?php else: ?>
    <h3>Take a Loan</h3>
    <form method="POST">
        <input type="hidden" name="action" value="borrow">
        <input type="number" name="amount" placeholder="Enter loan amount (max ₹5,00,000)" required>
        <button type="submit">Borrow</button>
    </form>
<?php endif; ?>
