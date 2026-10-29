<?php
$pageTitle = 'Compose';
require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    db_run(
        "INSERT INTO mailbox_messages (sender_name, sender_email, subject, message, folder, created_by)
         VALUES (?, ?, ?, ?, 'sent', ?)",
        'ssssi',
        [
            $_SESSION['full_name'] ?? 'Admin',
            $_SESSION['email'] ?? '',
            post('subject'),
            post('message'),
            (int) $_SESSION['user_id'],
        ]
    );
    flash_set('success', 'Message saved in Sent folder.');
    header('Location: mailbox.php');
    exit;
}

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 fw-bold mb-3">Compose Message</h1>
            <form method="POST" class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="mb-3">
                        <label class="form-label">To</label>
                        <input class="form-control" name="to_email" value="<?php echo e($_GET['to'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input class="form-control" name="subject" required value="<?php echo e($_GET['subject'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="8" required></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Save / Send</button>
                    <a class="btn btn-light" href="mailbox.php">Cancel</a>
                </div>
            </form>
        </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
<?php include __DIR__ . '/partials/scripts.php'; ?>
