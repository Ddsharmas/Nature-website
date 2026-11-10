<?php
session_start();
header('Content-Type: application/json');

// 1. Ensure the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Require connection details to update the database
require_once "db.php"; 

// 2. Setup and create the local upload folder securely
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// 3. Process the file payload from JavaScript
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $file = $_FILES['profile_image'];
    
    // Check for standard server-side upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'File upload error code: ' . $file['error']]);
        exit;
    }

    // Limit image size to 2MB to preserve server space
    if ($file['size'] > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File size limits exceeded (Max 2MB).']);
        exit;
    }

    // Verify safe image file extensions using deep content inspection
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($mimeType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file format. Please upload JPG, PNG, or WEBP.']);
        exit;
    }

    // 4. Generate a unique file name using user ID to prevent name conflicts
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = "user_" . $_SESSION['id'] . "_" . time() . "." . $extension;
    $destination = $uploadDir . $newFilename;

    // 5. Finalize the file relocation
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        
        // --- PERMANENT CHANGE: Update the path in the database ---
        try {
            $stmt = $pdo->prepare("UPDATE users SET profile_pic = :profile_pic WHERE id = :id");
            $stmt->execute([
                'profile_pic' => $destination,
                'id' => $_SESSION['id']
            ]);
            
            // Return success response along with the brand new destination path
            echo json_encode([
                'success' => true,
                'filepath' => $destination
            ]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error updating profile image reference.']);
            exit;
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'System error relocating uploaded file. Check directory write permissions.']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid Request Method.']);
