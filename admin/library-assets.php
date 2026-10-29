<?php
$pageTitle = 'Library Assets';
require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post('action');

    if ($action === 'delete') {
        db_run("DELETE FROM library_assets WHERE asset_id = ?", 'i', [post_int('asset_id')]);
        flash_set('success', 'Library item deleted.');
    } elseif ($action === 'save') {
        $id = post_int('asset_id');
        $values = [
            post('title'),
            post('category'),
            post('author'),
            post('location'),
            post('status') ?: 'available',
            post_int('copies') ?: 1,
        ];

        if ($id > 0) {
            db_run(
                "UPDATE library_assets SET title=?, category=?, author=?, location=?, status=?, copies=? WHERE asset_id=?",
                'sssssii',
                [...$values, $id]
            );
            flash_set('success', 'Library item updated.');
        } else {
            db_run(
                "INSERT INTO library_assets (title, category, author, location, status, copies)
                 VALUES (?, ?, ?, ?, ?, ?)",
                'sssssi',
                $values
            );
            flash_set('success', 'Library item added.');
        }
    }

    header('Location: library-assets.php');
    exit;
}

$edit = get_int('edit') > 0 ? db_one("SELECT * FROM library_assets WHERE asset_id = ?", 'i', [get_int('edit')]) : null;
$assets = db_all("SELECT * FROM library_assets ORDER BY title");

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 font-bold mb-3">Library Assets</h1>
            <?php include __DIR__ . '/partials/alert.php'; ?>

            <form method="POST" class="dashboard-card mb-3">
                <div class="dashboard-card-header"><h5 class="mb-0"><?php echo $edit ? 'Edit Item' : 'Add Item'; ?></h5></div>
                        <div class="dashboard-card-body">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="asset_id" value="<?php echo (int) ($edit['asset_id'] ?? 0); ?>">
                            <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Title</label><input class="form-control" name="title" required value="<?php echo e($edit['title'] ?? ''); ?>"></div>
                        <div class="col-md-3"><label class="form-label">Category</label><input class="form-control" name="category" value="<?php echo e($edit['category'] ?? ''); ?>"></div>
                        <div class="col-md-3"><label class="form-label">Author</label><input class="form-control" name="author" value="<?php echo e($edit['author'] ?? ''); ?>"></div>
                        <div class="col-md-2"><label class="form-label">Copies</label><input class="form-control" type="number" name="copies" min="1" value="<?php echo e($edit['copies'] ?? 1); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Location</label><input class="form-control" name="location" value="<?php echo e($edit['location'] ?? ''); ?>"></div>
                                <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <?php foreach (['available', 'unavailable', 'maintenance', 'archived'] as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo (($edit['status'] ?? 'available') === $status) ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                <?php endforeach; ?>
                                    </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <?php if ($edit): ?><a class="btn btn-light" href="library-assets.php">Cancel</a><?php endif; ?>
                    </div>
                </div>
            </form>

<div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Title</th><th>Author</th><th>Category</th><th>Location</th><th>Copies</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            <?php if (!$assets): ?>
                                <tr><td colspan="7" class="text-muted">No library items yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($assets as $asset): ?>
                                <tr>
                                    <td><?php echo e($asset['title']); ?></td>
                                    <td><?php echo e($asset['author'] ?: '-'); ?></td>
                                    <td><?php echo e($asset['category'] ?: '-'); ?></td>
                                    <td><?php echo e($asset['location'] ?: '-'); ?></td>
                                    <td><?php echo (int) $asset['copies']; ?></td>
                                    <td><?php echo e($asset['status']); ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-secondary" href="library-assets.php?edit=<?php echo (int) $asset['asset_id']; ?>">Edit</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this item?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="asset_id" value="<?php echo (int) $asset['asset_id']; ?>">
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
