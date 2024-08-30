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
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $teacher_id = $_SESSION['userid'];

    if (!empty($title) && !empty($start_time) && !empty($end_time)) {
        $sql = "INSERT INTO schedules (teacher_id, title, description, start_time, end_time) VALUES (?, ?, ?, ?, ?)";
        $statement = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "issss", $teacher_id, $title, $description, $start_time, $end_time);
            mysqli_stmt_execute($statement);
            header("Location: fetch_schedules.php");
            exit();
        } else {
            $error_msg = "Error preparing SQL statement.";
        }
    } else {
        $error_msg = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Schedule</title>
    <link rel="stylesheet" href="styles.css"> <!-- Your custom styles -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f9fc;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="datetime-local"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form method="POST" action="">
            <h1>Add New Schedule</h1>
            <div>
                <label>Title</label>
                <input type="text" name="title" required>
            </div>
            <div>
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>
            <div>
                <label>Start Time</label>
                <input type="datetime-local" name="start_time" required>
            </div>
            <div>
                <label>End Time</label>
                <input type="datetime-local" name="end_time" required>
            </div>
            <div>
                <input type="submit" name="add_schedule" value="Add Schedule">
            </div>
            <div class="error-message">
                <?php echo $error_msg; ?>
            </div>
        </form>
    </div>
</body>
</html>
