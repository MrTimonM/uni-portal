<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $update = "UPDATE users SET password = ? WHERE email = ?";
        $updateStmt = $conn->prepare($update);
        $updateStmt->execute([$new_password, $email]);

        $message = "Password updated successfully. You can login now.";
    } else {
        $message = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container small">
    <h1>Forgot Password</h1>

    <p class="success"><?php echo $message; ?></p>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter your registered email" required>

        <input type="password" name="new_password" placeholder="Enter new password" required>

        <button type="submit">Change Password</button>
    </form>

    <a class="btn" href="index.php">Back to Login</a>
</div>

</body>
</html>