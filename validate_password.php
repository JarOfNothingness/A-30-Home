<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

include("../LoginRegisterAuthentication/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folder_id = $_POST['folder_id'];
    $password = $_POST['password'];
    $action_type = $_POST['action_type'];

    // Sanitize input to prevent SQL injection
    $folder_id = mysqli_real_escape_string($connection, $folder_id);
    $password = mysqli_real_escape_string($connection, $password);

    // Fetch the folder's password from the database
    $query = "SELECT folder_password FROM fileserver_folders WHERE folder_id = '$folder_id'";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashed_password = $row['folder_password'];

        // Verify the provided password against the stored hashed password
        if (password_verify($password, $hashed_password)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Folder not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
