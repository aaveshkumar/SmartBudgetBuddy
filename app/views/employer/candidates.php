<?php
$meta = generateMetaTags('Browse Candidates', 'Search and filter job seekers');
require __DIR__ . '/../common/header.php';
?>

<div class="container my-5">
    <h2><i class="fas fa-users"></i> Browse Candidates</h2>
    
    <!-- Search/Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="/employer/candidates" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" 
                               value="<?= htmlspecialchars($_GET['city'] ?? '') ?>" placeholder="e.g., Mumbai">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control"
                               value="<?= htmlspecialchars($_GET['state'] ?? '') ?>" placeholder="e.g., Maharashtra">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Skill</label>
                        <input type="text" name="skill" class="form-control"
                               value="<?= htmlspecialchars($_GET['skill'] ?? '') ?>" placeholder="e.g., PHP, JavaScript">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Min Experience (Years)</label>
                        <input type="number" name="min_experience" class="form-control" min="0"
                               value="<?= htmlspecialchars($_GET['min_experience'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Max Experience (Years)</label>
                        <input type="number" name="max_experience" class="form-control" min="0"
                               value="<?= htmlspecialchars($_GET['max_experience'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Availability</label>
                        <select name="availability" class="form-control">
                            <option value="">Any</option>
                            <option value="immediate" <?= ($_GET['availability'] ?? '') === 'immediate' ? 'selected' : '' ?>>Immediate</option>
                            <option value="15_days" <?= ($_GET['availability'] ?? '') === '15_days' ? 'selected' : '' ?>>15 Days</option>
                            <option value="1_month" <?= ($_GET['availability'] ?? '') === '1_month' ? 'selected' : '' ?>>1 Month</option>
                            <option value="2_months" <?= ($_GET['availability'] ?? '') === '2_months' ? 'selected' : '' ?>>2 Months</option>
                            <option value="3_months" <?= ($_GET['availability'] ?? '') === '3_months' ? 'selected' : '' ?>>3+ Months</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Search Candidates
                        </button>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <a href="/employer/candidates" class="btn btn-secondary w-100">
                            <i class="fas fa-redo"></i> Reset Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Candidates List -->
    <p class="text-muted">Found <?= count($candidates) ?> candidates</p>
    
    <?php if (empty($candidates)): ?>
        <div class="alert alert-info">
            No candidates found. Try adjusting your search filters.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($candidates as $candidate): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <div class="d-flex">
                                <?php if ($candidate['profile_picture']): ?>
                                    <img src="/uploads/profiles/<?= htmlspecialchars($candidate['profile_picture']) ?>" 
                                         alt="Profile" class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 80px; height: 80px; font-size: 32px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-grow-1">
                                    <h5 class="mb-1"><?= htmlspecialchars($candidate['name']) ?></h5>
                                    <?php if ($candidate['headline']): ?>
                                        <p class="text-muted mb-2"><?= htmlspecialchars($candidate['headline']) ?></p>
                                    <?php endif; ?>
                                    
                                    <p class="mb-1">
                                        <?php if ($candidate['city']): ?>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($candidate['city']) ?>, <?= htmlspecialchars($candidate['state']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($candidate['total_experience_years']): ?>
                                            <span class="badge bg-info">
                                                <i class="fas fa-briefcase"></i> <?= $candidate['total_experience_years'] ?> years exp
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($candidate['availability']): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-clock"></i> <?= ucfirst(str_replace('_', ' ', $candidate['availability'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                    
                                    <?php if ($candidate['skills']): ?>
                                        <p class="mb-2">
                                            <strong>Skills:</strong> 
                                            <small><?= htmlspecialchars(substr($candidate['skills'], 0, 100)) ?><?= strlen($candidate['skills']) > 100 ? '...' : '' ?></small>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="mt-2">
                                        <a href="/employer/candidates/<?= $candidate['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View Full Profile
                                        </a>
                                        <?php if ($candidate['resume_file']): ?>
                                            <a href="/uploads/resumes/<?= htmlspecialchars($candidate['resume_file']) ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-file-pdf"></i> Resume
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="openReportModal('user', <?= $candidate['id'] ?>)">
                                            <i class="fas fa-flag"></i> Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
