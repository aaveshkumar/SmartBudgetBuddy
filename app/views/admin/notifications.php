<?php
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <h1 class="mb-4"><i class="fas fa-bullhorn"></i> System Notifications</h1>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if ($error = getFlash('error')): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Create New Notification</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/notifications/send" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                        
                        <div class="mb-3">
                            <label for="target_role" class="form-label">Target Audience</label>
                            <select class="form-select" id="target_role" name="target_role" required>
                                <option value="employer">Employers Only</option>
                                <option value="jobseeker">Job Seekers Only</option>
                                <option value="all">All Users</option>
                            </select>
                            <small class="text-muted">Select who should receive this notification</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Notification Title</label>
                            <input type="text" class="form-control" id="title" name="title" required maxlength="255" placeholder="e.g., New Feature: Enhanced Search">
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Notification Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required placeholder="Describe the update, new feature, or announcement..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Send this notification to all selected users?')">
                            <i class="fas fa-paper-plane"></i> Send Notification
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Sent Notifications</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($systemNotifications)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-bell-slash fa-3x mb-3"></i>
                            <p>No notifications have been sent yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Message</th>
                                        <th>Sent To</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($systemNotifications as $notification): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($notification['title']) ?></strong></td>
                                        <td>
                                            <small><?= htmlspecialchars(substr($notification['message'], 0, 80)) ?><?= strlen($notification['message']) > 80 ? '...' : '' ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $notification['recipient_count'] ?> users</span>
                                        </td>
                                        <td>
                                            <small><?= date('M j, Y g:i A', strtotime($notification['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <form action="/admin/notifications/delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this notification from all users? This cannot be undone.')">
                                                <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                                                <input type="hidden" name="title" value="<?= htmlspecialchars($notification['title']) ?>">
                                                <input type="hidden" name="message" value="<?= htmlspecialchars($notification['message']) ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle"></i> About System Notifications</h6>
                    <p class="small text-muted mb-2">Use this feature to inform users about:</p>
                    <ul class="small text-muted mb-0">
                        <li>New features and updates</li>
                        <li>System maintenance notices</li>
                        <li>Policy changes</li>
                        <li>Important announcements</li>
                    </ul>
                    <hr>
                    <p class="small text-muted mb-0">
                        <strong>Note:</strong> Notifications will appear in the user's notification bell and on their notifications page.
                    </p>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-lightbulb"></i> Example Notifications</h6>
                    <div class="small">
                        <p class="mb-2"><strong>For Employers:</strong></p>
                        <ul class="text-muted mb-3">
                            <li>New candidate filtering options</li>
                            <li>Enhanced job posting features</li>
                            <li>Chat feature improvements</li>
                        </ul>
                        <p class="mb-2"><strong>For Job Seekers:</strong></p>
                        <ul class="text-muted mb-0">
                            <li>Profile enhancement tips</li>
                            <li>New job categories added</li>
                            <li>Application tracking updates</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
