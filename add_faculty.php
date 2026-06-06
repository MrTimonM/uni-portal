<?php
require_once __DIR__ . '/app.php';
require_login(['admin']);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->beginTransaction();

    $stmt = $conn->prepare("INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, 'faculty')");
    $stmt->execute([
        trim($_POST['name']),
        trim($_POST['email']),
        password_hash($_POST['password'], PASSWORD_DEFAULT),
    ]);

    $userId = $conn->lastInsertId();
    $stmt = $conn->prepare('INSERT INTO faculty(user_id, department) VALUES (?, ?)');
    $stmt->execute([$userId, trim($_POST['department'])]);
    $conn->commit();
    cache_clear();

    $message = 'Faculty member added successfully.';
}

render_header('Add Faculty', 'faculty_list.php');
?>

<?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>

<section class="panel small">
    <form method="POST" class="form-grid">
        <div class="field full">
            <label>Faculty Name</label>
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
        <div class="field full">
            <label>Department</label>
            <input name="department" required>
        </div>
        <button type="submit">Add Faculty</button>
        <a class="btn secondary" href="faculty_list.php">Back</a>
    </form>
</section>

<?php render_footer(); ?>
