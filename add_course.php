<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_code = $_POST['course_code'];
    $title = $_POST['title'];
    $credit = $_POST['credit'];

    $sql = "INSERT INTO courses(course_code, title, credit) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$course_code, $title, $credit]);

    $message = "Course added successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container small">
    <h1>Add Course</h1>
    <p class="success"><?php echo $message; ?></p>

    <form method="POST">
        <input type="text" name="course_code" placeholder="Course Code" required>
        <input type="text" name="title" placeholder="Course Title" required>
        <input type="number" name="credit" placeholder="Credit" required>

        <button type="submit">Add Course</button>
    </form>

    <a class="btn" href="dashboard.php">Back</a>
</div>

</body>
</html>