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
    die("Only faculty members can submit results.");
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
    $grade = $_POST['grade'];
    $gpa = $_POST['gpa'];

    $check = "SELECT section_id FROM sections 
              WHERE section_id = ? AND faculty_id = ?";
    $checkStmt = $conn->prepare($check);
    $checkStmt->execute([$section_id, $faculty_id]);

    if ($checkStmt->rowCount() > 0) {
        $sql = "INSERT INTO results(student_id, section_id, grade, gpa)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$student_id, $section_id, $grade, $gpa]);

        $message = "Result submitted successfully!";
    } else {
        $message = "You are not allowed to submit result for this section.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Submit Results</h1>

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

        <input type="text" name="grade" placeholder="Grade" required>

        <input type="text" name="gpa" placeholder="GPA" required>

        <button type="submit">Submit Result</button>
    </form>

    <a class="btn" href="dashboard.php">Back</a>
</div>

</body>
</html>