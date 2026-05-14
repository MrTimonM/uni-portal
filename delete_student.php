<?php
include "db.php";

$id = $_GET['id'];

$sql = "SELECT user_id FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($student) {
    $sql1 = "DELETE FROM students WHERE student_id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute([$id]);

    $sql2 = "DELETE FROM users WHERE user_id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute([$student['user_id']]);
}

header("Location: student_list.php");
exit();
?>