<?php
include("../LoginRegisterAuthentication/connection.php");

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch students and their grades from the database
$query = "
    SELECT s.id AS student_id, s.firstname, s.lastname, g.grade, g.date_awarded, sub.name AS subject
    FROM students s
    LEFT JOIN grades g ON s.id = g.student_id
    LEFT JOIN subjects sub ON g.subject_id = sub.id
";
$result = mysqli_query($connection, $query);
$students = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Function to calculate letter grade
function calculateGrade($score) {
    if ($score >= 90) return 'A';
    if ($score >= 80) return 'B';
    if ($score >= 70) return 'C';
    if ($score >= 60) return 'D';
    return 'F';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handling grade submission
    $student_id = $_POST['student_id'];
    $grade = $_POST['grade'];
    
    $update_query = "UPDATE grades SET grade = $grade WHERE student_id = $student_id";
    mysqli_query($connection, $update_query);

    header("Location: grades.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grading System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        h2 {
            text-align: center;
        }

        form {
            display: flex;
            justify-content: space-between;
        }

        input[type="number"], select {
            padding: 5px;
            margin-right: 10px;
            flex: 1;
        }

        input[type="submit"] {
            padding: 5px 10px;
        }
    </style>
</head>
<a href="../home/homepage.php" class="btn btn-secondary">Back to Home</a>
   
<body>
    
    <div class="container">
        <h2>Grading System</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Subject</th>
                    <th>Grade</th>
                    <th>Date Awarded</th>
                    <th>Letter Grade</th>
                    <th>Update Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $student['student_id']; ?></td>
                    <td><?= $student['firstname'] . ' ' . $student['lastname']; ?></td>
                    <td><?= $student['subject']; ?></td>
                    <td><?= $student['grade']; ?></td>
                    <td><?= $student['date_awarded']; ?></td>
                    <td><?= calculateGrade($student['grade']); ?></td>
                    <td>
                        <form action="grades.php" method="POST">
                            <input type="hidden" name="student_id" value="<?= $student['student_id']; ?>">
                            <input type="number" name="grade" min="0" max="100" value="<?= $student['grade']; ?>" required>
                            <input type="submit" value="Update">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
