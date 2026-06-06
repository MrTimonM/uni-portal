<?php
require_once __DIR__ . '/app.php';
require_login(['admin']);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->beginTransaction();

    $stmt = $conn->prepare("INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, 'student')");
    $stmt->execute([
        trim($_POST['name']),
        trim($_POST['email']),
        password_hash($_POST['password'], PASSWORD_DEFAULT),
    ]);

    $userId = $conn->lastInsertId();
    $stmt = $conn->prepare('INSERT INTO students(user_id, program, semester, cgpa) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, trim($_POST['program']), (int) $_POST['semester'], $_POST['cgpa']]);
    $conn->commit();
    cache_clear();

    $message = 'Student added successfully.';
}

render_header('Add Student', 'student_list.php');
?>

<?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>

<section class="panel small">
    <form method="POST" class="form-grid">
        <div class="field full">
            <label>Student Name</label>
            <input name="name" required>
        </div>
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="field">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="field">
            <label>Program</label>
            <input name="program" required>
        </div>
        <div class="field">
            <label>Semester</label>
            <input type="number" name="semester" min="1" required>
        </div>
        <div class="field">
            <label>CGPA</label>
            <input name="cgpa" required>
        </div>
        <button type="submit">Add Student</button>
        <a class="btn secondary" href="student_list.php">Back</a>
    </form>
</section>

<?php render_footer(); ?>
