<?php
require_once __DIR__ . '/app.php';
require_login(['admin']);

$message = flash('section');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('INSERT INTO sections(course_id, faculty_id, section_name, schedule, capacity, status) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $_POST['course_id'],
        $_POST['faculty_id'],
        trim($_POST['section_name']),
        trim($_POST['schedule']),
        (int) $_POST['capacity'],
        $_POST['status'],
    ]);
    cache_clear();
    set_flash('section', 'Section created successfully.');
    redirect('admin_sections.php');
}

$courses = cached_fetch_all('admin_sections', 'SELECT course_id, course_code, title FROM courses ORDER BY course_code');
$faculty = cached_fetch_all('admin_sections', 'SELECT faculty.faculty_id, users.name, faculty.department FROM faculty INNER JOIN users ON faculty.user_id = users.user_id ORDER BY users.name');
$sections = cached_fetch_all('admin_sections', 'SELECT sections.*, courses.course_code, courses.title, users.name AS faculty_name
    FROM sections
    INNER JOIN courses ON sections.course_id = courses.course_id
    INNER JOIN faculty ON sections.faculty_id = faculty.faculty_id
    INNER JOIN users ON faculty.user_id = users.user_id
    ORDER BY courses.course_code, sections.section_name');

render_header('Section Planning', 'admin_sections.php');
?>

<?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>

<section class="panel">
    <div class="section-heading">
        <h2>Create Section</h2>
    </div>
    <form method="POST" class="form-grid">
        <div class="field">
            <label>Course</label>
            <select name="course_id" required>
                <option value="">Select course</option>
                <?php foreach ($courses as $course) { ?>
                    <option value="<?php echo h($course['course_id']); ?>"><?php echo h($course['course_code'] . ' - ' . $course['title']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="field">
            <label>Faculty</label>
            <select name="faculty_id" required>
                <option value="">Select faculty</option>
                <?php foreach ($faculty as $person) { ?>
                    <option value="<?php echo h($person['faculty_id']); ?>"><?php echo h($person['name'] . ' - ' . $person['department']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="field">
            <label>Section</label>
            <input name="section_name" placeholder="A" required>
        </div>
        <div class="field">
            <label>Schedule</label>
            <input name="schedule" placeholder="Sun Tue 10:00-11:30" required>
        </div>
        <div class="field">
            <label>Capacity</label>
            <input type="number" name="capacity" min="1" value="40" required>
        </div>
        <div class="field">
            <label>Status</label>
            <select name="status" required>
                <option>Open</option>
                <option>Closed</option>
                <option>Planned</option>
            </select>
        </div>
        <button type="submit">Create Section</button>
    </form>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <tr>
                <th>Course</th>
                <th>Section</th>
                <th>Faculty</th>
                <th>Schedule</th>
                <th>Capacity</th>
                <th>Status</th>
            </tr>
            <?php foreach ($sections as $section) { ?>
                <tr>
                    <td><?php echo h($section['course_code'] . ' - ' . $section['title']); ?></td>
                    <td><?php echo h($section['section_name']); ?></td>
                    <td><?php echo h($section['faculty_name']); ?></td>
                    <td><?php echo h($section['schedule']); ?></td>
                    <td><?php echo h($section['capacity']); ?></td>
                    <td><span class="badge <?php echo h($section['status']); ?>"><?php echo h($section['status']); ?></span></td>
                </tr>
            <?php } ?>
        </table>
        <?php if (!$sections) { ?><div class="empty-state">No sections have been created yet.</div><?php } ?>
    </div>
</section>

<?php render_footer(); ?>
