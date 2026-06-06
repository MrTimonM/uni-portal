<?php
require_once __DIR__ . '/app.php';
require_login(['student']);

$student = fetch_one('SELECT student_id FROM students WHERE user_id = ?', [$_SESSION['user_id']]);
$results = [];

if ($student) {
    $results = cached_fetch_all('view_results', 'SELECT courses.course_code, courses.title, results.grade, results.gpa
        FROM results
        INNER JOIN sections ON results.section_id = sections.section_id
        INNER JOIN courses ON sections.course_id = courses.course_id
        WHERE results.student_id = ?
        ORDER BY courses.course_code', [$student['student_id']]);
}

$totalGpa = 0;
foreach ($results as $row) {
    $totalGpa += (float) $row['gpa'];
}
$avgGpa = count($results) ? number_format($totalGpa / count($results), 2) : '0.00';

render_header('Academic Results', 'view_results.php');
?>

<section class="stats-grid">
    <div class="stat-card"><span>Published Results</span><strong><?php echo h(count($results)); ?></strong></div>
    <div class="stat-card"><span>Average GPA</span><strong><?php echo h($avgGpa); ?></strong></div>
    <div class="stat-card"><span>Best Grade</span><strong><?php echo h($results[0]['grade'] ?? '-'); ?></strong></div>
    <div class="stat-card"><span>Status</span><strong>Active</strong></div>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <tr>
                <th>Course</th>
                <th>Grade</th>
                <th>GPA</th>
            </tr>
            <?php foreach ($results as $row) { ?>
                <tr>
                    <td><?php echo h($row['course_code'] . ' - ' . $row['title']); ?></td>
                    <td><span class="badge success"><?php echo h($row['grade']); ?></span></td>
                    <td><?php echo h($row['gpa']); ?></td>
                </tr>
            <?php } ?>
        </table>
        <?php if (!$results) { ?><div class="empty-state">No results have been published yet.</div><?php } ?>
    </div>
</section>

<?php render_footer(); ?>
