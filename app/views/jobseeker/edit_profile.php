<?php
$meta = generateMetaTags('Edit Profile', 'Update your professional profile');
require __DIR__ . '/../common/header.php';

$profile = $data['profile'] ?? [];
$workExperiences = $data['work_experiences'] ?? [];
$education = $data['education'] ?? [];
$skills = $data['skills'] ?? [];
$certifications = $data['certifications'] ?? [];
$languages = $data['languages'] ?? [];
$projects = $data['projects'] ?? [];
?>

<style>
.section-card {
    background: #fff;
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 2px solid #0d6efd;
    padding-bottom: 10px;
}
.badge-proficiency {
    font-size: 0.75rem;
    padding: 4px 8px;
}
.item-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 12px;
    border-left: 3px solid #0d6efd;
}
</style>

<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-edit"></i> Edit Professional Profile</h2>
                <a href="/jobseeker/profile" class="btn btn-outline-secondary">
                    <i class="fas fa-eye"></i> View Profile
                </a>
            </div>

            <?php if ($error = getFlash('error')): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success = getFlash('success')): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <!-- Basic Information -->
            <div class="section-card">
                <div class="section-header">
                    <h4><i class="fas fa-info-circle"></i> Basic Information</h4>
                </div>
                <form action="/jobseeker/profile/update-basic" method="POST">
                    <?= csrfField() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Professional Headline *</label>
                            <input type="text" name="headline" class="form-control" 
                                   value="<?= htmlspecialchars($profile['headline'] ?? '') ?>"
                                   placeholder="e.g., Full Stack Developer | React & Node.js Expert" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" 
                                   value="<?= htmlspecialchars($profile['phone'] ?? '') ?>"
                                   placeholder="+91 9876543210">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Professional Summary</label>
                        <textarea name="summary" class="form-control" rows="4"
                                  placeholder="Write a compelling summary of your professional background, skills, and career objectives..."><?= htmlspecialchars($profile['summary'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City *</label>
                            <input type="text" name="city" class="form-control" 
                                   value="<?= htmlspecialchars($profile['city'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">State *</label>
                            <input type="text" name="state" class="form-control" 
                                   value="<?= htmlspecialchars($profile['state'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" 
                                   value="<?= htmlspecialchars($profile['country'] ?? 'India') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control" 
                                   value="<?= htmlspecialchars($profile['postal_code'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Address</label>
                        <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" 
                                   value="<?= htmlspecialchars($profile['date_of_birth'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-control">
                                <option value="">Select</option>
                                <option value="male" <?= ($profile['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($profile['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= ($profile['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                                <option value="prefer_not_to_say" <?= ($profile['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total Experience (Years)</label>
                            <input type="number" name="total_experience_years" class="form-control" min="0" max="50"
                                   value="<?= htmlspecialchars($profile['total_experience_years'] ?? 0) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Current Salary (₹)</label>
                            <input type="number" name="current_salary" class="form-control" 
                                   value="<?= htmlspecialchars($profile['current_salary'] ?? '') ?>"
                                   placeholder="e.g., 800000">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Expected Salary (₹)</label>
                            <input type="number" name="expected_salary" class="form-control" 
                                   value="<?= htmlspecialchars($profile['expected_salary'] ?? '') ?>"
                                   placeholder="e.g., 1200000">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Notice Period</label>
                            <input type="text" name="notice_period" class="form-control" 
                                   value="<?= htmlspecialchars($profile['notice_period'] ?? '') ?>"
                                   placeholder="e.g., 30 days">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Availability</label>
                            <select name="availability" class="form-control">
                                <option value="immediate" <?= ($profile['availability'] ?? 'immediate') === 'immediate' ? 'selected' : '' ?>>Immediate</option>
                                <option value="15_days" <?= ($profile['availability'] ?? '') === '15_days' ? 'selected' : '' ?>>15 Days</option>
                                <option value="1_month" <?= ($profile['availability'] ?? '') === '1_month' ? 'selected' : '' ?>>1 Month</option>
                                <option value="2_months" <?= ($profile['availability'] ?? '') === '2_months' ? 'selected' : '' ?>>2 Months</option>
                                <option value="3_months" <?= ($profile['availability'] ?? '') === '3_months' ? 'selected' : '' ?>>3+ Months</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="willing_to_relocate" 
                                       name="willing_to_relocate" value="1"
                                       <?= !empty($profile['willing_to_relocate']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="willing_to_relocate">
                                    Willing to Relocate
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">LinkedIn URL</label>
                            <input type="url" name="linkedin_url" class="form-control" 
                                   value="<?= htmlspecialchars($profile['linkedin_url'] ?? '') ?>"
                                   placeholder="https://linkedin.com/in/yourprofile">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">GitHub URL</label>
                            <input type="url" name="github_url" class="form-control" 
                                   value="<?= htmlspecialchars($profile['github_url'] ?? '') ?>"
                                   placeholder="https://github.com/yourusername">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Portfolio URL</label>
                            <input type="url" name="portfolio_url" class="form-control" 
                                   value="<?= htmlspecialchars($profile['portfolio_url'] ?? '') ?>"
                                   placeholder="https://yourportfolio.com">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Basic Information
                    </button>
                </form>
            </div>

            <!-- Profile Picture & Resume Upload -->
            <div class="section-card">
                <div class="section-header">
                    <h4><i class="fas fa-file-upload"></i> Files Upload</h4>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <form action="/jobseeker/profile/upload-picture" method="POST" enctype="multipart/form-data">
                            <?= csrfField() ?>
                            <label class="form-label">Profile Picture (JPG, PNG - Max 2MB)</label>
                            <?php if (!empty($profile['profile_picture'])): ?>
                                <div class="mb-2">
                                    <img src="/uploads/profiles/<?= htmlspecialchars($profile['profile_picture']) ?>" 
                                         alt="Profile" class="img-thumbnail" style="max-width: 150px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="profile_picture" class="form-control mb-2" accept="image/*" required>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-upload"></i> Upload Picture
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="/jobseeker/profile/upload-resume" method="POST" enctype="multipart/form-data">
                            <?= csrfField() ?>
                            <label class="form-label">Resume/CV (PDF, DOC, DOCX - Max 5MB)</label>
                            <?php if (!empty($profile['resume_file'])): ?>
                                <div class="mb-2">
                                    <a href="/uploads/resumes/<?= htmlspecialchars($profile['resume_file']) ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-file-pdf"></i> View Current Resume
                                    </a>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="resume" class="form-control mb-2" 
                                   accept=".pdf,.doc,.docx,.txt" required>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-upload"></i> Upload Resume
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Work Experience -->
            <div class="section-card">
                <div class="section-header">
                    <h4><i class="fas fa-briefcase"></i> Work Experience</h4>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addWorkExpModal">
                        <i class="fas fa-plus"></i> Add Experience
                    </button>
                </div>

                <?php if (empty($workExperiences)): ?>
                    <p class="text-muted">No work experience added yet. Click "Add Experience" to get started.</p>
                <?php else: ?>
                    <?php foreach ($workExperiences as $exp): ?>
                        <div class="item-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5><?= htmlspecialchars($exp['job_title']) ?></h5>
                                    <p class="mb-1"><strong><?= htmlspecialchars($exp['company_name']) ?></strong></p>
                                    <p class="mb-1 text-muted">
                                        <i class="fas fa-calendar"></i>
                                        <?= formatDate($exp['start_date']) ?> - 
                                        <?= $exp['is_current'] ? 'Present' : formatDate($exp['end_date']) ?>
                                        <?php if ($exp['is_current']): ?>
                                            <span class="badge bg-success ms-2">Current</span>
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($exp['location']): ?>
                                        <p class="mb-1 text-muted"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($exp['location']) ?></p>
                                    <?php endif; ?>
                                    <?php if ($exp['description']): ?>
                                        <p class="mt-2"><?= nl2br(htmlspecialchars($exp['description'])) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <form action="/jobseeker/profile/delete-work-experience" method="POST" style="display:inline;"
                                          onsubmit="return confirm('Delete this work experience?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= $exp['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Education -->
            <div class="section-card">
                <div class="section-header">
                    <h4><i class="fas fa-graduation-cap"></i> Education</h4>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEducationModal">
                        <i class="fas fa-plus"></i> Add Education
                    </button>
                </div>

                <?php if (empty($education)): ?>
                    <p class="text-muted">No education added yet. Click "Add Education" to get started.</p>
                <?php else: ?>
                    <?php foreach ($education as $edu): ?>
                        <div class="item-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5><?= htmlspecialchars($edu['degree']) ?></h5>
                                    <?php if ($edu['field_of_study']): ?>
                                        <p class="mb-1">Field: <?= htmlspecialchars($edu['field_of_study']) ?></p>
                                    <?php endif; ?>
                                    <p class="mb-1"><strong><?= htmlspecialchars($edu['institution']) ?></strong></p>
                                    <?php if ($edu['start_date']): ?>
                                        <p class="mb-1 text-muted">
                                            <i class="fas fa-calendar"></i>
                                            <?= formatDate($edu['start_date']) ?> - <?= formatDate($edu['end_date']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($edu['grade']): ?>
                                        <p class="mb-1">Grade: <strong><?= htmlspecialchars($edu['grade']) ?></strong></p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <form action="/jobseeker/profile/delete-education" method="POST" style="display:inline;"
                                          onsubmit="return confirm('Delete this education?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= $edu['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Skills -->
            <div class="section-card">
                <div class="section-header">
                    <h4><i class="fas fa-code"></i> Skills</h4>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                        <i class="fas fa-plus"></i> Add Skill
                    </button>
                </div>

                <?php if (empty($skills)): ?>
                    <p class="text-muted">No skills added yet. Click "Add Skill" to get started.</p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($skills as $skill): ?>
                            <div class="col-md-4 mb-3">
                                <div class="item-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($skill['skill_name']) ?></h6>
                                            <span class="badge badge-proficiency bg-<?= 
                                                $skill['proficiency'] === 'expert' ? 'danger' : 
                                                ($skill['proficiency'] === 'advanced' ? 'warning' : 
                                                ($skill['proficiency'] === 'intermediate' ? 'info' : 'secondary'))
                                            ?>">
                                                <?= ucfirst($skill['proficiency']) ?>
                                            </span>
                                            <?php if ($skill['years_of_experience'] > 0): ?>
                                                <small class="text-muted d-block mt-1">
                                                    <?= $skill['years_of_experience'] ?> years
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        <form action="/jobseeker/profile/delete-skill" method="POST" style="display:inline;"
                                              onsubmit="return confirm('Delete this skill?');">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $skill['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Certifications -->
            <div class="section-card">
                <div class="section-header">
                    <h4><i class="fas fa-certificate"></i> Certifications</h4>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCertModal">
                        <i class="fas fa-plus"></i> Add Certification
                    </button>
                </div>

                <?php if (empty($certifications)): ?>
                    <p class="text-muted">No certifications added yet. Click "Add Certification" to get started.</p>
                <?php else: ?>
                    <?php foreach ($certifications as $cert): ?>
                        <div class="item-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5><?= htmlspecialchars($cert['certification_name']) ?></h5>
                                    <p class="mb-1"><strong><?= htmlspecialchars($cert['issuing_organization']) ?></strong></p>
                                    <?php if ($cert['issue_date']): ?>
                                        <p class="mb-1 text-muted">
                                            <i class="fas fa-calendar"></i> Issued: <?= formatDate($cert['issue_date']) ?>
                                            <?php if ($cert['expiry_date']): ?>
                                                | Expires: <?= formatDate($cert['expiry_date']) ?>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($cert['credential_url']): ?>
                                        <p class="mb-1">
                                            <a href="<?= htmlspecialchars($cert['credential_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt"></i> View Credential
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <form action="/jobseeker/profile/delete-certification" method="POST" style="display:inline;"
                                          onsubmit="return confirm('Delete this certification?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= $cert['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Languages -->
            <div class="section-card">
                <div class="section-header">
                    <h4><i class="fas fa-language"></i> Languages</h4>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                        <i class="fas fa-plus"></i> Add Language
                    </button>
                </div>

                <?php if (empty($languages)): ?>
                    <p class="text-muted">No languages added yet. Click "Add Language" to get started.</p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($languages as $lang): ?>
                            <div class="col-md-3 mb-3">
                                <div class="item-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($lang['language_name']) ?></h6>
                                            <span class="badge bg-secondary"><?= ucfirst($lang['proficiency']) ?></span>
                                        </div>
                                        <form action="/jobseeker/profile/delete-language" method="POST" style="display:inline;"
                                              onsubmit="return confirm('Delete this language?');">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $lang['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Projects -->
            <div class="section-card">
                <div class="section-header">
                    <h4><i class="fas fa-project-diagram"></i> Projects</h4>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                        <i class="fas fa-plus"></i> Add Project
                    </button>
                </div>

                <?php if (empty($projects)): ?>
                    <p class="text-muted">No projects added yet. Click "Add Project" to get started.</p>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <div class="item-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5><?= htmlspecialchars($project['project_name']) ?></h5>
                                    <?php if ($project['project_url']): ?>
                                        <p class="mb-1">
                                            <a href="<?= htmlspecialchars($project['project_url']) ?>" target="_blank">
                                                <i class="fas fa-link"></i> View Project
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($project['description']): ?>
                                        <p class="mt-2"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                                    <?php endif; ?>
                                    <?php if ($project['technologies_used']): ?>
                                        <p class="mb-1"><strong>Technologies:</strong> <?= htmlspecialchars($project['technologies_used']) ?></p>
                                    <?php endif; ?>
                                    <?php if ($project['start_date']): ?>
                                        <p class="mb-1 text-muted">
                                            <i class="fas fa-calendar"></i>
                                            <?= formatDate($project['start_date']) ?> - 
                                            <?= $project['is_ongoing'] ? 'Ongoing' : formatDate($project['end_date']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <form action="/jobseeker/profile/delete-project" method="POST" style="display:inline;"
                                          onsubmit="return confirm('Delete this project?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= $project['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<?php require __DIR__ . '/profile_modals.php'; ?>

<?php require __DIR__ . '/../common/footer.php'; ?>
