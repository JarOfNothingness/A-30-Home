<?php
session_start();
include("../LoginRegisterAuthentication/connection.php");

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userid']; // Assuming you store the user's ID in the session

// Retrieve folder_id from GET or POST
$folder_id = isset($_POST['folder_id']) ? $_POST['folder_id'] : (isset($_GET['folder_id']) ? $_GET['folder_id'] : null);

// Validate folder_id
if ($folder_id === null || !filter_var($folder_id, FILTER_VALIDATE_INT)) {
    die("Invalid folder ID.");
}

// Delete all files associated with the folder
$file_query = "SELECT file_path FROM fileserver_files WHERE folder_id = ?";
$file_stmt = $connection->prepare($file_query);
$file_stmt->bind_param("i", $folder_id);
$file_stmt->execute();
$file_result = $file_stmt->get_result();

while ($file = $file_result->fetch_assoc()) {
    if (file_exists($file['file_path'])) {
        unlink($file['file_path']); // Delete the file from the server
    }
}

// Delete the folder
$folder_query = "DELETE FROM fileserver_folders WHERE folder_id = ?";
$folder_stmt = $connection->prepare($folder_query);
$folder_stmt->bind_param("i", $folder_id);

if ($folder_stmt->execute()) {
    header("Location: admin_file_server.php");  
    exit();
} else {
    die("Failed to delete folder: " . $folder_stmt->error);
}

$folder_stmt->close();
$file_stmt->close();
$connection->close();
?>
