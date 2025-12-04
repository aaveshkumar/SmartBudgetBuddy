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
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-paper-plane"></i> Send System Notification</h5>
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
