<?php
session_start();
include("../LoginRegisterAuthentication/connection.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userid']; // Assuming you store the user's ID in the session
$file_id = isset($_POST['file_id']) ? $_POST['file_id'] : null;

// Debugging output
if ($file_id === null) {
    die("file_id parameter is missing.");
} elseif (!filter_var($file_id, FILTER_VALIDATE_INT)) {
    die("Invalid file ID format.");
}

// Get file path from the database
$file_query = "SELECT file_path FROM userfileserverfiles WHERE file_id = ? AND folder_id IN (SELECT folder_id FROM userfileserverfolders WHERE userid = ?)";
$file_stmt = $connection->prepare($file_query);
$file_stmt->bind_param("ii", $file_id, $user_id);
$file_stmt->execute();
$file_result = $file_stmt->get_result();
$file = $file_result->fetch_assoc();

if ($file) {
    // Delete the file from the server
    if (file_exists($file['file_path'])) {
        if (!unlink($file['file_path'])) {
            die("Failed to delete the file from the server.");
        }
    } else {
        die("File not found on the server.");
    }

    // Delete the file record from the database
    $delete_query = "DELETE FROM userfileserverfiles WHERE file_id = ?";
    $delete_stmt = $connection->prepare($delete_query);
    $delete_stmt->bind_param("i", $file_id);

    if ($delete_stmt->execute()) {
        header("Location: userviewfolder.php?folder_id=" . $_POST['folder_id']);
        exit();
    } else {
        die("Failed to delete file record: " . $delete_stmt->error);
    }

    $delete_stmt->close();
} else {
    die("File not found.");
}

$file_stmt->close();
$connection->close();
?>
