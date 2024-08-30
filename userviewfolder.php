<?php
session_start();
include("../LoginRegisterAuthentication/connection.php");

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Get the user's ID from the session
$userid = $_SESSION['userid']; // Assuming you store the user's ID in the session
$folder_id = $_GET['folder_id'];

// Validate folder_id
if (!filter_var($folder_id, FILTER_VALIDATE_INT)) {
    die("Invalid folder ID.");
}

// Prepare and execute SQL statement
$query = "SELECT file_id, file_name, file_path FROM userfileserverfiles WHERE folder_id = ?";
$stmt = $connection->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $connection->error);
}

$stmt->bind_param("i", $folder_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files in Folder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Files in this folder</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><a href="<?php echo htmlspecialchars($row['file_path']); ?>" download><?php echo htmlspecialchars($row['file_name']); ?></a></td>
                    <td>
                        <form action="userdeletefile.php" method="post" style="display:inline;">
                            <input type="hidden" name="file_id" value="<?php echo htmlspecialchars($row['file_id']); ?>">
                            <input type="hidden" name="folder_id" value="<?php echo htmlspecialchars($folder_id); ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the statement and connection
$stmt->close();
$connection->close();
?>
