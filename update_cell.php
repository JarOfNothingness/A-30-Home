<?php
include('../LoginRegisterAuthentication/connection.php');

// Validate input
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$column = isset($_POST['column']) ? intval($_POST['column']) : 0;
$value = isset($_POST['value']) ? mysqli_real_escape_string($connection, $_POST['value']) : '';

// Define column names for update
$columns = [
    2 => 'schoolId', // Example index for the schoolId column
    3 => 'schoolName',
    // Add other column mappings here
    // Make sure to match column indices with your DataTable columns
];

// Determine which column to update
$columnName = isset($columns[$column]) ? $columns[$column] : '';

if ($columnName) {
    $update_query = "UPDATE sf2_attendance_report SET $columnName = '$value' WHERE form2Id = $id";
    if (mysqli_query($connection, $update_query)) {
        echo 'OK';
    } else {
        echo 'Error: ' . mysqli_error($connection);
    }
} else {
    echo 'Invalid column';
}
?>
