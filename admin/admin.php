<?php
$pageTitle = 'Dashboard';
require __DIR__ . '/bootstrap.php';

$stats = [
    'students'    => db_count("SELECT COUNT(*) AS total FROM users WHERE role = 'student'"),
    'professors'  => db_count("SELECT COUNT(*) AS total FROM professors"),
    'courses'     => db_count("SELECT COUNT(*) AS total FROM courses"),
    'departments' => db_count("SELECT COUNT(*) AS total FROM departments"),
];

$recentStudents = db_all(
    "SELECT u.full_name, u.status, u.created_at, d.department_name, sp.course
     FROM users u
     LEFT JOIN student_profiles sp ON sp.user_id = u.id
     LEFT JOIN departments d ON d.department_id = sp.department_id
     WHERE u.role = 'student'
     ORDER BY u.created_at DESC
     LIMIT 5"
);

$popularCourses = db_all(
    "SELECT c.course_name, c.course_code, COUNT(e.enrollment_id) AS enrolled,
            COALESCE(u.full_name, p.employee_number) AS professor_name
     FROM courses c
     LEFT JOIN enrollments e ON e.course_id = c.course_id AND e.status = 'enrolled'
     LEFT JOIN professors p ON p.professor_id = c.professor_id
     LEFT JOIN users u ON u.id = p.user_id
     GROUP BY c.course_id, c.course_name, c.course_code, professor_name
     ORDER BY enrolled DESC, c.course_name
     LIMIT 5"
);

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <div class="mb-3">
                <h1 class="h3 font-bold">Dashboard Overview</h1>
                <p class="text-muted text-sm">Live counts from the StudentHub database. No dummy numbers.</p>
            </div>
            <?php include __DIR__ . '/partials/alert.php'; ?>

            <div class="dashboard-row">
                <div class="dashboard-grid grid-cols-4">
                    <div class="stats-card">
                        <div class="stats-card-label">Students</div>
                        <div class="stats-card-value"><?php echo (int) $stats['students']; ?></div>
                    </div>
                    <div class="stats-card">
                        <div class="stats-card-label">Professors</div>
                        <div class="stats-card-value"><?php echo (int) $stats['professors']; ?></div>
                    </div>
                    <div class="stats-card">
                        <div class="stats-card-label">Courses</div>
                        <div class="stats-card-value"><?php echo (int) $stats['courses']; ?></div>
                    </div>
                    <div class="stats-card">
                        <div class="stats-card-label">Departments</div>
                        <div class="stats-card-value"><?php echo (int) $stats['departments']; ?></div>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid grid-cols-2">
                <div class="dashboard-card">
                    <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                        <h5 class="dashboard-card-title mb-0">Recent Students</h5>
                        <a href="all-students.php" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                    <div class="dashboard-card-body">
                        <?php if (!$recentStudents): ?>
                            <p class="text-muted mb-0">No students found.</p>
                        <?php endif; ?>
                        <?php foreach ($recentStudents as $student): ?>
                            <div class="d-flex align-items-center py-2 border-bottom">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo e($student['full_name']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo e($student['department_name'] ?: $student['course'] ?: 'No department'); ?>
                                    </small>
                                </div>
                                <span class="badge bg-success"><?php echo e($student['status']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                        <h5 class="dashboard-card-title mb-0">Courses</h5>
                        <a href="all-courses.php" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                    <div class="dashboard-card-body">
                        <?php if (!$popularCourses): ?>
                            <p class="text-muted mb-0">No courses found.</p>
                        <?php endif; ?>
                        <?php foreach ($popularCourses as $course): ?>
                            <div class="d-flex align-items-center py-2 border-bottom">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo e($course['course_code'] . ' - ' . $course['course_name']); ?></h6>
                                    <small class="text-muted"><?php echo e($course['professor_name'] ?: 'No professor'); ?></small>
                                </div>
                                <span class="badge bg-primary"><?php echo (int) $course['enrolled']; ?> enrolled</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
<?php include __DIR__ . '/partials/scripts.php'; ?>
