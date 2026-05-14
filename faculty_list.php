<?php
include "db.php";

$sql = "SELECT faculty.faculty_id, users.name, users.email, faculty.department
        FROM faculty
        INNER JOIN users ON faculty.user_id = users.user_id";

$stmt = $conn->prepare($sql);
$stmt->execute();
$facultyList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Faculty List</h1>
    <p class="subtitle">Faculty records from database</p>

    <table>
        <tr>
            <th>Faculty ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
        </tr>

        <?php foreach ($facultyList as $faculty) { ?>
        <tr>
            <td><?php echo $faculty['faculty_id']; ?></td>
            <td><?php echo $faculty['name']; ?></td>
            <td><?php echo $faculty['email']; ?></td>
            <td><?php echo $faculty['department']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <a class="btn" href="dashboard.php">Back</a>
</div>

</body>
</html>