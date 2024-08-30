<?php
session_start();
include("../LoginRegisterAuthentication/connection.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userid']; // Assuming you store the user's ID in the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $folder_id = $_POST['folder_id'];
    $file = $_FILES['file_upload'];

    // Check if the selected folder belongs to the current user
    $folder_check_query = "SELECT folder_id FROM fileserver_folders WHERE folder_id = ? AND user_id = ?";
    $stmt = $connection->prepare($folder_check_query);
    $stmt->bind_param("ii", $folder_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        die("Error: You do not have permission to upload files to this folder.");
    }

    // Check if the uploads directory exists and is writable
    $uploads_dir = __DIR__ . '/uploads/';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true); // Create the directory if it doesn't exist
    }
    
    if (!is_writable($uploads_dir)) {
        die("Uploads directory is not writable.");
    }

    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_name = $file['name'];
        $file_path = $uploads_dir . basename($file_name);

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Insert file info into the database with the user ID
            $query = "INSERT INTO userfileserverfiles (folder_id, file_name, file_path, uploaded_at, user_id) VALUES (?, ?, ?, NOW(), ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("issi", $folder_id, $file_name, $file_path, $user_id);
            if ($stmt->execute()) {
                header("Location: fileserver.php");
                exit();
            } else {
                die("Failed to upload file: " . $stmt->error);
            }
        } else {
            die("Failed to move uploaded file.");
        }
    } else {
        die("File upload error: " . $file['error']);
    }
}
?>
