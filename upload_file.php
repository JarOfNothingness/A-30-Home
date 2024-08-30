<?php
include("../LoginRegisterAuthentication/connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $folder_id = $_POST['folder_id'];
    $file_name = $_FILES['file_upload']['name'];
    $file_tmp = $_FILES['file_upload']['tmp_name'];

    $upload_dir = 'uploads/';
    $file_path = $upload_dir . basename($file_name);

    if (move_uploaded_file($file_tmp, $file_path)) {
        $query = "INSERT INTO fileserver_files (folder_id, file_name, file_path) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("iss", $folder_id, $file_name, $file_path);
        if ($stmt->execute()) {
            header("Location: admin_file_server.php");
            exit();
        } else {
            die("Failed to upload file: " . mysqli_error($connection));
        }
    } else {
        die("Failed to move uploaded file.");
    }
}
?>
