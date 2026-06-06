<?php
require_once __DIR__ . '/app.php';
require_login(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cache_clear();
    set_flash('cache', 'Cache cleared successfully.');
    redirect('cache_status.php');
}

$message = flash('cache');
$stats = cache_stats();
$size = $stats['bytes'] > 0 ? number_format($stats['bytes'] / 1024, 2) . ' KB' : '0 KB';

render_header('Cache Status', 'cache_status.php');
?>

<?php if ($message) { ?><p class="success"><?php echo h($message); ?></p><?php } ?>

<section class="stats-grid">
    <div class="stat-card">
        <span>Cached Files</span>
        <strong><?php echo h($stats['files']); ?></strong>
    </div>
    <div class="stat-card">
        <span>Cache Size</span>
        <strong><?php echo h($size); ?></strong>
    </div>
    <div class="stat-card">
        <span>Data TTL</span>
        <strong><?php echo h(CACHE_DEFAULT_TTL); ?>s</strong>
    </div>
    <div class="stat-card">
        <span>Asset Cache</span>
        <strong>30d</strong>
    </div>
</section>

<section class="panel">
    <div class="section-heading">
        <div>
            <h2>Server Cache</h2>
            <p class="subtitle">Repeated dashboard, list, report, notice, calendar, library, and support queries are saved locally for faster reloads.</p>
        </div>
    </div>
    <form method="POST">
        <button type="submit">Clear Cache</button>
    </form>
</section>

<?php render_footer(); ?>
