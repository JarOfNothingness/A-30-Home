<?php
include('../crud/header.php');
include("../LoginRegisterAuthentication/connection.php");

// Check if ID is provided in the query string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No attendance record ID provided.");
}

$attendance_id = intval($_GET['id']);

// Fetch the existing attendance record from the database
$query = "SELECT * FROM sf2_attendance_report WHERE form2Id = $attendance_id";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

$attendance = mysqli_fetch_assoc($result);

if (!$attendance) {
    die("Attendance record not found.");
}

// Handle form submission for updating the attendance record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $month = mysqli_real_escape_string($connection, $_POST['month']);
    $day = intval($_POST['day']); // Get selected day
    $total_present = intval($_POST['total_present']);
    $total_absent = intval($_POST['total_absent']);
    $total_late = intval($_POST['total_late']);
    $total_excused = intval($_POST['total_excused']);
    $remarks = mysqli_real_escape_string($connection, $_POST['remarks']);
    $day_status = mysqli_real_escape_string($connection, $_POST['day_status']); // Day status

    $update_query = "UPDATE sf2_attendance_report SET month = '$month', day_" . str_pad($day, 2, '0', STR_PAD_LEFT) . " = '$day_status', total_present = $total_present, total_absent = $total_absent, total_late = $total_late, total_excused = $total_excused, remarks = '$remarks' WHERE form2Id = $attendance_id";

    if (mysqli_query($connection, $update_query)) {
        echo "<div class='alert alert-success'>Attendance record updated successfully. <a href='attendance.php'>Back to Attendance</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating record: " . mysqli_error($connection) . "</div>";
    }
}

// Function to generate options for day dropdown
function generate_day_options($selected_day) {
    $options = '';
    for ($i = 1; $i <= 31; $i++) {
        $selected = ($i == $selected_day) ? 'selected' : '';
        $options .= "<option value='$i' $selected>$i</option>";
    }
    return $options;
}

// Function to generate options for day status dropdown
function generate_day_status_options($selected_status) {
    $statuses = ['PRESENT', 'ABSENT', 'LATE', 'EXCUSED'];
    $options = '';
    foreach ($statuses as $status) {
        $selected = ($status == $selected_status) ? 'selected' : '';
        $options .= "<option value='$status' $selected>$status</option>";
    }
    return $options;
}
?>

<div class="container mt-5">
    <h2>Update Attendance Record</h2>

    <!-- Update Attendance Form -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="month">Month:</label>
            <input type="text" name="month" id="month" class="form-control" value="<?php echo htmlspecialchars($attendance['month']); ?>" required>
        </div>
        <div class="form-group">
            <label for="day">Day:</label>
            <select name="day" id="day" class="form-control" required>
                <?php echo generate_day_options($attendance['day']); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="day_status">Day Status:</label>
            <select name="day_status" id="day_status" class="form-control" required>
                <?php echo generate_day_status_options($attendance['day_' . str_pad($attendance['day'], 2, '0', STR_PAD_LEFT)]); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="total_present">Total Present:</label>
            <input type="number" name="total_present" id="total_present" class="form-control" value="<?php echo htmlspecialchars($attendance['total_present']); ?>" required>
        </div>
        <div class="form-group">
            <label for="total_absent">Total Absent:</label>
            <input type="number" name="total_absent" id="total_absent" class="form-control" value="<?php echo htmlspecialchars($attendance['total_absent']); ?>" required>
        </div>
        <div class="form-group">
            <label for="total_late">Total Late:</label>
            <input type="number" name="total_late" id="total_late" class="form-control" value="<?php echo htmlspecialchars($attendance['total_late']); ?>" required>
        </div>
        <div class="form-group">
            <label for="total_excused">Total Excused:</label>
            <input type="number" name="total_excused" id="total_excused" class="form-control" value="<?php echo htmlspecialchars($attendance['total_excused']); ?>" required>
        </div>
        <div class="form-group">
            <label for="remarks">Remarks:</label>
            <textarea name="remarks" id="remarks" class="form-control" rows="3"><?php echo htmlspecialchars($attendance['remarks']); ?></textarea>
        </div>
        <input type="submit" class="btn btn-primary mt-3" value="Update Record">
    </form>
</div>

<?php include('../crud/footer.php'); ?>
