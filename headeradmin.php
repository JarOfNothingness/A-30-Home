
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

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
  
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
        