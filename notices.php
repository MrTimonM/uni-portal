<?php
require_once __DIR__ . '/app.php';
require_login();

$message = flash('notice');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'admin') {
    $stmt = $conn->prepare('INSERT INTO notices(title, body, audience, priority) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        trim($_POST['title']),
        trim($_POST['body']),
        $_POST['audience'],
        $_POST['priority'],
    ]);
    cache_clear();
    set_flash('notice', 'Notice published successfully.');
    redirect('notices.php');
}

$notices = cached_fetch_all('notices', "SELECT * FROM notices WHERE audience IN ('all', ?) ORDER BY created_at DESC", [$_SESSION['role']]);
render_header('Campus Notices', 'notices.php');
?>

<?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>

<?php if ($_SESSION['role'] === 'admin') { ?>
<section class="panel">
    <div class="section-heading">
        <h2>Publish Notice</h2>
    </div>
    <form method="POST" class="form-grid">
        <div class="field">
            <label>Title</label>
            <input name="title" required>
        </div>
        <div class="field">
            <label>Audience</label>
            <select name="audience" required>
                <option value="all">All users</option>
                <option value="student">Students</option>
                <option value="faculty">Faculty</option>
                <option value="admin">Admins</option>
            </select>
        </div>
        <div class="field">
            <label>Priority</label>
            <select name="priority" required>
                <option value="normal">Normal</option>
                <option value="important">Important</option>
                <option value="urgent">Urgent</option>
            </select>
        </div>
        <div class="field full">
            <label>Message</label>
            <textarea name="body" required></textarea>
        </div>
        <button type="submit">Publish Notice</button>
    </form>
</section>
<?php } ?>

<section class="panel">
    <div class="notice-list">
        <?php foreach ($notices as $notice) { ?>
            <article class="notice-item">
                <h3><?php echo h($notice['title']); ?> <span class="badge <?php echo h($notice['priority']); ?>"><?php echo h($notice['priority']); ?></span></h3>
                <p><?php echo h($notice['body']); ?></p>
                <p class="muted"><?php echo h(ucfirst($notice['audience'])); ?> · <?php echo h(date('M d, Y', strtotime($notice['created_at']))); ?></p>
            </article>
        <?php } ?>
        <?php if (!$notices) { ?><div class="empty-state">No notices found.</div><?php } ?>
    </div>
</section>

<?php render_footer(); ?>
