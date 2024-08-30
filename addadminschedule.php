<?php
include("../LoginRegisterAuthentication/connection.php");

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$error_msg = "";
if (isset($_POST['add_schedule'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_datetime = $_POST['start_time']; // Changed to match column name
    $end_datetime = $_POST['end_time']; // Changed to match column name
    $admin_id = $_SESSION['userid'];

    if (!empty($title) && !empty($start_datetime) && !empty($end_datetime)) {
        $sql = "INSERT INTO admin_schedule (admin_id, title, description, start_datetime, end_datetime) VALUES (?, ?, ?, ?, ?)";
        $statement = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "issss", $admin_id, $title, $description, $start_datetime, $end_datetime);
            mysqli_stmt_execute($statement);
            header("Location: adminschedule.php");
            exit();
        } else {
            $error_msg = "Failed to prepare the SQL statement.";
        }
    } else {
        $error_msg = "All fields are required.";
    }
}

include('headeradmin.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Schedule</title>
    <link rel="stylesheet" href="styles.css"> <!-- Your custom styles -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f9fc;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 10px;
            font-weight: bold;
        }
        input, textarea {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Add New Schedule</h1>
    </header>
    <div class="form-container">
        <?php if ($error_msg): ?>
            <div class="error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>
        <form action="addadminschedule.php" method="post">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
            
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required></textarea>
            
            <label for="start_time">Start Time</label>
            <input type="datetime-local" id="start_time" name="start_time" required>
            
            <label for="end_time">End Time</label>
            <input type="datetime-local" id="end_time" name="end_time" required>
            
            <button type="submit" name="add_schedule">Add Schedule</button>
        </form>
    </div>
</body>
</html>
