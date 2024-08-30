<?php
include("../LoginRegisterAuthentication/connection.php");

$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : '';

if ($student_id) {
    // Initialize response array
    $filters = [];

    // Fetch the school level of the selected student
    $school_level_query = "SELECT school_level FROM students WHERE id = $student_id";
    $school_level_result = mysqli_query($connection, $school_level_query);
    $school_level_row = mysqli_fetch_assoc($school_level_result);
    $school_level = $school_level_row['school_level'];

    // Fetch subjects related to the selected student's school level
    if ($school_level) {
        $subjects_query = "SELECT id, name FROM subjects";
        $subjects_result = mysqli_query($connection, $subjects_query);
        $filters['subjects'] = mysqli_fetch_all($subjects_result, MYSQLI_ASSOC);
    } else {
        $filters['subjects'] = [];
    }

    // Fetch sections related to the selected student
    $sections_query = "SELECT DISTINCT section FROM students WHERE id = $student_id";
    $sections_result = mysqli_query($connection, $sections_query);
    $filters['sections'] = mysqli_fetch_all($sections_result, MYSQLI_ASSOC);

    // Fetch school years related to the selected student
    $school_years_query = "SELECT DISTINCT school_year FROM students WHERE id = $student_id";
    $school_years_result = mysqli_query($connection, $school_years_query);
    $filters['school_years'] = mysqli_fetch_all($school_years_result, MYSQLI_ASSOC);

    // Format response
    $response = [
        'subjects' => array_map(function($row) { return ['value' => $row['id'], 'text' => $row['name']]; }, $filters['subjects']),
        'sections' => array_map(function($row) { return ['value' => $row['section'], 'text' => $row['section']]; }, $filters['sections']),
        'school_years' => array_map(function($row) { return ['value' => $row['school_year'], 'text' => $row['school_year']]; }, $filters['school_years']),
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
