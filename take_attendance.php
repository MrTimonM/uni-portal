<?php
session_start();
include "db.php";

$message = "";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT faculty_id FROM faculty WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$faculty) {
    die("Only faculty members can take attendance.");
}

$faculty_id = $faculty['faculty_id'];

$sql = "SELECT 
            sections.section_id,
            courses.course_code,
            courses.title,
            sections.section_name
        FROM sections
        INNER JOIN courses ON sections.course_id = courses.course_id
        WHERE sections.faculty_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$faculty_id]);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $section_id = $_POST['section_id'];
    $class_date = $_POST['class_date'];
    $status = $_POST['status'];

    $check = "SELECT section_id FROM sections 
              WHERE section_id = ? AND faculty_id = ?";
    $checkStmt = $conn->prepare($check);
    $checkStmt->execute([$section_id, $faculty_id]);

    if ($checkStmt->rowCount() > 0) {
        $sql = "INSERT INTO attendance(student_id, section_id, class_date, status)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$student_id, $section_id, $class_date, $status]);

        $message = "Attendance submitted successfully!";
    } else {
        $message = "You are not allowed to take attendance for this section.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Attendance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Take Attendance</h1>

    <p class="success"><?php echo $message; ?></p>

    <form method="POST">
        <input type="number" name="student_id" placeholder="Student ID" required>

        <select name="section_id" required>
            <option value="">Select Your Section</option>

            <?php foreach ($sections as $section) { ?>
                <option value="<?php echo $section['section_id']; ?>">
                    <?php echo $section['course_code'] . " - " . $section['title'] . " - Section " . $section['section_name']; ?>
                </option>
            <?php } ?>
        </select>

        <input type="date" name="class_date" required>

        <select name="status" required>
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
        </select>

        <button type="submit">Submit Attendance</button>
    </form>

    <a class="btn" href="dashboard.php">Back</a>
</div>

</body>
</html>