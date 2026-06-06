<?php
require_once __DIR__ . '/app.php';

if (isset($_SESSION['user_id'])) {
    redirect(role_dashboard_path());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Portal | University Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="portal-select-body">
    <main class="portal-select">
        <div class="login-brand">
            <img class="logo-mark" src="assets/logo.svg" alt="University Portal logo">
            <span>
                <strong>University Portal</strong><br>
                <small class="muted">Choose your workspace</small>
            </span>
        </div>

        <section class="portal-hero">
            <p class="eyebrow">Role based access</p>
            <h1>Different portals for different academic work.</h1>
            <p>Admins manage operations, teachers manage classes, and students manage their academic life from focused dashboards.</p>
        </section>

        <section class="role-grid">
            <a class="role-card admin-card" href="admin_login.php">
                <span class="role-kicker">Operations</span>
                <strong>Admin Portal</strong>
                <span>Students, faculty, courses, sections, notices, events, library, and support desk.</span>
            </a>
            <a class="role-card teacher-card" href="teacher_login.php">
                <span class="role-kicker">Teaching</span>
                <strong>Teacher Portal</strong>
                <span>Assigned sections, attendance entry, result submission, notices, and class support.</span>
            </a>
            <a class="role-card student-card" href="student_login.php">
                <span class="role-kicker">Academic life</span>
                <strong>Student Portal</strong>
                <span>Advising, attendance, results, payments, events, library, and support requests.</span>
            </a>
        </section>
    </main>
</body>
</html>
