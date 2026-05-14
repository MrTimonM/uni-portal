<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT student_id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$student_id = $student['student_id'];

$sql = "SELECT courses.title, results.grade, results.gpa
        FROM results
        INNER JOIN sections ON results.section_id = sections.section_id
        INNER JOIN courses ON sections.course_id = courses.course_id
        WHERE results.student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$student_id]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Academic Results</h1>

    <table>
        <tr>
            <th>Course</th>
            <th>Grade</th>
            <th>GPA</th>
        </tr>

        <?php foreach ($results as $row) { ?>
        <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['grade']; ?></td>
            <td><?php echo $row['gpa']; ?></td>
        </tr>
        <?php } ?>

    </table>

    <a class="btn" href="dashboard.php">Back</a>
</div>

</body>
</html>