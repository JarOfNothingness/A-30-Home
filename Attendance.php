<?php

session_start(); // Start the session at the very top

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('../crud/header.php'); 
include("../LoginRegisterAuthentication/connection.php");

// Fetch distinct values for filters
$grade_levels_query = "SELECT DISTINCT gradeLevel FROM sf2_attendance_report ORDER BY gradeLevel";
$grade_levels_result = mysqli_query($connection, $grade_levels_query);

$sections_query = "SELECT DISTINCT section FROM sf2_attendance_report ORDER BY section";
$sections_result = mysqli_query($connection, $sections_query);

$learners_query = "SELECT id, learners_name, school_id, grade, section FROM students ORDER BY learners_name";
$learners_result = mysqli_query($connection, $learners_query);

// Fetch distinct school years for the dropdown
$school_years_query = "SELECT DISTINCT school_year FROM students ORDER BY school_year";
$school_years_result = mysqli_query($connection, $school_years_query);

// Handle filtering of records
$filters = [];
$filter_sql = '';

if (isset($_POST['filter'])) {
    if (!empty($_POST['grade_level'])) {
        $filters[] = "gradeLevel = '" . mysqli_real_escape_string($connection, $_POST['grade_level']) . "'";
    }
    if (!empty($_POST['section'])) {
        $filters[] = "section = '" . mysqli_real_escape_string($connection, $_POST['section']) . "'";
    }
    if (!empty($_POST['learner_name'])) {
        $filters[] = "learnerName = '" . mysqli_real_escape_string($connection, $_POST['learner_name']) . "'";
    }
    if (!empty($_POST['school_year'])) {
        $filters[] = "schoolYear = '" . mysqli_real_escape_string($connection, $_POST['school_year']) . "'";
    }
    if (!empty($_POST['month'])) {
        $filters[] = "month = '" . mysqli_real_escape_string($connection, $_POST['month']) . "'";
    }
    
    if (count($filters) > 0) {
        $filter_sql = 'WHERE ' . implode(' AND ', $filters);
    }
}

$query = "SELECT * FROM sf2_attendance_report $filter_sql";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#attendanceTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true
            });
        });
    </script>
</head>
<body>
<div class="container mt-5">
    <h2>Attendance Records</h2>
    
    <!-- Filter Form -->
    <div class="mb-4">
        <h3>Filter Attendance Records</h3>
        <form method="POST" action="" class="row g-3">
            <div class="col-md-3">
                <label for="grade_level" class="form-label">Grade Level:</label>
                <select name="grade_level" id="grade_level" class="form-control">
                    <option value="">Select Grade Level</option>
                    <?php while ($row = mysqli_fetch_assoc($grade_levels_result)) { ?>
                        <option value="<?php echo htmlspecialchars($row['gradeLevel']); ?>">
                            <?php echo htmlspecialchars($row['gradeLevel']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="section" class="form-label">Section:</label>
                <select name="section" id="section" class="form-control">
                    <option value="">Select Section</option>
                    <?php while ($row = mysqli_fetch_assoc($sections_result)) { ?>
                        <option value="<?php echo htmlspecialchars($row['section']); ?>">
                            <?php echo htmlspecialchars($row['section']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="learner_name" class="form-label">Learner Name:</label>
                <select name="learner_name" id="learner_name" class="form-control">
                    <option value="">Select Learner</option>
                    <?php while ($row = mysqli_fetch_assoc($learners_result)) { ?>
                        <option value="<?php echo htmlspecialchars($row['learners_name']); ?>">
                            <?php echo htmlspecialchars($row['learners_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="school_year" class="form-label">School Year:</label>
                <select name="school_year" id="school_year" class="form-control">
                    <option value="">Select School Year</option>
                    <?php while ($row = mysqli_fetch_assoc($school_years_result)) { ?>
                        <option value="<?php echo htmlspecialchars($row['school_year']); ?>">
                            <?php echo htmlspecialchars($row['school_year']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="month" class="form-label">Month:</label>
                <input type="month" name="month" id="month" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" name="filter" class="btn btn-primary mt-3">Filter Records</button>
                <a href="AddAttendance.php" class="btn btn-secondary mt-3 ml-2">Add New Record</a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
        <table id="attendanceTable" class="table table-bordered table-striped table-sm">
            <thead class="thead-dark">
                <tr>
                    <th>School ID</th>
                    <th>Learner Name</th>
                    <th>Grade Level</th>
                    <th>Section</th>
                    <th>School Year</th>
                    <th>Month</th>
                    <?php for ($i = 1; $i <= 31; $i++): ?>
                        <th>Day <?php echo $i; ?></th>
                    <?php endfor; ?>
                    <th>Total Present</th>
                    <th>Total Absent</th>
                    <th>Total Late</th>
                    <th>Total Excused</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['schoolId']); ?></td>
                        <td><?php echo htmlspecialchars($row['learnerName']); ?></td>
                        <td><?php echo htmlspecialchars($row['gradeLevel']); ?></td>
                        <td><?php echo htmlspecialchars($row['section']); ?></td>
                        <td><?php echo htmlspecialchars($row['schoolYear']); ?></td>
                        <td><?php echo htmlspecialchars($row['month']); ?></td>
                        <?php for ($i = 1; $i <= 31; $i++): ?>
                            <td><?php echo htmlspecialchars($row['day_' . str_pad($i, 2, '0', STR_PAD_LEFT)]); ?></td>
                        <?php endfor; ?>
                        <td><?php echo htmlspecialchars($row['total_present']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_absent']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_late']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_excused']); ?></td>
                        <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                        <td>
                            <a href="update_attendance.php?id=<?php echo urlencode($row['form2Id']); ?>" class="btn btn-success btn-sm">Update</a>
                            <a href="delete_attendance.php?id=<?php echo urlencode($row['form2Id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Include Bootstrap JavaScript and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php include('../crud/footer.php'); ?>
