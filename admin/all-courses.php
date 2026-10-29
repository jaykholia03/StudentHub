<?php
$pageTitle = 'All Courses';
require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'delete') {
    db_run("DELETE FROM courses WHERE course_id = ?", 'i', [post_int('course_id')]);
    flash_set('success', 'Course deleted.');
    header('Location: all-courses.php');
    exit;
}

$courses = db_all(
    "SELECT c.*, d.department_name, COALESCE(u.full_name, p.employee_number) AS professor_name,
            (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.course_id) AS enrolled
     FROM courses c
     LEFT JOIN departments d ON d.department_id = c.department_id
     LEFT JOIN professors p ON p.professor_id = c.professor_id
     LEFT JOIN users u ON u.id = p.user_id
     ORDER BY c.course_name"
);

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 font-bold mb-1">All Courses</h1>
                    <p class="text-muted mb-0">Create, edit and delete courses.</p>
                </div>
                <a href="add-course.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Add Course</a>
            </div>
            <?php include __DIR__ . '/partials/alert.php'; ?>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Professor</th>
                                <th>Enrolled</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$courses): ?>
                                <tr><td colspan="7" class="text-muted">No courses yet. Click Add Course.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?php echo e($course['course_code']); ?></td>
                                    <td><?php echo e($course['course_name']); ?></td>
                                    <td><?php echo e($course['department_name'] ?: '-'); ?></td>
                                    <td><?php echo e($course['professor_name'] ?: '-'); ?></td>
                                    <td><?php echo (int) $course['enrolled']; ?></td>
                                    <td><span class="badge bg-<?php echo $course['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo e($course['status']); ?></span></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="course-info.php?id=<?php echo (int) $course['course_id']; ?>">View</a>
                                        <a class="btn btn-sm btn-outline-secondary" href="edit-course.php?id=<?php echo (int) $course['course_id']; ?>">Edit</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this course?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="course_id" value="<?php echo (int) $course['course_id']; ?>">
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
