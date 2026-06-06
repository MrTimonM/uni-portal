<?php
require_once __DIR__ . '/app.php';
require_login();
redirect(role_dashboard_path());
?>
