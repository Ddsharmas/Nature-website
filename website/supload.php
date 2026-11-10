<?php
session_start();
header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(["error" => "Unauthorized access."]);
    exit;
}

require_once "db.php";

$targetDir = "uploads/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $uploadedPaths = [];
    $errors = [];
    
    foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
        $fileName = basename($_FILES['files']['name'][$key]);
        // Secure filename with timestamp to prevent collisions
        $uniqueName = time() . '_' . bin2hex(random_bytes(4)) . '_' . $fileName;
        $targetFilePath = $targetDir . $uniqueName;
        
        // Validate that it's a legitimate image file
        $check = getimagesize($tmpName);
        if ($check !== false) {
            if (move_uploaded_file($tmpName, $targetFilePath)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO user_uploads (user_id, file_path) VALUES (:user_id, :file_path)");
                    $stmt->execute([
                        'user_id' => $_SESSION['id'],
                        'file_path' => $targetFilePath
                    ]);
                    $uploadedPaths[] = $targetFilePath;
                } catch (PDOException $e) {
                    $errors[] = "Database error saving reference for " . $fileName;
                }
            } else {
                $errors[] = "Failed to save file: " . $fileName;
            }
        } else {
            $errors[] = $fileName . " is not a valid image file.";
        }
    }
    
    echo json_encode([
        "status" => count($errors) === 0 ? "success" : "partial_error",
        "images" => $uploadedPaths,
        "errors" => $errors
    ]);
    exit;
}

echo json_encode(["error" => "No files submitted."]);
?>
