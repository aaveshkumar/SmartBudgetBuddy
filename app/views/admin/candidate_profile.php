<?php
$meta = generateMetaTags('Candidate Profile - Admin', 'View candidate profile');
require __DIR__ . '/../common/header.php';

$profile = $data['profile'] ?? [];
$user = $data['user'] ?? [];
$workExperiences = $data['work_experiences'] ?? [];
$education = $data['education'] ?? [];
$skills = $data['skills'] ?? [];
$certifications = $data['certifications'] ?? [];
$languages = $data['languages'] ?? [];
$projects = $data['projects'] ?? [];
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user"></i> Candidate Profile</h2>
        <a href="/admin/candidates" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Candidates
        </a>
    </div>

    <?php if (empty($profile)): ?>
        <div class="alert alert-info">
            <h5>Profile Incomplete</h5>
            <p>This candidate has not completed their profile yet.</p>
        </div>
    <?php else: ?>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center">
                        <?php if (!empty($profile['profile_picture'])): ?>
                            <img src="/uploads/profiles/<?= htmlspecialchars($profile['profile_picture']) ?>" 
                                 alt="Profile" class="img-thumbnail rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 120px; height: 120px; font-size: 48px;">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-10">
                        <h3><?= htmlspecialchars($user['name']) ?></h3>
                        <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                        <?php if (!empty($user['mobile_no'])): ?>
                            <p class="text-muted"><i class="fas fa-phone"></i> <?= htmlspecialchars($user['mobile_no']) ?></p>
                        <?php endif; ?>
                        
                        <?php if ($profile['headline']): ?>
                            <h5 class="text-muted mt-2"><?= htmlspecialchars($profile['headline']) ?></h5>
                        <?php endif; ?>
                        
                        <?php if ($profile['city']): ?>
                            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($profile['city']) ?>, <?= htmlspecialchars($profile['state']) ?></p>
                        <?php endif; ?>
                        
                        <?php if ($profile['summary']): ?>
                            <p class="mt-3"><?= nl2br(htmlspecialchars($profile['summary'])) ?></p>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <?php if (!empty($skills)): ?>
                                <p><strong>Top Skills:</strong> 
                                <?php 
                                $skillNames = array_map(fn($s) => $s['skill_name'], array_slice($skills, 0, 5));
                                echo implode(', ', $skillNames);
                                ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($profile['total_experience_years']): ?>
                                <span class="badge bg-info me-2"><?= $profile['total_experience_years'] ?> years experience</span>
                            <?php endif; ?>
                            
                            <?php if ($profile['availability']): ?>
                                <span class="badge bg-success me-2">Available: <?= ucfirst(str_replace('_', ' ', $profile['availability'])) ?></span>
                            <?php endif; ?>
                            
                            <?php if ($profile['willing_to_relocate']): ?>
                                <span class="badge bg-primary">Open to Relocation</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($profile['resume_file'])): ?>
                            <div class="mt-3">
                                <a href="/uploads/resumes/<?= htmlspecialchars($profile['resume_file']) ?>" 
                                   target="_blank" class="btn btn-outline-success">
                                    <i class="fas fa-file-pdf"></i> View Resume
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Work Experience -->
        <?php if (!empty($workExperiences)): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-briefcase"></i> Work Experience</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($workExperiences as $exp): ?>
                        <div class="mb-4">
                            <h5><?= htmlspecialchars($exp['job_title']) ?></h5>
                            <p class="mb-1"><strong><?= htmlspecialchars($exp['company_name']) ?></strong></p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-calendar"></i> 
                                <?= formatDate($exp['start_date']) ?> - <?= $exp['is_current'] ? 'Present' : formatDate($exp['end_date']) ?>
                            </p>
                            <?php if ($exp['description']): ?>
                                <p><?= nl2br(htmlspecialchars($exp['description'])) ?></p>
                            <?php endif; ?>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Education -->
        <?php if (!empty($education)): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Education</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($education as $edu): ?>
                        <div class="mb-4">
                            <h5><?= htmlspecialchars($edu['degree']) ?></h5>
                            <p class="mb-1"><strong><?= htmlspecialchars($edu['school_name']) ?></strong></p>
                            <?php if ($edu['field_of_study']): ?>
                                <p class="mb-2">Field of Study: <?= htmlspecialchars($edu['field_of_study']) ?></p>
                            <?php endif; ?>
                            <p class="text-muted mb-2">
                                <i class="fas fa-calendar"></i> 
                                <?= formatDate($edu['start_date']) ?> - <?= $edu['is_current'] ? 'Ongoing' : formatDate($edu['end_date']) ?>
                            </p>
                            <?php if ($edu['description']): ?>
                                <p><?= nl2br(htmlspecialchars($edu['description'])) ?></p>
                            <?php endif; ?>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Skills -->
        <?php if (!empty($skills)): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Skills</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($skills as $skill): ?>
                            <div class="col-md-4 mb-2">
                                <span class="badge bg-light text-dark p-2">
                                    <?= htmlspecialchars($skill['skill_name']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Certifications -->
        <?php if (!empty($certifications)): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-certificate"></i> Certifications</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($certifications as $cert): ?>
                        <div class="mb-4">
                            <h5><?= htmlspecialchars($cert['certification_name']) ?></h5>
                            <p class="mb-1"><strong><?= htmlspecialchars($cert['issuing_organization']) ?></strong></p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-calendar"></i> Issued: <?= formatDate($cert['issue_date']) ?>
                            </p>
                            <?php if ($cert['expiry_date']): ?>
                                <p class="text-muted mb-2">Expires: <?= formatDate($cert['expiry_date']) ?></p>
                            <?php endif; ?>
                            <?php if ($cert['credential_url']): ?>
                                <p>
                                    <a href="<?= htmlspecialchars($cert['credential_url']) ?>" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> View Credential
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Languages -->
        <?php if (!empty($languages)): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-language"></i> Languages</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($languages as $lang): ?>
                            <div class="col-md-6 mb-2">
                                <p class="mb-1">
                                    <strong><?= htmlspecialchars($lang['language_name']) ?></strong>
                                    <span class="badge bg-info"><?= htmlspecialchars(ucfirst($lang['proficiency_level'])) ?></span>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Projects -->
        <?php if (!empty($projects)): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-project-diagram"></i> Projects</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($projects as $project): ?>
                        <div class="mb-4">
                            <h5><?= htmlspecialchars($project['project_name']) ?></h5>
                            <?php if ($project['description']): ?>
                                <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                            <?php endif; ?>
                            <?php if ($project['start_date']): ?>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-calendar"></i> 
                                    <?= formatDate($project['start_date']) ?> - <?= $project['end_date'] ? formatDate($project['end_date']) : 'Ongoing' ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($project['project_url']): ?>
                                <p>
                                    <a href="<?= htmlspecialchars($project['project_url']) ?>" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> View Project
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="/admin/candidates" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Candidates
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
