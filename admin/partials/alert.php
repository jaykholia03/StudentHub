<?php if (!empty($flash)): ?>
    <div class="alert alert-<?php echo ($flash['type'] ?? '') === 'error' ? 'danger' : 'success'; ?> mb-3">
        <?php echo e($flash['message']); ?>
    </div>
<?php endif; ?>
