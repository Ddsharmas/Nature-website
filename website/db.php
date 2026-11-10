<?php
// db.php
$host = 'sql105.infinityfree.com';
$db   = 'if0_43370454_user_system';
$user = 'if0_43370454';
$pass = 'ddevks2006';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Essential for catching duplicate profile entries
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     error_log("Database connection failure: " . $e->getMessage());
     die("A secure connection layer could not be established.");
}
?>