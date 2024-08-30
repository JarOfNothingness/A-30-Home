<?php
session_start(); // Ensure session is started

include("../LoginRegisterAuthentication/connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $folder_name = $_POST['folder_name'];
    $folder_password = password_hash($_POST['folder_password'], PASSWORD_DEFAULT);
    
    // Get the user's ID from the session
    $user_id = $_SESSION['userid']; // Assuming you store the user's ID in the session

    $query = "INSERT INTO userfileserverfolders (userid, folder_name, folder_password) VALUES (?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("iss", $user_id, $folder_name, $folder_password);
    if ($stmt->execute()) {
        header("Location: fileserver.php");
        exit();
    } else {
        die("Failed to add folder: " . mysqli_error($connection));
    }
}
?>
