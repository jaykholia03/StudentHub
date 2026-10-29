<?php
$pageTitle = 'Departments';
require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post('action');

    if ($action === 'delete') {
        db_run("DELETE FROM departments WHERE department_id = ?", 'i', [post_int('department_id')]);
        flash_set('success', 'Department deleted.');
    } elseif ($action === 'save') {
        $id = post_int('department_id');
        $name = post('department_name');
        $code = post('department_code');
        $year = post_int('established_year');
        $status = post('status') ?: 'active';
        $description = post('description');
        $head = post('head_name');
        $visible = isset($_POST['publicly_visible']) ? 1 : 0;

        if ($id > 0) {
            db_run(
                "UPDATE departments
                 SET department_name=?, department_code=?, established_year=NULLIF(?,0), status=?,
                     description=?, head_name=?, publicly_visible=?
                 WHERE department_id=?",
                'ssisssii',
                [$name, $code, $year, $status, $description, $head, $visible, $id]
            );
            flash_set('success', 'Department updated.');
        } else {
            db_run(
                "INSERT INTO departments
                 (department_name, department_code, established_year, status, description, head_name, publicly_visible)
                 VALUES (?, ?, NULLIF(?,0), ?, ?, ?, ?)",
                'ssisssi',
                [$name, $code, $year, $status, $description, $head, $visible]
            );
            flash_set('success', 'Department added.');
        }
    }

    header('Location: departments.php');
    exit;
}

$edit = null;
if (get_int('edit') > 0) {
    $edit = db_one("SELECT * FROM departments WHERE department_id = ?", 'i', [get_int('edit')]);
}

$departments = db_all(
    "SELECT d.*,
            (SELECT COUNT(*) FROM student_profiles sp WHERE sp.department_id = d.department_id) AS student_count,
            (SELECT COUNT(*) FROM professors p WHERE p.department_id = d.department_id) AS professor_count
     FROM departments d
     ORDER BY d.department_name"
);

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 font-bold mb-3">Departments</h1>
            <?php include __DIR__ . '/partials/alert.php'; ?>

            <form method="POST" class="dashboard-card mb-3">
                <div class="dashboard-card-header">
                    <h5 class="mb-0"><?php echo $edit ? 'Edit Department' : 'Add Department'; ?></h5>
                </div>
                <div class="dashboard-card-body">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="department_id" value="<?php echo (int) ($edit['department_id'] ?? 0); ?>">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Name</label><input class="form-control" name="department_name" required value="<?php echo e($edit['department_name'] ?? ''); ?>"></div>
                        <div class="col-md-2"><label class="form-label">Code</label><input class="form-control" name="department_code" required value="<?php echo e($edit['department_code'] ?? ''); ?>"></div>
                        <div class="col-md-2"><label class="form-label">Year</label><input class="form-control" type="number" name="established_year" value="<?php echo e($edit['established_year'] ?? ''); ?>"></div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" <?php echo (($edit['status'] ?? '') === 'active') ? 'selected' : ''; ?>>active</option>
                                <option value="inactive" <?php echo (($edit['status'] ?? '') === 'inactive') ? 'selected' : ''; ?>>inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2"><label class="form-label">Head</label><input class="form-control" name="head_name" value="<?php echo e($edit['head_name'] ?? ''); ?>"></div>
                        <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="2"><?php echo e($edit['description'] ?? ''); ?></textarea></div>
                        <div class="col-12">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="publicly_visible" value="1" <?php echo ((int) ($edit['publicly_visible'] ?? 1) === 1) ? 'checked' : ''; ?>>
                                <span class="form-check-label">Show on home page</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <?php if ($edit): ?><a class="btn btn-light" href="departments.php">Cancel</a><?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Name</th><th>Code</th><th>Head</th><th>Students</th><th>Faculty</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            <?php if (!$departments): ?>
                                <tr><td colspan="7" class="text-muted">No departments yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($departments as $department): ?>
                                <tr>
                                    <td><?php echo e($department['department_name']); ?></td>
                                    <td><?php echo e($department['department_code']); ?></td>
                                    <td><?php echo e($department['head_name'] ?: '-'); ?></td>
                                    <td><?php echo (int) $department['student_count']; ?></td>
                                    <td><?php echo (int) $department['professor_count']; ?></td>
                                    <td><?php echo e($department['status']); ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-secondary" href="departments.php?edit=<?php echo (int) $department['department_id']; ?>">Edit</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this department?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="department_id" value="<?php echo (int) $department['department_id']; ?>">
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
