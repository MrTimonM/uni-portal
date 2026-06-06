<?php
require_once __DIR__ . '/app.php';
require_login();

$message = flash('support');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['role'] === 'admin' && isset($_POST['ticket_id'], $_POST['status'])) {
        $stmt = $conn->prepare('UPDATE support_tickets SET status = ? WHERE ticket_id = ?');
        $stmt->execute([$_POST['status'], $_POST['ticket_id']]);
        cache_clear();
        set_flash('support', 'Ticket status updated.');
    } else {
        $stmt = $conn->prepare('INSERT INTO support_tickets(user_id, subject, category, message) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $_SESSION['user_id'],
            trim($_POST['subject']),
            trim($_POST['category']),
            trim($_POST['message']),
        ]);
        cache_clear();
        set_flash('support', 'Support ticket submitted.');
    }
    redirect('support.php');
}

if ($_SESSION['role'] === 'admin') {
    $tickets = cached_fetch_all('support', 'SELECT support_tickets.*, users.name, users.role FROM support_tickets INNER JOIN users ON support_tickets.user_id = users.user_id ORDER BY support_tickets.updated_at DESC');
} else {
    $tickets = cached_fetch_all('support', 'SELECT support_tickets.*, users.name, users.role FROM support_tickets INNER JOIN users ON support_tickets.user_id = users.user_id WHERE support_tickets.user_id = ? ORDER BY support_tickets.updated_at DESC', [$_SESSION['user_id']]);
}

render_header('Support Desk', 'support.php');
?>

<?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>

<?php if ($_SESSION['role'] !== 'admin') { ?>
<section class="panel">
    <div class="section-heading">
        <h2>New Request</h2>
    </div>
    <form method="POST" class="form-grid">
        <div class="field">
            <label>Subject</label>
            <input name="subject" required>
        </div>
        <div class="field">
            <label>Category</label>
            <select name="category" required>
                <option>Academic</option>
                <option>Accounts</option>
                <option>Technical</option>
                <option>Library</option>
                <option>Other</option>
            </select>
        </div>
        <div class="field full">
            <label>Message</label>
            <textarea name="message" required></textarea>
        </div>
        <button type="submit">Submit Ticket</button>
    </form>
</section>
<?php } ?>

<section class="panel">
    <div class="table-wrap">
        <table>
            <tr>
                <th>ID</th>
                <th>Requester</th>
                <th>Subject</th>
                <th>Category</th>
                <th>Status</th>
                <th>Updated</th>
                <?php if ($_SESSION['role'] === 'admin') { ?><th>Action</th><?php } ?>
            </tr>
            <?php foreach ($tickets as $ticket) { ?>
                <tr>
                    <td>#<?php echo h($ticket['ticket_id']); ?></td>
                    <td><?php echo h($ticket['name']); ?> <span class="muted">(<?php echo h($ticket['role']); ?>)</span></td>
                    <td><?php echo h($ticket['subject']); ?><br><span class="muted"><?php echo h($ticket['message']); ?></span></td>
                    <td><?php echo h($ticket['category']); ?></td>
                    <td><span class="badge <?php echo h($ticket['status']); ?>"><?php echo h($ticket['status']); ?></span></td>
                    <td><?php echo h(date('M d, Y', strtotime($ticket['updated_at']))); ?></td>
                    <?php if ($_SESSION['role'] === 'admin') { ?>
                        <td>
                            <form method="POST" class="actions">
                                <input type="hidden" name="ticket_id" value="<?php echo h($ticket['ticket_id']); ?>">
                                <select name="status">
                                    <option <?php echo $ticket['status'] === 'Open' ? 'selected' : ''; ?>>Open</option>
                                    <option <?php echo $ticket['status'] === 'In Review' ? 'selected' : ''; ?>>In Review</option>
                                    <option <?php echo $ticket['status'] === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
        <?php if (!$tickets) { ?><div class="empty-state">No support tickets found.</div><?php } ?>
    </div>
</section>

<?php render_footer(); ?>
