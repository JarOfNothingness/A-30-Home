<?php
session_start(); // Start the session at the very top

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<?php include('../crud/header.php'); ?>
<?php include("../LoginRegisterAuthentication/connection.php"); ?>

<?php
// Ensure that $school_level is defined
$school_level = isset($_GET['school_level']) ? $_GET['school_level'] : '';

// Handle form submission for new grade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_grade'])) {
    $student_id = intval($_POST['student_id']);
    $written_exam = mysqli_real_escape_string($connection, $_POST['written_exam']);
    $performance_task = mysqli_real_escape_string($connection, $_POST['performance_task']);
    $quarterly_exam = mysqli_real_escape_string($connection, $_POST['quarterly_exam']);
    $final_grade = mysqli_real_escape_string($connection, $_POST['final_grade']);

    $insert_query = "INSERT INTO student_grades (student_id, written_exam, performance_task, quarterly_exam, final_grade) 
                     VALUES ($student_id, '$written_exam', '$performance_task', '$quarterly_exam', '$final_grade')";
    if (mysqli_query($connection, $insert_query)) {
        echo "<div class='alert alert-success'>Grade record added successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error adding record: " . mysqli_error($connection) . "</div>";
    }
}

// SQL query to fetch student grades
$query = "SELECT sg.*, s.learners_name FROM student_grades sg
          JOIN students s ON sg.student_id = s.id WHERE 1=1";

if ($school_level) {
    $query .= " AND s.school_level = '$school_level'";
}

$query .= " ORDER BY s.learners_name ASC";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Fetch students for dropdown
$students_query = "SELECT id, learners_name FROM students";
$students_result = mysqli_query($connection, $students_query);

// Display grading scale as HTML content based on school level
$grading_scale_html = "";
if ($school_level === 'JHS') {
    $grading_scale_html = '
    <h4>Grading Scale for Junior High School (JHS)</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Grade</th>
                <th>Equivalency</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>90 - 100</td>
                <td>Outstanding</td>
            </tr>
            <tr>
                <td>85 - 89</td>
                <td>Very Satisfactory</td>
            </tr>
            <tr>
                <td>80 - 84</td>
                <td>Satisfactory</td>
            </tr>
            <tr>
                <td>75 - 79</td>
                <td>Fairly Satisfactory</td>
            </tr>
            <tr>
                <td>Below 75</td>
                <td>Needs Improvement</td>
            </tr>
        </tbody>
    </table>';
} elseif ($school_level === 'SHS') {
    $grading_scale_html = '
    <h4>Grading Scale for Senior High School (SHS)</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Grade</th>
                <th>Equivalency</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>90 - 100</td>
                <td>Excellent</td>
            </tr>
            <tr>
                <td>85 - 89</td>
                <td>Very Good</td>
            </tr>
            <tr>
                <td>80 - 84</td>
                <td>Good</td>
            </tr>
            <tr>
                <td>75 - 79</td>
                <td>Fair</td>
            </tr>
            <tr>
                <td>Below 75</td>
                <td>Unsatisfactory</td>
            </tr>
        </tbody>
    </table>';
} else {
    $grading_scale_html = '
    <h4>Grading Guide</h4>
    <p>Please select a school level to view the appropriate grading scale.</p>';
}
?>

<div class="container mt-5">
    <h2>Grading System</h2>

    <!-- Display Grading Scale HTML -->
    <div class="mb-4">
        <?php echo $grading_scale_html; ?>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="" class="form-inline mb-3">
        <label for="school_level" class="mr-2">School Level:</label>
        <select name="school_level" id="school_level" class="form-control mr-3">
            <option value="">Select School Level</option>
            <option value="JHS" <?php if ($school_level === 'JHS') echo 'selected'; ?>>JHS</option>
            <option value="SHS" <?php if ($school_level === 'SHS') echo 'selected'; ?>>SHS</option>
        </select>
        <input type="submit" class="btn btn-primary" value="Filter">
    </form>

    <!-- Grades Table -->
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Student Name</th>
                <th>Written Exam</th>
                <th>Performance Task</th>
                <th>Quarterly Exam</th>
                <th>Final Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['learners_name']); ?></td>
                <td><?php echo htmlspecialchars($row['written_exam']); ?></td>
                <td><?php echo htmlspecialchars($row['performance_task']); ?></td>
                <td><?php echo htmlspecialchars($row['quarterly_exam']); ?></td>
                <td><?php echo htmlspecialchars($row['final_grade']); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include('../crud/footer.php'); ?>
