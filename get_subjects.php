<?php
include("../LoginRegisterAuthentication/connection.php");

if (isset($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);
    
    // Fetch the subject from the students table
    $query = "SELECT subject FROM students WHERE id = $student_id";
    $result = mysqli_query($connection, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);
        $subjectName = $student['subject'];

        // Fetch subject details from the subjects table
        $subjectQuery = "SELECT id, name FROM subjects WHERE name = '$subjectName'";
        $subjectResult = mysqli_query($connection, $subjectQuery);

        if (mysqli_num_rows($subjectResult) > 0) {
            echo '<option value="">Select Subject</option>';
            while ($row = mysqli_fetch_assoc($subjectResult)) {
                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
            }
        } else {
            echo '<option value="">No subjects available</option>';
        }
    } else {
        echo '<option value="">No subjects available</option>';
    }
}
?>
