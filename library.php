<?php
$pageTitle = 'Library';
include 'header.php';

$assets = db_all(
    "SELECT title, category, author, location, status, copies
     FROM library_assets
     ORDER BY title"
);
?>

<div class="user-layout">
    <main class="events-page">
        <div class="events-heading">
            <div>
                <h2>Library</h2>
                <p>Books and resources available in the college library.</p>
            </div>
        </div>

        <section class="events-grid">
            <?php if ($assets): ?>
                <?php foreach ($assets as $asset): ?>
                    <article class="event-card">
                        <div class="event-content">
                            <h3><?php echo e($asset['title']); ?></h3>
                            <p class="event-description"><?php echo e($asset['author'] ?: 'Author not listed'); ?></p>
                            <p class="event-info"><strong>Category:</strong> <?php echo e($asset['category'] ?: 'General'); ?></p>
                            <p class="event-info"><strong>Location:</strong> <?php echo e($asset['location'] ?: 'Library'); ?></p>
                            <p class="event-info"><strong>Copies:</strong> <?php echo (int) $asset['copies']; ?></p>
                            <p class="event-info"><strong>Status:</strong> <?php echo e($asset['status']); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-events">
                    <h3>No library items yet</h3>
                    <p>The admin can add books from the Library page.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
