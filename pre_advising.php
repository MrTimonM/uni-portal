<?php
session_start();
include "db.php";

$sql = "SELECT * FROM courses";
$stmt = $conn->prepare($sql);
$stmt->execute();

$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pre-Advising</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Pre-Advising</h1>

    <table>

        <tr>
            <th>Course Code</th>
            <th>Title</th>
            <th>Credit</th>
        </tr>

        <?php foreach($courses as $course) { ?>

        <tr>
            <td><?php echo $course['course_code']; ?></td>
            <td><?php echo $course['title']; ?></td>
            <td><?php echo $course['credit']; ?></td>
        </tr>

        <?php } ?>

    </table>

    <a class="btn" href="dashboard.php">Back</a>

</div>

</body>
</html>