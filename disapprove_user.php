<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include_once("../LoginRegisterAuthentication/connection.php");

// Check if the connection is successful
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if 'userid' is set in the query string
if (isset($_GET['userid'])) {
    $userid = intval($_GET['userid']);

    // Update the user status to 'disapproved'
    $updateQuery = "UPDATE user SET status = 'disapproved' WHERE userid = ?";
    $stmt = mysqli_prepare($connection, $updateQuery);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userid);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($result) {
            // Successfully updated, redirect to admin homepage
            header("Location: adminhomepage.php");
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($connection);
        }
    } else {
        echo "Error preparing statement: " . mysqli_error($connection);
    }
} else {
    echo "No user ID provided.";
}

// Close the database connection
mysqli_close($connection);
?>
