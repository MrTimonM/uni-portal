<?php
require_once __DIR__ . '/app.php';
require_login();

$message = flash('event');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'admin') {
    $stmt = $conn->prepare('INSERT INTO events(title, location, event_date, event_time, category) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        trim($_POST['title']),
        trim($_POST['location']),
        $_POST['event_date'],
        $_POST['event_time'] ?: null,
        trim($_POST['category']),
    ]);
    cache_clear();
    set_flash('event', 'Event added successfully.');
    redirect('events.php');
}

$events = cached_fetch_all('events', 'SELECT * FROM events ORDER BY event_date DESC, event_time DESC');
render_header('Academic Calendar', 'events.php');
?>

<?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>

<?php if ($_SESSION['role'] === 'admin') { ?>
<section class="panel">
    <div class="section-heading">
        <h2>Add Event</h2>
    </div>
    <form method="POST" class="form-grid">
        <div class="field">
            <label>Title</label>
            <input name="title" required>
        </div>
        <div class="field">
            <label>Location</label>
            <input name="location" required>
        </div>
        <div class="field">
            <label>Date</label>
            <input type="date" name="event_date" required>
        </div>
        <div class="field">
            <label>Time</label>
            <input type="time" name="event_time">
        </div>
        <div class="field">
            <label>Category</label>
            <input name="category" value="Academic" required>
        </div>
        <button type="submit">Add Event</button>
    </form>
</section>
<?php } ?>

<section class="panel">
    <div class="table-wrap">
        <table>
            <tr>
                <th>Event</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Category</th>
            </tr>
            <?php foreach ($events as $event) { ?>
                <tr>
                    <td><?php echo h($event['title']); ?></td>
                    <td><?php echo h(date('M d, Y', strtotime($event['event_date']))); ?></td>
                    <td><?php echo h($event['event_time'] ? substr($event['event_time'], 0, 5) : 'TBA'); ?></td>
                    <td><?php echo h($event['location']); ?></td>
                    <td><span class="badge"><?php echo h($event['category']); ?></span></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</section>

<?php render_footer(); ?>
