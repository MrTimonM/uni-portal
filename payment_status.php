<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT student_id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$student_id = $student['student_id'];

$sql = "SELECT payments.amount, payments.status, registrations.status AS reg_status
        FROM payments
        INNER JOIN registrations ON payments.reg_id = registrations.reg_id
        WHERE registrations.student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$student_id]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Status</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Payment Status</h1>

    <table>
        <tr>
            <th>Amount</th>
            <th>Payment Status</th>
            <th>Registration Status</th>
        </tr>

        <?php foreach ($payments as $row) { ?>
        <tr>
            <td><?php echo $row['amount']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo $row['reg_status']; ?></td>
        </tr>
        <?php } ?>

    </table>

    <a class="btn" href="dashboard.php">Back</a>
</div>

</body>
</html>