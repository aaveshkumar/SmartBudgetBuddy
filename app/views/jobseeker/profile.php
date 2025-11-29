<?php
$meta = generateMetaTags('My Profile', 'View your professional profile');
require __DIR__ . '/../common/header.php';

$profile = $data['profile'] ?? [];
$workExperiences = $data['work_experiences'] ?? [];
$education = $data['education'] ?? [];
$skills = $data['skills'] ?? [];
$certifications = $data['certifications'] ?? [];
$languages = $data['languages'] ?? [];
$projects = $data['projects'] ?? [];
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user"></i> My Profile</h2>
        <a href="/jobseeker/profile/edit" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Profile
        </a>
    </div>

    <?php if (empty($profile)): ?>
        <div class="alert alert-info">
            <h5>Complete Your Profile</h5>
            <p>Your profile is empty. Click "Edit Profile" to add your professional information, work experience, education, skills, and more!</p>
            <a href="/jobseeker/profile/edit" class="btn btn-primary mt-2">
                <i class="fas fa-plus"></i> Create Profile Now
            </a>
        </div>
    <?php else: ?>
        <div class="card shadow">
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
                        <h3><?= htmlspecialchars($data['user']['name']) ?></h3>
                        <?php if ($profile['headline']): ?>
                            <h5 class="text-muted"><?= htmlspecialchars($profile['headline']) ?></h5>
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
                                    <i class="fas fa-file-pdf"></i> Download Resume
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($workExperiences)): ?>
            <div class="card shadow mt-4">
                <div class="card-body">
                    <h4><i class="fas fa-briefcase"></i> Work Experience</h4>
                    <hr>
                    <?php foreach ($workExperiences as $exp): ?>
                        <div class="mb-3">
                            <h5><?= htmlspecialchars($exp['job_title']) ?></h5>
                            <p class="mb-0"><strong><?= htmlspecialchars($exp['company_name']) ?></strong></p>
                            <p class="text-muted"><?= formatDate($exp['start_date']) ?> - <?= $exp['is_current'] ? 'Present' : formatDate($exp['end_date']) ?></p>
                            <?php if ($exp['description']): ?>
                                <p><?= nl2br(htmlspecialchars($exp['description'])) ?></p>
                            <?php endif; ?>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($education)): ?>
            <div class="card shadow mt-4">
                <div class="card-body">
                    <h4><i class="fas fa-graduation-cap"></i> Education</h4>
                    <hr>
                    <?php foreach ($education as $edu): ?>
                        <div class="mb-3">
                            <h5><?= htmlspecialchars($edu['degree']) ?></h5>
                            <?php if ($edu['field_of_study']): ?>
                                <p class="mb-0">Field: <?= htmlspecialchars($edu['field_of_study']) ?></p>
                            <?php endif; ?>
                            <p class="mb-0"><strong><?= htmlspecialchars($edu['institution']) ?></strong></p>
                            <?php if ($edu['start_date']): ?>
                                <p class="text-muted"><?= formatDate($edu['start_date']) ?> - <?= formatDate($edu['end_date']) ?></p>
                            <?php endif; ?>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($skills)): ?>
            <div class="card shadow mt-4">
                <div class="card-body">
                    <h4><i class="fas fa-code"></i> Skills</h4>
                    <hr>
                    <div class="row">
                        <?php foreach ($skills as $skill): ?>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-primary me-1"><?= htmlspecialchars($skill['skill_name']) ?></span>
                                <small class="text-muted">(<?= ucfirst($skill['proficiency']) ?>)</small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
