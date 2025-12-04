<?php
require __DIR__ . '/../common/header.php';

$pendingCount = $statusCounts['pending'] ?? 0;
$reviewedCount = $statusCounts['reviewed'] ?? 0;
$resolvedCount = $statusCounts['resolved'] ?? 0;
$dismissedCount = $statusCounts['dismissed'] ?? 0;
$currentStatus = $_GET['status'] ?? '';
?>

<div class="container">
    <h1 class="mb-4"><i class="fas fa-flag"></i> Reports Management</h1>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if ($error = getFlash('error')): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <h3><?= $pendingCount ?></h3>
                    <p class="mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h3><?= $reviewedCount ?></h3>
                    <p class="mb-0">Reviewed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h3><?= $resolvedCount ?></h3>
                    <p class="mb-0">Resolved</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary">
                <div class="card-body text-center">
                    <h3><?= $dismissedCount ?></h3>
                    <p class="mb-0">Dismissed</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> All Reports</h5>
            <div class="btn-group">
                <a href="/admin/reports" class="btn btn-sm <?= empty($currentStatus) ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
                <a href="/admin/reports?status=pending" class="btn btn-sm <?= $currentStatus === 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">Pending</a>
                <a href="/admin/reports?status=reviewed" class="btn btn-sm <?= $currentStatus === 'reviewed' ? 'btn-info' : 'btn-outline-info' ?>">Reviewed</a>
                <a href="/admin/reports?status=resolved" class="btn btn-sm <?= $currentStatus === 'resolved' ? 'btn-success' : 'btn-outline-success' ?>">Resolved</a>
                <a href="/admin/reports?status=dismissed" class="btn btn-sm <?= $currentStatus === 'dismissed' ? 'btn-secondary' : 'btn-outline-secondary' ?>">Dismissed</a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($reports)): ?>
                <p class="text-muted text-center py-4">No reports found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Reporter</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td>#<?= $report['id'] ?></td>
                                    <td>
                                        <?php if ($report['reported_type'] === 'job'): ?>
                                            <span class="badge bg-primary"><i class="fas fa-briefcase"></i> Job</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><i class="fas fa-user"></i> User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($report['reporter_name']) ?></td>
                                    <td><?= htmlspecialchars(substr($report['message'], 0, 50)) ?>...</td>
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
                                    <td><?= date('M d, Y', strtotime($report['created_at'])) ?></td>
                                    <td>
                                        <a href="/admin/reports/<?= $report['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
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

<?php require __DIR__ . '/../common/footer.php'; ?>
