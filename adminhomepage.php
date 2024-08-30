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

// Query for total students
$totalStudentsQuery = "SELECT COUNT(*) as total_students FROM students";
$totalStudentsResult = mysqli_query($connection, $totalStudentsQuery);
$totalStudents = mysqli_fetch_assoc($totalStudentsResult)['total_students'];

// Query for total teachers
$totalTeachersQuery = "SELECT COUNT(*) as total_teachers FROM user WHERE role = 'Teacher'";
$totalTeachersResult = mysqli_query($connection, $totalTeachersQuery);
$totalTeachers = mysqli_fetch_assoc($totalTeachersResult)['total_teachers'];

// Query for total attendance
$attendanceQuery = "SELECT COUNT(*) as total_attendance FROM attendance";
$attendanceResult = mysqli_query($connection, $attendanceQuery);
$totalAttendance = mysqli_fetch_assoc($attendanceResult)['total_attendance'];

// Calculate attendance rate
$attendanceRate = ($totalStudents > 0) ? round(($totalAttendance / $totalStudents) * 100, 2) : 0;

// Query for users waiting for approval
$pendingUsersQuery = "SELECT userid, name, username FROM user WHERE status = 'pending'";
$pendingUsersResult = mysqli_query($connection, $pendingUsersQuery);

// Query for planned schedule
$scheduleQuery = "SELECT * FROM admin_schedule WHERE DATE(start_datetime) = CURDATE()";
$scheduleResult = mysqli_query($connection, $scheduleQuery);
$schedules = []; // Initialize the schedule array

if ($scheduleResult) {
    while ($schedule = mysqli_fetch_assoc($scheduleResult)) {
        $schedules[] = $schedule; // Store each schedule in the array
    }
}  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
        }

        header {
            background-color: #0047ab;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 28px;
        }

        .sidebar {
            width: 60px;
            background-color: #333;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            transition: width 0.3s;
            overflow-x: hidden;
            white-space: nowrap;
        }

        .sidebar:hover {
            width: 250px;
        }

        .sidebar a {
            color: #fff;
            padding: 15px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .sidebar a span {
            display: none;
            margin-left: 10px;
        }

        .sidebar:hover a span {
            display: inline;
        }

        .main-content {
            margin-left: 60px;
            padding: 20px;
            transition: margin-left 0.3s, width 0.3s;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 250px;
        }

        .dashboard-header {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .dashboard-cards {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
          /* Clock styling */
          .clock {
            font-size: 36px; /* Enlarged font size */
            margin-bottom: 20px;
            text-align: center;
            color: #007bff;
        }

        .dashboard-cards .card {
            background-color: #fff;
            width: 30%;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .dashboard-cards .card:hover {
            transform: scale(1.05);
        }

        .dashboard-cards .card h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #0047ab;
        }

        .dashboard-cards .card p {
            font-size: 36px;
            margin: 0;
            color: #333;
        }

        .card i {
            font-size: 50px;
            color: #0047ab;
            margin-bottom: 15px;
        }

        .animated {
            animation-duration: 1.5s;
            animation-fill-mode: both;
        }

        @keyframes fadeInUp {
            from {
                transform: translate3d(0, 100%, 0);
                visibility: visible;
            }

            to {
                transform: translate3d(0, 0, 0);
            }
        }

        .fadeInUp {
            animation-name: fadeInUp;
        }

        .pending-users {
            margin-top: 20px;
        }

        .pending-users h3 {
            margin-bottom: 15px;
            color: #0047ab;
        }

        .pending-users table {
            width: 100%;
            border-collapse: collapse;
        }

        .pending-users table, .pending-users th, .pending-users td {
            border: 1px solid #ddd;
        }

        .pending-users th, .pending-users td {
            padding: 10px;
            text-align: left;
        }

        .pending-users th {
            background-color: #f4f4f4;
        }

        .btn-approve, .btn-disapprove {
            padding: 5px 10px;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin: 0 5px;
        }

        .btn-approve {
            background-color: #28a745;
        }

        .btn-approve:hover {
            background-color: #218838;
        }

        .btn-disapprove {
            background-color: #dc3545;
        }

        .btn-disapprove:hover {
            background-color: #c82333;
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

   border-radius: 8px;
            bo
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        Admin Dashboard
    </header>
    <div class="sidebar">
        <a href="adminhomepage.php">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="AdminCrud.php">
            <i class="fas fa-users"></i>
            <span>Masterlist</span>
        </a>
        <a href="view_activities.php">
            <i class="fas fa-tasks"></i>
            <span>Activities</span>
        </a>
        <a href="manage_user.php">
            <i class="fas fa-user-cog"></i>
            <span>Manage Users</span>
        </a>
        <a href="admin_file_server.php">
            <i class="fas fa-file-alt"></i>
            <span>File Server</span>
        </a>
        <a href="announcements.php">
        <i class="fas fa-bullhorn"></i>
        <span>Announcements</span>
    </a>
        <a href="adminschedule.php">
            <i class="fas fa-calendar-alt"></i>
            <span>Schedule</span>
        </a>
        <a href="adminmanageaccount.php">
    <i class="fas fa-key"></i>
    <span>Change Password</span>
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
    <div class="main-content">
        <div class="dashboard-header">
            <h2>Welcome Admin, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Students</h3>
                    <p><?php echo $totalStudents; ?></p>
                </div>
                <div class="card">
                    <h3>Total Teachers</h3>
                    <p><?php echo $totalTeachers; ?></p>
                </div>
                <div class="card">
                    <h3>Attendance Rate</h3>
                    <p><?php echo $attendanceRate . "%"; ?></p>
                </div>
            </div>
        </div>
        <div class="main-content">
        <div class="clock" id="clock"></div>
        
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
                            $start_time = new DateTime($schedule['start_datetime']);
                            $start_time->setTimezone(new DateTimeZone('Asia/Manila')); // Adjust timezone as needed
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($schedule['title']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['description']); ?></td>
                                <td><?php echo $start_time->format('Y-m-d H:i'); ?></td>
                                <td><?php echo htmlspecialchars($schedule['end_datetime']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>


        <div class="pending-users">
            <h3>Pending User Approvals</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($pendingUsersResult)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>
                            <a href="admin_approval.php?action=approve&userid=<?php echo htmlspecialchars($row['userid']); ?>" class="btn-approve">Approve</a>
                            <a href="admin_approval.php?action=disapprove&userid=<?php echo htmlspecialchars($row['userid']); ?>" class="btn-disapprove">Disapprove</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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
