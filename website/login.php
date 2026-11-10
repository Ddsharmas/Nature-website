<?php
// login.php
session_start();

// Redirect straight to dashboard if already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

require_once "db.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    if(empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            
            if($stmt->execute()) {
                if($stmt->rowCount() == 1) {
                    if($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $db_username = $row["username"];
                        $hashed_password = $row["password"];
                        
                        if(password_verify($password, $hashed_password)) {
                            // SECURITY FIX: Prevent session fixation hijacking
                            session_regenerate_id(true);
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $db_username;
                            
                            header("location: dashboard.php");
                            exit; 
                        } else {
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else {
                error_log("Database execution failed during a login attempt.");
                $login_err = "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="form.css">
</head>

<body>
    <header>
        <h1>Welcome to My Website</h1>
    </header>
    <div class="container">
        <main>
            <h1>SIGNUP FORM</h1>
            <section>
                <button type="button" name="button" class="bt"><a href="index.php">Signup</a></button>
                <button type="button" name="button" class="bt"><a href="login.php">Login</a></button>
                <?php if(!empty($login_err)): ?>
                    <div class="alert-danger"><?php echo $login_err; ?></div>
                <?php endif; ?>
                <div id="myForm">
                    <form action="login.php" method="post">
                        <div class="form">
                            <label for="username">USERNAME :</label>
                            <input type="text" name="username" id="username" placeholder="Name" required>
                            <!-- FIXED: Added Error Message Output -->
                            <?php if(!empty($username_err)): ?>
                                <span class="error-msg"><?php echo $username_err; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="formp">
                            <label for="password">PASSWORD :</label>
                            <input type="password" name="password" id="password" placeholder="*******" required>
                            <!-- FIXED: Added Error Message Output -->
                            <?php if(!empty($password_err)): ?>
                                <span class="error-msg"><?php echo $password_err; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <button type="submit" name="submit">Submit</button>
                        </div>
                        <p>Sign Up To Log In Into Website</p>
                    </form>
                </div>
                <script src="form.js"></script>
            </section>
        </main>
    </div>
    <footer>
        <p>&copy; 2026 My Website. All rights reserved.</p>
    </footer>
</body>

</html>