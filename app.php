<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

define('CACHE_DIR', __DIR__ . '/cache');
define('CACHE_DEFAULT_TTL', 60);

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect($path)
{
    header("Location: $path");
    exit();
}

function role_dashboard_path($role = null)
{
    $role = $role ?? ($_SESSION['role'] ?? '');

    $paths = [
        'admin' => 'admin_dashboard.php',
        'faculty' => 'teacher_dashboard.php',
        'student' => 'student_dashboard.php',
    ];

    return $paths[$role] ?? 'index.php';
}

function role_login_path($role)
{
    $paths = [
        'admin' => 'admin_login.php',
        'faculty' => 'teacher_login.php',
        'student' => 'student_login.php',
    ];

    return $paths[$role] ?? 'index.php';
}

function role_label($role)
{
    return $role === 'faculty' ? 'Teacher' : ucfirst((string) $role);
}

function render_logo_mark()
{
    return '<img class="logo-mark" src="assets/logo.svg" alt="University Portal logo">';
}

function require_login($roles = [])
{
    if (!isset($_SESSION['user_id'])) {
        redirect('index.php');
    }

    if ($roles && !in_array($_SESSION['role'], $roles, true)) {
        redirect(role_dashboard_path());
    }
}

function flash($key)
{
    if (!isset($_SESSION['flash'][$key])) {
        return '';
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $message;
}

function set_flash($key, $message)
{
    $_SESSION['flash'][$key] = $message;
}

function cache_key($namespace, $sql, $params = [])
{
    return preg_replace('/[^a-z0-9_-]/i', '_', $namespace) . '_' . sha1($sql . serialize($params));
}

function cache_path($key)
{
    if (!is_dir(CACHE_DIR)) {
        mkdir(CACHE_DIR, 0775, true);
    }

    return CACHE_DIR . DIRECTORY_SEPARATOR . $key . '.cache.php';
}

function cache_get($namespace, $sql, $params = [], $ttl = CACHE_DEFAULT_TTL)
{
    $path = cache_path(cache_key($namespace, $sql, $params));

    if (!is_file($path) || filemtime($path) + $ttl < time()) {
        return null;
    }

    $payload = include $path;
    return is_array($payload) && array_key_exists('data', $payload) ? $payload['data'] : null;
}

function cache_set($namespace, $sql, $params, $data)
{
    $path = cache_path(cache_key($namespace, $sql, $params));
    $export = var_export(['created_at' => time(), 'data' => $data], true);
    file_put_contents($path, "<?php\nreturn $export;\n");
}

function cache_clear($namespace = null)
{
    if (!is_dir(CACHE_DIR)) {
        return;
    }

    $pattern = $namespace ? CACHE_DIR . DIRECTORY_SEPARATOR . preg_replace('/[^a-z0-9_-]/i', '_', $namespace) . '_*.cache.php' : CACHE_DIR . DIRECTORY_SEPARATOR . '*.cache.php';
    foreach (glob($pattern) ?: [] as $file) {
        unlink($file);
    }
}

function cache_stats()
{
    if (!is_dir(CACHE_DIR)) {
        return ['files' => 0, 'bytes' => 0];
    }

    $files = glob(CACHE_DIR . DIRECTORY_SEPARATOR . '*.cache.php') ?: [];
    $bytes = 0;

    foreach ($files as $file) {
        $bytes += filesize($file);
    }

    return ['files' => count($files), 'bytes' => $bytes];
}

function scalar_query($sql, $params = [])
{
    global $conn;

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function cached_scalar_query($namespace, $sql, $params = [], $ttl = CACHE_DEFAULT_TTL)
{
    $cached = cache_get($namespace, $sql, $params, $ttl);

    if ($cached !== null) {
        return (int) $cached;
    }

    $value = scalar_query($sql, $params);
    cache_set($namespace, $sql, $params, $value);
    return $value;
}

function fetch_all($sql, $params = [])
{
    global $conn;

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function cached_fetch_all($namespace, $sql, $params = [], $ttl = CACHE_DEFAULT_TTL)
{
    $cached = cache_get($namespace, $sql, $params, $ttl);

    if ($cached !== null) {
        return $cached;
    }

    $rows = fetch_all($sql, $params);
    cache_set($namespace, $sql, $params, $rows);
    return $rows;
}

function fetch_one($sql, $params = [])
{
    global $conn;

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

function ensure_core_tables()
{
    global $conn;

    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(160) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','faculty','student') NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS students (
        student_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        program VARCHAR(120) NOT NULL,
        semester INT NOT NULL,
        cgpa DECIMAL(3,2) NOT NULL DEFAULT 0.00
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS faculty (
        faculty_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        department VARCHAR(120) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS courses (
        course_id INT AUTO_INCREMENT PRIMARY KEY,
        course_code VARCHAR(40) NOT NULL UNIQUE,
        title VARCHAR(180) NOT NULL,
        credit INT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS sections (
        section_id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        faculty_id INT NOT NULL,
        section_name VARCHAR(40) NOT NULL,
        schedule VARCHAR(160) NOT NULL,
        capacity INT NOT NULL DEFAULT 40,
        status VARCHAR(40) NOT NULL DEFAULT 'Open'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS registrations (
        reg_id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        section_id INT NULL,
        status VARCHAR(40) NOT NULL DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS payments (
        payment_id INT AUTO_INCREMENT PRIMARY KEY,
        reg_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(40) NOT NULL DEFAULT 'Unpaid',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS attendance (
        attendance_id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        section_id INT NOT NULL,
        class_date DATE NOT NULL,
        status VARCHAR(40) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS results (
        result_id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        section_id INT NOT NULL,
        grade VARCHAR(10) NOT NULL,
        gpa DECIMAL(3,2) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if (scalar_query('SELECT COUNT(*) FROM users') === 0) {
        $stmt = $conn->prepare("INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['Portal Admin', 'admin@gmail.com', password_hash('12345', PASSWORD_DEFAULT)]);
    }

    if (scalar_query('SELECT COUNT(*) FROM courses') === 0) {
        $stmt = $conn->prepare('INSERT INTO courses(course_code, title, credit) VALUES (?, ?, ?)');
        $stmt->execute(['CSE101', 'Introduction to Programming', 3]);
        $stmt->execute(['MAT101', 'Calculus I', 3]);
        $stmt->execute(['ENG101', 'Academic English', 3]);
    }
}

function ensure_portal_tables()
{
    global $conn;

    $conn->exec("CREATE TABLE IF NOT EXISTS notices (
        notice_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(160) NOT NULL,
        body TEXT NOT NULL,
        audience ENUM('all','admin','faculty','student') NOT NULL DEFAULT 'all',
        priority ENUM('normal','important','urgent') NOT NULL DEFAULT 'normal',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS events (
        event_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(160) NOT NULL,
        location VARCHAR(160) NOT NULL,
        event_date DATE NOT NULL,
        event_time TIME NULL,
        category VARCHAR(80) NOT NULL DEFAULT 'Academic',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS support_tickets (
        ticket_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        subject VARCHAR(180) NOT NULL,
        category VARCHAR(80) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('Open','In Review','Resolved') NOT NULL DEFAULT 'Open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS library_books (
        book_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(180) NOT NULL,
        author VARCHAR(140) NOT NULL,
        isbn VARCHAR(40) NULL,
        department VARCHAR(100) NULL,
        total_copies INT NOT NULL DEFAULT 1,
        available_copies INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if (scalar_query('SELECT COUNT(*) FROM notices') === 0) {
        $stmt = $conn->prepare('INSERT INTO notices(title, body, audience, priority) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Registration window open', 'Students can review available courses and complete advising from the portal.', 'student', 'important']);
        $stmt->execute(['Faculty grade deadline', 'All pending results should be submitted before the end of the current academic week.', 'faculty', 'normal']);
        $stmt->execute(['Administrative audit', 'Please verify student, faculty, course, and section records for the current semester.', 'admin', 'normal']);
    }

    if (scalar_query('SELECT COUNT(*) FROM events') === 0) {
        $stmt = $conn->prepare('INSERT INTO events(title, location, event_date, event_time, category) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute(['Orientation and advising clinic', 'Auditorium', date('Y-m-d', strtotime('+5 days')), '10:00:00', 'Academic']);
        $stmt->execute(['Career services workshop', 'Room 402', date('Y-m-d', strtotime('+12 days')), '14:30:00', 'Career']);
    }

    if (scalar_query('SELECT COUNT(*) FROM library_books') === 0) {
        $stmt = $conn->prepare('INSERT INTO library_books(title, author, isbn, department, total_copies, available_copies) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute(['Database System Concepts', 'Silberschatz, Korth, Sudarshan', '9780073523323', 'CSE', 6, 4]);
        $stmt->execute(['Clean Code', 'Robert C. Martin', '9780132350884', 'CSE', 5, 3]);
        $stmt->execute(['Principles of Economics', 'N. Gregory Mankiw', '9781305585126', 'Business', 4, 2]);
    }
}

function get_or_create_user($name, $email, $role, $password = '12345')
{
    global $conn;

    $user = fetch_one('SELECT user_id FROM users WHERE email = ?', [$email]);

    if ($user) {
        return (int) $user['user_id'];
    }

    $stmt = $conn->prepare('INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
    return (int) $conn->lastInsertId();
}

function get_or_create_faculty($userId, $department)
{
    global $conn;

    $faculty = fetch_one('SELECT faculty_id FROM faculty WHERE user_id = ?', [$userId]);

    if ($faculty) {
        return (int) $faculty['faculty_id'];
    }

    $stmt = $conn->prepare('INSERT INTO faculty(user_id, department) VALUES (?, ?)');
    $stmt->execute([$userId, $department]);
    return (int) $conn->lastInsertId();
}

function get_or_create_student($userId, $program, $semester, $cgpa)
{
    global $conn;

    $student = fetch_one('SELECT student_id FROM students WHERE user_id = ?', [$userId]);

    if ($student) {
        return (int) $student['student_id'];
    }

    $stmt = $conn->prepare('INSERT INTO students(user_id, program, semester, cgpa) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $program, $semester, $cgpa]);
    return (int) $conn->lastInsertId();
}

function get_or_create_course($code, $title, $credit)
{
    global $conn;

    $course = fetch_one('SELECT course_id FROM courses WHERE course_code = ?', [$code]);

    if ($course) {
        return (int) $course['course_id'];
    }

    $stmt = $conn->prepare('INSERT INTO courses(course_code, title, credit) VALUES (?, ?, ?)');
    $stmt->execute([$code, $title, $credit]);
    return (int) $conn->lastInsertId();
}

function get_or_create_section($courseId, $facultyId, $sectionName, $schedule, $capacity, $status = 'Open')
{
    global $conn;

    $section = fetch_one(
        'SELECT section_id FROM sections WHERE course_id = ? AND faculty_id = ? AND section_name = ?',
        [$courseId, $facultyId, $sectionName]
    );

    if ($section) {
        return (int) $section['section_id'];
    }

    $stmt = $conn->prepare('INSERT INTO sections(course_id, faculty_id, section_name, schedule, capacity, status) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$courseId, $facultyId, $sectionName, $schedule, $capacity, $status]);
    return (int) $conn->lastInsertId();
}

function ensure_registration_payment($studentId, $sectionId, $status, $amount, $paymentStatus)
{
    global $conn;

    $registration = fetch_one('SELECT reg_id FROM registrations WHERE student_id = ? AND section_id = ?', [$studentId, $sectionId]);

    if (!$registration) {
        $stmt = $conn->prepare('INSERT INTO registrations(student_id, section_id, status) VALUES (?, ?, ?)');
        $stmt->execute([$studentId, $sectionId, $status]);
        $regId = (int) $conn->lastInsertId();
    } else {
        $regId = (int) $registration['reg_id'];
    }

    $payment = fetch_one('SELECT payment_id FROM payments WHERE reg_id = ?', [$regId]);

    if (!$payment) {
        $stmt = $conn->prepare('INSERT INTO payments(reg_id, amount, status) VALUES (?, ?, ?)');
        $stmt->execute([$regId, $amount, $paymentStatus]);
    }
}

function ensure_attendance($studentId, $sectionId, $classDate, $status)
{
    global $conn;

    $existing = fetch_one(
        'SELECT attendance_id FROM attendance WHERE student_id = ? AND section_id = ? AND class_date = ?',
        [$studentId, $sectionId, $classDate]
    );

    if (!$existing) {
        $stmt = $conn->prepare('INSERT INTO attendance(student_id, section_id, class_date, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$studentId, $sectionId, $classDate, $status]);
    }
}

function ensure_result($studentId, $sectionId, $grade, $gpa)
{
    global $conn;

    $existing = fetch_one('SELECT result_id FROM results WHERE student_id = ? AND section_id = ?', [$studentId, $sectionId]);

    if (!$existing) {
        $stmt = $conn->prepare('INSERT INTO results(student_id, section_id, grade, gpa) VALUES (?, ?, ?, ?)');
        $stmt->execute([$studentId, $sectionId, $grade, $gpa]);
    }
}

function seed_sample_data()
{
    global $conn;

    $seedExists = fetch_one("SELECT user_id FROM users WHERE email = 'tanvir.hossain@uiu.com'");

    if ($seedExists) {
        return;
    }

    $conn->beginTransaction();

    $teachers = [
        ['Dr. Mahmudul Karim', 'mahmudul.karim@uiu.com', 'Computer Science and Engineering'],
        ['Prof. Nusrat Jahan', 'nusrat.jahan@uiu.com', 'Business Administration'],
        ['Dr. Rezaul Haque', 'rezaul.haque@uiu.com', 'English and Humanities'],
    ];

    $facultyIds = [];
    foreach ($teachers as $teacher) {
        $userId = get_or_create_user($teacher[0], $teacher[1], 'faculty');
        $facultyIds[] = get_or_create_faculty($userId, $teacher[2]);
    }

    $students = [
        ['Tanvir Hossain', 'tanvir.hossain@uiu.com', 'BSc in Computer Science and Engineering', 5, '3.72'],
        ['Mehjabin Akter', 'mehjabin.akter@uiu.com', 'BBA in Finance', 4, '3.48'],
        ['Samiul Islam', 'samiul.islam@uiu.com', 'BA in English', 3, '3.86'],
    ];

    $studentIds = [];
    foreach ($students as $student) {
        $userId = get_or_create_user($student[0], $student[1], 'student');
        $studentIds[] = get_or_create_student($userId, $student[2], $student[3], $student[4]);
    }

    $courseIds = [
        get_or_create_course('CSE201', 'Data Structures and Algorithms', 3),
        get_or_create_course('BUS210', 'Principles of Marketing', 3),
        get_or_create_course('ENG205', 'Business Communication', 3),
    ];

    $sectionIds = [
        get_or_create_section($courseIds[0], $facultyIds[0], 'A', 'Sun Tue 10:00-11:30', 35),
        get_or_create_section($courseIds[1], $facultyIds[1], 'B', 'Mon Wed 12:00-13:30', 40),
        get_or_create_section($courseIds[2], $facultyIds[2], 'C', 'Sun Thu 14:00-15:30', 32),
    ];

    $paymentStatuses = [
        ['Approved', 18500, 'Paid'],
        ['Approved', 17500, 'Paid'],
        ['Pending', 16500, 'Unpaid'],
    ];

    foreach ($studentIds as $studentIndex => $studentId) {
        foreach ($sectionIds as $sectionIndex => $sectionId) {
            ensure_registration_payment(
                $studentId,
                $sectionId,
                $paymentStatuses[$studentIndex][0],
                $paymentStatuses[$studentIndex][1] + ($sectionIndex * 750),
                $paymentStatuses[$studentIndex][2]
            );
        }
    }

    $attendanceMatrix = [
        ['Present', 'Present', 'Absent', 'Present'],
        ['Present', 'Absent', 'Present', 'Present'],
        ['Absent', 'Present', 'Present', 'Present'],
    ];

    foreach ($studentIds as $studentIndex => $studentId) {
        foreach ($sectionIds as $sectionIndex => $sectionId) {
            for ($week = 0; $week < 4; $week++) {
                $date = date('Y-m-d', strtotime('-' . (28 - ($week * 7) - $sectionIndex) . ' days'));
                $status = $attendanceMatrix[$studentIndex][$week];
                ensure_attendance($studentId, $sectionId, $date, $status);
            }
        }
    }

    $results = [
        [['A', '4.00'], ['A-', '3.70'], ['B+', '3.30']],
        [['B+', '3.30'], ['A-', '3.70'], ['B', '3.00']],
        [['A-', '3.70'], ['B+', '3.30'], ['A', '4.00']],
    ];

    foreach ($studentIds as $studentIndex => $studentId) {
        foreach ($sectionIds as $sectionIndex => $sectionId) {
            ensure_result($studentId, $sectionId, $results[$studentIndex][$sectionIndex][0], $results[$studentIndex][$sectionIndex][1]);
        }
    }

    $stmt = $conn->prepare('INSERT INTO support_tickets(user_id, subject, category, message, status) VALUES (?, ?, ?, ?, ?)');
    $studentUser = fetch_one("SELECT user_id FROM users WHERE email = 'tanvir.hossain@uiu.com'");
    $teacherUser = fetch_one("SELECT user_id FROM users WHERE email = 'mahmudul.karim@uiu.com'");
    $stmt->execute([$studentUser['user_id'], 'ID card collection', 'Academic', 'I need confirmation about my new student ID card collection date.', 'Open']);
    $stmt->execute([$teacherUser['user_id'], 'Projector issue in Room 402', 'Technical', 'The classroom projector needs maintenance before the next lecture.', 'In Review']);

    $conn->commit();
    cache_clear();
}

function nav_items_for_role($role)
{
    $common = [
        [role_dashboard_path($role), 'Dashboard', 'grid'],
        ['notices.php', 'Notices', 'bell'],
        ['events.php', 'Events', 'calendar'],
        ['library.php', 'Library', 'book'],
        ['support.php', 'Support', 'life-buoy'],
    ];

    $roleItems = [
        'admin' => [
            ['student_list.php', 'Students', 'users'],
            ['faculty_list.php', 'Faculty', 'briefcase'],
            ['course_list.php', 'Courses', 'layers'],
            ['admin_sections.php', 'Sections', 'layout'],
            ['cache_status.php', 'Cache', 'database'],
        ],
        'faculty' => [
            ['manage_sections.php', 'My Sections', 'layout'],
            ['take_attendance.php', 'Attendance', 'check-square'],
            ['submit_results.php', 'Results', 'award'],
        ],
        'student' => [
            ['pre_advising.php', 'Advising', 'clipboard'],
            ['view_attendance.php', 'Attendance', 'check-square'],
            ['view_results.php', 'Results', 'award'],
            ['payment_status.php', 'Payments', 'credit-card'],
        ],
    ];

    return array_merge($common, $roleItems[$role] ?? []);
}

function icon_svg($name)
{
    $icons = [
        'grid' => '<path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/>',
        'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
        'calendar' => '<path d="M8 2v4M16 2v4M3 10h18"/><rect x="3" y="4" width="18" height="18" rx="2"/>',
        'book' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"/>',
        'life-buoy' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="m4.93 4.93 4.24 4.24M14.83 14.83l4.24 4.24M14.83 9.17l4.24-4.24M14.83 9.17l4.24-4.24M9.17 14.83l-4.24 4.24"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'layers' => '<path d="m12 2 10 5-10 5L2 7z"/><path d="m2 17 10 5 10-5M2 12l10 5 10-5"/>',
        'layout' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
        'check-square' => '<path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'award' => '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>',
        'clipboard' => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>',
        'credit-card' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
        'database' => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/><path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"/>',
    ];

    return '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' . ($icons[$name] ?? $icons['grid']) . '</svg>';
}

function render_header($title, $active = '')
{
    $role = $_SESSION['role'] ?? 'guest';
    $name = $_SESSION['name'] ?? 'Guest';
    $initials = strtoupper(substr($name, 0, 1));
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title); ?> | University Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app-body">
<aside class="sidebar">
    <a class="brand" href="<?php echo h(role_dashboard_path($role)); ?>">
        <?php echo render_logo_mark(); ?>
        <span>
            <strong>University Portal</strong>
            <small>Academic operations</small>
        </span>
    </a>
    <nav class="side-nav">
        <?php foreach (nav_items_for_role($role) as $item) { ?>
            <a class="<?php echo $active === $item[0] ? 'active' : ''; ?>" href="<?php echo h($item[0]); ?>">
                <?php echo icon_svg($item[2]); ?>
                <span><?php echo h($item[1]); ?></span>
            </a>
        <?php } ?>
    </nav>
</aside>
<main class="main-shell">
    <header class="topbar">
        <div>
            <p class="eyebrow"><?php echo h(role_label($role)); ?> workspace</p>
            <h1><?php echo h($title); ?></h1>
        </div>
        <div class="user-chip">
            <span class="avatar"><?php echo h($initials); ?></span>
            <span><?php echo h($name); ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </header>
    <?php
}

function render_role_login($role, $title, $subtitle, $imageUrl, $demoText, $accentClass = '')
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['user_id'])) {
        redirect(role_dashboard_path());
    }

    $error = $_GET['error'] ?? '';
    $message = $_GET['message'] ?? '';
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title); ?> | University Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body <?php echo h($accentClass); ?>">
    <section class="login-panel">
        <div class="login-card">
            <div class="login-brand">
                <?php echo render_logo_mark(); ?>
                <span>
                    <strong><?php echo h($title); ?></strong><br>
                    <small class="muted">University Portal</small>
                </span>
            </div>

            <h1><?php echo h(role_label($role)); ?> sign in</h1>
            <p class="subtitle"><?php echo h($subtitle); ?></p>

            <?php if ($error) { ?><p class="alert"><?php echo h($error); ?></p><?php } ?>
            <?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>

            <form action="login.php" method="POST">
                <input type="hidden" name="expected_role" value="<?php echo h($role); ?>">
                <div class="field">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" required autocomplete="email">
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit">Sign in as <?php echo h(role_label($role)); ?></button>
            </form>

            <div class="link-row">
                <span><?php echo h($demoText); ?></span>
                <a class="forgot-link" href="forgot_password.php">Reset password</a>
            </div>
            <div class="role-switch">
                <a href="index.php">Choose another portal</a>
            </div>
        </div>
    </section>

    <section class="login-art" style="--login-image: url('<?php echo h($imageUrl); ?>')">
        <div>
            <h2><?php echo h($title); ?></h2>
            <p><?php echo h($subtitle); ?></p>
        </div>
    </section>
</body>
</html>
<?php
}

function render_footer()
{
    ?>
</main>
</body>
</html>
<?php
}

ensure_core_tables();
ensure_portal_tables();
seed_sample_data();

if (!headers_sent() && preg_match('/\.(?:css|js|jpg|jpeg|png|svg|webp)$/i', $_SERVER['REQUEST_URI'] ?? '')) {
    header('Cache-Control: public, max-age=604800, immutable');
}
?>
