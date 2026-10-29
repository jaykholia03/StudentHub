<?php
$pageTitle = 'Feedback';
require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post('action');
    $id = post_int('feedback_id');

    if ($action === 'delete') {
        db_run("DELETE FROM feedback WHERE feedback_id = ?", 'i', [$id]);
        flash_set('success', 'Feedback deleted.');
    } elseif ($action === 'status') {
        db_run("UPDATE feedback SET status = ? WHERE feedback_id = ?", 'si', [post('status'), $id]);
        flash_set('success', 'Feedback status updated.');
    }

    header('Location: feedback.php');
    exit;
}

$items = db_all(
    "SELECT f.*, u.full_name
     FROM feedback f
     LEFT JOIN users u ON u.id = f.user_id
     ORDER BY f.created_at DESC"
);

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 font-bold mb-3">Student Feedback</h1>
            <?php include __DIR__ . '/partials/alert.php'; ?>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Student</th><th>Course</th><th>Rating</th><th>Feedback</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            <?php if (!$items): ?>
                                <tr><td colspan="6" class="text-muted">No feedback yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo e($item['full_name'] ?: 'Guest'); ?></td>
                                    <td><?php echo e($item['course_name'] ?: '-'); ?></td>
                                    <td><?php echo e($item['rating'] ?: '-'); ?></td>
                                    <td><?php echo e($item['feedback_text']); ?></td>
                                    <td><?php echo e($item['status']); ?></td>
                                    <td class="text-end">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="status">
                                            <input type="hidden" name="feedback_id" value="<?php echo (int) $item['feedback_id']; ?>">
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                <?php foreach (['new', 'reviewed', 'resolved'] as $status): ?>
                                                    <option value="<?php echo $status; ?>" <?php echo $item['status'] === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this feedback?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="feedback_id" value="<?php echo (int) $item['feedback_id']; ?>">
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
