<?php
$pageTitle = 'Edit Course';
require __DIR__ . '/bootstrap.php';

$id = get_int('id');
$course = db_one("SELECT * FROM courses WHERE course_id = ?", 'i', [$id]);
if (!$course) {
    flash_set('error', 'Course not found.');
    header('Location: all-courses.php');
    exit;
}

$departments = db_all("SELECT department_id, department_name FROM departments ORDER BY department_name");
$professors = db_all(
    "SELECT p.professor_id, COALESCE(u.full_name, p.employee_number) AS professor_name
     FROM professors p
     LEFT JOIN users u ON u.id = p.user_id
     ORDER BY professor_name"
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    db_run(
        "UPDATE courses SET
            department_id = NULLIF(?,0), professor_id = NULLIF(?,0), course_code = ?, course_name = ?,
            description = ?, credits = NULLIF(?,0), semester = NULLIF(?,0), academic_year = ?,
            schedule_days = ?, start_time = NULLIF(?,''), end_time = NULLIF(?,''), room = ?, status = ?
         WHERE course_id = ?",
        'iisssiissssssi',
        [
            post_int('department_id'),
            post_int('professor_id'),
            post('course_code'),
            post('course_name'),
            post('description'),
            post_int('credits'),
            post_int('semester'),
            post('academic_year'),
            post('schedule_days'),
            post('start_time'),
            post('end_time'),
            post('room'),
            post('status') ?: 'active',
            $id,
        ]
    );
    flash_set('success', 'Course updated.');
    header('Location: all-courses.php');
    exit;
}

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 fw-bold mb-3">Edit Course</h1>
            <form method="POST" class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Course Code</label>
                            <input class="form-control" name="course_code" value="<?php echo e($course['course_code']); ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Course Name</label>
                            <input class="form-control" name="course_name" value="<?php echo e($course['course_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department_id">
                                <option value="0">None</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?php echo (int) $department['department_id']; ?>" <?php echo ((int) $course['department_id'] === (int) $department['department_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($department['department_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Professor</label>
                            <select class="form-select" name="professor_id">
                                <option value="0">None</option>
                                <?php foreach ($professors as $professor): ?>
                                    <option value="<?php echo (int) $professor['professor_id']; ?>" <?php echo ((int) $course['professor_id'] === (int) $professor['professor_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($professor['professor_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"><?php echo e($course['description']); ?></textarea>
                        </div>
                        <div class="col-md-3"><label class="form-label">Credits</label><input class="form-control" type="number" name="credits" value="<?php echo e($course['credits']); ?>"></div>
                        <div class="col-md-3"><label class="form-label">Semester</label><input class="form-control" type="number" name="semester" value="<?php echo e($course['semester']); ?>"></div>
                        <div class="col-md-3"><label class="form-label">Academic Year</label><input class="form-control" name="academic_year" value="<?php echo e($course['academic_year']); ?>"></div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" <?php echo $course['status'] === 'active' ? 'selected' : ''; ?>>active</option>
                                <option value="inactive" <?php echo $course['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label">Days</label><input class="form-control" name="schedule_days" value="<?php echo e($course['schedule_days']); ?>"></div>
                        <div class="col-md-3"><label class="form-label">Start Time</label><input class="form-control" type="time" name="start_time" value="<?php echo e(substr((string) $course['start_time'], 0, 5)); ?>"></div>
                        <div class="col-md-3"><label class="form-label">End Time</label><input class="form-control" type="time" name="end_time" value="<?php echo e(substr((string) $course['end_time'], 0, 5)); ?>"></div>
                        <div class="col-md-2"><label class="form-label">Room</label><input class="form-control" name="room" value="<?php echo e($course['room']); ?>"></div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Update Course</button>
                        <a class="btn btn-light" href="all-courses.php">Back</a>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
<?php include __DIR__ . '/partials/scripts.php'; ?>
