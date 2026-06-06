<?php
require_once __DIR__ . '/app.php';
require_login();

$message = flash('profile');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    $user = fetch_one('SELECT * FROM users WHERE user_id = ?', [$_SESSION['user_id']]);

    if ($newPassword !== '') {
        $validPassword = password_verify($currentPassword, $user['password']) || hash_equals($user['password'], $currentPassword);
        if (!$validPassword) {
            $error = 'Current password is incorrect.';
        }
    }

    if (!$error) {
        if ($newPassword !== '') {
            $stmt = $conn->prepare('UPDATE users SET name = ?, email = ?, password = ? WHERE user_id = ?');
            $stmt->execute([$name, $email, password_hash($newPassword, PASSWORD_DEFAULT), $_SESSION['user_id']]);
        } else {
            $stmt = $conn->prepare('UPDATE users SET name = ?, email = ? WHERE user_id = ?');
            $stmt->execute([$name, $email, $_SESSION['user_id']]);
        }

        $_SESSION['name'] = $name;
        set_flash('profile', 'Profile updated successfully.');
        redirect('profile.php');
    }
}

$user = fetch_one('SELECT * FROM users WHERE user_id = ?', [$_SESSION['user_id']]);
render_header('My Profile', 'profile.php');
?>

<?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>
<?php if ($error) { ?><p class="alert"><?php echo h($error); ?></p><?php } ?>

<section class="panel">
    <form method="POST" class="form-grid">
        <div class="field">
            <label>Name</label>
            <input name="name" value="<?php echo h($user['name']); ?>" required>
        </div>
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo h($user['email']); ?>" required>
        </div>
        <div class="field">
            <label>Role</label>
            <input value="<?php echo h(ucfirst($user['role'])); ?>" disabled>
        </div>
        <div class="field">
            <label>Current Password</label>
            <input type="password" name="current_password" autocomplete="current-password">
        </div>
        <div class="field">
            <label>New Password</label>
            <input type="password" name="new_password" autocomplete="new-password">
        </div>
        <button type="submit">Save Profile</button>
    </form>
</section>

<?php render_footer(); ?>
