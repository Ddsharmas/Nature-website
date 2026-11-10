<?php
session_start();

// 1. Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "db.php";

// 2. Fetch the fresh details of the logged-in user using their session ID
try {
    // Modified query to fetch the profile_pic value along with the username
    $stmt = $pdo->prepare("SELECT username, profile_pic FROM users WHERE id = :id");

    $stmt->execute(['id' => $_SESSION['id']]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. If for some reason the user is no longer found in the DB, log them out
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    // --- PERMANENT FIX: Check if database contains a valid uploaded photo path ---
    if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) {
        $profile_pic = $user['profile_pic'];
    } else {
        // Fallback to default original SVG source image
        $profile_pic = "https://ssc.gov.in/candidate-portal/assets/image/Profile_White.svg";
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <header>
        <h1>Welcome To The Home Page.</h1>
    </header>
    <nav class="navbar">
        <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">☰</button>
        <ul class="nav-items">
            <li><a href="home.php">Home</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li class="dashboard-card">
                <a href="dashboard.php">
                    <img class="profile profile-preview" src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
                </a>
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
                <h1>Nature Website</h1>
                <p>our content goes here. we are publish our best images of nature.</p>
                <section>
                    <div class="image-container">
                        <img src="https://static.vecteezy.com/vite/assets/photo-masthead-375-BoK_p8LG.webp"
                            alt="natuer image"></img>
                    </div>
                    <footer>
                        <p>&copy; 2024 My Website. All rights reserved.</p>
                    </footer>
                </section>
            </main>
        </div>
    </div>
    <script src="main.js"></script>
    <script src="history.js"></script>
</body>

</html>