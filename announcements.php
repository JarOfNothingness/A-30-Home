<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include_once("../LoginRegisterAuthentication/connection.php");
include_once("headeradmin.php");

// Check if the connection is successful
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form has been submitted
if (isset($_POST['submit_announcement'])) {
    $title = mysqli_real_escape_string($connection, $_POST['title']);
    $content = mysqli_real_escape_string($connection, $_POST['content']);

    // Insert the announcement into the database
    $insertQuery = "INSERT INTO announcements (title, content, created_at) VALUES ('$title', '$content', NOW())";
    if (mysqli_query($connection, $insertQuery)) {
        echo '<div class="alert alert-success">Announcement added successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Error: ' . mysqli_error($connection) . '</div>';
    }
    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007bff;
            padding: 20px;
            text-align: center;
            color: #fff;
            font-size: 24px;
        }

        .announcement-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .announcement-form h3 {
            margin-bottom: 20px;
            color: #0047ab;
        }

        .announcement-form .form-label {
            color: #333;
        }

        .announcement-form .form-control {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>Announcements</header>
    <div class="announcement-form">
            <h3>Add New Announcement</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                </div>
                <button type="submit" name="submit_announcement" class="btn btn-primary">Add Announcement</button>
            </form>
        </div>
    </div>
</body>
</html>
