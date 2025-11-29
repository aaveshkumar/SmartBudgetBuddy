<?php
$meta = generateMetaTags('Admin Dashboard', 'Admin panel for ConnectWith9');
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="display-4"><?= $stats['total_users'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Job Seekers</h5>
                    <p class="display-4"><?= $stats['jobseekers'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Employers</h5>
                    <p class="display-4"><?= $stats['employers'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Total Jobs</h5>
                    <p class="display-4"><?= $stats['total_jobs'] ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Jobs</h5>
                    <p class="display-6"><?= $stats['pending_jobs'] ?></p>
                    <a href="/admin/jobs?status=Pending" class="btn btn-warning">Review Jobs</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Approved Jobs</h5>
                    <p class="display-6"><?= $stats['approved_jobs'] ?></p>
                    <a href="/admin/jobs?status=Approved" class="btn btn-success">View Jobs</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="/admin/users" class="btn btn-primary me-2"><i class="fas fa-users"></i> Manage Users</a>
                    <a href="/admin/jobs" class="btn btn-primary me-2"><i class="fas fa-briefcase"></i> Manage Jobs</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Jobs</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Employer</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Posted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentJobs as $job): ?>
                                <tr>
                                    <td><?= htmlspecialchars($job['title']) ?></td>
                                    <td><?= htmlspecialchars($job['employer_name']) ?></td>
                                    <td><?= htmlspecialchars($job['location']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $job['status'] === 'Approved' ? 'success' : ($job['status'] === 'Pending' ? 'warning' : 'danger') ?>">
                                            <?= $job['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= formatDate($job['created_at']) ?></td>
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
