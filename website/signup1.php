<?php
// signup1.php
session_start();
require_once "db.php";

// Redirect back to Step 1 if values are missing
if(!isset($_SESSION["reg_fname"]) || !isset($_SESSION["reg_email"]) || !isset($_SESSION["reg_phone"])){
    header("location: index.php");
    exit;
}

$username = $password = "";
$username_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate username uniqueness
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT id FROM users WHERE username = :username";
        if($stmt = $pdo->prepare($sql)){
            $param_username = trim($_POST["username"]);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            }
            unset($stmt);
        }
    }
    
    // Validate password length
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // If credentials are valid, complete single-row entry
    if(empty($username_err) && empty($password_err)){
        
        // Final complete SQL insert statement
        $sql = "INSERT INTO users (fname, email, phone, username, password) VALUES (:fname, :email, :phone, :username, :password)";
         
        if($stmt = $pdo->prepare($sql)){
            // Grab Step 1 values from System Session
            $stmt->bindParam(":fname", $_SESSION["reg_fname"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $_SESSION["reg_email"], PDO::PARAM_STR);
            $stmt->bindParam(":phone", $_SESSION["reg_phone"], PDO::PARAM_STR);
            
            // Grab Step 2 values from current POST form data
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            
            // Securely hash the password string
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
            
            if($stmt->execute()){
                // Clear out temporary session tracking arrays
                unset($_SESSION["reg_fname"]);
                unset($_SESSION["reg_email"]);
                unset($_SESSION["reg_phone"]);
                
                header("location: login.php?signup=success");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
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
    <title>Signup Step 2</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="form.css">
</head>

<body>
    <header>
        <h1>Welcome to My Website</h1>
    </header>
    <div class="container">
        <main>
            <h1>LOGIN FORM</h1>
            <section>
                <button type="button" name="button" class="bt"><a href="index.html">Signup</a></button>
                <button type="button" name="button" class="bt"><a href="form.html">Login</a></button>
                <form id="myForm" action="" method="post">
                    <div class="form">
                        <label for="username">USERNAME :</label>
                        <!-- Retain entered username value so they don't have to retype it -->
                        <input type="text" name="username" id="username" placeholder="Name" required>
                        <?php if(!empty($username_err)): ?>
                            <span class="error-msg"><?php echo $username_err; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="formp">
                        <label for="password">PASSWORD :</label>
                        <input type="password" name="password" id="password" placeholder="*******" required>
                        <?php if(!empty($password_err)): ?>
                            <span class="error-msg"><?php echo $password_err; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="su">
                        <button type="submit" name="submit">Submit</button>
                    </div>
                </form>
                <script src="form.js"></script>
                <p>Log In To See The Website</p>
            </section>
        </main>
    </div>
    <footer>
        <p>&copy; 2026 My Website. All rights reserved.</p>
    </footer>
</body>

</html>