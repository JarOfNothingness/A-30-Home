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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_grade'])) {
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : '';
    $subject_id = isset($_POST['subject_id']) ? intval($_POST['subject_id']) : '';
    $written_exam = isset($_POST['written_exam']) ? floatval($_POST['written_exam']) : 0;
    $performance_task = isset($_POST['performance_task']) ? floatval($_POST['performance_task']) : 0;
    $quarterly_exam = isset($_POST['quarterly_exam']) ? floatval($_POST['quarterly_exam']) : 0;

    // Calculate final grade
    $final_grade = ($written_exam * $weights['written_exam']) + 
                   ($performance_task * $weights['performance_task']) + 
                   ($quarterly_exam * $weights['quarterly_exam']);

    if ($student_id && $subject_id) {
        $insert_query = "INSERT INTO student_grades (student_id, subject_id, written_exam, performance_task, quarterly_exam, final_grade) 
                         VALUES ($student_id, $subject_id, $written_exam, $performance_task, $quarterly_exam, $final_grade)";
        if (mysqli_query($connection, $insert_query)) {
            // Redirect to ClassRecord.php after successful record addition
            header("Location: ClassRecord.php");
            exit;
        } else {
            echo "<div class='alert alert-danger'>Error adding record: " . mysqli_error($connection) . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Please select both student and subject.</div>";
    }
}

// Fetch students and subjects for dropdowns
$students_query = "SELECT id, learners_name FROM students";
$students_result = mysqli_query($connection, $students_query);

$subjects_query = "SELECT id, name FROM subjects";
$subjects_result = mysqli_query($connection, $subjects_query);

if (!$students_result || !$subjects_result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<div class="container mt-5">
    <h2>Add New Grade Record</h2>

    <!-- Add New Grade Form -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="student_id">Student:</label>
            <select name="student_id" id="student_id" class="form-control">
                <option value="">Select Student</option>
                <?php while ($student = mysqli_fetch_assoc($students_result)) { ?>
                    <option value="<?php echo htmlspecialchars($student['id']); ?>">
                        <?php echo htmlspecialchars($student['learners_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="subject_id">Subject:</label>
            <select name="subject_id" id="subject_id" class="form-control">
                <option value="">Select Subject</option>
                <?php while ($subject = mysqli_fetch_assoc($subjects_result)) { ?>
                    <option value="<?php echo htmlspecialchars($subject['id']); ?>">
                        <?php echo htmlspecialchars($subject['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="written_exam">Written Exam:</label>
            <input type="text" name="written_exam" id="written_exam" class="form-control">
        </div>

        <div class="form-group">
            <label for="performance_task">Performance Task:</label>
            <input type="text" name="performance_task" id="performance_task" class="form-control">
        </div>

        <div class="form-group">
            <label for="quarterly_exam">Quarterly Exam:</label>
            <input type="text" name="quarterly_exam" id="quarterly_exam" class="form-control">
        </div>

        <div class="form-group">
            <label for="final_grade">Final Grade:</label>
            <input type="text" name="final_grade" id="final_grade" class="form-control" readonly>
        </div>

        <button type="submit" name="add_grade" class="btn btn-success">Calculate and Add Grade</button>
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
</script>

<?php include('../crud/footer.php'); ?>

<?php
// End output buffering and flush
ob_end_flush();
?>
