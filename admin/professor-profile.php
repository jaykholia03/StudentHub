<?php
$pageTitle = 'Professor Profile';
require __DIR__ . '/bootstrap.php';

$id = get_int('id');
$professor = db_one(
    "SELECT p.*, COALESCE(u.full_name, p.employee_number) AS full_name, u.email, d.department_name
     FROM professors p
     LEFT JOIN users u ON u.id = p.user_id
     LEFT JOIN departments d ON d.department_id = p.department_id
     WHERE p.professor_id = ?",
    'i',
    [$id]
);

if (!$professor) {
    flash_set('error', 'Professor not found.');
    header('Location: all-professors.php');
    exit;
}

$courses = db_all("SELECT course_code, course_name, status FROM courses WHERE professor_id = ? ORDER BY course_name", 'i', [$id]);

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 fw-bold mb-3"><?php echo e($professor['full_name']); ?></h1>
            <div class="dashboard-card mb-3">
                <div class="dashboard-card-body">
                    <p><strong>Employee No:</strong> <?php echo e($professor['employee_number'] ?: '-'); ?></p>
                    <p><strong>Email:</strong> <?php echo e($professor['email'] ?: '-'); ?></p>
                    <p><strong>Department:</strong> <?php echo e($professor['department_name'] ?: '-'); ?></p>
                    <p><strong>Designation:</strong> <?php echo e($professor['designation'] ?: '-'); ?></p>
                    <p><strong>Specialization:</strong> <?php echo e($professor['specialization'] ?: '-'); ?></p>
                    <p class="mb-0"><?php echo e($professor['bio'] ?: 'No bio added.'); ?></p>
                </div>
            </div>
                <div class="dashboard-card">
                <div class="dashboard-card-header"><h5 class="mb-0">Assigned Courses</h5></div>
                    <div class="dashboard-card-body">
                    <?php if (!$courses): ?>
                        <p class="text-muted">No courses assigned.</p>
                    <?php endif; ?>
                    <?php foreach ($courses as $course): ?>
                        <p><?php echo e($course['course_code'] . ' - ' . $course['course_name']); ?></p>
                    <?php endforeach; ?>
                    <a class="btn btn-outline-secondary" href="all-professors.php?edit=<?php echo (int) $id; ?>">Edit professor</a>
                                </div>
                            </div>
                        </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
<?php include __DIR__ . '/partials/scripts.php'; ?>
