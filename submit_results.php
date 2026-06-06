<?php
require_once __DIR__ . '/app.php';
require_login(['faculty']);

$message = '';
$faculty = fetch_one('SELECT faculty_id FROM faculty WHERE user_id = ?', [$_SESSION['user_id']]);

if (!$faculty) {
    redirect('dashboard.php');
}

$facultyId = $faculty['faculty_id'];
$sections = cached_fetch_all('submit_results', 'SELECT sections.section_id, courses.course_code, courses.title, sections.section_name
    FROM sections
    INNER JOIN courses ON sections.course_id = courses.course_id
    WHERE sections.faculty_id = ?
    ORDER BY courses.course_code', [$facultyId]);

$students = cached_fetch_all('submit_results', 'SELECT students.student_id, users.name, students.program
    FROM students
    INNER JOIN users ON students.user_id = users.user_id
    ORDER BY users.name');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowed = fetch_one('SELECT section_id FROM sections WHERE section_id = ? AND faculty_id = ?', [$_POST['section_id'], $facultyId]);

    if ($allowed) {
        $stmt = $conn->prepare('INSERT INTO results(student_id, section_id, grade, gpa) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_POST['student_id'], $_POST['section_id'], strtoupper(trim($_POST['grade'])), $_POST['gpa']]);
        cache_clear();
        $message = 'Result submitted successfully.';
    } else {
        $message = 'You are not allowed to submit result for this section.';
    }
}

render_header('Submit Results', 'submit_results.php');
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
            <label>Grade</label>
            <select name="grade" required>
                <option>A</option>
                <option>A-</option>
                <option>B+</option>
                <option>B</option>
                <option>C+</option>
                <option>C</option>
                <option>D</option>
                <option>F</option>
            </select>
        </div>
        <div class="field">
            <label>GPA</label>
            <input name="gpa" placeholder="4.00" required>
        </div>
        <button type="submit">Submit Result</button>
    </form>
</section>

<?php render_footer(); ?>
