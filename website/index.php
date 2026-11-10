<?php
// signup.php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: dashboard.php");
    exit;
}

require_once "db.php";

$fname = $email = $phone = "";
$fname_err = $email_err = $phone_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate Full Name
    if(empty(trim($_POST["fname"]))){
        $fname_err = "Please enter your name.";
    } else {
        $fname = trim($_POST["fname"]);
    }
    
    // Validate Email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else {
        $sql = "SELECT id FROM users WHERE email = :email";
        if($stmt = $pdo->prepare($sql)){
            $param_email = trim($_POST["email"]);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_err = "This email is already registered.";
                } else {
                    $email = trim($_POST["email"]);
                }
            }
            unset($stmt);
        }
    }

    // Validate Phone Number
    if(empty(trim($_POST["phone"]))){
        $phone_err = "Please enter your phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }
    
    // If no errors, store all 3 items in session and go to step 2
    if(empty($fname_err) && empty($email_err) && empty($phone_err)){
        $_SESSION["reg_fname"] = $fname;
        $_SESSION["reg_email"] = $email;
        $_SESSION["reg_phone"] = $phone;
        
        header("location: signup1.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                <b>
                    <button type="button" name="button" class="bt"><a href="index.php">Signup</a></button>
                    <button type="button" name="button" class="bt"><a href="login.php">Login</a></button>

                    <form id="myForm" action="" method="post">
                        <div class="form">
                            <label for="fname">Name :</label>
                            <input type="text" name="fname" id="fname" placeholder="Full Name" required>
                        </div>
                         <div class="formp">
                            <label for="phone">PHONE NO. :</label>
                            <input type="tel" name="phone" id="phone" placeholder="1234567890" required>
                        </div>
                        <div class="forme">
                            <label for="email">EMAIL :</label>
                            <input type="text" name="email" id="email" placeholder="name@example.com" required>
                        </div>
                        <button type="submit" name="submit">Submit</button>
                        <p>Sigu Up To Log In Into Website</p>
                    </form>
                    <script src="form.js"></script>
                </b>
            </section>
        </main>
    </div>
    <footer>
        <p>&copy; 2024 My Website. All rights reserved.</p>
    </footer>
</body>

</html>