<?php
require_once __DIR__ . '/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $newPassword = $_POST['new_password'];

    $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $update = $conn->prepare('UPDATE users SET password = ? WHERE email = ?');
        $update->execute([password_hash($newPassword, PASSWORD_DEFAULT), $email]);
        header('Location: index.php?message=' . urlencode('Password updated successfully. You can login now.'));
        exit();
    }

    $error = 'Email not found.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | University Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <section class="login-panel">
        <div class="login-card">
            <div class="login-brand">
                <img class="logo-mark" src="assets/logo.svg" alt="University Portal logo">
                <span>
                    <strong>University Portal</strong><br>
                    <small class="muted">Account recovery</small>
                </span>
            </div>
            <h1>Reset password</h1>
            <p class="subtitle">Enter your registered email and choose a new password for your portal account.</p>

            <?php if ($message) { ?><p class="success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p><?php } ?>
            <?php if ($error) { ?><p class="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p><?php } ?>

            <form method="POST">
                <div class="field">
                    <label>Email address</label>
                    <input type="email" name="email" required>
                </div>
                <div class="field">
                    <label>New password</label>
                    <input type="password" name="new_password" required>
                </div>
                <button type="submit">Change Password</button>
            </form>
            <div class="link-row">
                <a href="index.php">Back to login</a>
            </div>
        </div>
    </section>
    <section class="login-art">
        <div>
            <h2>Keep access to the academic systems you use every day.</h2>
            <p>Your updated password is stored securely and can be used immediately.</p>
        </div>
    </section>
</body>
</html>
