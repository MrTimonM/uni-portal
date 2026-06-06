<?php
require_once __DIR__ . '/app.php';
require_login(['student']);

$courses = cached_fetch_all('pre_advising', 'SELECT * FROM courses ORDER BY course_code');
$sections = cached_fetch_all('pre_advising', "SELECT sections.section_id, sections.section_name, sections.schedule, sections.capacity, sections.status, courses.course_code, courses.title, courses.credit, users.name AS faculty_name
    FROM sections
    INNER JOIN courses ON sections.course_id = courses.course_id
    LEFT JOIN faculty ON sections.faculty_id = faculty.faculty_id
    LEFT JOIN users ON faculty.user_id = users.user_id
    WHERE sections.status = 'Open'
    ORDER BY courses.course_code, sections.section_name");

render_header('Pre-Advising', 'pre_advising.php');
?>

<section class="panel">
    <div class="section-heading">
        <div>
            <h2>Open Sections</h2>
            <p class="subtitle">Review live section options before completing registration.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Course</th>
                <th>Credit</th>
                <th>Section</th>
                <th>Faculty</th>
                <th>Schedule</th>
                <th>Capacity</th>
                <th>Status</th>
            </tr>
            <?php foreach ($sections as $section) { ?>
                <tr>
                    <td><?php echo h($section['course_code'] . ' - ' . $section['title']); ?></td>
                    <td><?php echo h($section['credit']); ?></td>
                    <td><?php echo h($section['section_name']); ?></td>
                    <td><?php echo h($section['faculty_name'] ?: 'TBA'); ?></td>
                    <td><?php echo h($section['schedule']); ?></td>
                    <td><?php echo h($section['capacity']); ?></td>
                    <td><span class="badge success"><?php echo h($section['status']); ?></span></td>
                </tr>
            <?php } ?>
        </table>
        <?php if (!$sections) { ?><div class="empty-state">No open sections are available right now.</div><?php } ?>
    </div>
</section>

<section class="panel">
    <div class="section-heading">
        <h2>Course Catalog</h2>
    </div>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Course Code</th>
                <th>Title</th>
                <th>Credit</th>
            </tr>
            <?php foreach ($courses as $course) { ?>
                <tr>
                    <td><?php echo h($course['course_code']); ?></td>
                    <td><?php echo h($course['title']); ?></td>
                    <td><?php echo h($course['credit']); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</section>

<?php render_footer(); ?>
