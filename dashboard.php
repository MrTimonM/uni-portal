<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <h2>University Portal</h2>
    <a href="logout.php">Logout</a>
</div>

<div class="container">
    <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>
    <p class="subtitle">Role: <?php echo $_SESSION['role']; ?></p>

    <div class="grid">

        <?php if ($_SESSION['role'] == 'admin') { ?>

            <a class="card-link" href="add_student.php">Add Student</a>
            <a class="card-link" href="student_list.php">View Students</a>

            <a class="card-link" href="add_faculty.php">Add Faculty</a>
            <a class="card-link" href="faculty_list.php">View Faculty</a>

            <a class="card-link" href="add_course.php">Add Course</a>
            <a class="card-link" href="course_list.php">View Courses</a>

        <?php } ?>

        <?php if ($_SESSION['role'] == 'student') { ?>

            <a class="card-link" href="view_attendance.php">
            View Attendance
             </a>

            <a class="card-link" href="view_results.php">
             View Results
            </a>

            <a class="card-link" href="pre_advising.php">
             Pre-Advising
            </a>

            <a class="card-link" href="payment_status.php">
             Payment Status
            </a>

        <?php } ?>


        <?php if ($_SESSION['role'] == 'faculty') { ?>

            <a class="card-link" href="manage_sections.php">
             Manage Sections
            </a>

            <a class="card-link" href="take_attendance.php">
              Take Attendance
            </a>

            <a class="card-link" href="submit_results.php">
               Submit Results
            </a>

        <?php } ?>

    </div>
</div>

</body>
</html>