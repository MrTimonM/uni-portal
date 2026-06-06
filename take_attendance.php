<?php
require_once __DIR__ . '/app.php';
require_login(['faculty']);

$message = '';
$faculty = fetch_one('SELECT faculty_id FROM faculty WHERE user_id = ?', [$_SESSION['user_id']]);

if (!$faculty) {
    redirect('dashboard.php');
}

$facultyId = $faculty['faculty_id'];
$sections = cached_fetch_all('take_attendance', 'SELECT sections.section_id, courses.course_code, courses.title, sections.section_name
    FROM sections
    INNER JOIN courses ON sections.course_id = courses.course_id
    WHERE sections.faculty_id = ?
    ORDER BY courses.course_code', [$facultyId]);

$students = cached_fetch_all('take_attendance', 'SELECT students.student_id, users.name, students.program
    FROM students
    INNER JOIN users ON students.user_id = users.user_id
    ORDER BY users.name');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowed = fetch_one('SELECT section_id FROM sections WHERE section_id = ? AND faculty_id = ?', [$_POST['section_id'], $facultyId]);

    if ($allowed) {
        $stmt = $conn->prepare('INSERT INTO attendance(student_id, section_id, class_date, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_POST['student_id'], $_POST['section_id'], $_POST['class_date'], $_POST['status']]);
        cache_clear();
        $message = 'Attendance submitted successfully.';
    } else {
        $message = 'You are not allowed to take attendance for this section.';
    }
}

render_header('Take Attendance', 'take_attendance.php');
?>

<?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>

<section class="panel small">
    <form method="POST" class="form-grid">
        <div class="field full">
            <label>Student</label>
            <select name="student_id" required>
                <option value="">Select student</option>
                <?php foreach ($students as $student) { ?>
                    <option value="<?php echo h($student['student_id']); ?>"><?php echo h($student['student_id'] . ' - ' . $student['name'] . ' - ' . $student['program']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="field full">
            <label>Section</label>
            <select name="section_id" required>
                <option value="">Select your section</option>
                <?php foreach ($sections as $section) { ?>
                    <option value="<?php echo h($section['section_id']); ?>"><?php echo h($section['course_code'] . ' - ' . $section['title'] . ' - Section ' . $section['section_name']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="field">
            <label>Class Date</label>
            <input type="date" name="class_date" value="<?php echo h(date('Y-m-d')); ?>" required>
        </div>
        <div class="field">
            <label>Status</label>
            <select name="status" required>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
            </select>
        </div>
        <button type="submit">Submit Attendance</button>
    </form>
</section>

<?php render_footer(); ?>
