<?php
session_start();

include("../LoginRegisterAuthentication/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folder_id = $_POST['folder_id'];
    $folder_password = $_POST['folder_password'];
    $action_type = $_POST['action_type'];

    // Fetch the folder password from the database
    $query = "SELECT folder_password FROM userfileserverfolders WHERE folder_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $folder_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $folder = $result->fetch_assoc();

    if ($folder && password_verify($folder_password, $folder['folder_password'])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
