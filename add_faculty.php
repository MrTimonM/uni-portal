<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $department = $_POST['department'];

    $sql1 = "INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, 'faculty')";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute([$name, $email, $password]);

    $user_id = $conn->lastInsertId();

    $sql2 = "INSERT INTO faculty(user_id, department) VALUES (?, ?)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute([$user_id, $department]);

    $message = "Faculty added successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Faculty</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container small">
    <h1>Add Faculty</h1>

    <p class="success"><?php echo $message; ?></p>

    <form method="POST">
        <input type="text" name="name" placeholder="Faculty Name" required>
        <input type="email" name="email" placeholder="Faculty Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="department" placeholder="Department" required>

        <button type="submit">Add Faculty</button>
    </form>

    <a class="btn" href="dashboard.php">Back</a>
</div>

</body>
</html>