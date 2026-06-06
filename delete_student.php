<?php
require_once __DIR__ . '/app.php';
require_login(['admin']);

$id = (int) ($_GET['id'] ?? 0);
$student = fetch_one('SELECT user_id FROM students WHERE student_id = ?', [$id]);

if ($student) {
    $conn->beginTransaction();
    $stmt = $conn->prepare('DELETE FROM students WHERE student_id = ?');
    $stmt->execute([$id]);
    $stmt = $conn->prepare('DELETE FROM users WHERE user_id = ?');
    $stmt->execute([$student['user_id']]);
    $conn->commit();
    cache_clear();
}

redirect('student_list.php');
?>
