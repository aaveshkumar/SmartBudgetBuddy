<?php
$meta = generateMetaTags(
    $job['title'] . ' - ' . $job['location'],
    substr(strip_tags($job['description']), 0, 155),
    $job['title'] . ', ' . $job['location'] . ', ' . $job['category_name']
);

$schema = generateJobSchema($job);

require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <?php if ($error = getFlash('error')): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h1 class="h2"><?= htmlspecialchars($job['title']) ?></h1>
                    
                    <div class="mb-3">
                        <p class="text-muted mb-1">
                            <i class="fas fa-building"></i> <?= htmlspecialchars($job['employer_name']) ?>
                        </p>
                        <p class="text-muted mb-1">
                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?>
                        </p>
                        <p class="mb-2">
                            <span class="badge bg-info"><?= htmlspecialchars($job['type']) ?></span>
                            <span class="badge bg-secondary"><?= htmlspecialchars($job['category_name']) ?></span>
                            <?php if ($job['salary']): ?>
                                <span class="badge bg-success"><?= formatSalary($job['salary']) ?></span>
                            <?php endif; ?>
                        </p>
                        <p class="text-muted">
                            <small><i class="fas fa-clock"></i> Posted <?= formatDate($job['created_at']) ?></small>
                        </p>
                    </div>
                    
                    <hr>
                    
                    <h3 class="h5">Job Description</h3>
                    <div class="job-description">
                        <?= nl2br(htmlspecialchars($job['description'])) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow sticky-top" style="top: 20px;">
                <div class="card-body">
                    <?php if (isLoggedIn()): ?>
                        <?php $user = getCurrentUser(); ?>
                        <?php if ($user['type'] === USER_TYPE_JOBSEEKER): ?>
                            <?php if ($hasApplied): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> You have already applied for this job
                                </div>
                            <?php else: ?>
                                <h5 class="card-title">Apply for this Job</h5>
                                <form action="/jobs/<?= $job['id'] ?>/apply" method="POST" enctype="multipart/form-data">
                                    <?= csrfField() ?>
                                    
                                    <div class="mb-3">
                                        <label for="resume" class="form-label">Upload Resume *</label>
                                        <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx,.txt" required>
                                        <small class="text-muted">PDF, DOC, DOCX, TXT (Max 5MB)</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-paper-plane"></i> Submit Application
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Only job seekers can apply for jobs
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <h5 class="card-title">Apply for this Job</h5>
                        <p>Please login to apply for this job</p>
                        <a href="/login" class="btn btn-primary w-100">Login to Apply</a>
                        <a href="/register" class="btn btn-outline-primary w-100 mt-2">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
