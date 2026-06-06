<?php
require_once __DIR__ . '/app.php';

render_role_login(
    'faculty',
    'Teacher Portal',
    'Manage your assigned sections, submit attendance, publish results, and follow class-related notices.',
    'assets/images/teacher-login.jpg',
    'Use a faculty account created by admin',
    'teacher-login'
);
?>

