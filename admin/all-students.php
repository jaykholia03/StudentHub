<?php
$pageTitle = 'All Students';
require __DIR__ . '/bootstrap.php';

$departments = db_all("SELECT department_id, department_name FROM departments ORDER BY department_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post('action');

    if ($action === 'delete') {
        db_run("DELETE FROM users WHERE id = ? AND role = 'student'", 'i', [post_int('user_id')]);
        flash_set('success', 'Student deleted.');
    } elseif ($action === 'save') {
        $userId = post_int('user_id');
        $fullName = post('full_name');
        $username = post('username');
        $email = post('email');
        $phone = post('phone');
        $studentNumber = post('student_number');
        $departmentId = post_int('department_id');
        $course = post('course');
        $status = post('status') ?: 'active';

        if ($userId > 0) {
            db_run(
                "UPDATE users SET full_name=?, username=?, email=?, phone=?, status=? WHERE id=? AND role='student'",
                'sssssi',
                [$fullName, $username, $email, $phone, $status, $userId]
            );
            db_run(
                "UPDATE student_profiles SET student_number=NULLIF(?,''), department_id=NULLIF(?,0), course=? WHERE user_id=?",
                'sisi',
                [$studentNumber, $departmentId, $course, $userId]
            );
            flash_set('success', 'Student updated.');
        } else {
            $password = password_hash(post('password') ?: 'student123', PASSWORD_DEFAULT);
            db_run(
                "INSERT INTO users (full_name, username, email, phone, password, role, status)
                 VALUES (?, ?, ?, ?, ?, 'student', ?)",
                'ssssss',
                [$fullName, $username, $email, $phone, $password, $status]
            );
            $newId = $conn->insert_id;
            db_run(
                "INSERT INTO student_profiles (user_id, student_number, department_id, course)
                 VALUES (?, NULLIF(?,''), NULLIF(?,0), ?)",
                'isis',
                [$newId, $studentNumber, $departmentId, $course]
            );
            flash_set('success', 'Student added.');
        }
    }

    header('Location: all-students.php');
    exit;
}

$edit = null;
if (get_int('edit') > 0) {
    $edit = db_one(
        "SELECT u.*, sp.student_number, sp.department_id, sp.course
         FROM users u
         LEFT JOIN student_profiles sp ON sp.user_id = u.id
         WHERE u.id = ? AND u.role = 'student'",
        'i',
        [get_int('edit')]
    );
}

$students = db_all(
    "SELECT u.id, u.full_name, u.email, u.phone, u.status, sp.student_number, sp.course, d.department_name
     FROM users u
     LEFT JOIN student_profiles sp ON sp.user_id = u.id
     LEFT JOIN departments d ON d.department_id = sp.department_id
     WHERE u.role = 'student'
     ORDER BY u.full_name"
);

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 font-bold mb-3">Students</h1>
            <?php include __DIR__ . '/partials/alert.php'; ?>

            <form method="POST" class="dashboard-card mb-3">
                <div class="dashboard-card-header"><h5 class="mb-0"><?php echo $edit ? 'Edit Student' : 'Add Student'; ?></h5></div>
                <div class="dashboard-card-body">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="user_id" value="<?php echo (int) ($edit['id'] ?? 0); ?>">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Full Name</label><input class="form-control" name="full_name" required value="<?php echo e($edit['full_name'] ?? ''); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Username</label><input class="form-control" name="username" required value="<?php echo e($edit['username'] ?? ''); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required value="<?php echo e($edit['email'] ?? ''); ?>"></div>
                        <div class="col-md-3"><label class="form-label">Phone</label><input class="form-control" name="phone" value="<?php echo e($edit['phone'] ?? ''); ?>"></div>
                        <div class="col-md-3"><label class="form-label">Student Number</label><input class="form-control" name="student_number" value="<?php echo e($edit['student_number'] ?? ''); ?>"></div>
                        <div class="col-md-3">
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
                        <div class="col-md-3"><label class="form-label">Course</label><input class="form-control" name="course" value="<?php echo e($edit['course'] ?? ''); ?>"></div>
                        <?php if (!$edit): ?>
                            <div class="col-md-4"><label class="form-label">Password</label><input class="form-control" name="password" placeholder="Default: student123"></div>
                        <?php endif; ?>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <?php foreach (['active', 'inactive', 'blocked'] as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo (($edit['status'] ?? 'active') === $status) ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <?php if ($edit): ?><a class="btn btn-light" href="all-students.php">Cancel</a><?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Name</th><th>Student No</th><th>Department</th><th>Email</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            <?php if (!$students): ?>
                                <tr><td colspan="6" class="text-muted">No students yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo e($student['full_name']); ?></td>
                                    <td><?php echo e($student['student_number'] ?: '-'); ?></td>
                                    <td><?php echo e($student['department_name'] ?: $student['course'] ?: '-'); ?></td>
                                    <td><?php echo e($student['email']); ?></td>
                                    <td><?php echo e($student['status']); ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="student-profile.php?id=<?php echo (int) $student['id']; ?>">View</a>
                                        <a class="btn btn-sm btn-outline-secondary" href="all-students.php?edit=<?php echo (int) $student['id']; ?>">Edit</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this student?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo (int) $student['id']; ?>">
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
