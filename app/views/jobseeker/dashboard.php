<?php
$meta = generateMetaTags('Job Seeker Dashboard', 'View your job applications and find new opportunities');
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <h1 class="mb-4">Job Seeker Dashboard</h1>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">My Applications</h5>
                    <p class="display-4"><?= $stats['applications'] ?></p>
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
                    <a href="/jobs" class="btn btn-primary me-2 mb-2"><i class="fas fa-search"></i> Browse Jobs</a>
                    <a href="/jobseeker/applications" class="btn btn-outline-primary me-2 mb-2"><i class="fas fa-file-alt"></i> View My Applications</a>
                    <a href="/jobseeker/profile" class="btn btn-outline-success me-2 mb-2"><i class="fas fa-id-card"></i> My Profile</a>
                    <a href="/jobseeker/profile/edit" class="btn btn-outline-info mb-2"><i class="fas fa-edit"></i> Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Applications</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recentApplications)): ?>
                        <p class="text-muted">You haven't applied to any jobs yet.</p>
                        <a href="/jobs" class="btn btn-primary">Start Browsing Jobs</a>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentApplications as $app): ?>
                            <div class="list-group-item">
                                <h6 class="mb-1"><?= htmlspecialchars($app['job_title']) ?></h6>
                                <p class="mb-1 text-muted">
                                    <small>
                                        <i class="fas fa-building"></i> <?= htmlspecialchars($app['employer_name']) ?> |
                                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($app['location']) ?>
                                    </small>
                                </p>
                                <small class="text-muted">Applied: <?= formatDate($app['applied_at']) ?></small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Latest Jobs</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php foreach ($latestJobs as $job): ?>
                        <a href="/jobs/<?= $job['id'] ?>" class="list-group-item list-group-item-action">
                            <h6 class="mb-1"><?= htmlspecialchars($job['title']) ?></h6>
                            <p class="mb-1 text-muted">
                                <small>
                                    <i class="fas fa-building"></i> <?= htmlspecialchars($job['employer_name']) ?> |
                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?>
                                </small>
                            </p>
                            <small class="text-muted"><?= formatDate($job['created_at']) ?></small>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
