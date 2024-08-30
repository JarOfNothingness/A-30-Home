<?php
session_start(); // Start the session at the very top


ob_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include('../crud/header.php');
include("../LoginRegisterAuthentication/connection.php");

// Define weightages
$weights = [
    'written_exam' => 0.30,
    'performance_task' => 0.50,
    'quarterly_exam' => 0.20,
];

// Get the record ID from the query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the record details from the database
$query = "SELECT sg.*, s.learners_name, sub.name as subject_name 
          FROM student_grades sg
          JOIN students s ON sg.student_id = s.id
          JOIN subjects sub ON sg.subject_id = sub.id
          WHERE sg.id = $id";
$result = mysqli_query($connection, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Record not found.");
}

$record = mysqli_fetch_assoc($result);

// Fetch students and subjects for the dropdowns
$students_query = "SELECT id, learners_name FROM students";
$students_result = mysqli_query($connection, $students_query);

$subjects_query = "SELECT id, name FROM subjects";
$subjects_result = mysqli_query($connection, $subjects_query);

if (!$students_result || !$subjects_result) {
    die("Query failed: " . mysqli_error($connection));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = intval($_POST['student_id']);
    $subject_id = intval($_POST['subject_id']);
    $written_exam = floatval($_POST['written_exam']);
    $performance_task = floatval($_POST['performance_task']);
    $quarterly_exam = floatval($_POST['quarterly_exam']);

    // Calculate final grade
    $final_grade = ($written_exam * $weights['written_exam']) + 
                   ($performance_task * $weights['performance_task']) + 
                   ($quarterly_exam * $weights['quarterly_exam']);

    $update_query = "UPDATE student_grades SET 
                        student_id = $student_id,
                        subject_id = $subject_id,
                        written_exam = $written_exam,
                        performance_task = $performance_task,
                        quarterly_exam = $quarterly_exam,
                        final_grade = $final_grade
                    WHERE id = $id";

    if (mysqli_query($connection, $update_query)) {
        echo "<div class='alert alert-success'>Record updated successfully.</div>";
        // Redirect after 1 second
        echo "<script>setTimeout(function() { window.location.href = 'ClassRecord.php'; }, 1000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error updating record: " . mysqli_error($connection) . "</div>";
    }

    // Reload the updated record
    $result = mysqli_query($connection, $query);
    $record = mysqli_fetch_assoc($result);
}
?>

<div class="container mt-5">
    <h2>Edit Grade Record</h2>

    <!-- Edit Form -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="student_id">Student:</label>
            <select name="student_id" id="student_id" class="form-control">
                <?php while ($student = mysqli_fetch_assoc($students_result)) { ?>
                    <option value="<?php echo htmlspecialchars($student['id']); ?>" <?php if ($record['student_id'] == $student['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($student['learners_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="subject_id">Subject:</label>
            <select name="subject_id" id="subject_id" class="form-control">
                <?php while ($subject = mysqli_fetch_assoc($subjects_result)) { ?>
                    <option value="<?php echo htmlspecialchars($subject['id']); ?>" <?php if ($record['subject_id'] == $subject['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($subject['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="written_exam">Written Exam:</label>
            <input type="number" step="0.01" name="written_exam" id="written_exam" class="form-control" value="<?php echo htmlspecialchars($record['written_exam']); ?>" required>
        </div>

        <div class="form-group">
            <label for="performance_task">Performance Task:</label>
            <input type="number" step="0.01" name="performance_task" id="performance_task" class="form-control" value="<?php echo htmlspecialchars($record['performance_task']); ?>" required>
        </div>

        <div class="form-group">
            <label for="quarterly_exam">Quarterly Exam:</label>
            <input type="number" step="0.01" name="quarterly_exam" id="quarterly_exam" class="form-control" value="<?php echo htmlspecialchars($record['quarterly_exam']); ?>" required>
        </div>

        <div class="form-group">
            <label for="final_grade">Final Grade:</label>
            <input type="number" step="0.01" name="final_grade" id="final_grade" class="form-control" value="<?php echo htmlspecialchars($record['final_grade']); ?>" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Update Record</button>
        <a href="ClassRecord.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
document.getElementById('written_exam').addEventListener('input', calculateFinalGrade);
document.getElementById('performance_task').addEventListener('input', calculateFinalGrade);
document.getElementById('quarterly_exam').addEventListener('input', calculateFinalGrade);

function calculateFinalGrade() {
    const written = parseFloat(document.getElementById('written_exam').value) || 0;
    const performance = parseFloat(document.getElementById('performance_task').value) || 0;
    const quarterly = parseFloat(document.getElementById('quarterly_exam').value) || 0;

    const finalGrade = (written * 0.30) + (performance * 0.50) + (quarterly * 0.20);
    document.getElementById('final_grade').value = finalGrade.toFixed(2);
}

// Initialize final grade on page load
calculateFinalGrade();
</script>

<?php include('../crud/footer.php'); ?>

<?php
// End output buffering and flush
ob_end_flush();
?>
