<?php
session_start(); // Ensure this is at the very top of the file

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('../crud/header.php');
include("../LoginRegisterAuthentication/connection.php");

// Ensure that $learners_name is defined
$learners_name = isset($_GET['learners_name']) ? $_GET['learners_name'] : '';

// SQL query to fetch reports
$query = "SELECT s.learners_name, s.id, sf2.*
          FROM students s
          JOIN sf2_attendance_report sf2 ON s.learners_name = sf2.learnerName
          WHERE 1=1";

if ($learners_name) {
    $query .= " AND s.learners_name LIKE '%" . mysqli_real_escape_string($connection, $learners_name) . "%'";
}

$query .= " ORDER BY s.learners_name ASC";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Fetch students for filter dropdown
$student_query = "SELECT DISTINCT learners_name FROM students ORDER BY learners_name ASC";
$students_result = mysqli_query($connection, $student_query);

if (!$students_result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<div class="container mt-5">
    <h2>Student Reports</h2>

    <!-- Filter Form -->
    <form method="GET" action="" class="form-inline mb-3">
        <div class="form-group mr-3">
            <label for="learners_name" class="mr-2">Learners Name:</label>
            <select name="learners_name" id="learners_name" class="form-control mr-3">
                <option value="">Select Learners Name</option>
                <?php while ($student = mysqli_fetch_assoc($students_result)) { ?>
                    <option value="<?php echo htmlspecialchars($student['learners_name']); ?>"
                        <?php if ($learners_name === $student['learners_name']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($student['learners_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <input type="submit" class="btn btn-primary" value="Filter">
    </form>

    <!-- Reports Table -->
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Learners Name</th>
                <th>Form 2</th>
                <th>Form 137</th>
                <th>Form 14</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['learners_name']); ?></td>
                <td><a href="view_form2.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-info btn-sm">View Form 2</a></td>
                <td><a href="view_form137.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-info btn-sm">View Form 137</a></td>
                <td><a href="view_form14.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-info btn-sm">View Form 14</a></td>
                <td><a href="index.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-primary btn-sm">Send Report to Gmail</a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include('../crud/footer.php'); ?>
