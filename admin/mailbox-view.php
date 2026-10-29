<?php
$pageTitle = 'View Message';
require __DIR__ . '/bootstrap.php';

$id = get_int('id');
$message = db_one("SELECT * FROM contact_messages WHERE message_id = ?", 'i', [$id]);

if (!$message) {
    flash_set('error', 'Message not found.');
    header('Location: mailbox.php');
    exit;
}

db_run("UPDATE contact_messages SET status = IF(status = 'new', 'read', status) WHERE message_id = ?", 'i', [$id]);

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <a href="mailbox.php" class="btn btn-light mb-3">Back to mailbox</a>
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <h1 class="h4"><?php echo e($message['subject']); ?></h1>
                    <p class="text-muted">From <?php echo e($message['name']); ?> (<?php echo e($message['email']); ?>) on <?php echo e($message['created_at']); ?></p>
                    <p><?php echo nl2br(e($message['message'])); ?></p>
                    <a class="btn btn-primary" href="mailbox-compose.php?to=<?php echo urlencode($message['email']); ?>&subject=<?php echo urlencode('Re: ' . $message['subject']); ?>">Reply</a>
                </div>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
<?php include __DIR__ . '/partials/scripts.php'; ?>
