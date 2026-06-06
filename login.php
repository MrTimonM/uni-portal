<?php
session_start();
require_once __DIR__ . '/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$expectedRole = $_POST['expected_role'] ?? '';
$allowedRoles = ['admin', 'faculty', 'student'];

if (!in_array($expectedRole, $allowedRoles, true)) {
    header('Location: index.php');
    exit();
}

$stmt = $conn->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

$valid = false;

if ($user) {
    $storedPassword = $user['password'] ?? '';
    $valid = password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);

    if ($valid && !password_get_info($storedPassword)['algo']) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare('UPDATE users SET password = ? WHERE user_id = ?');
        $update->execute([$hash, $user['user_id']]);
    }
}

if ($valid && $user['role'] !== $expectedRole) {
    $label = $expectedRole === 'faculty' ? 'teacher' : $expectedRole;
    header('Location: ' . role_login_path($expectedRole) . '?error=' . urlencode("This account is not a $label account."));
    exit();
}

if ($valid) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    header('Location: ' . role_dashboard_path($user['role']));
    exit();
}

header('Location: ' . role_login_path($expectedRole) . '?error=' . urlencode('Invalid email or password.'));
exit();
?>
