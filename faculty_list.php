<?php
require_once __DIR__ . '/app.php';
require_login(['admin']);

$facultyList = cached_fetch_all('faculty_list', 'SELECT faculty.faculty_id, users.name, users.email, faculty.department
    FROM faculty
    INNER JOIN users ON faculty.user_id = users.user_id
    ORDER BY users.name');

render_header('Faculty', 'faculty_list.php');
?>

<section class="panel">
    <div class="section-heading">
        <div>
            <h2>Faculty Directory</h2>
            <p class="subtitle"><?php echo h(count($facultyList)); ?> faculty profiles</p>
        </div>
        <a class="btn" href="add_faculty.php">Add Faculty</a>
    </div>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Faculty ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
            </tr>
            <?php foreach ($facultyList as $faculty) { ?>
                <tr>
                    <td><?php echo h($faculty['faculty_id']); ?></td>
                    <td><?php echo h($faculty['name']); ?></td>
                    <td><?php echo h($faculty['email']); ?></td>
                    <td><?php echo h($faculty['department']); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</section>

<?php render_footer(); ?>
