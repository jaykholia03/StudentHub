<?php
$pageTitle = 'Dashboard';
include 'header.php';

$userId   = (int) $_SESSION['user_id'];
$userName = $_SESSION['full_name'] ?? 'Student';

$student = db_one(
    "SELECT u.full_name, u.status, sp.course, sp.semester, d.department_name, sp.student_id
     FROM users u
     LEFT JOIN student_profiles sp ON sp.user_id = u.id
     LEFT JOIN departments d ON d.department_id = sp.department_id
     WHERE u.id = ?",
    'i',
    [$userId]
);

$courseCount = db_count(
    "SELECT COUNT(*) AS total
     FROM enrollments e
     JOIN student_profiles sp ON sp.student_id = e.student_id
     WHERE sp.user_id = ? AND e.status = 'enrolled'",
    'i',
    [$userId]
);

$attendance = db_one(
    "SELECT
        COUNT(*) AS total,
        SUM(a.attendance_status = 'present') AS present_count
     FROM attendance a
     JOIN enrollments e ON e.enrollment_id = a.enrollment_id
     JOIN student_profiles sp ON sp.student_id = e.student_id
     WHERE sp.user_id = ?",
    'i',
    [$userId]
);

$totalClasses = (int) ($attendance['total'] ?? 0);
$presentCount = (int) ($attendance['present_count'] ?? 0);
$attendancePercent = $totalClasses > 0 ? round(($presentCount / $totalClasses) * 100) : 0;
$attendanceStatus = 'N/A';
if ($totalClasses > 0) {
    if ($attendancePercent >= 75) {
        $attendanceStatus = 'Good';
    } elseif ($attendancePercent >= 50) {
        $attendanceStatus = 'Average';
    } else {
        $attendanceStatus = 'Low';
    }
}

$eventCount = db_count("SELECT COUNT(*) AS total FROM events WHERE status = 'published'");
$messageCount = db_count(
    "SELECT COUNT(*) AS total FROM contact_messages WHERE user_id = ?",
    'i',
    [$userId]
);

$courses = db_all(
    "SELECT c.course_code, c.course_name, c.schedule_days, c.room
     FROM enrollments e
     JOIN courses c ON c.course_id = e.course_id
     JOIN student_profiles sp ON sp.student_id = e.student_id
     WHERE sp.user_id = ? AND e.status = 'enrolled'
     ORDER BY c.course_name",
    'i',
    [$userId]
);
?>

<div class="user-layout">
    <main class="dashboard">
        <section class="welcome">
            <div>
                <h1>Welcome back, <?php echo e($userName); ?>!</h1>
                <p>Manage your student activities from one place.</p>
            </div>
        </section>

        <section class="dashboard-stats">
            <div class="dashboard-box">
                <h3>Courses</h3>
                <h2><?php echo str_pad((string) $courseCount, 2, '0', STR_PAD_LEFT); ?></h2>
                <p>Current enrollments</p>
            </div>
            <div class="dashboard-box">
                <h3>Attendance</h3>
                <h2><?php echo $attendancePercent; ?>%</h2>
                <p><?php echo e($attendanceStatus); ?></p>
            </div>
            <div class="dashboard-box">
                <h3>Events</h3>
                <h2><?php echo str_pad((string) $eventCount, 2, '0', STR_PAD_LEFT); ?></h2>
                <p>Published events</p>
            </div>
            <div class="dashboard-box">
                <h3>Messages</h3>
                <h2><?php echo str_pad((string) $messageCount, 2, '0', STR_PAD_LEFT); ?></h2>
                <p>Sent to admin</p>
            </div>
        </section>

        <section class="dashboard-content">
            <div class="dashboard-card">
                <h2>My Courses</h2>
                <?php if ($courses): ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="activity">
                            <span>✓</span>
                            <div>
                                <strong><?php echo e($course['course_code'] . ' - ' . $course['course_name']); ?></strong>
                                <p>
                                    <?php echo e($course['schedule_days'] ?: 'Schedule not set'); ?>
                                    <?php echo $course['room'] ? ' | Room: ' . e($course['room']) : ''; ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #64748b; padding: 14px 0;">No course enrollments yet.</p>
                <?php endif; ?>
            </div>

            <div class="dashboard-card">
                <h2>Student Information</h2>
                <div class="info-row">
                    <span>Name</span>
                    <strong><?php echo e($student['full_name'] ?? 'N/A'); ?></strong>
                </div>
                <div class="info-row">
                    <span>Department</span>
                    <strong><?php echo e($student['department_name'] ?? 'N/A'); ?></strong>
                </div>
                <div class="info-row">
                    <span>Course</span>
                    <strong><?php echo e($student['course'] ?? 'N/A'); ?></strong>
                </div>
                <div class="info-row">
                    <span>Semester</span>
                    <strong><?php echo e($student['semester'] ?? 'N/A'); ?></strong>
                </div>
                <div class="info-row">
                    <span>Status</span>
                    <strong class="active"><?php echo e($student['status'] ?? 'active'); ?></strong>
                </div>
            </div>
        </section>

        <section class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-container">
                <a href="profile.php">👤<span>My Profile</span></a>
                <a href="feedback.php">💬<span>Give Feedback</span></a>
                <a href="faq.php">❓<span>Help &amp; FAQ</span></a>
            </div>
        </section>
    </main>
</div>
</body>
</html>
