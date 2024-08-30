<?php
include("../LoginRegisterAuthentication/connection.php");

// Get folder ID from GET parameters
$folder_id = isset($_GET['folder_id']) ? intval($_GET['folder_id']) : 0;

// Query to get files in the folder
$query = "SELECT file_id, file_name, file_path FROM fileserver_files WHERE folder_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $folder_id);
$stmt->execute();
$result = $stmt->get_result();

// Back button to go to the previous page or folder list
echo "<a href='admin_file_server.php' style='text-decoration:none;'>
        <button style='margin-bottom: 20px;'>← Back to Folder List</button>
      </a>";

// Display files in a table with download and delete options
echo "<h2>Files in this Folder</h2>";
echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<tr>
        <th>File Name</th>
        <th>Download</th>
        <th>Delete</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";
    echo "<td><a href='" . htmlspecialchars($row['file_path']) . "' download>Download</a></td>";
    
    // Form to delete file
    echo "<td>
            <form action='deletefile.php' method='POST' style='display:inline;'>
                <input type='hidden' name='file_id' value='" . htmlspecialchars($row['file_id']) . "'>
                <input type='submit' value='Delete' onclick='return confirm(\"Are you sure you want to delete this file?\");'>
            </form>
          </td>";
    echo "</tr>";
}

echo "</table>";

// Close the statement and connection
$stmt->close();
$connection->close();
?>
