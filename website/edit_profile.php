<?php
// edit_profile.php
session_start();

// 1. Force authorization layer verification
if (!isset($_SESSION['id']) || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// 2. Generate a secure fallback CSRF tracking token if missing
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once "db.php";
$userId = $_SESSION['id'];

// 3. Fetch latest data map records to avoid reading stale information
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $currentUser = $stmt->fetch();

    if (!$currentUser) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (\PDOException $e) {
    error_log("Failed retrieving active profile mapping details: " . $e->getMessage());
    die("An unexpected error occurred loading your layout configuration details.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Your Profile</title>
    <link rel="stylesheet" href="form.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <main>
            <section>
                <h1>Edit Your Details</h1>

                <!-- Render alert notification feedback loops accurately -->
                <?php 
                if (isset($_SESSION['update_msg'])) {
                    echo '<p style="color: green;">' . htmlspecialchars($_SESSION['update_msg']) . '</p>';
                    unset($_SESSION['update_msg']);
                }
                if (isset($_SESSION['update_err'])) {
                    echo '<p style="color: red;">' . htmlspecialchars($_SESSION['update_err']) . '</p>';
                    unset($_SESSION['update_err']);
                }
                ?>

                <form action="update.php" method="POST">
                    <!-- Secure Token validation hidden tracking node -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="form">
                        <label for="username">USERNAME :</label>
                        <input type="text" name="username" id="username"
                            value="<?php echo htmlspecialchars($currentUser['username']); ?>" required>
                    </div>

                    <div class="formp">
                        <label for="password">NEW PASSWORD (Leave blank to keep current) :</label>
                        <input type="password" name="password" id="password" placeholder="Leave blank to stay unchanged">
                    </div>
                    
                    <div class="form">
                        <label for="fname">Name :</label>
                        <input type="text" name="fname" id="fname"
                            value="<?php echo htmlspecialchars($currentUser['fname']); ?>" required>
                    </div>
                    
                    <div class="formp">
                        <label for="phone">PHONE NO. :</label>
                        <input type="tel" name="phone" id="phone"
                            value="<?php echo htmlspecialchars($currentUser['phone']); ?>" required>
                    </div>
                    
                    <div class="forme">
                        <label for="email">EMAIL :</label>
                        <input type="email" name="email" id="email"
                            value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                    </div>
                    
                    <button type="submit" name="submit_update">Save Changes</button>
                </form>
                <p><a href="dashboard.php">Back to Dashboard</a></p>
            </section>
        </main>
    </div>
</body>
</html>