<?php
session_start();
include("../LoginRegisterAuthentication/connection.php");

if (!isset($_SESSION['username'])) {
    die("Unauthorized access.");
}

$username = $_SESSION['username']; // Assuming the username is stored in session after login
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Fetch the user's current password from the database
$sql = "SELECT password FROM user WHERE username = ?";
$stmt = $connection->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();

    // Check if the current password matches the stored password
    if ($current_password === $stored_password) {
        // Check if the new password and confirm password match
        if ($new_password === $confirm_password) {
            // Update the password in the database
            $update_sql = "UPDATE user SET password = ? WHERE username = ?";
            $update_stmt = $connection->prepare($update_sql);
            $update_stmt->bind_param("ss", $new_password, $username);
            
            if ($update_stmt->execute()) {
                // Display success message and redirect after 1 second
                echo '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Password Changed</title>
                    <style>
                        body {
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            margin: 0;
                            background-color: #f8f9fa;
                        }
                        .message {
                            font-size: 18px;
                            color: green;
                        }
                    </style>
                </head>
                <body>
                    <div class="message">Password changed successfully. Redirecting...</div>
                    <script>
                        setTimeout(function() {
                            window.location.href = "dashboard.php";
                        }, 1000); // 2 second delay
                    </script>
                </body>
                </html>';
            } else {
                echo "Error updating password.";
            }
            
            $update_stmt->close();
        } else {
            echo "New passwords do not match.";
        }
    } else {
        echo "Current password is incorrect.";
    }
} else {
    echo "Failed to prepare the statement.";
}

$connection->close();
?>
