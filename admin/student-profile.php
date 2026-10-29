<?php
$pageTitle = 'Student Profile';
require __DIR__ . '/bootstrap.php';

$id = get_int('id');
$student = db_one(
    "SELECT u.*, sp.*, d.department_name
     FROM users u
     LEFT JOIN student_profiles sp ON sp.user_id = u.id
     LEFT JOIN departments d ON d.department_id = sp.department_id
     WHERE u.id = ? AND u.role = 'student'",
    'i',
    [$id]
);

if (!$student) {
    flash_set('error', 'Student not found.');
    header('Location: all-students.php');
    exit;
}

$courses = [];
if (!empty($student['student_id'])) {
    $courses = db_all(
        "SELECT c.course_code, c.course_name, e.status
         FROM enrollments e
         JOIN courses c ON c.course_id = e.course_id
         WHERE e.student_id = ?",
        'i',
        [$student['student_id']]
    );
}

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 fw-bold mb-3"><?php echo e($student['full_name']); ?></h1>
            <div class="dashboard-card mb-3">
                            <div class="dashboard-card-body">
                    <p><strong>Student No:</strong> <?php echo e($student['student_number'] ?: '-'); ?></p>
                    <p><strong>Email:</strong> <?php echo e($student['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo e($student['phone'] ?: '-'); ?></p>
                    <p><strong>Department:</strong> <?php echo e($student['department_name'] ?: '-'); ?></p>
                    <p><strong>Course:</strong> <?php echo e($student['course'] ?: '-'); ?></p>
                    <p><strong>Semester:</strong> <?php echo e($student['semester'] ?: '-'); ?></p>
                    <p class="mb-0"><strong>Status:</strong> <?php echo e($student['status']); ?></p>
                            </div>
                        </div>
                        <div class="dashboard-card">
                <div class="dashboard-card-header"><h5 class="mb-0">Enrollments</h5></div>
                            <div class="dashboard-card-body">
                    <?php if (!$courses): ?>
                        <p class="text-muted mb-0">No enrollments.</p>
                    <?php endif; ?>
                    <?php foreach ($courses as $course): ?>
                        <p><?php echo e($course['course_code'] . ' - ' . $course['course_name']); ?> (<?php echo e($course['status']); ?>)</p>
                    <?php endforeach; ?>
                    <a class="btn btn-outline-secondary" href="all-students.php?edit=<?php echo (int) $id; ?>">Edit student</a>
                </div>
                </div>
            </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
<?php include __DIR__ . '/partials/scripts.php'; ?>
