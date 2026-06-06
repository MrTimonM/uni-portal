<?php
require_once __DIR__ . '/app.php';
require_login(['admin']);

$stats = [
    ['Students', cached_scalar_query('admin_dashboard', 'SELECT COUNT(*) FROM students')],
    ['Teachers', cached_scalar_query('admin_dashboard', 'SELECT COUNT(*) FROM faculty')],
    ['Courses', cached_scalar_query('admin_dashboard', 'SELECT COUNT(*) FROM courses')],
    ['Open Tickets', cached_scalar_query('admin_dashboard', "SELECT COUNT(*) FROM support_tickets WHERE status <> 'Resolved'")],
];

$notices = cached_fetch_all('admin_dashboard', "SELECT * FROM notices WHERE audience IN ('all', 'admin') ORDER BY created_at DESC LIMIT 3");
$events = cached_fetch_all('admin_dashboard', 'SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date, event_time LIMIT 3');

render_header('Admin Dashboard', 'admin_dashboard.php');
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
            <h2>Administrative Control Center</h2>
            <p class="subtitle">Operate the institution: records, staffing, sections, notices, resources, and service requests.</p>
        </div>
        <a class="btn secondary" href="profile.php">Profile</a>
    </div>
    <div class="grid">
        <a class="feature-card" href="student_list.php"><strong>Student Records</strong><span>Admit, edit, and review student academic profiles.</span></a>
        <a class="feature-card" href="faculty_list.php"><strong>Teacher Directory</strong><span>Create faculty accounts and department assignments.</span></a>
        <a class="feature-card" href="course_list.php"><strong>Course Catalog</strong><span>Manage course codes, titles, and credit load.</span></a>
        <a class="feature-card" href="admin_sections.php"><strong>Section Planning</strong><span>Assign teachers, schedules, capacity, and status.</span></a>
        <a class="feature-card" href="notices.php"><strong>Campus Notices</strong><span>Publish role-aware announcements across the portal.</span></a>
        <a class="feature-card" href="support.php"><strong>Support Desk</strong><span>Review and resolve student and teacher requests.</span></a>
        <a class="feature-card" href="events.php"><strong>Academic Calendar</strong><span>Maintain campus events and important academic dates.</span></a>
        <a class="feature-card" href="library.php"><strong>Library Catalog</strong><span>Add books and monitor resource availability.</span></a>
        <a class="feature-card" href="cache_status.php"><strong>Cache Status</strong><span>Review cached data and clear it manually when needed.</span></a>
    </div>
</section>

<section class="split-grid">
    <div class="panel">
        <div class="section-heading">
            <h2>Admin Notices</h2>
            <a href="notices.php">View all</a>
        </div>
        <div class="notice-list">
            <?php foreach ($notices as $notice) { ?>
                <article class="notice-item">
                    <h3><?php echo h($notice['title']); ?> <span class="badge <?php echo h($notice['priority']); ?>"><?php echo h($notice['priority']); ?></span></h3>
                    <p><?php echo h($notice['body']); ?></p>
                </article>
            <?php } ?>
            <?php if (!$notices) { ?><div class="empty-state">No admin notices available.</div><?php } ?>
        </div>
    </div>
    <div class="panel">
        <div class="section-heading">
            <h2>Upcoming Events</h2>
            <a href="events.php">View all</a>
        </div>
        <div class="event-list">
            <?php foreach ($events as $event) { ?>
                <article class="event-item">
                    <h3><?php echo h($event['title']); ?></h3>
                    <p><?php echo h(date('M d, Y', strtotime($event['event_date']))); ?> at <?php echo h(substr((string) $event['event_time'], 0, 5)); ?></p>
                    <p><?php echo h($event['location']); ?> - <?php echo h($event['category']); ?></p>
                </article>
            <?php } ?>
            <?php if (!$events) { ?><div class="empty-state">No upcoming events scheduled.</div><?php } ?>
        </div>
    </div>
</section>

<?php render_footer(); ?>
