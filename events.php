<?php
$pageTitle = 'Events';
include 'header.php';

$events = db_all(
    "SELECT title, description, event_date, event_time, venue, category
     FROM events
     WHERE status = 'published'
     ORDER BY event_date ASC, event_time ASC"
);
?>

<div class="user-layout">
    <main class="events-page">
        <div class="events-heading">
            <div>
                <h2>College Events</h2>
                <p>Discover upcoming events and activities at your college.</p>
            </div>
        </div>

        <section class="events-grid">
            <?php if ($events): ?>
                <?php foreach ($events as $event): ?>
                    <article class="event-card">
                        <div class="event-date">
                            <span><?php echo date('M', strtotime($event['event_date'])); ?></span>
                            <strong><?php echo date('d', strtotime($event['event_date'])); ?></strong>
                            <small><?php echo date('Y', strtotime($event['event_date'])); ?></small>
                        </div>
                        <div class="event-content">
                            <h3><?php echo e($event['title']); ?></h3>
                            <p class="event-description"><?php echo e($event['description'] ?? ''); ?></p>
                            <p class="event-info"><strong>Category:</strong> <?php echo e($event['category']); ?></p>
                            <p class="event-info"><strong>Time:</strong> <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                            <p class="event-info"><strong>Venue:</strong> <?php echo e($event['venue']); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-events">
                    <h3>No Events</h3>
                    <p>There are no published events at the moment.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
