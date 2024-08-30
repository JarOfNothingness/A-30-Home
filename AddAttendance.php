<?php
session_start(); // Start the session at the very top

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

ob_start(); // Start output buffering

include('../crud/header.php'); 
include("../LoginRegisterAuthentication/connection.php");

// Fetch distinct values for dropdowns
$grade_levels_query = "SELECT DISTINCT grade FROM students ORDER BY grade";
$grade_levels_result = mysqli_query($connection, $grade_levels_query);

$sections_query = "SELECT DISTINCT section FROM students ORDER BY section";
$sections_result = mysqli_query($connection, $sections_query);

$learners_query = "SELECT DISTINCT learners_name, school_id, grade, section FROM students ORDER BY learners_name";
$learners_result = mysqli_query($connection, $learners_query);

// Fetch distinct school years for the dropdown
$school_years_query = "SELECT DISTINCT school_year FROM students ORDER BY school_year";
$school_years_result = mysqli_query($connection, $school_years_query);

// Handle form submission for adding attendance record
if (isset($_POST['add_attendance'])) {
    $learner_name = mysqli_real_escape_string($connection, $_POST['learner_name']);
    $school_id = mysqli_real_escape_string($connection, $_POST['school_id']);
    $grade_level = mysqli_real_escape_string($connection, $_POST['grade_level']);
    $section = mysqli_real_escape_string($connection, $_POST['section']);
    $school_year = mysqli_real_escape_string($connection, $_POST['school_year']);
    $month = mysqli_real_escape_string($connection, $_POST['month']);
    
    $days = [];
    for ($i = 1; $i <= 31; $i++) {
        $day = str_pad($i, 2, '0', STR_PAD_LEFT);
        $days[$day] = mysqli_real_escape_string($connection, $_POST["day_$day"]);
    }

    $total_present = array_count_values($days)['Present'] ?? 0;
    $total_absent = array_count_values($days)['Absent'] ?? 0;
    $total_late = array_count_values($days)['Late'] ?? 0;
    $total_excused = array_count_values($days)['Excused'] ?? 0;

    $query = "INSERT INTO sf2_attendance_report (schoolId, learnerName, gradeLevel, section, schoolYear, month, total_present, total_absent, total_late, total_excused, ";
    for ($i = 1; $i <= 31; $i++) {
        $query .= "day_" . str_pad($i, 2, '0', STR_PAD_LEFT) . ", ";
    }
    $query .= "remarks) VALUES ('$school_id', '$learner_name', '$grade_level', '$section', '$school_year', '$month', '$total_present', '$total_absent', '$total_late', '$total_excused', ";
    for ($i = 1; $i <= 31; $i++) {
        $query .= "'" . $days[str_pad($i, 2, '0', STR_PAD_LEFT)] . "', ";
    }
    $query .= "'')";

    // Remove trailing comma and space from the query
    $query = rtrim($query, ', ');
    
    if (mysqli_query($connection, $query)) {
        header("Location: Attendance.php"); // Redirect to Attendance.php after successful record addition
        ob_end_flush(); 
        exit();
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Attendance Record</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function populateStudentDetails() {
            const select = document.getElementById('learner_name');
            const selectedOption = select.options[select.selectedIndex];
            document.getElementById('school_id').value = selectedOption.getAttribute('data-school-id') || '';
            document.getElementById('grade_level').value = selectedOption.getAttribute('data-grade-level') || '';
            document.getElementById('section').value = selectedOption.getAttribute('data-section') || '';
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2>Add New Attendance Record</h2>

    <!-- Form to Add New Attendance Record -->
    <form method="POST" action="" class="row g-3">
        <!-- Learner Name Dropdown -->
        <div class="col-md-6">
            <label for="learner_name" class="form-label">Learner Name:</label>
            <select name="learner_name" id="learner_name" class="form-control" required onchange="populateStudentDetails()">
                <option value="">Select Learner</option>
                <?php while ($student = mysqli_fetch_assoc($learners_result)) { ?>
                    <option value="<?php echo htmlspecialchars($student['learners_name']); ?>"
                        data-school-id="<?php echo htmlspecialchars($student['school_id']); ?>"
                        data-grade-level="<?php echo htmlspecialchars($student['grade']); ?>"
                        data-section="<?php echo htmlspecialchars($student['section']); ?>">
                        <?php echo htmlspecialchars($student['learners_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- Hidden Fields to Store Selected Student Details -->
        <div class="col-md-6 d-none">
            <input type="hidden" id="school_id" name="school_id">
            <input type="hidden" id="grade_level" name="grade_level">
            <input type="hidden" id="section" name="section">
        </div>

        <!-- School Year and Month Fields -->
        <div class="col-md-6">
            <label for="school_year" class="form-label">School Year:</label>
            <select name="school_year" id="school_year" class="form-control" required>
                <option value="">Select School Year</option>
                <?php while ($row = mysqli_fetch_assoc($school_years_result)) { ?>
                    <option value="<?php echo htmlspecialchars($row['school_year']); ?>">
                        <?php echo htmlspecialchars($row['school_year']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="month" class="form-label">Month:</label>
            <input type="month" name="month" id="month" class="form-control" required>
        </div>

        <!-- Loop to generate inputs for each day of the month -->
        <?php for ($i = 1; $i <= 31; $i++): ?>
        <div class="col-md-2">
            <label for="day_<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" class="form-label">Day <?php echo $i; ?>:</label>
            <select name="day_<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" id="day_<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" class="form-control">
                <option value="">Status</option>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Late">Late</option>
                <option value="Excused">Excused</option>
            </select>
        </div>
        <?php endfor; ?>

        <div class="col-12">
            <button type="submit" name="add_attendance" class="btn btn-primary mt-3">Add Record</button>
            <a href="Attendance.php" class="btn btn-secondary mt-3 ml-2">Back to Attendance</a>
        </div>
    </form>
</div>

<!-- Include Bootstrap JavaScript and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php include('../crud/footer.php'); ?>
