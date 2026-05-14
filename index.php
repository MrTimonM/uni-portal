<!DOCTYPE html>
<html>
<head>
    <title>University Student Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container small">
    <h1>University Portal</h1>
   

    <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
    </form>
    <a class="forgot-link" href="forgot_password.php">Forgot Password?</a>

    <p class="hint">Demo: admin@gmail.com / 12345</p>
</div>

</body>
</html>