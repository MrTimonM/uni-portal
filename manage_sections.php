<?php
require_once __DIR__ . '/app.php';
require_login(['faculty']);

$faculty = fetch_one('SELECT faculty_id FROM faculty WHERE user_id = ?', [$_SESSION['user_id']]);
$sections = [];

if ($faculty) {
    $sections = cached_fetch_all('manage_sections', 'SELECT courses.title, courses.course_code, sections.section_name, sections.schedule, sections.capacity, sections.status
        FROM sections
        INNER JOIN courses ON sections.course_id = courses.course_id
        WHERE sections.faculty_id = ?
        ORDER BY courses.course_code, sections.section_name', [$faculty['faculty_id']]);
}

render_header('My Sections', 'manage_sections.php');
?>

<section class="panel">
    <div class="section-heading">
        <div>
            <h2>Assigned Sections</h2>
            <p class="subtitle"><?php echo h(count($sections)); ?> sections assigned to you</p>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Course Code</th>
                <th>Course</th>
                <th>Section</th>
                <th>Schedule</th>
                <th>Capacity</th>
                <th>Status</th>
            </tr>
            <?php foreach ($sections as $row) { ?>
                <tr>
                    <td><?php echo h($row['course_code']); ?></td>
                    <td><?php echo h($row['title']); ?></td>
                    <td><?php echo h($row['section_name']); ?></td>
                    <td><?php echo h($row['schedule']); ?></td>
                    <td><?php echo h($row['capacity']); ?></td>
                    <td><span class="badge <?php echo h($row['status']); ?>"><?php echo h($row['status']); ?></span></td>
                </tr>
            <?php } ?>
        </table>
        <?php if (!$sections) { ?><div class="empty-state">No sections are assigned yet.</div><?php } ?>
    </div>
</section>

<?php render_footer(); ?>
