<?php
session_start(); // Start the session at the very top

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<?php include('headerforreports.php'); ?>
<?php include("../LoginRegisterAuthentication/connection.php"); ?>

<?php
// Get the student ID from the query string
$student_id = isset($_GET['id']) ? intval($_GET['id']) : '';

// SQL query to fetch student details and Form 2 data
$query = "SELECT s.*, sf2.*
          FROM students s
          JOIN sf2_attendance_report sf2 ON s.learners_name = sf2.learnerName
          WHERE s.id = '$student_id'";

$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Fetch the student details
$student = mysqli_fetch_assoc($result);
?>

<div class="container mt-5">
    <h2 class="text-center">Form 2 - Attendance Report</h2>
    <p class="text-center"><strong>School ID: <?php echo htmlspecialchars($student['school_id']); ?> School Year: <?php echo htmlspecialchars($student['school_year']); ?></strong></p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Learners Full Name</th>
                <th>Grade & Section</th>
                <th>Attendance Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($student['id']); ?></td>
                <td><?php echo htmlspecialchars($student['learners_name']); ?></td>
                <td><?php echo htmlspecialchars($student['grade'] . ' - ' . $student['section']); ?></td>
                <td>
                    <!-- Display attendance details if available -->
                    <!-- Modify this section based on actual fields from sf2_attendance_report -->
                    <ul>
                        <li>Total Present: <?php echo htmlspecialchars($student['total_present']); ?></li>
                        <li>Total Absent: <?php echo htmlspecialchars($student['total_absent']); ?></li>
                        <li>Total Late: <?php echo htmlspecialchars($student['total_late']); ?></li>
                        <li>Total Excused: <?php echo htmlspecialchars($student['total_excused']); ?></li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <a href="javascript:window.print()" class="btn btn-primary print-btn">Print Form</a>
        <a href="r.php" class="btn btn-secondary">Back to Report</a>
    </div>
</div>

<!-- CSS to hide buttons in print view -->
<style>
@media print {
    .print-btn,
    .btn-success,
    .btn-secondary {
        display: none;
    }
}
</style>

<?php include('../crud/footer.php'); ?>
