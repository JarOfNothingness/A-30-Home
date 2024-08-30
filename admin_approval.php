<?php
session_start();
include("../LoginRegisterAuthentication/connection.php");

// Check if the admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Approve or Disapprove user based on the action taken
if (isset($_GET['userid']) && isset($_GET['action'])) {
    $userid = $_GET['userid'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        // Update user status to 'approved'
        $sql_update = "UPDATE user SET status = 'approved' WHERE userid = ?";
    } elseif ($action === 'disapprove') {
        // Delete user record or update status to disapproved
        $sql_update = "DELETE FROM user WHERE userid = ?"; // Or use an update query if you prefer to keep the record
    }

    $stmt = $connection->prepare($sql_update);
    $stmt->bind_param("i", $userid);

    if ($stmt->execute()) {
        // Redirect back to the admin homepage with a success message
        echo "<script>alert('Action performed successfully!'); window.location.href='adminhomepage.php';</script>";
        exit();
    } else {
        echo "<p>Error updating status.</p>";
    }

    $stmt->close();
}

// Fetch pending users
$query = "SELECT userid, name, username, role FROM user WHERE status = 'pending'";
$result = mysqli_query($connection, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Approval</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Pending User Approvals</h1>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['userid']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td>
                            <a href="admin_approval.php?userid=<?php echo htmlspecialchars($row['userid']); ?>&action=approve" class="btn-approve">Approve</a>
                            <a href="admin_approval.php?userid=<?php echo htmlspecialchars($row['userid']); ?>&action=disapprove" class="btn-disapprove">Disapprove</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
