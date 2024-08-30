<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

include("../LoginRegisterAuthentication/connection.php");
include("../crud/header.php");

// Fetch schedules
$teacher_id = $_SESSION['userid'];
$sql_schedules = "SELECT * FROM schedules WHERE teacher_id = ?";
$statement_schedules = mysqli_stmt_init($connection);
$schedules = [];

if (mysqli_stmt_prepare($statement_schedules, $sql_schedules)) {
    mysqli_stmt_bind_param($statement_schedules, "i", $teacher_id);
    mysqli_stmt_execute($statement_schedules);
    $result_schedules = mysqli_stmt_get_result($statement_schedules);
    while ($row = mysqli_fetch_assoc($result_schedules)) {
        $schedules[] = $row;
    }
}

// Fetch announcements
$sql_announcements = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC";
$statement_announcements = mysqli_stmt_init($connection);
$announcements = [];

if (mysqli_stmt_prepare($statement_announcements, $sql_announcements)) {
    mysqli_stmt_execute($statement_announcements);
    $result_announcements = mysqli_stmt_get_result($statement_announcements);
    while ($row = mysqli_fetch_assoc($result_announcements)) {
        $announcements[] = $row;
    }
}

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #fff;
            padding: 20px;
            text-align: center;
            color: #000;
            font-size: 24px;
            width: 100%;
        }


        .dashboard-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }

        .dashboard-container h1 {
            margin: 0;
            font-size: 28px;
        }

        .quote {
            font-style: italic;
            text-align: center;
            margin: 20px 0;
            font-size: 20px;
        }

        .quote-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quote-container img {
            width: 300px;
            height: auto;
        }

        .footer {
            background-color: #fff;
            color: #000;
            padding: 20px;
            text-align: center;
            position: relative;
            margin-top: 20px;
        }

        .quick-access {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            justify-items: center;
            align-items: center;
            margin-top: 10px;
        }

        .quick-access h4 {
            grid-column: span 4;
            margin-bottom: 10px;
        }

        .quick-access a {
            text-decoration: none;
            color: #000;
            text-align: center;
        }

        .quick-access img {
            width: 60px;
            height: 60px;
            display: block;
            margin: 0 auto 5px;
        }

        /* Clock styling */
        .clock {
            font-size: 36px; /* Enlarged font size */
            margin-bottom: 20px;
            text-align: center;
            color: #007bff;
        }

        /* Schedule styling */
        .schedule-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .schedule-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .schedule-container th, .schedule-container td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .schedule-container th {
            background-color: #007bff;
            color: white;
        }

        .schedule-container tr:hover {
            background-color: #f1f1f1;
        }

        /* Announcement styling */
        .announcement-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .announcement-section h3 {
            margin-bottom: 15px;
            color: #007bff;
        }

        .announcement {
            margin-bottom: 20px;
        }

        .announcement:last-child {
            margin-bottom: 0;
        }

        .announcement-title {
            font-size: 20px;
            font-weight: bold;
        }

        .announcement-content {
            font-size: 16px;
            margin: 5px 0 10px;
        }

        .announcement-date {
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <header>
        <div class="dashboard-container">
            <h1>Welcome, Teacher <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        </div>
    </header>

    <div class="main-content">
        <div class="clock" id="clock"></div>

        <!-- Announcements Section -->
        <div class="announcement-section">
            <h3>Announcements</h3>
            <?php if (empty($announcements)): ?>
                <p>No announcements at the moment.</p>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement">
                        <div class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></div>
                        <div class="announcement-content"><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></div>
                        <div class="announcement-date">
                            <small>Posted on: <?php echo date('F j, Y', strtotime($announcement['created_at'])); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Schedule Section -->
        <div class="schedule-container">
            <h2>Your Planned Schedule:</h2>
            <?php if (empty($schedules)): ?>
                <p>No upcoming schedules.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <?php
                            $start_time = new DateTime($schedule['start_time']);
                            $start_time->setTimezone(new DateTimeZone('Asia/Manila')); // Adjust timezone as needed
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($schedule['title']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['description']); ?></td>
                                <td><?php echo $start_time->format('Y-m-d H:i'); ?></td>
                                <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="quote-container">
            <p class="quote">"Teaching is the one profession that creates all other professions." Have a fantastic day ahead!</p>
            <img src="Images/teacher.jpeg" alt="Image">
        </div>
    </div>

    <div class="footer">
        <div class="quick-access">
            <h4>Quick access:</h4>
            <a href="Attendance.php">
            <i class="fas fa-clipboard-list"></i>
                <span>Attendance</span>
            </a>
            <a href="ClassRecord.php">
            <i class="fas fa-book"></i>
                <span>Class Record</span>
            </a>
            <a href="fileserver.php">
            <i class="fas fa-folder"></i>
                <span>File Server</span>
            </a>
            <a href="../crud/Crud.php">
            <i class="fas fa-user-graduate"></i>
                <span>MasterList</span>
            </a>
        </div>
    </div>

    <script>
        // Real-time clock function
        function updateClock() {
            var now = new Date();
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var seconds = now.getSeconds();
            var ampm = hours >= 12 ? 'PM' : 'AM';

            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            var timeStr = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
            document.getElementById('clock').innerText = timeStr;
        }

        setInterval(updateClock, 1000);
        updateClock(); // Initialize clock immediately

        // Function to show alerts
        function showAlert(message) {
            alert(message);
        }

        // Example usage of the alert function:
        <?php if (!empty($_SESSION['alert_message'])): ?>
            showAlert("<?php echo addslashes($_SESSION['alert_message']); ?>");
            <?php unset($_SESSION['alert_message']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
