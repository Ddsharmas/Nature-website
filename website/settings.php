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
        header("Location: form1.php");
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
    <title>Account Settings</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <header>
        <h1>Account Settings</h1>
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
        <div class="container">
            <main>
                <b>
                    <div class="profile-container">
                        <strong>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</strong>

                        <img class="profile_picture profile-preview" src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">

                        <input type="file" id="imageUploader" class="file-input-hidden" accept="image/*">
                        <label for="imageUploader" class="upload-label-btn">
                            📷 Update Image
                        </label>
                    </div>
                    <div class="edit">
                        <a href="edit_profile.php">
                            Edit Your Details
                        </a>
                    </div>
                    <a href="logout.php">
                        <button id="signOutBtn">🚪 <b>Sign Out</b></button>
                    </a>
                </b>
                <footer>
                    <p>&copy; 2026 My Website. All rights reserved.</p>
                </footer>
            </main>
        </div>
    </div>
    <script src="main.js"></script>
    <script src="dashboard.js"></script>
    <script src="history.js"></script>

</body>

</html>