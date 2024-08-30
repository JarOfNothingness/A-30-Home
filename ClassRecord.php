<?php
session_start(); // Start the session at the very top

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

ob_start();
include('../crud/header.php');
include("../LoginRegisterAuthentication/connection.php");

// Initialize filter variables
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : '';
$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : '';
$section = isset($_GET['section']) ? mysqli_real_escape_string($connection, $_GET['section']) : '';
$school_year = isset($_GET['school_year']) ? mysqli_real_escape_string($connection, $_GET['school_year']) : '';

// SQL query to fetch student grades based on filters
$query = "SELECT sg.*, s.learners_name, sub.name as subject_name 
          FROM student_grades sg
          JOIN students s ON sg.student_id = s.id
          JOIN subjects sub ON sg.subject_id = sub.id
          WHERE 1=1";

if ($student_id) {
    $query .= " AND sg.student_id = $student_id";
}
if ($subject_id) {
    $query .= " AND sg.subject_id = $subject_id";
}
if ($section) {
    $query .= " AND s.section = '$section'";
}
if ($school_year) {
    $query .= " AND s.school_year = '$school_year'";
}

$query .= " ORDER BY s.learners_name ASC";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Fetch students, subjects, sections, and school years for dropdowns
$students_query = "SELECT id, learners_name FROM students";
$students_result = mysqli_query($connection, $students_query);

$subjects_query = "SELECT id, name FROM subjects";
$subjects_result = mysqli_query($connection, $subjects_query);

$sections_query = "SELECT DISTINCT section FROM students";
$sections_result = mysqli_query($connection, $sections_query);

$school_years_query = "SELECT DISTINCT school_year FROM students";
$school_years_result = mysqli_query($connection, $school_years_query);

if (!$students_result || !$subjects_result || !$sections_result || !$school_years_result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<div class="container mt-5">
    <h2>Class Record</h2>

    <!-- Filter Form -->
    <form method="GET" action="" class="form-inline mb-3">
        <div class="form-group mx-2">
            <label for="student_id" class="sr-only">Student:</label>
            <select name="student_id" id="student_id" class="form-control" onchange="updateFilters()">
                <option value="">Select Student</option>
                <?php while ($student = mysqli_fetch_assoc($students_result)) { ?>
                    <option value="<?php echo htmlspecialchars($student['id']); ?>" <?php if ($student_id == $student['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($student['learners_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group mx-2">
            <label for="subject_id" class="sr-only">Subject:</label>
            <select name="subject_id" id="subject_id" class="form-control">
                <option value="">Select Subject</option>
                <!-- Subject options will be dynamically populated based on selected student -->
            </select>
        </div>

        <div class="form-group mx-2">
            <label for="section" class="sr-only">Section:</label>
            <select name="section" id="section" class="form-control">
                <option value="">Select Section</option>
                <!-- Section options will be dynamically populated based on selected student -->
            </select>
        </div>

        <div class="form-group mx-2">
            <label for="school_year" class="sr-only">School Year:</label>
            <select name="school_year" id="school_year" class="form-control">
                <option value="">Select School Year</option>
                <!-- School Year options will be dynamically populated based on selected student -->
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Redirect to Add New Grade Record -->
    <a href="add_grade.php" class="btn btn-success mb-3">Add Record</a>

    <!-- Data Table -->
    <h4>Grades Data</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student</th>
                <th>Subject</th>
                <th>Written Exam</th>
                <th>Performance Task</th>
                <th>Quarterly Exam</th>
                <th>Final Grade</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['learners_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['written_exam']); ?></td>
                    <td><?php echo htmlspecialchars($row['performance_task']); ?></td>
                    <td><?php echo htmlspecialchars($row['quarterly_exam']); ?></td>
                    <td><?php echo htmlspecialchars($row['final_grade']); ?></td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="editRecord(<?php echo $row['id']; ?>)">Edit</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
// Function to update filters based on selected student
function updateFilters() {
    var studentId = document.getElementById('student_id').value;

    if (studentId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_student_filters.php?student_id=' + studentId, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                populateDropdown('subject_id', data.subjects);
                populateDropdown('section', data.sections);
                populateDropdown('school_year', data.school_years);
            }
        };
        xhr.send();
    } else {
        // Reset dropdowns if no student is selected
        populateDropdown('subject_id', []);
        populateDropdown('section', []);
        populateDropdown('school_year', []);
    }
}

// Function to populate dropdown options
function populateDropdown(id, options) {
    var dropdown = document.getElementById(id);
    dropdown.innerHTML = '<option value="">Select</option>'; // Clear existing options
    options.forEach(function(option) {
        var opt = document.createElement('option');
        opt.value = option.value;
        opt.textContent = option.text;
        dropdown.appendChild(opt);
    });
}
function editRecord(id) {
    // Redirect to the edit page with the record ID
    window.location.href = 'edit_grade.php?id=' + id;
}
</script>

<?php include('../crud/footer.php'); ?>
