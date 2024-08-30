<?php 
include('../crud/header.php'); 
include("../LoginRegisterAuthentication/connection.php");

// Ensure that an ID is provided in the URL
if (isset($_GET['id'])) {
    $grade_id = intval($_GET['id']);

    // Fetch the existing grade record
    $query = "SELECT sg.*, s.learners_name, sub.name as subject_name 
              FROM student_grades sg
              JOIN students s ON sg.student_id = s.id
              JOIN subjects sub ON sg.subject_id = sub.id 
              WHERE sg.id = $grade_id";

    $result = mysqli_query($connection, $query);
    if (!$result || mysqli_num_rows($result) == 0) {
        die("Record not found or query failed: " . mysqli_error($connection));
    }

    $row = mysqli_fetch_assoc($result);

    // Fetch all students and subjects for the dropdowns
    $students_query = "SELECT id, learners_name FROM students";
    $students_result = mysqli_query($connection, $students_query);

    $subjects_query = "SELECT id, name FROM subjects";
    $subjects_result = mysqli_query($connection, $subjects_query);
} else {
    die("No ID provided.");
}

// Handle the update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_grade'])) {
    $student_id = intval($_POST['student_id']);
    $subject_id = intval($_POST['subject_id']);
    $written_exam = mysqli_real_escape_string($connection, $_POST['written_exam']);
    $performance_task = mysqli_real_escape_string($connection, $_POST['performance_task']);
    $quarterly_exam = mysqli_real_escape_string($connection, $_POST['quarterly_exam']);
    $final_grade = mysqli_real_escape_string($connection, $_POST['final_grade']);

    $update_query = "UPDATE student_grades SET 
                     student_id = $student_id,
                     subject_id = $subject_id,
                     written_exam = '$written_exam',
                     performance_task = '$performance_task',
                     quarterly_exam = '$quarterly_exam',
                     final_grade = '$final_grade'
                     WHERE id = $grade_id";

    if (mysqli_query($connection, $update_query)) {
        echo "<div class='alert alert-success'>Grade record updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating record: " . mysqli_error($connection) . "</div>";
    }
}
?>

<div class="container mt-5">
    <h2>Update Grade Record</h2>

    <!-- Update Form -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="student_id">Student:</label>
            <select name="student_id" id="student_id" class="form-control" required>
                <option value="">Select Student</option>
                <?php while ($student = mysqli_fetch_assoc($students_result)) { ?>
                    <option value="<?php echo htmlspecialchars($student['id']); ?>" 
                        <?php echo $student['id'] == $row['student_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($student['learners_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="subject_id">Subject:</label>
            <select name="subject_id" id="subject_id" class="form-control" required>
                <option value="">Select Subject</option>
                <?php while ($subject = mysqli_fetch_assoc($subjects_result)) { ?>
                    <option value="<?php echo htmlspecialchars($subject['id']); ?>" 
                        <?php echo $subject['id'] == $row['subject_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($subject['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="written_exam">Written Exam:</label>
            <input type="number" name="written_exam" id="written_exam" class="form-control" step="0.01" required 
                   value="<?php echo htmlspecialchars($row['written_exam']); ?>">
        </div>
        <div class="form-group">
            <label for="performance_task">Performance Task:</label>
            <input type="number" name="performance_task" id="performance_task" class="form-control" step="0.01" required 
                   value="<?php echo htmlspecialchars($row['performance_task']); ?>">
        </div>
        <div class="form-group">
            <label for="quarterly_exam">Quarterly Exam:</label>
            <input type="number" name="quarterly_exam" id="quarterly_exam" class="form-control" step="0.01" required 
                   value="<?php echo htmlspecialchars($row['quarterly_exam']); ?>">
        </div>
        <div class="form-group">
            <label for="final_grade">Final Grade:</label>
            <input type="number" name="final_grade" id="final_grade" class="form-control" step="0.01" required 
                   value="<?php echo htmlspecialchars($row['final_grade']); ?>">
        </div>
        <input type="submit" name="update_grade" class="btn btn-primary" value="Update Record">
    </form>

    <!-- Go Back to Class Record Link -->
    <div class="mt-3">
        <a href="ClassRecord.php" class="btn btn-secondary">Go Back to Class Records</a>
    </div>
</div>

<?php include('../crud/footer.php'); ?>
