<?php
// Modal forms for adding profile sections
?>

<!-- Add Work Experience Modal -->
<div class="modal fade" id="addWorkExpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Work Experience</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/jobseeker/profile/add-work-experience" method="POST">
                <?= csrfField() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Job Title *</label>
                        <input type="text" name="job_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Company Name *</label>
                        <input type="text" name="company_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g., Mumbai, India">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employment Type</label>
                            <select name="employment_type" class="form-control">
                                <option value="full_time">Full-time</option>
                                <option value="part_time">Part-time</option>
                                <option value="contract">Contract</option>
                                <option value="internship">Internship</option>
                                <option value="freelance">Freelance</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" id="work_end_date" class="form-control">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" name="is_current" 
                                       id="is_current_work" onclick="document.getElementById('work_end_date').disabled = this.checked">
                                <label class="form-check-label" for="is_current_work">Currently working here</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" 
                                  placeholder="Describe your responsibilities and role..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Key Achievements</label>
                        <textarea name="achievements" class="form-control" rows="3"
                                  placeholder="List your major achievements and accomplishments..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Experience</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Education Modal -->
<div class="modal fade" id="addEducationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Education</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/jobseeker/profile/add-education" method="POST">
                <?= csrfField() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Degree *</label>
                        <input type="text" name="degree" class="form-control" 
                               placeholder="e.g., Bachelor of Technology" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Field of Study</label>
                        <input type="text" name="field_of_study" class="form-control"
                               placeholder="e.g., Computer Science">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Institution *</label>
                        <input type="text" name="institution" class="form-control"
                               placeholder="e.g., IIT Delhi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control"
                               placeholder="e.g., New Delhi, India">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grade/CGPA</label>
                        <input type="text" name="grade" class="form-control"
                               placeholder="e.g., 8.5/10 or 85%">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Additional details about coursework, projects, etc..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Education</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/jobseeker/profile/add-skill" method="POST">
                <?= csrfField() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Skill Name *</label>
                        <input type="text" name="skill_name" class="form-control"
                               placeholder="e.g., JavaScript, React, Python" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proficiency Level</label>
                        <select name="proficiency" class="form-control">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate" selected>Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Years of Experience</label>
                        <input type="number" name="years_of_experience" class="form-control" 
                               min="0" max="50" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Skill</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Certification Modal -->
<div class="modal fade" id="addCertModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Certification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/jobseeker/profile/add-certification" method="POST">
                <?= csrfField() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Certification Name *</label>
                        <input type="text" name="certification_name" class="form-control"
                               placeholder="e.g., AWS Certified Solutions Architect" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Issuing Organization *</label>
                        <input type="text" name="issuing_organization" class="form-control"
                               placeholder="e.g., Amazon Web Services" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Issue Date</label>
                            <input type="date" name="issue_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expiry Date (if applicable)</label>
                            <input type="date" name="expiry_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Credential ID</label>
                        <input type="text" name="credential_id" class="form-control"
                               placeholder="e.g., ABC123XYZ">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Credential URL</label>
                        <input type="url" name="credential_url" class="form-control"
                               placeholder="https://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Certification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Language Modal -->
<div class="modal fade" id="addLanguageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Language</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/jobseeker/profile/add-language" method="POST">
                <?= csrfField() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Language *</label>
                        <input type="text" name="language_name" class="form-control"
                               placeholder="e.g., English, Hindi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proficiency Level</label>
                        <select name="proficiency" class="form-control">
                            <option value="basic">Basic</option>
                            <option value="conversational">Conversational</option>
                            <option value="professional" selected>Professional</option>
                            <option value="native">Native</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Language</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/jobseeker/profile/add-project" method="POST">
                <?= csrfField() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Project Name *</label>
                        <input type="text" name="project_name" class="form-control"
                               placeholder="e.g., E-commerce Website" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Project URL</label>
                        <input type="url" name="project_url" class="form-control"
                               placeholder="https://github.com/yourusername/project">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Describe the project, your role, and what you accomplished..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Technologies Used</label>
                        <input type="text" name="technologies_used" class="form-control"
                               placeholder="e.g., React, Node.js, MongoDB, AWS">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" id="project_end_date" class="form-control">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" name="is_ongoing"
                                       id="is_ongoing_project" onclick="document.getElementById('project_end_date').disabled = this.checked">
                                <label class="form-check-label" for="is_ongoing_project">Ongoing project</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Project</button>
                </div>
            </form>
        </div>
    </div>
</div>
