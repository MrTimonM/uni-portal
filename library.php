<?php
require_once __DIR__ . '/app.php';
require_login();

$message = flash('library');
$search = trim($_GET['q'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'admin') {
    $stmt = $conn->prepare('INSERT INTO library_books(title, author, isbn, department, total_copies, available_copies) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        trim($_POST['title']),
        trim($_POST['author']),
        trim($_POST['isbn']),
        trim($_POST['department']),
        (int) $_POST['total_copies'],
        (int) $_POST['available_copies'],
    ]);
    cache_clear();
    set_flash('library', 'Book added to library catalog.');
    redirect('library.php');
}

if ($search !== '') {
    $books = cached_fetch_all('library', 'SELECT * FROM library_books WHERE title LIKE ? OR author LIKE ? OR department LIKE ? OR isbn LIKE ? ORDER BY title', ["%$search%", "%$search%", "%$search%", "%$search%"]);
} else {
    $books = cached_fetch_all('library', 'SELECT * FROM library_books ORDER BY title');
}

render_header('Library Catalog', 'library.php');
?>

<?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>

<section class="panel">
    <form method="GET" class="form-grid">
        <div class="field full">
            <label>Search books</label>
            <input name="q" value="<?php echo h($search); ?>" placeholder="Title, author, ISBN, or department">
        </div>
        <button type="submit">Search</button>
        <a class="btn secondary" href="library.php">Clear</a>
    </form>
</section>

<?php if ($_SESSION['role'] === 'admin') { ?>
<section class="panel">
    <div class="section-heading">
        <h2>Add Book</h2>
    </div>
    <form method="POST" class="form-grid">
        <div class="field">
            <label>Title</label>
            <input name="title" required>
        </div>
        <div class="field">
            <label>Author</label>
            <input name="author" required>
        </div>
        <div class="field">
            <label>ISBN</label>
            <input name="isbn">
        </div>
        <div class="field">
            <label>Department</label>
            <input name="department">
        </div>
        <div class="field">
            <label>Total Copies</label>
            <input type="number" name="total_copies" min="1" value="1" required>
        </div>
        <div class="field">
            <label>Available Copies</label>
            <input type="number" name="available_copies" min="0" value="1" required>
        </div>
        <button type="submit">Add Book</button>
    </form>
</section>
<?php } ?>

<section class="panel">
    <div class="table-wrap">
        <table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Department</th>
                <th>ISBN</th>
                <th>Availability</th>
            </tr>
            <?php foreach ($books as $book) { ?>
                <tr>
                    <td><?php echo h($book['title']); ?></td>
                    <td><?php echo h($book['author']); ?></td>
                    <td><?php echo h($book['department']); ?></td>
                    <td><?php echo h($book['isbn']); ?></td>
                    <td><span class="badge <?php echo (int) $book['available_copies'] > 0 ? 'success' : 'urgent'; ?>"><?php echo h($book['available_copies']); ?> / <?php echo h($book['total_copies']); ?></span></td>
                </tr>
            <?php } ?>
        </table>
        <?php if (!$books) { ?><div class="empty-state">No books match your search.</div><?php } ?>
    </div>
</section>

<?php render_footer(); ?>
