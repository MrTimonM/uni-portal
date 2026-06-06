<?php
require_once __DIR__ . '/app.php';
require_login(['faculty']);

$faculty = fetch_one('SELECT faculty_id FROM faculty WHERE user_id = ?', [$_SESSION['user_id']]);
$facultyId = $faculty['faculty_id'] ?? 0;
$sections = cached_fetch_all('teacher_dashboard', 'SELECT sections.section_id, sections.section_name, sections.schedule, sections.capacity, sections.status, courses.course_code, courses.title
    FROM sections
    INNER JOIN courses ON sections.course_id = courses.course_id
    WHERE sections.faculty_id = ?
    ORDER BY courses.course_code, sections.section_name
    LIMIT 5', [$facultyId]);

$stats = [
    ['Assigned Sections', cached_scalar_query('teacher_dashboard', 'SELECT COUNT(*) FROM sections WHERE faculty_id = ?', [$facultyId])],
    ['Attendance Entries', cached_scalar_query('teacher_dashboard', 'SELECT COUNT(*) FROM attendance INNER JOIN sections ON attendance.section_id = sections.section_id WHERE sections.faculty_id = ?', [$facultyId])],
    ['Results Submitted', cached_scalar_query('teacher_dashboard', 'SELECT COUNT(*) FROM results INNER JOIN sections ON results.section_id = sections.section_id WHERE sections.faculty_id = ?', [$facultyId])],
    ['Open Tickets', cached_scalar_query('teacher_dashboard', "SELECT COUNT(*) FROM support_tickets WHERE user_id = ? AND status <> 'Resolved'", [$_SESSION['user_id']])],
];

$notices = cached_fetch_all('teacher_dashboard', "SELECT * FROM notices WHERE audience IN ('all', 'faculty') ORDER BY created_at DESC LIMIT 3");

render_header('Teacher Dashboard', 'teacher_dashboard.php');
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
            <h2>Teaching Workspace</h2>
            <p class="subtitle">Class operations for attendance, results, schedules, notices, and support.</p>
        </div>
        <a class="btn secondary" href="profile.php">Profile</a>
    </div>
    <div class="grid">
        <a class="feature-card" href="manage_sections.php"><strong>My Sections</strong><span>Review assigned classes, capacity, and schedule.</span></a>
        <a class="feature-card" href="take_attendance.php"><strong>Take Attendance</strong><span>Record present and absent status for students.</span></a>
        <a class="feature-card" href="submit_results.php"><strong>Submit Results</strong><span>Publish grades and GPA for assigned sections.</span></a>
        <a class="feature-card" href="notices.php"><strong>Teacher Notices</strong><span>Read academic and administrative announcements.</span></a>
        <a class="feature-card" href="library.php"><strong>Library Search</strong><span>Find teaching resources and available books.</span></a>
        <a class="feature-card" href="support.php"><strong>Support Desk</strong><span>Open and track requests with administration.</span></a>
    </div>
</section>

<section class="split-grid">
    <div class="panel">
        <div class="section-heading">
            <h2>Assigned Sections</h2>
            <a href="manage_sections.php">View all</a>
        </div>
        <div class="table-wrap">
            <table>
                <tr>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Schedule</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($sections as $section) { ?>
                    <tr>
                        <td><?php echo h($section['course_code'] . ' - ' . $section['title']); ?></td>
                        <td><?php echo h($section['section_name']); ?></td>
                        <td><?php echo h($section['schedule']); ?></td>
                        <td><span class="badge <?php echo h($section['status']); ?>"><?php echo h($section['status']); ?></span></td>
                    </tr>
                <?php } ?>
            </table>
            <?php if (!$sections) { ?><div class="empty-state">No sections are assigned yet.</div><?php } ?>
        </div>
    </div>
    <div class="panel">
        <div class="section-heading">
            <h2>Teacher Notices</h2>
            <a href="notices.php">View all</a>
        </div>
        <div class="notice-list">
            <?php foreach ($notices as $notice) { ?>
                <article class="notice-item">
                    <h3><?php echo h($notice['title']); ?></h3>
                    <p><?php echo h($notice['body']); ?></p>
                </article>
            <?php } ?>
            <?php if (!$notices) { ?><div class="empty-state">No teacher notices available.</div><?php } ?>
        </div>
    </div>
</section>

<?php render_footer(); ?>
