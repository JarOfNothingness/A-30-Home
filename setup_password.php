<?php
session_start();
include("../LoginRegisterAuthentication/connection.php");

if (!isset($_GET['userid'])) {
    header("Location: login.php");
    exit();
}
$success_message = '';
$error_message = '';

$userid = $_GET['userid'];

if (isset($_POST['set_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        // Hash the new password
   

        // Update the user password and set status to 'active'
        $update_query = "UPDATE user SET password = ?, status = 'active' WHERE userid = ?";
        $stmt = $connection->prepare($update_query);
        $stmt->bind_param("si", $password, $userid);

        if ($stmt->execute()) {
            $success_message = "Password successfully created. Redirecting...";
            // Redirect to homepage
            header("refresh:2;url=../Home/homepage.php");
            exit();
        } else {
            echo "<p>Error updating password.</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Passwords do not match.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
        <h2 class="mb-4">Setup Your Password</h2>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="password" class="form-label">Create Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Create Password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <button type="submit" name="set_password" class="btn btn-primary">Set Password</button>
        </form>
    </div>
</body>
</html>