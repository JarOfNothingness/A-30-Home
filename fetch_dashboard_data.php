<?php
include_once("../LoginRegisterAuthentication/connection.php");

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

// Query for total attendance records
$attendanceQuery = "SELECT COUNT(*) as total_attendance FROM attendance";
$attendanceResult = mysqli_query($connection, $attendanceQuery);
$totalAttendance = mysqli_fetch_assoc($attendanceResult)['total_attendance'];

// Calculate attendance rate
$attendanceRate = ($totalStudents > 0) ? round(($totalAttendance / $totalStudents) * 100, 2) : 0;

$data = array(
    'total_students' => $totalStudents,
    'total_teachers' => $totalTeachers,
    'attendance_rate' => $attendanceRate
);

echo json_encode($data);
?>
