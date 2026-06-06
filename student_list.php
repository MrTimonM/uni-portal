<?php
require_once __DIR__ . '/app.php';
require_login(['admin']);

$students = cached_fetch_all('student_list', 'SELECT students.student_id, users.name, users.email, students.program, students.semester, students.cgpa
    FROM students
    INNER JOIN users ON students.user_id = users.user_id
    ORDER BY users.name');

render_header('Students', 'student_list.php');
?>

<section class="panel">
    <div class="section-heading">
        <div>
            <h2>Student Records</h2>
            <p class="subtitle"><?php echo h(count($students)); ?> active student profiles</p>
        </div>
        <a class="btn" href="add_student.php">Add Student</a>
    </div>
    <div class="table-wrap">
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Program</th>
                <th>Semester</th>
                <th>CGPA</th>
                <th>Action</th>
            </tr>
            <?php foreach ($students as $student) { ?>
                <tr>
                    <td><?php echo h($student['student_id']); ?></td>
                    <td><?php echo h($student['name']); ?></td>
                    <td><?php echo h($student['email']); ?></td>
                    <td><?php echo h($student['program']); ?></td>
                    <td><?php echo h($student['semester']); ?></td>
                    <td><?php echo h($student['cgpa']); ?></td>
                    <td class="actions">
                        <a href="edit_student.php?id=<?php echo h($student['student_id']); ?>">Edit</a>
                        <a href="delete_student.php?id=<?php echo h($student['student_id']); ?>" onclick="return confirm('Delete this student?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</section>

<?php render_footer(); ?>
