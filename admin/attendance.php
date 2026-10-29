<?php
$pageTitle = 'Attendance';
require __DIR__ . '/bootstrap.php';

$enrollments = db_all(
    "SELECT e.enrollment_id, u.full_name, c.course_name
     FROM enrollments e
     JOIN student_profiles sp ON sp.student_id = e.student_id
     JOIN users u ON u.id = sp.user_id
     JOIN courses c ON c.course_id = e.course_id
     ORDER BY u.full_name, c.course_name"
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post('action');

    if ($action === 'delete') {
        db_run("DELETE FROM attendance WHERE attendance_id = ?", 'i', [post_int('attendance_id')]);
        flash_set('success', 'Attendance record deleted.');
    } elseif ($action === 'save') {
        db_run(
            "INSERT INTO attendance (enrollment_id, attendance_date, attendance_status, remarks)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE attendance_status = VALUES(attendance_status), remarks = VALUES(remarks)",
            'isss',
            [post_int('enrollment_id'), post('attendance_date'), post('attendance_status'), post('remarks')]
        );
        flash_set('success', 'Attendance saved.');
    }

    header('Location: attendance.php');
    exit;
}

$records = db_all(
    "SELECT a.*, u.full_name, c.course_name
     FROM attendance a
     JOIN enrollments e ON e.enrollment_id = a.enrollment_id
     JOIN student_profiles sp ON sp.student_id = e.student_id
     JOIN users u ON u.id = sp.user_id
     JOIN courses c ON c.course_id = e.course_id
     ORDER BY a.attendance_date DESC, u.full_name
     LIMIT 100"
);

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 font-bold mb-3">Attendance</h1>
            <?php include __DIR__ . '/partials/alert.php'; ?>

            <form method="POST" class="dashboard-card mb-3">
                <div class="dashboard-card-header"><h5 class="mb-0">Mark Attendance</h5></div>
                <div class="dashboard-card-body">
                    <input type="hidden" name="action" value="save">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Student / Course</label>
                            <select class="form-select" name="enrollment_id" required>
                                <option value="">Select enrollment</option>
                                <?php foreach ($enrollments as $enrollment): ?>
                                    <option value="<?php echo (int) $enrollment['enrollment_id']; ?>">
                                        <?php echo e($enrollment['full_name'] . ' - ' . $enrollment['course_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2"><label class="form-label">Date</label><input class="form-control" type="date" name="attendance_date" required value="<?php echo date('Y-m-d'); ?>"></div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="attendance_status">
                                <option value="present">present</option>
                                <option value="absent">absent</option>
                                <option value="late">late</option>
                                <option value="excused">excused</option>
                            </select>
                        </div>
                        <div class="col-md-3"><label class="form-label">Remarks</label><input class="form-control" name="remarks"></div>
                    </div>
                    <button class="btn btn-primary mt-3" type="submit">Save</button>
                </div>
            </form>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Date</th><th>Student</th><th>Course</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            <?php if (!$records): ?>
                                <tr><td colspan="5" class="text-muted">No attendance records yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo e($record['attendance_date']); ?></td>
                                    <td><?php echo e($record['full_name']); ?></td>
                                    <td><?php echo e($record['course_name']); ?></td>
                                    <td><?php echo e($record['attendance_status']); ?></td>
                                    <td class="text-end">
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="attendance_id" value="<?php echo (int) $record['attendance_id']; ?>">
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
