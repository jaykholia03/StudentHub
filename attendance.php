<?php
$pageTitle = 'Attendance';
include 'header.php';

$userId = (int) $_SESSION['user_id'];

$summary = db_one(
    "SELECT
        COUNT(*) AS total,
        SUM(a.attendance_status = 'present') AS present_count,
        SUM(a.attendance_status = 'absent') AS absent_count,
        SUM(a.attendance_status = 'late') AS late_count
     FROM attendance a
     JOIN enrollments e ON e.enrollment_id = a.enrollment_id
     JOIN student_profiles sp ON sp.student_id = e.student_id
     WHERE sp.user_id = ?",
    'i',
    [$userId]
);

$totalClasses   = (int) ($summary['total'] ?? 0);
$presentCount   = (int) ($summary['present_count'] ?? 0);
$absentCount    = (int) ($summary['absent_count'] ?? 0);
$lateCount      = (int) ($summary['late_count'] ?? 0);
$overallPercent = $totalClasses > 0 ? round(($presentCount / $totalClasses) * 100) : 0;

$subjectAttendance = db_all(
    "SELECT
        c.course_name AS subject_name,
        COUNT(a.attendance_id) AS total_classes,
        SUM(a.attendance_status IN ('present', 'late')) AS attended
     FROM attendance a
     JOIN enrollments e ON e.enrollment_id = a.enrollment_id
     JOIN courses c ON c.course_id = e.course_id
     JOIN student_profiles sp ON sp.student_id = e.student_id
     WHERE sp.user_id = ?
     GROUP BY c.course_id, c.course_name
     ORDER BY c.course_name",
    'i',
    [$userId]
);
?>

<div class="user-layout">
    <main class="attendance-page">
        <h2>Attendance</h2>
        <p class="page-text">Your attendance summary</p>

        <section class="attendance-summary">
            <div class="overall">
                <span>Overall Attendance</span>
                <strong><?php echo $overallPercent; ?>%</strong>
            </div>
            <div class="summary-item">
                <span>Total</span>
                <strong><?php echo str_pad((string) $totalClasses, 2, '0', STR_PAD_LEFT); ?></strong>
            </div>
            <div class="summary-item">
                <span>Present</span>
                <strong><?php echo str_pad((string) $presentCount, 2, '0', STR_PAD_LEFT); ?></strong>
            </div>
            <div class="summary-item">
                <span>Absent</span>
                <strong><?php echo str_pad((string) $absentCount, 2, '0', STR_PAD_LEFT); ?></strong>
            </div>
            <div class="summary-item">
                <span>Late</span>
                <strong><?php echo str_pad((string) $lateCount, 2, '0', STR_PAD_LEFT); ?></strong>
            </div>
        </section>

        <section class="subject-card">
            <h3>Course Attendance</h3>
            <?php if ($subjectAttendance): ?>
                <?php foreach ($subjectAttendance as $sub): ?>
                    <?php
                    $percent = $sub['total_classes'] > 0
                        ? round(($sub['attended'] / $sub['total_classes']) * 100)
                        : 0;
                    ?>
                    <div class="subject-row">
                        <div>
                            <strong><?php echo e($sub['subject_name']); ?></strong>
                            <span><?php echo (int) $sub['attended']; ?> / <?php echo (int) $sub['total_classes']; ?> classes</span>
                        </div>
                        <b><?php echo $percent; ?>%</b>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #64748b; padding: 14px 0;">No attendance records found.</p>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
