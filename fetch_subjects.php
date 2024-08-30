<?php
include("../LoginRegisterAuthentication/connection.php");

$grade = isset($_GET['grade']) ? $_GET['grade'] : '';

if ($grade) {
    $query = "SELECT name FROM subjects WHERE grade = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 's', $grade);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $subjects = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $subjects[] = $row['name'];
    }

    echo json_encode($subjects);
}
?>
