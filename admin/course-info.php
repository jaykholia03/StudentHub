<?php
$pageTitle = 'Course Info';
require __DIR__ . '/bootstrap.php';

$id = get_int('id');
$course = db_one(
    "SELECT c.*, d.department_name, COALESCE(u.full_name, p.employee_number) AS professor_name
     FROM courses c
     LEFT JOIN departments d ON d.department_id = c.department_id
     LEFT JOIN professors p ON p.professor_id = c.professor_id
     LEFT JOIN users u ON u.id = p.user_id
     WHERE c.course_id = ?",
    'i',
    [$id]
);

if (!$course) {
    flash_set('error', 'Course not found.');
    header('Location: all-courses.php');
    exit;
}

$students = db_all(
    "SELECT u.full_name, sp.student_number, e.status, e.enrolled_at
     FROM enrollments e
     JOIN student_profiles sp ON sp.student_id = e.student_id
     JOIN users u ON u.id = sp.user_id
     WHERE e.course_id = ?
     ORDER BY u.full_name",
    'i',
    [$id]
);

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb-3">
                <h1 class="h3 fw-bold"><?php echo e($course['course_code'] . ' - ' . $course['course_name']); ?></h1>
                <a class="btn btn-outline-secondary" href="edit-course.php?id=<?php echo (int) $id; ?>">Edit</a>
            </div>
            <div class="dashboard-card mb-3">
                <div class="dashboard-card-body">
                    <p><strong>Department:</strong> <?php echo e($course['department_name'] ?: '-'); ?></p>
                    <p><strong>Professor:</strong> <?php echo e($course['professor_name'] ?: '-'); ?></p>
                    <p><strong>Credits:</strong> <?php echo e($course['credits'] ?: '-'); ?></p>
                    <p><strong>Schedule:</strong> <?php echo e($course['schedule_days'] ?: '-'); ?> <?php echo e($course['start_time']); ?> - <?php echo e($course['end_time']); ?></p>
                    <p><strong>Room:</strong> <?php echo e($course['room'] ?: '-'); ?></p>
                    <p class="mb-0"><?php echo e($course['description'] ?: 'No description.'); ?></p>
                </div>
            </div>
            <div class="dashboard-card">
                <div class="dashboard-card-header"><h5 class="mb-0">Enrolled Students</h5></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Name</th><th>Student No</th><th>Status</th><th>Enrolled</th></tr></thead>
                        <tbody>
                            <?php if (!$students): ?>
                                <tr><td colspan="4" class="text-muted">No enrollments.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo e($student['full_name']); ?></td>
                                    <td><?php echo e($student['student_number'] ?: '-'); ?></td>
                                    <td><?php echo e($student['status']); ?></td>
                                    <td><?php echo e($student['enrolled_at']); ?></td>
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
