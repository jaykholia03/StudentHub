<?php
$pageTitle = 'Add Course';
require __DIR__ . '/bootstrap.php';

$departments = db_all("SELECT department_id, department_name FROM departments ORDER BY department_name");
$professors = db_all(
    "SELECT p.professor_id, COALESCE(u.full_name, p.employee_number) AS professor_name
     FROM professors p
     LEFT JOIN users u ON u.id = p.user_id
     ORDER BY professor_name"
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    db_run(
        "INSERT INTO courses
         (department_id, professor_id, course_code, course_name, description, credits, semester,
          academic_year, schedule_days, start_time, end_time, room, status)
         VALUES (NULLIF(?,0), NULLIF(?,0), ?, ?, ?, NULLIF(?,0), NULLIF(?,0), ?, ?, NULLIF(?,''), NULLIF(?,''), ?, ?)",
        'iisssiissssss',
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
        ]
    );
    flash_set('success', 'Course added.');
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
            <h1 class="h3 fw-bold mb-3">Add Course</h1>
            <form method="POST" class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Course Code</label>
                            <input class="form-control" name="course_code" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Course Name</label>
                            <input class="form-control" name="course_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department_id">
                                <option value="0">None</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?php echo (int) $department['department_id']; ?>"><?php echo e($department['department_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Professor</label>
                            <select class="form-select" name="professor_id">
                                <option value="0">None</option>
                                <?php foreach ($professors as $professor): ?>
                                    <option value="<?php echo (int) $professor['professor_id']; ?>"><?php echo e($professor['professor_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-3"><label class="form-label">Credits</label><input class="form-control" type="number" name="credits"></div>
                        <div class="col-md-3"><label class="form-label">Semester</label><input class="form-control" type="number" name="semester" min="1" max="8"></div>
                        <div class="col-md-3"><label class="form-label">Academic Year</label><input class="form-control" name="academic_year" placeholder="2026-27"></div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active">active</option>
                                <option value="inactive">inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label">Days</label><input class="form-control" name="schedule_days" placeholder="Mon, Wed"></div>
                        <div class="col-md-3"><label class="form-label">Start Time</label><input class="form-control" type="time" name="start_time"></div>
                        <div class="col-md-3"><label class="form-label">End Time</label><input class="form-control" type="time" name="end_time"></div>
                        <div class="col-md-2"><label class="form-label">Room</label><input class="form-control" name="room"></div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Save Course</button>
                        <a class="btn btn-light" href="all-courses.php">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
<?php include __DIR__ . '/partials/scripts.php'; ?>
