<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalpanapur</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="top-header">
        <h1>Kalpanapur</h1>
        <nav class="top-nav">
            <a href="?page=settings">⚙️ Settings</a>
            <a href="?page=logout">🚪 Logout</a>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
                <a href="/kalpanapur/admin/index.php">🛠️ Admin Panel</a>
            <?php endif; ?>
        </nav>
    </header>



    <div class="layout">
    <aside class="left-sidebar">
    <section class="player-stats">
        <p>👤 Justin [1]</p>
        <p>🏅 Level: 5</p>
        
        <?php
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }

        // Fetch energy and money
        $stmt = $conn->prepare('SELECT energy, money FROM users WHERE userid = ?');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($currentEnergy, $money);
        $stmt->fetch();
        $stmt->close();

        // Fetch current happiness and max happiness
        $currentHappiness = 0;
        $maxHappiness = 500; // Default fallback
        $stmt = $conn->prepare('SELECT u.happiness, p.max_happiness 
            FROM users u
            JOIN user_properties up ON u.userid = up.userid
            JOIN properties p ON up.property_id = p.property_id
            WHERE u.userid = ? AND up.is_moved_in = TRUE');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($currentHappiness, $maxHappiness);
        if (!$stmt->fetch()) {
            $currentHappiness = 0;
            $maxHappiness = 500;
        }
        $stmt->close();
        $conn->close();
        ?>

        <!-- Money -->
        <p>💰 Money: ₹<?= $money ?></p>

        <!-- Energy Bar -->
        <div class="stat-bar">
            <p>⚡ Energy: <?= $currentEnergy ?>/100</p>
            <div class="bar">
                <div class="fill" style="width: <?= ($currentEnergy / 100) * 100 ?>%; background-color: #FFD700;"></div>
            </div>
        </div>

        <!-- Happiness Bar -->
        <div class="stat-bar">
            <p>😊 Happiness: <?= htmlspecialchars($currentHappiness) ?>/<?= htmlspecialchars($maxHappiness) ?></p>
            <div class="bar">
                <div class="fill" style="width: <?= ($currentHappiness / $maxHappiness) * 100 ?>%; background-color: #32CD32;"></div>
            </div>
        </div>

        <!-- Placeholder Nerve and Life Bars -->
        <?php
        $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }

        $stmt = $conn->prepare('SELECT nerve FROM users WHERE userid = ?');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($currentNerve);
        $stmt->fetch();
        $stmt->close();
        $conn->close();

        $maxNerve = 35; // Default max nerve capacity
        ?>

        <div class="stat-bar">
            <p>⚡ Nerve: <?= htmlspecialchars($currentNerve) ?>/<?= $maxNerve ?></p>
            <div class="bar">
                <div class="fill" style="width: <?= ($currentNerve / $maxNerve) * 100 ?>%; background-color: #FF4500;"></div>
            </div>
        </div>

        <div class="stat-bar">
            <p>Life: 900/1000</p>
            <div class="bar">
                <div class="fill" style="width: 90%; background-color: #1E90FF;"></div>
            </div>
        </div>
    </section>
    <nav class="main-nav">
        <h3>Areas</h3>
        <ul>
            <?php
            $isJailed = false;
            $conn = new mysqli('localhost', 'root', '', 'kalpanapurdb');
            if ($conn->connect_error) {
                die('Database connection failed: ' . $conn->connect_error);
            }
            $stmt = $conn->prepare("SELECT jail_time_remaining FROM jail WHERE userid = ?");
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $stmt->bind_result($jailTime);
            if ($stmt->fetch() && $jailTime > 0) {
                $isJailed = true;
            }
            $stmt->close();
            $conn->close();
            ?>

            <?php if ($isJailed): ?>
                <li><a href="?page=jail">🚔 Jail</a></li>
            <?php else: ?>
                <li><a href="?page=home">🏠 Home</a></li>
                <li><a href="?page=inventory">📦 Inventory</a></li>
                <li><a href="?page=city">🌆 City</a></li>
                <li><a href="?page=job">💼 Job</a></li>
                <li><a href="?page=gym" class="<?php echo $currentEnergy == 100 ? 'highlight' : ''; ?>">🏋️ Gym</a></li>
                <li><a href="?page=properties">🏠 Properties</a></li>
                <li><a href="?page=education">📚 Education</a></li>
                <li><a href="?page=crimes" class="<?php echo $currentNerve == $maxNerve ? 'highlight' : ''; ?>">💣 Crimes</a></li>
                <li><a href="?page=jail">🚔 Jail</a></li>
                <li><a href="?page=hospital">🏥 Hospital</a></li>
                <li><a href="?page=casino">🎲 Casino</a></li>
                <li><a href="?page=mail" class="<?php echo $hasUnreadMessages ? 'highlight' : ''; ?>">✉️ Mailbox</a></li>
                <li><a href="?page=events">📅 Events</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>

<main class="content">
<?php
    // Dynamically determine the banner image and title
    if (isset($_GET['page'])) {
        $currentPage = $_GET['page'];
        $bannerImagePath = "images/banners/{$currentPage}_banner.jpeg";
        $bannerTitle = ucfirst(str_replace('_', ' ', $currentPage)); // Format title from the page name

        // Check if the banner image exists
        if (file_exists($bannerImagePath)) {
            echo "
            <div class='banner'>
                <img src='" . htmlspecialchars($bannerImagePath) . "' alt='" . htmlspecialchars($bannerTitle) . "'>
                <h1 class='banner-title'>" . htmlspecialchars($bannerTitle) . "</h1>
            </div>
            ";
        }
    }
    ?>