<?php
$meta = generateMetaTags('Employer Dashboard', 'Manage your job postings');
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <h1 class="mb-4">Employer Dashboard</h1>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Jobs</h5>
                    <p class="display-4"><?= $stats['total_jobs'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Approved Jobs</h5>
                    <p class="display-4"><?= $stats['approved_jobs'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Pending Jobs</h5>
                    <p class="display-4"><?= $stats['pending_jobs'] ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="/employer/post-job" class="btn btn-primary me-2 mb-2"><i class="fas fa-plus"></i> Post New Job</a>
                    <a href="/employer/jobs" class="btn btn-outline-primary me-2 mb-2"><i class="fas fa-list"></i> View All Jobs</a>
                    <a href="/employer/candidates" class="btn btn-outline-success mb-2"><i class="fas fa-users"></i> Find Candidates</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Jobs</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Posted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentJobs as $job): ?>
                                <tr>
                                    <td><?= htmlspecialchars($job['title']) ?></td>
                                    <td><?= htmlspecialchars($job['location']) ?></td>
                                    <td><span class="badge bg-info"><?= $job['type'] ?></span></td>
                                    <td>
                                        <span class="badge bg-<?= $job['status'] === 'Approved' ? 'success' : ($job['status'] === 'Pending' ? 'warning' : 'danger') ?>">
                                            <?= $job['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= formatDate($job['created_at']) ?></td>
                                    <td>
                                        <a href="/employer/jobs/<?= $job['id'] ?>/applications" class="btn btn-sm btn-primary">Applications</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
