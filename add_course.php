<?php
require_once __DIR__ . '/app.php';
require_login(['admin']);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('INSERT INTO courses(course_code, title, credit) VALUES (?, ?, ?)');
    $stmt->execute([strtoupper(trim($_POST['course_code'])), trim($_POST['title']), (int) $_POST['credit']]);
    cache_clear();
    $message = 'Course added successfully.';
}

render_header('Add Course', 'course_list.php');
?>

<?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>

<section class="panel small">
    <form method="POST" class="form-grid">
        <div class="field">
            <label>Course Code</label>
            <input name="course_code" placeholder="CSE101" required>
        </div>
        <div class="field">
            <label>Credit</label>
            <input type="number" name="credit" min="1" max="6" required>
        </div>
        <div class="field full">
            <label>Course Title</label>
            <input name="title" required>
        </div>
        <button type="submit">Add Course</button>
        <a class="btn secondary" href="course_list.php">Back</a>
    </form>
</section>

<?php render_footer(); ?>
