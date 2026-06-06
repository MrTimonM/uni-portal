<?php
require_once __DIR__ . '/app.php';
require_login(['admin']);

$courses = cached_fetch_all('course_list', 'SELECT * FROM courses ORDER BY course_code');
render_header('Courses', 'course_list.php');
?>

<section class="panel">
    <div class="section-heading">
        <div>
            <h2>Course Catalog</h2>
            <p class="subtitle"><?php echo h(count($courses)); ?> courses available</p>
        </div>
        <a class="btn" href="add_course.php">Add Course</a>
    </div>
    <div class="table-wrap">
        <table>
            <tr>
                <th>ID</th>
                <th>Course Code</th>
                <th>Title</th>
                <th>Credit</th>
            </tr>
            <?php foreach ($courses as $course) { ?>
                <tr>
                    <td><?php echo h($course['course_id']); ?></td>
                    <td><?php echo h($course['course_code']); ?></td>
                    <td><?php echo h($course['title']); ?></td>
                    <td><?php echo h($course['credit']); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</section>

<?php render_footer(); ?>
