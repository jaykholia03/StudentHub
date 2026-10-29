<?php
$pageTitle = 'Mailbox';
require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post('action');
    $id = post_int('message_id');

    if ($action === 'delete') {
        db_run("DELETE FROM contact_messages WHERE message_id = ?", 'i', [$id]);
        flash_set('success', 'Message deleted.');
    } elseif ($action === 'status') {
        db_run("UPDATE contact_messages SET status = ? WHERE message_id = ?", 'si', [post('status'), $id]);
        flash_set('success', 'Message status updated.');
    }

    header('Location: mailbox.php');
    exit;
}

$messages = db_all(
    "SELECT * FROM contact_messages ORDER BY created_at DESC"
);

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb-3">
                <h1 class="h3 font-bold">Mailbox</h1>
                <a class="btn btn-primary" href="mailbox-compose.php">Compose</a>
            </div>
            <?php include __DIR__ . '/partials/alert.php'; ?>
            <p class="text-muted">Messages sent from the student Contact page.</p>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>From</th><th>Subject</th><th>Status</th><th>Date</th><th></th></tr></thead>
                        <tbody>
                            <?php if (!$messages): ?>
                                <tr><td colspan="5" class="text-muted">No messages yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($messages as $message): ?>
                                <tr>
                                    <td><?php echo e($message['name']); ?><br><small><?php echo e($message['email']); ?></small></td>
                                    <td><?php echo e($message['subject']); ?></td>
                                    <td><?php echo e($message['status']); ?></td>
                                    <td><?php echo e($message['created_at']); ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="mailbox-view.php?id=<?php echo (int) $message['message_id']; ?>">Open</a>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="status">
                                            <input type="hidden" name="message_id" value="<?php echo (int) $message['message_id']; ?>">
                                            <input type="hidden" name="status" value="read">
                                            <button class="btn btn-sm btn-outline-secondary" type="submit">Mark read</button>
                                        </form>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this message?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="message_id" value="<?php echo (int) $message['message_id']; ?>">
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
<?php include __DIR__ . '/partials/scripts.php'; ?>
