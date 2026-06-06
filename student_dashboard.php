<?php
require_once __DIR__ . '/app.php';
require_login(['student']);

$student = fetch_one('SELECT student_id, program, semester, cgpa FROM students WHERE user_id = ?', [$_SESSION['user_id']]);
$studentId = $student['student_id'] ?? 0;

$totalAttendance = cached_scalar_query('student_dashboard', 'SELECT COUNT(*) FROM attendance WHERE student_id = ?', [$studentId]);
$presentAttendance = cached_scalar_query('student_dashboard', "SELECT COUNT(*) FROM attendance WHERE student_id = ? AND status = 'Present'", [$studentId]);
$attendanceRate = $totalAttendance ? round(($presentAttendance / $totalAttendance) * 100) : 0;

$stats = [
    ['Program', $student['program'] ?? '-'],
    ['Semester', $student['semester'] ?? '-'],
    ['CGPA', $student['cgpa'] ?? '-'],
    ['Attendance', $attendanceRate . '%'],
];

$results = cached_fetch_all('student_dashboard', 'SELECT courses.course_code, courses.title, results.grade, results.gpa
    FROM results
    INNER JOIN sections ON results.section_id = sections.section_id
    INNER JOIN courses ON sections.course_id = courses.course_id
    WHERE results.student_id = ?
    ORDER BY results.result_id DESC
    LIMIT 5', [$studentId]);

$notices = cached_fetch_all('student_dashboard', "SELECT * FROM notices WHERE audience IN ('all', 'student') ORDER BY created_at DESC LIMIT 3");

render_header('Student Dashboard', 'student_dashboard.php');
?>

<section class="stats-grid">
    <?php foreach ($stats as $stat) { ?>
        <div class="stat-card">
            <span><?php echo h($stat[0]); ?></span>
            <strong><?php echo h($stat[1]); ?></strong>
        </div>
    <?php } ?>
</section>

<section class="panel">
    <div class="section-heading">
        <div>
            <h2>Student Workspace</h2>
            <p class="subtitle">Your academic records, advising, payments, results, attendance, notices, and support.</p>
        </div>
        <a class="btn secondary" href="profile.php">Profile</a>
    </div>
    <div class="grid">
        <a class="feature-card" href="pre_advising.php"><strong>Pre-Advising</strong><span>Review available courses and open sections.</span></a>
        <a class="feature-card" href="view_attendance.php"><strong>Attendance</strong><span>Track class-by-class attendance records.</span></a>
        <a class="feature-card" href="view_results.php"><strong>Results</strong><span>Review published grades and GPA.</span></a>
        <a class="feature-card" href="payment_status.php"><strong>Payments</strong><span>Check payment and registration clearance.</span></a>
        <a class="feature-card" href="library.php"><strong>Library</strong><span>Find books and academic resources.</span></a>
        <a class="feature-card" href="support.php"><strong>Support Desk</strong><span>Open and track university service requests.</span></a>
    </div>
</section>

<section class="split-grid">
    <div class="panel">
        <div class="section-heading">
            <h2>Recent Results</h2>
            <a href="view_results.php">View all</a>
        </div>
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
            <?php if (!$results) { ?><div class="empty-state">No results published yet.</div><?php } ?>
        </div>
    </div>
    <div class="panel">
        <div class="section-heading">
            <h2>Student Notices</h2>
            <a href="notices.php">View all</a>
        </div>
        <div class="notice-list">
            <?php foreach ($notices as $notice) { ?>
                <article class="notice-item">
                    <h3><?php echo h($notice['title']); ?></h3>
                    <p><?php echo h($notice['body']); ?></p>
                </article>
            <?php } ?>
            <?php if (!$notices) { ?><div class="empty-state">No student notices available.</div><?php } ?>
        </div>
    </div>
</section>

<?php render_footer(); ?>
