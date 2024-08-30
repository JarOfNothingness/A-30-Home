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
$student_id = isset($_GET['id']) ? intval($_GET['id']) : '';

$query = "SELECT s.*, sg.final_grade, sub.name as subject_name 
          FROM students s 
          JOIN student_grades sg ON s.id = sg.student_id 
          JOIN subjects sub ON sg.subject_id = sub.id
          WHERE s.id = '$student_id'";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

$student = mysqli_fetch_assoc($result);
?>

<div class="container mt-5">
    <h2>Form 137 - <?php echo htmlspecialchars($student['learners_name']); ?></h2>

    <!-- Display the student's transcript of records -->
    <p><strong>Section:</strong> <?php echo htmlspecialchars($student['section']); ?></p>
    <p><strong>Grade:</strong> <?php echo htmlspecialchars($student['grade']); ?></p>

    <h4>Subject Grades:</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Final Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php
            mysqli_data_seek($result, 0); // Reset result pointer
            while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                <td><?php echo htmlspecialchars($row['final_grade']); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="javascript:window.print()" class="btn btn-primary">Print Form</a>
    <a href="r.php" class="btn btn-secondary">Back to Report</a>
</div>

<?php include('../crud/footer.php'); ?>
