<?php
require_once __DIR__ . '/app.php';
require_login(['admin']);

$id = (int) ($_GET['id'] ?? 0);
$student = fetch_one('SELECT students.student_id, users.user_id, users.name, users.email, students.program, students.semester, students.cgpa
    FROM students
    INNER JOIN users ON students.user_id = users.user_id
    WHERE students.student_id = ?', [$id]);

if (!$student) {
    redirect('student_list.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('UPDATE users SET name = ?, email = ? WHERE user_id = ?');
    $stmt->execute([trim($_POST['name']), trim($_POST['email']), $student['user_id']]);

    $stmt = $conn->prepare('UPDATE students SET program = ?, semester = ?, cgpa = ? WHERE student_id = ?');
    $stmt->execute([trim($_POST['program']), (int) $_POST['semester'], $_POST['cgpa'], $id]);
    cache_clear();

    redirect('student_list.php');
}

render_header('Edit Student', 'student_list.php');
?>

<section class="panel small">
    <form method="POST" class="form-grid">
        <div class="field full">
            <label>Name</label>
            <input name="name" value="<?php echo h($student['name']); ?>" required>
        </div>
        <div class="field full">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo h($student['email']); ?>" required>
        </div>
        <div class="field">
            <label>Program</label>
            <input name="program" value="<?php echo h($student['program']); ?>" required>
        </div>
        <div class="field">
            <label>Semester</label>
            <input type="number" name="semester" value="<?php echo h($student['semester']); ?>" required>
        </div>
        <div class="field">
            <label>CGPA</label>
            <input name="cgpa" value="<?php echo h($student['cgpa']); ?>" required>
        </div>
        <button type="submit">Update Student</button>
        <a class="btn secondary" href="student_list.php">Back</a>
    </form>
</section>

<?php render_footer(); ?>
