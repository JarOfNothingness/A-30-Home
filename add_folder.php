<?php
include("../LoginRegisterAuthentication/connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $folder_name = $_POST['folder_name'];
    $folder_password = password_hash($_POST['folder_password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO fileserver_folders (folder_name, folder_password) VALUES (?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $folder_name, $folder_password);
    if ($stmt->execute()) {
        header("Location: admin_file_server.php");
        exit();
    } else {
        die("Failed to add folder: " . mysqli_error($connection));
    }
}
?>
