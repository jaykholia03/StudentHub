<?php
$pageTitle = 'Events';
require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post('action');

    if ($action === 'delete') {
        db_run("DELETE FROM events WHERE id = ?", 'i', [post_int('id')]);
        flash_set('success', 'Event deleted.');
    } elseif ($action === 'save') {
        $id = post_int('id');
        $values = [
            post('title'),
            post('description'),
            post('event_date'),
            post('event_time'),
            post('venue'),
            post('category'),
            post('status') ?: 'draft',
        ];

        if ($id > 0) {
            db_run(
                "UPDATE events SET title=?, description=?, event_date=?, event_time=?, venue=?, category=?, status=? WHERE id=?",
                'sssssssi',
                [...$values, $id]
            );
            flash_set('success', 'Event updated.');
        } else {
            db_run(
                "INSERT INTO events (title, description, event_date, event_time, venue, category, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                'sssssss',
                $values
            );
            flash_set('success', 'Event added.');
        }
    }

    header('Location: events.php');
    exit;
}

$edit = get_int('edit') > 0 ? db_one("SELECT * FROM events WHERE id = ?", 'i', [get_int('edit')]) : null;
$events = db_all("SELECT * FROM events ORDER BY event_date DESC, event_time DESC");

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 font-bold mb-3">Events</h1>
            <?php include __DIR__ . '/partials/alert.php'; ?>

            <form method="POST" class="dashboard-card mb-3">
                <div class="dashboard-card-header"><h5 class="mb-0"><?php echo $edit ? 'Edit Event' : 'Add Event'; ?></h5></div>
                <div class="dashboard-card-body">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?php echo (int) ($edit['id'] ?? 0); ?>">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Title</label><input class="form-control" name="title" required value="<?php echo e($edit['title'] ?? ''); ?>"></div>
                        <div class="col-md-3"><label class="form-label">Category</label><input class="form-control" name="category" required value="<?php echo e($edit['category'] ?? ''); ?>"></div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="draft" <?php echo (($edit['status'] ?? '') === 'draft') ? 'selected' : ''; ?>>draft</option>
                                <option value="published" <?php echo (($edit['status'] ?? '') === 'published') ? 'selected' : ''; ?>>published</option>
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label">Date</label><input class="form-control" type="date" name="event_date" required value="<?php echo e($edit['event_date'] ?? ''); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Time</label><input class="form-control" type="time" name="event_time" required value="<?php echo e(substr((string) ($edit['event_time'] ?? ''), 0, 5)); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Venue</label><input class="form-control" name="venue" required value="<?php echo e($edit['venue'] ?? ''); ?>"></div>
                        <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3"><?php echo e($edit['description'] ?? ''); ?></textarea></div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <?php if ($edit): ?><a class="btn btn-light" href="events.php">Cancel</a><?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Title</th><th>Date</th><th>Venue</th><th>Category</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            <?php if (!$events): ?>
                                <tr><td colspan="6" class="text-muted">No events yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><?php echo e($event['title']); ?></td>
                                    <td><?php echo e($event['event_date'] . ' ' . substr((string) $event['event_time'], 0, 5)); ?></td>
                                    <td><?php echo e($event['venue']); ?></td>
                                    <td><?php echo e($event['category']); ?></td>
                                    <td><?php echo e($event['status']); ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-secondary" href="events.php?edit=<?php echo (int) $event['id']; ?>">Edit</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this event?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int) $event['id']; ?>">
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
