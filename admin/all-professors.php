<?php
$pageTitle = 'All Professors';
require __DIR__ . '/bootstrap.php';

$departments = db_all("SELECT department_id, department_name FROM departments ORDER BY department_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post('action');

    if ($action === 'delete') {
        db_run("DELETE FROM professors WHERE professor_id = ?", 'i', [post_int('professor_id')]);
        flash_set('success', 'Professor deleted.');
    } elseif ($action === 'save') {
        $professorId = post_int('professor_id');
        $fullName = post('full_name');
        $email = post('email');
        $employeeNumber = post('employee_number');
        $departmentId = post_int('department_id');
        $designation = post('designation');
        $specialization = post('specialization');
        $qualification = post('qualification');
        $status = post('status') ?: 'active';

        if ($professorId > 0) {
            $row = db_one("SELECT user_id FROM professors WHERE professor_id = ?", 'i', [$professorId]);
            if (!empty($row['user_id'])) {
                db_run("UPDATE users SET full_name=?, email=?, status=? WHERE id=?", 'sssi', [$fullName, $email, $status, $row['user_id']]);
            } elseif ($fullName !== '' && $email !== '') {
                $username = strtolower(preg_replace('/[^a-z0-9]+/i', '', $fullName)) . rand(10, 99);
                $password = password_hash('professor123', PASSWORD_DEFAULT);
                db_run(
                    "INSERT INTO users (full_name, username, email, password, role, status)
                     VALUES (?, ?, ?, ?, 'professor', ?)",
                    'sssss',
                    [$fullName, $username, $email, $password, $status]
                );
                db_run("UPDATE professors SET user_id = ? WHERE professor_id = ?", 'ii', [$conn->insert_id, $professorId]);
            }
            db_run(
                "UPDATE professors
                 SET department_id=NULLIF(?,0), employee_number=?, qualification=?, specialization=?, designation=?, status=?
                 WHERE professor_id=?",
                'isssssi',
                [$departmentId, $employeeNumber, $qualification, $specialization, $designation, $status, $professorId]
            );
            flash_set('success', 'Professor updated.');
        } else {
            $username = strtolower(preg_replace('/[^a-z0-9]+/i', '', $fullName)) . rand(10, 99);
            $password = password_hash(post('password') ?: 'professor123', PASSWORD_DEFAULT);
            db_run(
                "INSERT INTO users (full_name, username, email, password, role, status)
                 VALUES (?, ?, ?, ?, 'professor', ?)",
                'sssss',
                [$fullName, $username, $email, $password, $status]
            );
            $userId = $conn->insert_id;
            db_run(
                "INSERT INTO professors
                 (user_id, department_id, employee_number, qualification, specialization, designation, status)
                 VALUES (?, NULLIF(?,0), ?, ?, ?, ?, ?)",
                'iisssss',
                [$userId, $departmentId, $employeeNumber, $qualification, $specialization, $designation, $status]
            );
            flash_set('success', 'Professor added.');
        }
    }

    header('Location: all-professors.php');
    exit;
}

$edit = null;
if (get_int('edit') > 0) {
    $edit = db_one(
        "SELECT p.*, u.full_name, u.email
         FROM professors p
         LEFT JOIN users u ON u.id = p.user_id
         WHERE p.professor_id = ?",
        'i',
        [get_int('edit')]
    );
}

$professors = db_all(
    "SELECT p.*, COALESCE(u.full_name, p.employee_number) AS full_name, u.email, d.department_name
     FROM professors p
     LEFT JOIN users u ON u.id = p.user_id
     LEFT JOIN departments d ON d.department_id = p.department_id
     ORDER BY full_name"
);

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 font-bold mb-3">Professors</h1>
            <?php include __DIR__ . '/partials/alert.php'; ?>

            <form method="POST" class="dashboard-card mb-3">
                <div class="dashboard-card-header"><h5 class="mb-0"><?php echo $edit ? 'Edit Professor' : 'Add Professor'; ?></h5></div>
                <div class="dashboard-card-body">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="professor_id" value="<?php echo (int) ($edit['professor_id'] ?? 0); ?>">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Full Name</label><input class="form-control" name="full_name" required value="<?php echo e($edit['full_name'] ?? ''); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required value="<?php echo e($edit['email'] ?? ''); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Employee Number</label><input class="form-control" name="employee_number" value="<?php echo e($edit['employee_number'] ?? ''); ?>"></div>
                        <div class="col-md-4">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department_id">
                                <option value="0">None</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?php echo (int) $department['department_id']; ?>" <?php echo ((int) ($edit['department_id'] ?? 0) === (int) $department['department_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($department['department_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label">Designation</label><input class="form-control" name="designation" value="<?php echo e($edit['designation'] ?? ''); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Specialization</label><input class="form-control" name="specialization" value="<?php echo e($edit['specialization'] ?? ''); ?>"></div>
                        <div class="col-md-6"><label class="form-label">Qualification</label><input class="form-control" name="qualification" value="<?php echo e($edit['qualification'] ?? ''); ?>"></div>
                        <?php if (!$edit): ?>
                            <div class="col-md-3"><label class="form-label">Password</label><input class="form-control" name="password" placeholder="Default: professor123"></div>
                        <?php endif; ?>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" <?php echo (($edit['status'] ?? 'active') === 'active') ? 'selected' : ''; ?>>active</option>
                                <option value="inactive" <?php echo (($edit['status'] ?? '') === 'inactive') ? 'selected' : ''; ?>>inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <?php if ($edit): ?><a class="btn btn-light" href="all-professors.php">Cancel</a><?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Name</th><th>Employee No</th><th>Department</th><th>Designation</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            <?php if (!$professors): ?>
                                <tr><td colspan="6" class="text-muted">No professors yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($professors as $professor): ?>
                                <tr>
                                    <td><?php echo e($professor['full_name']); ?></td>
                                    <td><?php echo e($professor['employee_number'] ?: '-'); ?></td>
                                    <td><?php echo e($professor['department_name'] ?: '-'); ?></td>
                                    <td><?php echo e($professor['designation'] ?: '-'); ?></td>
                                    <td><?php echo e($professor['status']); ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="professor-profile.php?id=<?php echo (int) $professor['professor_id']; ?>">View</a>
                                        <a class="btn btn-sm btn-outline-secondary" href="all-professors.php?edit=<?php echo (int) $professor['professor_id']; ?>">Edit</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this professor?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="professor_id" value="<?php echo (int) $professor['professor_id']; ?>">
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
