<?php
session_start(); // Start the session at the very top

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include("../LoginRegisterAuthentication/connection.php");

// Handle deletion before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $schedule_id = $_POST['schedule_id'];
        $delete_sql = "DELETE FROM schedules WHERE id = ?";
        if ($delete_stmt = mysqli_prepare($connection, $delete_sql)) {
            mysqli_stmt_bind_param($delete_stmt, "i", $schedule_id);
            mysqli_stmt_execute($delete_stmt);
            header("Location: fetch_schedules.php");
            exit(); // Ensure script stops running after the redirect
        }
    }
}

include('../crud/header.php');

$teacher_id = $_SESSION['userid'];
$sql = "SELECT * FROM schedules WHERE teacher_id = ?";
$statement = mysqli_stmt_init($connection);
$schedules = [];

if (mysqli_stmt_prepare($statement, $sql)) {
    mysqli_stmt_bind_param($statement, "i", $teacher_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    while ($row = mysqli_fetch_assoc($result)) {
        $schedules[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher's Schedule</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
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
        .schedule-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .add-schedule {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .add-schedule:hover {
            background-color: #0056b3;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons form {
            display: inline-block;
        }
        .action-buttons button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .action-buttons button.update {
            background-color: #4CAF50;
        }
        .action-buttons button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <header>
        <h1>Teacher's Class Schedule</h1>
    </header>
    <div class="schedule-container">
        <table id="scheduleTable">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($schedule['title']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['description']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
                        <td class="action-buttons">
                            <form action="update_schedule.php" method="get">
                                <input type="hidden" name="schedule_id" value="<?php echo $schedule['id']; ?>">
                                <button class="update">Update</button>
                            </form>
                            <form action="fetch_schedules.php" method="post">
                                <input type="hidden" name="schedule_id" value="<?php echo $schedule['id']; ?>">
                                <button type="submit" name="delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a class="add-schedule" href="add_schedule.php">Add New Schedule</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#scheduleTable').DataTable({
                "paging": true,
                "searching": true,
                "info": true,
                "ordering": true
            });
        });
    </script>
</body>
</html>
