<?php
require_once __DIR__ . '/app.php';

render_role_login(
    'student',
    'Student Portal',
    'Track advising, attendance, results, payments, library resources, events, and support requests.',
    'assets/images/student-login.jpg',
    'Use a student account created by admin',
    'student-login'
);
?>

