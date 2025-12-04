<?php
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-bell"></i> Notifications</h1>
        <?php if ($unreadCount > 0): ?>
        <button type="button" class="btn btn-outline-primary" onclick="markAllRead()">
            <i class="fas fa-check-double"></i> Mark All as Read
        </button>
        <?php endif; ?>
    </div>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if ($error = getFlash('error')): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if (empty($notifications)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No notifications yet</h5>
                <p class="text-muted">You'll receive notifications about job selections, new jobs, and more.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($notifications as $notification): ?>
                <?php
                    $iconClass = 'fa-bell text-secondary';
                    $bgClass = $notification['is_read'] ? '' : 'bg-light';
                    
                    switch($notification['type']) {
                        case 'job_selected':
                            $iconClass = 'fa-trophy text-success';
                            break;
                        case 'new_job':
                            $iconClass = 'fa-briefcase text-primary';
                            break;
                        case 'chat_message':
                            $iconClass = 'fa-comment text-info';
                            break;
                        case 'system':
                            $iconClass = 'fa-cog text-warning';
                            break;
                    }
                    
                    $link = '#';
                    if ($notification['type'] === 'job_selected') {
                        $link = '/chat';
                    } elseif ($notification['type'] === 'new_job' && $notification['related_id']) {
                        $link = '/jobs/' . $notification['related_id'];
                    } elseif ($notification['type'] === 'chat_message' && $notification['related_id']) {
                        $link = '/chat/' . $notification['related_id'];
                    }
                ?>
                <div class="list-group-item list-group-item-action <?= $bgClass ?>" id="notification-<?= $notification['id'] ?>">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="fas <?= $iconClass ?> fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <?php if ($link !== '#'): ?>
                                            <a href="<?= $link ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($notification['title']) ?></a>
                                        <?php else: ?>
                                            <?= htmlspecialchars($notification['title']) ?>
                                        <?php endif; ?>
                                    </h6>
                                    <p class="mb-1 text-muted"><?= htmlspecialchars($notification['message']) ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> <?= formatDate($notification['created_at']) ?>
                                        <?php if ($notification['actor_name']): ?>
                                            &middot; From: <?= htmlspecialchars($notification['actor_name']) ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <div class="ms-2">
                                    <?php if (!$notification['is_read']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="markAsRead(<?= $notification['id'] ?>)" title="Mark as read">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteNotification(<?= $notification['id'] ?>)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function markAsRead(id) {
    fetch('/notifications/' + id + '/read', { method: 'POST' })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                var item = document.getElementById('notification-' + id);
                if (item) {
                    item.classList.remove('bg-light');
                    var btn = item.querySelector('.btn-outline-primary');
                    if (btn) btn.remove();
                }
            }
        });
}

function markAllRead() {
    fetch('/notifications/mark-all-read', { method: 'POST' })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                location.reload();
            }
        });
}

function deleteNotification(id) {
    if (!confirm('Delete this notification?')) return;
    
    fetch('/notifications/' + id + '/delete', { method: 'POST' })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                var item = document.getElementById('notification-' + id);
                if (item) item.remove();
            }
        });
}
</script>

<?php require __DIR__ . '/../common/footer.php'; ?>
