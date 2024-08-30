<?php
session_start(); // Start the session at the very top

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('../crud/header.php'); 
include("../LoginRegisterAuthentication/connection.php");

// Fetch student names for the dropdown
$students_query = "SELECT id, learners_name FROM students ORDER BY learners_name ASC";
$students_result = mysqli_query($connection, $students_query);

if (!$students_result) {
    die("Query failed: " . mysqli_error($connection));
}

// Ensure that $student_id is defined
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : '';

// SQL query to fetch final grades for the selected student
$query = "SELECT sg.*, s.learners_name FROM student_grades sg
          JOIN students s ON sg.student_id = s.id WHERE 1=1";

if ($student_id) {
    $query .= " AND sg.student_id = $student_id";
}

$query .= " ORDER BY s.learners_name ASC";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Function to calculate total scores and other metrics
function calculateMetrics($grades) {
    $metrics = [
        'total_score' => 0,
        'no_of_cases' => 0,
        'highest_possible_score' => 0,
        'highest_score' => 0,
        'lowest_score' => null, // Changed from PHP_INT_MAX
        'average_mean' => 0,
        'mps' => 0,
        'students_75_pl' => 0,
        'percentage_75_pl' => 0
    ];
    $total_scores = [];

    foreach ($grades as $grade) {
        // Ensure required fields are set
        $written_exam = isset($grade['written_exam']) ? $grade['written_exam'] : 0;
        $performance_task = isset($grade['performance_task']) ? $grade['performance_task'] : 0;
        $quarterly_exam = isset($grade['quarterly_exam']) ? $grade['quarterly_exam'] : 0;
        $final_grade = isset($grade['final_grade']) ? $grade['final_grade'] : 0;
        $highest_possible_score = isset($grade['highest_possible_score']) ? $grade['highest_possible_score'] : 0;

        // Calculate the total score based on DepEd weights
        $total_score = ($written_exam * 0.40) + ($performance_task * 0.40) + ($quarterly_exam * 0.20);
        $metrics['total_score'] += $total_score;
        $metrics['no_of_cases']++;
        $metrics['highest_possible_score'] = max($metrics['highest_possible_score'], $highest_possible_score);
        $metrics['highest_score'] = max($metrics['highest_score'], $final_grade);

        // Update the lowest score
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


// Retrieve grades and calculate metrics
$grades = [];
if ($student_id) {
    while ($row = mysqli_fetch_assoc($result)) {
        $grades[] = $row;
    }
    $metrics = calculateMetrics($grades);
}
?>

<div class="container mt-5">
    <h2>Final Grades</h2>

    <!-- Filter Form -->
    <form method="GET" action="" class="form-inline mb-3">
        <label for="student_id" class="mr-2">Select Student:</label>
        <select name="student_id" id="student_id" class="form-control mr-3">
            <option value="">Select Student</option>
            <?php while ($student = mysqli_fetch_assoc($students_result)) { ?>
                <option value="<?php echo htmlspecialchars($student['id']); ?>"
                    <?php if ($student_id == $student['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($student['learners_name']); ?>
                </option>
            <?php } ?>
        </select>
        <input type="submit" class="btn btn-primary" value="Filter">
    </form>

    <!-- Final Grades Table -->
    <?php if (!empty($grades)) { ?>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>NAMES</th>
                <th>FIRST</th>
                <th>SECOND</th>
                <th>THIRD</th>
                <th>FOURTH</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($grades[0]['learners_name']); ?></td>
                <td><?php echo $metrics['total_score']; ?></td>
                <td><?php echo $metrics['total_score']; ?></td>
                <td><?php echo $metrics['total_score']; ?></td>
                <td><?php echo $metrics['total_score']; ?></td>
            </tr>
            <tr>
                <td>Total Score</td>
                <td><?php echo $metrics['total_score']; ?></td>
                <td><?php echo $metrics['total_score']; ?></td>
                <td><?php echo $metrics['total_score']; ?></td>
                <td><?php echo $metrics['total_score']; ?></td>
            </tr>
            <tr>
                <td>No of Cases</td>
                <td><?php echo $metrics['no_of_cases']; ?></td>
                <td><?php echo $metrics['no_of_cases']; ?></td>
                <td><?php echo $metrics['no_of_cases']; ?></td>
                <td><?php echo $metrics['no_of_cases']; ?></td>
            </tr>
            <tr>
                <td>Highest Possible Score</td>
                <td><?php echo $metrics['highest_possible_score']; ?></td>
                <td><?php echo $metrics['highest_possible_score']; ?></td>
                <td><?php echo $metrics['highest_possible_score']; ?></td>
                <td><?php echo $metrics['highest_possible_score']; ?></td>
            </tr>
            <tr>
                <td>Highest Score</td>
                <td><?php echo $metrics['highest_score']; ?></td>
                <td><?php echo $metrics['highest_score']; ?></td>
                <td><?php echo $metrics['highest_score']; ?></td>
                <td><?php echo $metrics['highest_score']; ?></td>
            </tr>
            <tr>
                <td>Lowest Score</td>
                <td><?php echo $metrics['lowest_score']; ?></td>
                <td><?php echo $metrics['lowest_score']; ?></td>
                <td><?php echo $metrics['lowest_score']; ?></td>
                <td><?php echo $metrics['lowest_score']; ?></td>
            </tr>
            <tr>
                <td>Average Mean</td>
                <td><?php echo $metrics['average_mean']; ?></td>
                <td><?php echo $metrics['average_mean']; ?></td>
                <td><?php echo $metrics['average_mean']; ?></td>
                <td><?php echo $metrics['average_mean']; ?></td>
            </tr>
            <tr>
                <td>MPS</td>
                <td><?php echo $metrics['mps']; ?></td>
                <td><?php echo $metrics['mps']; ?></td>
                <td><?php echo $metrics['mps']; ?></td>
                <td><?php echo $metrics['mps']; ?></td>
            </tr>
            <tr>
                <td>No. of Students Getting 75% PL</td>
                <td><?php echo $metrics['students_75_pl']; ?></td>
                <td><?php echo $metrics['students_75_pl']; ?></td>
                <td><?php echo $metrics['students_75_pl']; ?></td>
                <td><?php echo $metrics['students_75_pl']; ?></td>
            </tr>
            <tr>
                <td>Percentage of Students Getting 75% PL</td>
                <td><?php echo $metrics['percentage_75_pl'] . '%'; ?></td>
                <td><?php echo $metrics['percentage_75_pl'] . '%'; ?></td>
                <td><?php echo $metrics['percentage_75_pl'] . '%'; ?></td>
                <td><?php echo $metrics['percentage_75_pl'] . '%'; ?></td>
            </tr>
        </tbody>
    </table>
    <?php } else { ?>
        <p>No data available for the selected student.</p>
    <?php } ?>
</div>

<?php include('../crud/footer.php'); ?>
