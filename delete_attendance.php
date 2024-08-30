<?php
include("../LoginRegisterAuthentication/connection.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Delete record
    $delete_query = "DELETE FROM sf2_attendance_report WHERE form2Id = $id";
    if (mysqli_query($connection, $delete_query)) {
        header("Location: Attendance.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($connection);
    }
} else {
    echo "Invalid ID.";
}

mysqli_close($connection);
?>
