<?php
$pageTitle = 'Feedback';
include 'header.php';

$userId  = (int) $_SESSION['user_id'];
$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating       = post_int('rating');
    $courseName   = post('course_name');
    $feedbackText = post('feedback');

    if ($feedbackText === '') {
        $error = 'Please write your feedback.';
    } else {
        db_run(
            "INSERT INTO feedback (user_id, course_name, rating, feedback_text)
             VALUES (?, ?, NULLIF(?, 0), ?)",
            'isis',
            [$userId, $courseName, $rating, $feedbackText]
        );
        $success = 'Thank you. Your feedback was saved.';
    }
}

$courses = db_all(
    "SELECT c.course_name
     FROM enrollments e
     JOIN courses c ON c.course_id = e.course_id
     JOIN student_profiles sp ON sp.student_id = e.student_id
     WHERE sp.user_id = ?
     ORDER BY c.course_name",
    'i',
    [$userId]
);
?>

<div class="user-layout">
    <main class="feedback-page">
        <h2>Feedback</h2>
        <p class="page-text">Share your feedback with us</p>

        <?php if ($success): ?>
            <p style="color: #16a34a; margin: 10px 0; font-weight: 600;"><?php echo e($success); ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p style="color: #d9534f; margin: 10px 0; font-weight: 600;"><?php echo e($error); ?></p>
        <?php endif; ?>

        <section class="feedback-card">
            <form method="POST">
                <label>How was your experience?</label>
                <div class="rating">
                    <label><input type="radio" name="rating" value="5"> Excellent</label>
                    <label><input type="radio" name="rating" value="4"> Good</label>
                    <label><input type="radio" name="rating" value="3"> Average</label>
                    <label><input type="radio" name="rating" value="2"> Poor</label>
                </div>

                <label for="course_name">Course (optional)</label>
                <select id="course_name" name="course_name">
                    <option value="">General feedback</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo e($course['course_name']); ?>">
                            <?php echo e($course['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="feedback">Your Feedback</label>
                <textarea id="feedback" name="feedback" rows="6" placeholder="Write your feedback here..." required></textarea>
                <button type="submit">Submit Feedback</button>
            </form>
        </section>
    </main>
</div>
</body>
</html>
