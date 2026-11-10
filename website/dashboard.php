<?php
// dashboard.php
session_start();

// 1. Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "db.php";

// 2. Fetch the fresh details of the logged-in user using their session ID
try {
    // FIXED: Added 'profile_pic' to the selected columns
    $stmt = $pdo->prepare("SELECT username, email, fname, created_at, profile_pic FROM users WHERE id = :id");

    $stmt->execute(['id' => $_SESSION['id']]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. If for some reason the user is no longer found in the DB, log them out
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    // --- CHECK: Validate uploaded photo path or fallback to default ---
    if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) {
        $profile_pic = $user['profile_pic'];
    } else {
        // Fallback to default original SVG source image
        $profile_pic = "https://ssc.gov.in/candidate-portal/assets/image/Profile_White.svg";
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// 4. Securely display the dashboard to your user
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <header>
        <h1>Hi, <b>
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
            </b>. Welcome to your dashboard!</h1>
    </header>
    <nav class="navbar">
        <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">>☰</button>
        <ul class="nav-items">
            <li><a href="home.php">Home</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li class="dashboard-card">
                <a href="dashboard.php">
                    <img class="profile profile-preview" id="dashboardVisual" src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
                </a>
                <b><button class="dashboard-btn" id="dashboardBtn">▾</button></b>
                <ul class="dropdown-menu" id="dropdownMenu">
                    <li><a href="history.php">📜 View History</a></li>
                    <li><a href="settings.php">⚙️ Settings</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div class="page-content-wrapper">
        <div id="currentTitle"></div>
        <ul id="historyList" class="history-list">
            <!-- JavaScript dynamically inserts browsing logs here -->
        </ul>
        <div class="container">
            <main>
                <section>
                    <div class="info-group">
                        <h1>Welcome back, <?php echo htmlspecialchars($user['fname']); ?>!</h1>
                    </div>
                    <div class="info-group">
                        <div class="label">Email Address</div>
                        <div class="value"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    <div class="info-group">
                        <div class="label">Member Since</div>
                        <div class="value"><?php echo date('F d, Y', strtotime($user['created_at'])); ?></div>
                    </div>
                    <footer>
                        <p>&copy; 2024 My Website. All rights reserved.</p>
                    </footer>
                </section>
            </main>
        </div>
    </div>
    <script src="main.js"></script>
    <script src="dashboard.js"></script>
    <script src="history.js"></script>
</body>

</html>