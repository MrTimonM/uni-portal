<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT faculty_id FROM faculty WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

$faculty_id = $faculty['faculty_id'];

$sql = "SELECT 
            courses.title,
            courses.course_code,
            sections.section_name,
            sections.schedule,
            sections.capacity,
            sections.status
        FROM sections
        INNER JOIN courses ON sections.course_id = courses.course_id
        WHERE sections.faculty_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$faculty_id]);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Sections</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Manage Sections</h1>

    <table>
        <tr>
            <th>Course Code</th>
            <th>Course</th>
            <th>Section</th>
            <th>Schedule</th>
            <th>Capacity</th>
            <th>Status</th>
        </tr>

        <?php foreach ($sections as $row) { ?>
        <tr>
            <td><?php echo $row['course_code']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['section_name']; ?></td>
            <td><?php echo $row['schedule']; ?></td>
            <td><?php echo $row['capacity']; ?></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
        <?php } ?>

    </table>

    <a class="btn" href="dashboard.php">Back</a>

</div>

</body>
</html>