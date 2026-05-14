<?php
include "db.php";

$sql = "SELECT students.student_id, users.name, users.email, students.program, students.semester, students.cgpa
        FROM students
        INNER JOIN users ON students.user_id = users.user_id";

$stmt = $conn->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Student List</h1>
    <p class="subtitle">SELECT query output</p>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Program</th>
            <th>Semester</th>
            <th>CGPA</th>
            <th>Action</th>
        </tr>

        <?php foreach ($students as $student) { ?>
        <tr>
            <td><?php echo $student['student_id']; ?></td>
            <td><?php echo $student['name']; ?></td>
            <td><?php echo $student['email']; ?></td>
            <td><?php echo $student['program']; ?></td>
            <td><?php echo $student['semester']; ?></td>
            <td><?php echo $student['cgpa']; ?></td>
            <td>
                <a href="edit_student.php?id=<?php echo $student['student_id']; ?>">Edit</a> |
                <a href="delete_student.php?id=<?php echo $student['student_id']; ?>" onclick="return confirm('Delete this student?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <a class="btn" href="dashboard.php">Back</a>
</div>

</body>
</html>