<?php
$pageTitle = 'Profile';
include 'header.php';

$userId  = (int) $_SESSION['user_id'];
$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName      = post('full_name');
    $email         = post('email');
    $phone         = post('phone');
    $dob           = empty_to_null(post('dob'));
    $gender        = empty_to_null(post('gender'));
    $address       = post('address');
    $course        = post('course');
    $departmentId  = post_int('department_id');
    $semester      = post_int('semester');
    $division      = post('division');
    $admissionYear = post_int('admission_year');
    $college       = post('college');

    db_run(
        "UPDATE users
         SET full_name = ?, email = ?, phone = ?, dob = ?, gender = ?, address = ?
         WHERE id = ?",
        'ssssssi',
        [$fullName, $email, $phone, $dob, $gender, $address, $userId]
    );

    $profile = db_one("SELECT student_id FROM student_profiles WHERE user_id = ?", 'i', [$userId]);
    if ($profile) {
        db_run(
            "UPDATE student_profiles
             SET department_id = NULLIF(?, 0), course = ?, semester = NULLIF(?, 0),
                 division = ?, admission_year = NULLIF(?, 0), college = ?
             WHERE user_id = ?",
            'isisssi',
            [$departmentId, $course, $semester, $division, $admissionYear, $college, $userId]
        );
    } else {
        db_run(
            "INSERT INTO student_profiles
             (user_id, department_id, course, semester, division, admission_year, college)
             VALUES (?, NULLIF(?, 0), ?, NULLIF(?, 0), ?, NULLIF(?, 0), ?)",
            'iisissi',
            [$userId, $departmentId, $course, $semester, $division, $admissionYear, $college]
        );
    }

    $_SESSION['full_name'] = $fullName;
    $_SESSION['email'] = $email;
    $success = 'Profile updated successfully!';
}

$userData = db_one(
    "SELECT u.*, sp.student_number, sp.department_id, sp.course, sp.semester,
            sp.division, sp.college, sp.admission_year
     FROM users u
     LEFT JOIN student_profiles sp ON sp.user_id = u.id
     WHERE u.id = ?",
    'i',
    [$userId]
);

$departments = db_all("SELECT department_id, department_name FROM departments WHERE status = 'active' ORDER BY department_name");
?>

<div class="user-layout">
    <main class="profile-page">
        <h2>Profile</h2>

        <?php if ($success): ?>
            <p style="color: #16a34a; margin: 10px 0; font-weight: 600;"><?php echo e($success); ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p style="color: #d9534f; margin: 10px 0; font-weight: 600;"><?php echo e($error); ?></p>
        <?php endif; ?>

        <section class="profile-card">
            <div class="profile-top">
                <img src="images/profile-placeholder.svg" alt="Profile picture" class="profile-image">
                <div>
                    <h3><?php echo e($userData['full_name'] ?? ''); ?></h3>
                    <p>Student No: <?php echo e($userData['student_number'] ?? 'Not assigned'); ?></p>
                </div>
            </div>

            <form method="POST" action="">
                <div class="profile-info">
                    <h3>Personal Information</h3>
                    <div class="profile-grid">
                        <div>
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="<?php echo e($userData['full_name'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Username</label>
                            <input type="text" value="<?php echo e($userData['username'] ?? ''); ?>" readonly>
                        </div>
                        <div>
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo e($userData['email'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Phone</label>
                            <input type="tel" name="phone" value="<?php echo e($userData['phone'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Date of Birth</label>
                            <input type="date" name="dob" value="<?php echo e($userData['dob'] ?? ''); ?>">
                        </div>
                        <div class="radio-group">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="">Select</option>
                                <?php foreach (['Male', 'Female', 'Other'] as $gender): ?>
                                    <option value="<?php echo $gender; ?>" <?php echo ($userData['gender'] ?? '') === $gender ? 'selected' : ''; ?>>
                                        <?php echo $gender; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="full-width">
                            <label>Address</label>
                            <textarea name="address" rows="3"><?php echo e($userData['address'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <br><hr><br>
                    <h3 class="section-title">Academic Information</h3>
                    <div class="profile-grid">
                        <div>
                            <label>Course</label>
                            <input type="text" name="course" value="<?php echo e($userData['course'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Department</label>
                            <select name="department_id">
                                <option value="0">Select department</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?php echo (int) $department['department_id']; ?>"
                                        <?php echo ((int) ($userData['department_id'] ?? 0) === (int) $department['department_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($department['department_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="radio-group">
                            <label>Semester</label>
                            <select name="semester">
                                <option value="0">Select</option>
                                <?php for ($i = 1; $i <= 8; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ((int) ($userData['semester'] ?? 0) === $i) ? 'selected' : ''; ?>>
                                        Semester <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label>Division</label>
                            <input type="text" name="division" value="<?php echo e($userData['division'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Admission Year</label>
                            <input type="number" name="admission_year" value="<?php echo e($userData['admission_year'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>College</label>
                            <input type="text" name="college" value="<?php echo e($userData['college'] ?? ''); ?>">
                        </div>
                    </div>

                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </section>
    </main>
</div>
</body>
</html>
