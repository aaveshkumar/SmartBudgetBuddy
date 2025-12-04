<?php
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-comments"></i> Messages</h1>
        <?php if ($totalUnread > 0): ?>
            <span class="badge bg-danger"><?= $totalUnread ?> unread</span>
        <?php endif; ?>
    </div>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if ($error = getFlash('error')): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if (empty($conversations)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No conversations yet</h5>
                <?php if ($currentUser['type'] === USER_TYPE_JOBSEEKER): ?>
                    <p class="text-muted">You'll be able to chat with employers after you get selected for a job.</p>
                <?php else: ?>
                    <p class="text-muted">Select a candidate from your job applications to start a conversation.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($conversations as $conversation): ?>
                <?php
                    $hasUnread = ($conversation['unread_count'] ?? 0) > 0;
                    $bgClass = $hasUnread ? 'bg-light' : '';
                ?>
                <a href="/chat/<?= $conversation['id'] ?>" class="list-group-item list-group-item-action <?= $bgClass ?>">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <?= htmlspecialchars($conversation['other_party_name']) ?>
                                        <?php if ($hasUnread): ?>
                                            <span class="badge bg-danger ms-1"><?= $conversation['unread_count'] ?></span>
                                        <?php endif; ?>
                                    </h6>
                                    <p class="mb-1 text-muted small">
                                        <i class="fas fa-briefcase"></i> <?= htmlspecialchars($conversation['job_title']) ?>
                                    </p>
                                    <?php if ($conversation['last_message']): ?>
                                        <p class="mb-0 text-muted small text-truncate" style="max-width: 300px;">
                                            <?= htmlspecialchars(substr($conversation['last_message'], 0, 50)) ?>...
                                        </p>
                                    <?php else: ?>
                                        <p class="mb-0 text-muted small fst-italic">No messages yet</p>
                                    <?php endif; ?>
                                </div>
                                <div class="text-end">
                                    <?php if ($conversation['last_message_time']): ?>
                                        <small class="text-muted"><?= formatDate($conversation['last_message_time']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
