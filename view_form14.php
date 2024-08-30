<?php
session_start(); // Start the session at the very top

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include('headerforreports.php');
include("../LoginRegisterAuthentication/connection.php");

// Get the student ID from the query string
$student_id = isset($_GET['id']) ? intval($_GET['id']) : '';

// SQL query to fetch student details and grades
$query = "SELECT s.*, sg.*
          FROM students s 
          JOIN student_grades sg ON s.id = sg.student_id
          WHERE s.id = '$student_id'";

$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Fetch the student details
$student = mysqli_fetch_assoc($result);

// Initialize variables for grades
$first_quarter = isset($student['first_quarter']) ? $student['first_quarter'] : 0;
$second_quarter = isset($student['second_quarter']) ? $student['second_quarter'] : 0;
$third_quarter = isset($student['third_quarter']) ? $student['third_quarter'] : 0;
$fourth_quarter = isset($student['fourth_quarter']) ? $student['fourth_quarter'] : 0;

// Calculate total and average
$total = $first_quarter + $second_quarter + $third_quarter + $fourth_quarter;
$average = $total > 0 ? $total / 4 : 0;

// Fetch final grades and metrics
$grades_query = "SELECT sg.*, s.learners_name FROM student_grades sg
                 JOIN students s ON sg.student_id = s.id
                 WHERE sg.student_id = '$student_id'";
$grades_result = mysqli_query($connection, $grades_query);

if (!$grades_result) {
    die("Query failed: " . mysqli_error($connection));
}

$grades = [];
while ($row = mysqli_fetch_assoc($grades_result)) {
    $grades[] = $row;
}

function calculateMetrics($grades) {
    $metrics = [
        'total_score' => 0,
        'no_of_cases' => 0,
        'highest_possible_score' => 0,
        'highest_score' => 0,
        'lowest_score' => null,
        'average_mean' => 0,
        'mps' => 0,
        'students_75_pl' => 0,
        'percentage_75_pl' => 0
    ];
    $total_scores = [];

    foreach ($grades as $grade) {
        $written_exam = isset($grade['written_exam']) ? $grade['written_exam'] : 0;
        $performance_task = isset($grade['performance_task']) ? $grade['performance_task'] : 0;
        $quarterly_exam = isset($grade['quarterly_exam']) ? $grade['quarterly_exam'] : 0;
        $final_grade = isset($grade['final_grade']) ? $grade['final_grade'] : 0;
        $highest_possible_score = isset($grade['highest_possible_score']) ? $grade['highest_possible_score'] : 0;

        $total_score = ($written_exam * 0.40) + ($performance_task * 0.40) + ($quarterly_exam * 0.20);
        $metrics['total_score'] += $total_score;
        $metrics['no_of_cases']++;
        $metrics['highest_possible_score'] = max($metrics['highest_possible_score'], $highest_possible_score);
        $metrics['highest_score'] = max($metrics['highest_score'], $final_grade);

        if ($metrics['lowest_score'] === null || $total_score < $metrics['lowest_score']) {
            $metrics['lowest_score'] = $total_score;
        }

        $total_scores[] = $total_score;
    }

    if ($metrics['no_of_cases'] > 0) {
        $metrics['average_mean'] = $metrics['total_score'] / $metrics['no_of_cases'];
        $metrics['mps'] = $metrics['average_mean'];
        $metrics['students_75_pl'] = count(array_filter($total_scores, fn($score) => $score >= 75));
        $metrics['percentage_75_pl'] = ($metrics['students_75_pl'] / $metrics['no_of_cases']) * 100;
    }

    return $metrics;
}

$metrics = calculateMetrics($grades);
?>

<!-- Custom styles for this page -->
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .container {
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .table {
        margin-bottom: 20px;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .print-btn {
        margin-right: 10px;
    }
    @media print {
        .print-btn,
        .btn-secondary {
            display: none;
        }
        .container {
            border: none;
            box-shadow: none;
        }
    }
</style>

<div class="container">
    <h2 class="text-center">LANAO NATIONAL HIGH SCHOOL</h2>
    <p class="text-center"><strong>School Year: 2023-2024</strong></p>
    <p class="text-center"><strong>Grade & Section: <?php echo htmlspecialchars($student['grade'] . ' - ' . $student['section']); ?></strong></p>
    <p class="text-center"><strong>Subject: <?php echo htmlspecialchars($student['subject']); ?></strong></p>
    <p class="text-center"><strong>Form 14</strong></p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Learners Full Name</th>
                <th>Grade & Section</th>
                <th>Teacher</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($student['id']); ?></td>
                <td><?php echo htmlspecialchars($student['learners_name']); ?></td>
                <td><?php echo htmlspecialchars($student['grade'] . ' - ' . $student['section']); ?></td>
                <td><?php // Display teacher name if applicable; otherwise leave blank or handle as needed ?></td>
            </tr>
        </tbody>
    </table>

    <h4>Student Scores:</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Subject</th>
                <th>First Quarter</th>
                <th>Second Quarter</th>
                <th>Third Quarter</th>
                <th>Fourth Quarter</th>
                <th>Total</th>
                <th>Average</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($student['subject']); ?></td>
                <td><?php echo htmlspecialchars($first_quarter); ?></td>
                <td><?php echo htmlspecialchars($second_quarter); ?></td>
                <td><?php echo htmlspecialchars($third_quarter); ?></td>
                <td><?php echo htmlspecialchars($fourth_quarter); ?></td>
                <td><?php echo $total; ?></td>
                <td><?php echo number_format($average, 2); ?></td>
            </tr>
        </tbody>
    </table>

    <h4>Summary Statistics:</h4>
    <table class="table table-bordered">
        <tr>
            <td>Total Score:</td>
            <td><?php echo $metrics['total_score']; ?></td>
        </tr>
        <tr>
            <td>Average MPS:</td>
            <td><?php echo number_format($metrics['average_mean'], 2); ?></td>
        </tr>
        <tr>
            <td>Highest Possible Score:</td>
            <td><?php echo $metrics['highest_possible_score']; ?></td>
        </tr>
        <tr>
            <td>Highest Score:</td>
            <td><?php echo $metrics['highest_score']; ?></td>
        </tr>
        <tr>
            <td>Lowest Score:</td>
            <td><?php echo $metrics['lowest_score']; ?></td>
        </tr>
        <tr>
            <td>No. of Students Getting 75% PL:</td>
            <td><?php echo $metrics['students_75_pl']; ?></td>
        </tr>
        <tr>
            <td>Percentage of Students Getting 75% PL:</td>
            <td><?php echo number_format($metrics['percentage_75_pl'], 2) . '%'; ?></td>
        </tr>
    </table>

    <div class="text-center">
        <button class="btn btn-secondary print-btn" onclick="window.print()">Print</button>
        <a href="r.php" class="btn btn-primary">Back</a>
    </div>
</div>

<?php include('../crud/footer.php'); ?>
