<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $program = $_POST['program'];
    $semester = $_POST['semester'];
    $cgpa = $_POST['cgpa'];

    $sql1 = "INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, 'student')";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute([$name, $email, $password]);

    $user_id = $conn->lastInsertId();

    $sql2 = "INSERT INTO students(user_id, program, semester, cgpa) VALUES (?, ?, ?, ?)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute([$user_id, $program, $semester, $cgpa]);

    $message = "Student added successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container small">
    <h1>Add Student</h1>
    <p class="success"><?php echo $message; ?></p>

    <form method="POST">
        <input type="text" name="name" placeholder="Student Name" required>
        <input type="email" name="email" placeholder="Student Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="program" placeholder="Program" required>
        <input type="number" name="semester" placeholder="Semester" required>
        <input type="text" name="cgpa" placeholder="CGPA" required>

        <button type="submit">Add Student</button>
    </form>

    <a class="btn" href="dashboard.php">Back</a>
</div>

</body>
</html>