<?php
include("../LoginRegisterAuthentication/connection.php");
include("functions.php");

session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Get user ID from query parameter
$userId = $_GET['id'] ?? 0;

// Ensure a valid user ID is provided
if ($userId == 0) {
    die("Invalid user ID.");
}

// Query to delete user's profile from user_profiles table
$query = "DELETE FROM user_profiles WHERE user_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: admin_file_server.php?msg=ProfileDeleted");
    exit();
} else {
    die("Profile deletion failed. Please try again.");
}
?>
