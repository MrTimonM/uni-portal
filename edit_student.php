<?php
include "db.php";

$id = $_GET['id'];

$sql = "SELECT students.student_id, users.user_id, users.name, users.email, students.program, students.semester, students.cgpa
        FROM students
        INNER JOIN users ON students.user_id = users.user_id
        WHERE students.student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $program = $_POST['program'];
    $semester = $_POST['semester'];
    $cgpa = $_POST['cgpa'];

    $sql1 = "UPDATE users SET name = ?, email = ? WHERE user_id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute([$name, $email, $student['user_id']]);

    $sql2 = "UPDATE students SET program = ?, semester = ?, cgpa = ? WHERE student_id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute([$program, $semester, $cgpa, $id]);

    header("Location: student_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container small">
    <h1>Edit Student</h1>

    <form method="POST">
        <input type="text" name="name" value="<?php echo $student['name']; ?>" required>
        <input type="email" name="email" value="<?php echo $student['email']; ?>" required>
        <input type="text" name="program" value="<?php echo $student['program']; ?>" required>
        <input type="number" name="semester" value="<?php echo $student['semester']; ?>" required>
        <input type="text" name="cgpa" value="<?php echo $student['cgpa']; ?>" required>

        <button type="submit">Update Student</button>
    </form>

    <a class="btn" href="student_list.php">Back</a>
</div>

</body>
</html>