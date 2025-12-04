<?php
$meta = generateMetaTags('View Job - Admin', 'Job details');
require __DIR__ . '/../common/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h2 mb-2"><?= htmlspecialchars($job['title']) ?></h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-building"></i> <?= htmlspecialchars($job['employer_name']) ?>
                            </p>
                        </div>
                        <span class="badge bg-<?= $job['status'] === 'Approved' ? 'success' : ($job['status'] === 'Pending' ? 'warning' : 'danger') ?>">
                            <?= $job['status'] ?>
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted small">Location</h5>
                            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted small">Job Type</h5>
                            <p><?= htmlspecialchars($job['type']) ?></p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted small">Category</h5>
                            <p><?= htmlspecialchars($job['category_name']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted small">Salary</h5>
                            <p><?= $job['salary'] ? formatSalary($job['salary']) : 'Not specified' ?></p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted small">Posted Date</h5>
                            <p><?= formatDate($job['created_at']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted small">Experience Required</h5>
                            <p><?= htmlspecialchars($job['experience_required'] ?? 'Not specified') ?></p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h3 class="h5 mb-3">Job Description</h3>
                    <div class="job-description bg-light p-3 rounded">
                        <?= nl2br(htmlspecialchars($job['description'])) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <?php if ($job['status'] === 'Pending'): ?>
                        <form action="/admin/jobs/<?= $job['id'] ?>/approve" method="POST" class="mb-2">
                            <?= csrfField() ?>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check"></i> Approve Job
                            </button>
                        </form>
                        <form action="/admin/jobs/<?= $job['id'] ?>/reject" method="POST" class="mb-2">
                            <?= csrfField() ?>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-times"></i> Reject Job
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <form action="/admin/jobs/<?= $job['id'] ?>/delete" method="POST" class="mb-2" onsubmit="return confirm('Are you sure you want to delete this job?')">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Delete Job
                        </button>
                    </form>
                    
                    <a href="/admin/jobs" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left"></i> Back to Jobs
                    </a>
                </div>
            </div>
            
            <div class="card shadow mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Job Statistics</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Applications:</strong> 
                        <span class="badge bg-info">0</span>
                    </p>
                    <p class="mb-0">
                        <strong>Views:</strong>
                        <span class="badge bg-secondary">0</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
