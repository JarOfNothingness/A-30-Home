<?php
session_start();
include("../LoginRegisterAuthentication/connection.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userid']; // Assuming you store the user's ID in the session
$folder_id = $_GET['folder_id'];

// Validate folder_id
if (!filter_var($folder_id, FILTER_VALIDATE_INT)) {
    die("Invalid folder ID.");
}

// Begin transaction
$connection->begin_transaction();

try {
    // Delete all files associated with the folder
    $file_query = "SELECT file_path FROM userfileserverfiles WHERE folder_id = ?";
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
    $folder_query = "DELETE FROM userfileserverfolders WHERE folder_id = ? AND userid = ?";
    $folder_stmt = $connection->prepare($folder_query);
    $folder_stmt->bind_param("ii", $folder_id, $user_id);

    if ($folder_stmt->execute()) {
        // Commit the transaction
        $connection->commit();
        header("Location: fileserver.php");
        exit();
    } else {
        // Rollback the transaction on error
        $connection->rollback();
        die("Failed to delete folder: " . $folder_stmt->error);
    }

    $folder_stmt->close();
    $file_stmt->close();
} catch (Exception $e) {
    // Rollback the transaction on exception
    $connection->rollback();
    die("An error occurred: " . $e->getMessage());
}

$connection->close();
?>
