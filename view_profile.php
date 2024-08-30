<?php
include("../LoginRegisterAuthentication/connection.php");
include("functions.php");

session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: AdminFileServer.php");
    exit();
}

$user_id = $_GET['id'];
$query = "SELECT * FROM user WHERE userid = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007bff;
            padding: 20px;
            text-align: center;
            color: #fff;
            font-size: 24px;
            width: 100%;
        }

        .container {
            padding: 20px;
        }

        .profile-info {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        View User Profile
    </header>
    <div class="container">
        <h2>User Profile</h2>
        <div class="profile-info">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($user['userid']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($user['contact_number']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($user['status']); ?></p>
        </div>
        <a href="admin_file_server.php" class="btn btn-primary">Back to List</a>
    </div>
</body>
</html>
