<?php
include("../LoginRegisterAuthentication/connection.php");

$folder_id = $_POST['folder_id'];
$folder_password = $_POST['folder_password'];

$query = "SELECT folder_password FROM userfileserverfolders WHERE folder_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $folder_id);
$stmt->execute();
$stmt->bind_result($hashed_password);
$stmt->fetch();

$response = array('success' => password_verify($folder_password, $hashed_password));
echo json_encode($response);
?>
