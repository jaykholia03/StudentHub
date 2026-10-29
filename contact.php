<?php
$pageTitle = 'Contact Admin';
include 'header.php';

$userId  = (int) $_SESSION['user_id'];
$success = '';
$error   = '';

$user = db_one("SELECT full_name, email FROM users WHERE id = ?", 'i', [$userId]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = post('name');
    $email   = post('email');
    $subject = post('subject');
    $message = post('message');

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        $error = 'Please fill in all fields.';
    } else {
        db_run(
            "INSERT INTO contact_messages (user_id, name, email, subject, message)
             VALUES (?, ?, ?, ?, ?)",
            'issss',
            [$userId, $name, $email, $subject, $message]
        );
        $success = 'Your message was sent to the admin.';
    }
}

$adminEmail = db_one("SELECT email FROM users WHERE role = 'admin' ORDER BY id LIMIT 1");
?>

<div class="user-layout">
    <main>
        <h2>Contact Admin</h2>

        <?php if ($success): ?>
            <p style="color: #16a34a; margin: 10px 0; font-weight: 600;"><?php echo e($success); ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p style="color: #d9534f; margin: 10px 0; font-weight: 600;"><?php echo e($error); ?></p>
        <?php endif; ?>

        <div class="contact-grid">
            <section class="card contact-form">
                <form method="POST">
                    <div>
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo e($user['full_name'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo e($user['email'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" placeholder="Enter the subject" required>
                    </div>
                    <div>
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" rows="5" placeholder="Type your message here..." required></textarea>
                    </div>
                    <div>
                        <button type="submit">Send Message</button>
                    </div>
                </form>
            </section>

            <div class="right-column">
                <section class="card info-card">
                    <h3>Contact Information</h3>
                    <p><strong>Email:</strong> <?php echo e($adminEmail['email'] ?? 'admin@studenthub.local'); ?></p>
                    <p><strong>Support:</strong> Use this form and the admin will see it in Mailbox.</p>
                </section>
                <section class="card map-card">
                    <h3>Location</h3>
                    <img src="images/location-placeholder.svg" alt="Admin location map">
                </section>
            </div>
        </div>
    </main>
</div>
</body>
</html>
