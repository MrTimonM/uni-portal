<?php
require_once __DIR__ . '/app.php';
require_login(['student']);

$student = fetch_one('SELECT student_id FROM students WHERE user_id = ?', [$_SESSION['user_id']]);
$payments = [];

if ($student) {
    $payments = cached_fetch_all('payment_status', 'SELECT payments.amount, payments.status, registrations.status AS reg_status
        FROM payments
        INNER JOIN registrations ON payments.reg_id = registrations.reg_id
        WHERE registrations.student_id = ?', [$student['student_id']]);
}

$total = 0;
foreach ($payments as $payment) {
    $total += (float) $payment['amount'];
}

render_header('Payment Status', 'payment_status.php');
?>

<section class="stats-grid">
    <div class="stat-card"><span>Total Records</span><strong><?php echo h(count($payments)); ?></strong></div>
    <div class="stat-card"><span>Total Amount</span><strong><?php echo h(number_format($total, 0)); ?></strong></div>
    <div class="stat-card"><span>Clearance</span><strong><?php echo $payments ? h($payments[0]['status']) : '-'; ?></strong></div>
    <div class="stat-card"><span>Registration</span><strong><?php echo $payments ? h($payments[0]['reg_status']) : '-'; ?></strong></div>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <tr>
                <th>Amount</th>
                <th>Payment Status</th>
                <th>Registration Status</th>
            </tr>
            <?php foreach ($payments as $row) { ?>
                <tr>
                    <td><?php echo h(number_format((float) $row['amount'], 2)); ?></td>
                    <td><span class="badge <?php echo h($row['status']); ?>"><?php echo h($row['status']); ?></span></td>
                    <td><span class="badge <?php echo h($row['reg_status']); ?>"><?php echo h($row['reg_status']); ?></span></td>
                </tr>
            <?php } ?>
        </table>
        <?php if (!$payments) { ?><div class="empty-state">No payment records found.</div><?php } ?>
    </div>
</section>

<?php render_footer(); ?>
