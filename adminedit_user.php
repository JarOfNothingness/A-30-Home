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

// Query to fetch user data from the `user` table
$queryUser = "SELECT * FROM user WHERE userid = ?";
$stmtUser = $connection->prepare($queryUser);
$stmtUser->bind_param("i", $userId);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$user = $resultUser->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Query to fetch profile data from the `user_profiles` table
$queryProfile = "SELECT * FROM user_profiles WHERE user_id = ?";
$stmtProfile = $connection->prepare($queryProfile);
$stmtProfile->bind_param("i", $userId);
$stmtProfile->execute();
$resultProfile = $stmtProfile->get_result();
$profile = $resultProfile->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update user account data
    $username = $_POST['username'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $updateUserQuery = "UPDATE user SET username = ?, name = ?, role = ?, status = ? WHERE userid = ?";
    $stmtUserUpdate = $connection->prepare($updateUserQuery);
    $stmtUserUpdate->bind_param("ssssi", $username, $name, $role, $status, $userId);
    $stmtUserUpdate->execute();

    // Update user profile data
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];

    if ($profile) {
        // If profile exists, update it
        $updateProfileQuery = "UPDATE user_profiles SET address = ?, gender = ?, contact_number = ?, email = ? WHERE user_id = ?";
        $stmtProfileUpdate = $connection->prepare($updateProfileQuery);
        $stmtProfileUpdate->bind_param("sssii", $address, $gender, $contact_number, $email, $userId);
    } else {
        // If profile doesn't exist, insert new record
        $insertProfileQuery = "INSERT INTO user_profiles (user_id, address, gender, contact_number, email) VALUES (?, ?, ?, ?, ?)";
        $stmtProfileUpdate = $connection->prepare($insertProfileQuery);
        $stmtProfileUpdate->bind_param("isssi", $userId, $address, $gender, $contact_number, $email);
    }
    $stmtProfileUpdate->execute();

    if ($stmtUserUpdate->affected_rows > 0 || $stmtProfileUpdate->affected_rows > 0) {
        header("Location: admin_file_server.php?msg=UpdateSuccess");
        exit();
    } else {
        $error = "Update failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
        }

        .header {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Edit User</h2>
        </div>
        <form method="POST">
            <!-- User account fields -->
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <input type="text" class="form-control" id="role" name="role" value="<?php echo htmlspecialchars($user['role']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <input type="text" class="form-control" id="status" name="status" value="<?php echo htmlspecialchars($user['status']); ?>" required>
            </div>
            
            <!-- User profile fields -->
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($profile['address'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <input type="text" class="form-control" id="gender" name="gender" value="<?php echo htmlspecialchars($profile['gender'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($profile['contact_number'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="admin_file_server.php" class="btn btn-secondary">Cancel</a>
        </form>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>
    </div>
</body>
</html>
