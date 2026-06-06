<?php
require_once __DIR__ . '/app.php';
require_login(['student']);

$student = fetch_one('SELECT student_id FROM students WHERE user_id = ?', [$_SESSION['user_id']]);
$attendance = [];

if ($student) {
    $attendance = cached_fetch_all('view_attendance', 'SELECT courses.title, attendance.class_date, attendance.status
        FROM attendance
        INNER JOIN sections ON attendance.section_id = sections.section_id
        INNER JOIN courses ON sections.course_id = courses.course_id
        WHERE attendance.student_id = ?
        ORDER BY attendance.class_date DESC', [$student['student_id']]);
}

$present = 0;
foreach ($attendance as $row) {
    if ($row['status'] === 'Present') {
        $present++;
    }
}
$rate = count($attendance) ? round(($present / count($attendance)) * 100) : 0;

render_header('Attendance Report', 'view_attendance.php');
?>

<section class="stats-grid">
    <div class="stat-card"><span>Total Classes</span><strong><?php echo h(count($attendance)); ?></strong></div>
    <div class="stat-card"><span>Present</span><strong><?php echo h($present); ?></strong></div>
    <div class="stat-card"><span>Attendance Rate</span><strong><?php echo h($rate); ?>%</strong></div>
    <div class="stat-card"><span>Absent</span><strong><?php echo h(count($attendance) - $present); ?></strong></div>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <tr>
                <th>Course</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
            <?php foreach ($attendance as $row) { ?>
                <tr>
                    <td><?php echo h($row['title']); ?></td>
                    <td><?php echo h($row['class_date']); ?></td>
                    <td><span class="badge <?php echo h($row['status']); ?>"><?php echo h($row['status']); ?></span></td>
                </tr>
            <?php } ?>
        </table>
        <?php if (!$attendance) { ?><div class="empty-state">No attendance records found.</div><?php } ?>
    </div>
</section>

<?php render_footer(); ?>
