<?php
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-flag"></i> Report #<?= $report['id'] ?></h1>
        <a href="/admin/reports" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if ($error = getFlash('error')): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Report Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Type:</th>
                            <td>
                                <?php if ($report['reported_type'] === 'job'): ?>
                                    <span class="badge bg-primary"><i class="fas fa-briefcase"></i> Job Report</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="fas fa-user"></i> User Report</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Reporter:</th>
                            <td><?= htmlspecialchars($report['reporter_name']) ?> (<?= htmlspecialchars($report['reporter_email']) ?>)</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php
                                $statusClass = match($report['status']) {
                                    'pending' => 'bg-warning',
                                    'reviewed' => 'bg-info',
                                    'resolved' => 'bg-success',
                                    'dismissed' => 'bg-secondary',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= ucfirst($report['status']) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Reported On:</th>
                            <td><?= date('F d, Y h:i A', strtotime($report['created_at'])) ?></td>
                        </tr>
                    </table>
                    
                    <h6 class="mt-3">Report Message:</h6>
                    <div class="bg-light p-3 rounded">
                        <?= nl2br(htmlspecialchars($report['message'])) ?>
                    </div>
                    
                    <?php if ($report['admin_notes']): ?>
                        <h6 class="mt-3">Admin Notes:</h6>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <?= nl2br(htmlspecialchars($report['admin_notes'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Update Status</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/reports/<?= $report['id'] ?>/update" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" <?= $report['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="reviewed" <?= $report['status'] === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                                <option value="resolved" <?= $report['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                <option value="dismissed" <?= $report['status'] === 'dismissed' ? 'selected' : '' ?>>Dismissed</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Admin Notes</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"><?= htmlspecialchars($report['admin_notes'] ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <?php if ($report['reported_type'] === 'job'): ?>
                            <i class="fas fa-briefcase"></i> Reported Job
                        <?php else: ?>
                            <i class="fas fa-user"></i> Reported User
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($reportedItem): ?>
                        <?php if ($report['reported_type'] === 'job'): ?>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Job Title:</th>
                                    <td><?= htmlspecialchars($reportedItem['title']) ?></td>
                                </tr>
                                <tr>
                                    <th>Employer:</th>
                                    <td><?= htmlspecialchars($reportedItem['employer_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?= htmlspecialchars($reportedItem['employer_email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td><?= htmlspecialchars($reportedItem['location'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <?php
                                        $jobStatusClass = match($reportedItem['status'] ?? '') {
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $jobStatusClass ?>"><?= ucfirst($reportedItem['status'] ?? 'Unknown') ?></span>
                                    </td>
                                </tr>
                            </table>
                            <a href="/admin/jobs/<?= $reportedItem['id'] ?>" class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> View Job Details
                            </a>
                            <form action="/admin/jobs/<?= $reportedItem['id'] ?>/reject" method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this job?')">
                                    <i class="fas fa-ban"></i> Reject Job
                                </button>
                            </form>
                        <?php else: ?>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Name:</th>
                                    <td><?= htmlspecialchars($reportedItem['name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?= htmlspecialchars($reportedItem['email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td><span class="badge bg-info"><?= ucfirst($reportedItem['type']) ?></span></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <?php
                                        $userStatusClass = $reportedItem['status'] === 'active' ? 'bg-success' : 'bg-danger';
                                        ?>
                                        <span class="badge <?= $userStatusClass ?>"><?= ucfirst($reportedItem['status']) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Joined:</th>
                                    <td><?= date('M d, Y', strtotime($reportedItem['created_at'])) ?></td>
                                </tr>
                            </table>
                            <a href="/admin/users/<?= $reportedItem['id'] ?>" class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> View User Details
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">The reported item has been deleted or is no longer available.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-trash"></i> Delete Report</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Once deleted, this report cannot be recovered.</p>
                    <form action="/admin/reports/<?= $report['id'] ?>/delete" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this report?')">
                            <i class="fas fa-trash"></i> Delete Report
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
