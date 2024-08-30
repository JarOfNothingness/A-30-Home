<?php
include("../LoginRegisterAuthentication/connection.php");
include("functions.php");

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}



session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch announcements
$announcements = getAnnouncements($connection);
?>

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher's Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        header {
            background-color: #007bff;
            padding: 20px;
            text-align: center;
            color: #fff;
            font-size: 24px;
            width: 100%;
        }

        .sidebar {
            width: 60px; /* Initial sidebar width showing only icons */
            background-color: #333;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            transition: width 0.3s; /* Smooth transition for expansion */
            overflow-x: hidden;
            white-space: nowrap;
        }

        .sidebar:hover {
            width: 250px; /* Expand sidebar on hover */
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .sidebar a span {
            display: none; /* Hide text initially */
            margin-left: 10px;
        }

        .sidebar:hover a span {
            display: inline; /* Show text on hover */
        }

        .main-content {
            margin-left: 60px; /* Adjust based on sidebar's initial width */
            padding: 20px;
            width: calc(100% - 60px); /* Adjust width based on sidebar */
            transition: margin-left 0.3s, width 0.3s; /* Smooth transition */
        }

        .sidebar:hover ~ .main-content {
            margin-left: 250px; /* Shift content when sidebar expands */
            width: calc(100% - 250px);
        }

        .dashboard-header {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            font-size: 32px;
        }
           
        .announcement-section {
            margin-top: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .announcement-section h3 {
            margin-bottom: 15px;
            color: #007bff;
        }
        
        .announcement-section .announcement {
            margin-bottom: 20px;
        }
        
        .announcement-section .announcement:last-child {
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

        .form-links {
            position: fixed;
            top: 20px;
            right: 20px;
        }

        .form-links a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <header>
       

        <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    </header>
    <div class="sidebar">
        <a href="dashboard.php">
            <img src="Images/home-icon.png" alt="Home" style="width:30px;">
            <span>Dashboard</span>
        </a>
        <a href="../crud/Crud.php">
            <img src="Images/students-icon.png" alt="Students" style="width:30px;">
            <span>Masterlist</span>
        </a>
        <a href="ClassRecord.php">
            <img src="Images/grades-icon.png" alt="Grades" style="width:30px;">
            <span>ClassRecords</span>
        </a>

        <a href="Attendance.php">
            <img src="Images/grades-icon.png" alt="Grades" style="width:30px;">
            <span>Attendance</span>
        </a>

        <a href="GradingSystem.php">
            <img src="Images/grades-icon.png" alt="Grades" style="width:30px;">
            <span>Grading System</span>
        </a>

        <a href="FinalGrade.php">
            <img src="Images/grades-icon.png" alt="Grades" style="width:30px;">
            <span>Final Grade</span>
        </a>

        <a href="r.php">
            <img src="Images/grades-icon.png" alt="Grades" style="width:30px;">
            <span>Reports</span>
        </a>
        <a href="fileserver.php">
            <img src="Images/files-icon.png" alt="File Server" style="width:30px;">
            <span>File Server</span>
        </a>
        <a href="fetch_schedules.php">
            <img src="Images/schedule-icon.png" alt="Schedule" style="width:30px;">
            <span>Schedule</span>
        </a>
        <a href="manageaccount.php">
          
          <span>Manage Account</span>
      </a>


        <a href="logout.php">
          
            <span>Logout</span>
        </a>

    
    </div>
    <div class="main-content">
    <div class="dashboard-header">
        <h2>Welcome to the Teacher's Portal</h2>
        <!-- Additional content can be added here -->
    </div>
    
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
</div>
</body>
</html>