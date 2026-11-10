<?php
// update.php
session_start();

// 1. Kick out unauthorized users immediately
if (!isset($_SESSION['id']) || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

// 2. Validate that the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_update'])) {
    
    // CRITICAL SECURITY CHECK: Validate CSRF Token before any processing
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['update_err'] = "Security token validation failed. Please try again.";
        header("Location: edit_profile.php");
        exit();
    }
    
    // Gather and clean all inputs from the form array
    $newUsername = trim($_POST['username'] ?? '');
    $newPassword = $_POST['password'] ?? '';
    $newFName    = trim($_POST['fname'] ?? '');
    $newPhone    = trim($_POST['phone'] ?? '');
    $newEmail    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    
    $loggedInUserId = $_SESSION['id']; 

    // Structural constraint validation
    if (empty($newUsername) || empty($newFName) || empty($newPhone) || !$newEmail) {
        $_SESSION['update_err'] = "Please fill in all required fields with valid information.";
        header("Location: edit_profile.php");
        exit();
    }

    try {
        // Build the base query string mapping properties
        $sql = "UPDATE users SET username = :username, fname = :fname, phone = :phone, email = :email";
        
        $params = [
            ':username' => $newUsername,
            ':fname'    => $newFName,
            ':phone'    => $newPhone,
            ':email'    => $newEmail,
            ':id'       => $loggedInUserId
        ];

        // Conditional password injection strategy
        if (!empty($newPassword)) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Update current structural session markers so things refresh smoothly
        $_SESSION['username'] = $newUsername;
        
        // Regenerate CSRF token array parameters to prevent re-submission attacks
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $_SESSION['update_msg'] = "Your profile has been updated successfully!";
        header("Location: edit_profile.php");
        exit();

    } catch (\PDOException $e) {
        error_log("Profile Update Error Trace: " . $e->getMessage());
        
        // Catch duplicate database unique row indexing codes safely
        if ($e->getCode() == 23000) {
            $_SESSION['update_err'] = "The username or email address is already in use by another account.";
        } else {
            $_SESSION['update_err'] = "A database error occurred while saving your data.";
        }
        
        header("Location: edit_profile.php");
        exit();
    }
} else {
    header("Location: edit_profile.php");
    exit();
}
?>