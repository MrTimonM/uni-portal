<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT student_id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$student_id = $student['student_id'];

$sql = "SELECT courses.title, attendance.class_date, attendance.status
        FROM attendance
        INNER JOIN sections ON attendance.section_id = sections.section_id
        INNER JOIN courses ON sections.course_id = courses.course_id
        WHERE attendance.student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$student_id]);
$attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Attendance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Attendance Report</h1>

    <table>
        <tr>
            <th>Course</th>
            <th>Date</th>
            <th>Status</th>
        </tr>

        <?php foreach ($attendance as $row) { ?>
        <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['class_date']; ?></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
        <?php } ?>

    </table>

    <a class="btn" href="dashboard.php">Back</a>
</div>

</body>
</html>