<?php
require_once __DIR__ . '/../models/Job.php';
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../models/JobSeekerProfile.php';
require_once __DIR__ . '/../../includes/functions.php';

class JobSeekerController {
    private $jobModel;
    private $applicationModel;
    private $profileModel;
    
    public function __construct() {
        requireRole(USER_TYPE_JOBSEEKER);
        $this->jobModel = new Job();
        $this->applicationModel = new Application();
        $this->profileModel = new JobSeekerProfile();
    }
    
    public function dashboard() {
        $user = getCurrentUser();
        
        $stats = [
            'applications' => $this->applicationModel->countByUser($user['id'])
        ];
        
        $recentApplications = $this->applicationModel->getByUser($user['id'], 5);
        $latestJobs = $this->jobModel->getAll(['status' => JOB_STATUS_APPROVED, 'limit' => 5]);
        
        require __DIR__ . '/../views/jobseeker/dashboard.php';
    }
    
    public function applications() {
        $user = getCurrentUser();
        $applications = $this->applicationModel->getByUser($user['id']);
        
        require __DIR__ . '/../views/jobseeker/applications.php';
    }
    
    // Profile Management
    public function showProfile() {
        $user = getCurrentUser();
        $data = $this->profileModel->getCompleteProfile($user['id']);
        $data['user'] = $user;
        
        require __DIR__ . '/../views/jobseeker/profile.php';
    }
    
    public function editProfile() {
        $user = getCurrentUser();
        $data = $this->profileModel->getCompleteProfile($user['id']);
        $data['user'] = $user;
        
        require __DIR__ . '/../views/jobseeker/edit_profile.php';
    }
    
    public function updateBasicInfo() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        
        $data = [
            'headline' => sanitize($_POST['headline'] ?? ''),
            'summary' => sanitize($_POST['summary'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'address' => sanitize($_POST['address'] ?? ''),
            'city' => sanitize($_POST['city'] ?? ''),
            'state' => sanitize($_POST['state'] ?? ''),
            'country' => sanitize($_POST['country'] ?? 'India'),
            'postal_code' => sanitize($_POST['postal_code'] ?? ''),
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
            'gender' => $_POST['gender'] ?? null,
            'linkedin_url' => sanitize($_POST['linkedin_url'] ?? ''),
            'github_url' => sanitize($_POST['github_url'] ?? ''),
            'portfolio_url' => sanitize($_POST['portfolio_url'] ?? ''),
            'total_experience_years' => (int)($_POST['total_experience_years'] ?? 0),
            'current_salary' => $_POST['current_salary'] ?? null,
            'expected_salary' => $_POST['expected_salary'] ?? null,
            'notice_period' => sanitize($_POST['notice_period'] ?? ''),
            'availability' => $_POST['availability'] ?? 'immediate',
            'willing_to_relocate' => isset($_POST['willing_to_relocate']) ? 1 : 0
        ];
        
        $profile = $this->profileModel->getProfile($user['id']);
        
        if ($profile) {
            $this->profileModel->updateProfile($user['id'], $data);
        } else {
            $data['user_id'] = $user['id'];
            $this->profileModel->createProfile($data);
        }
        
        setFlash('success', 'Profile updated successfully');
        redirect('/jobseeker/profile');
    }
    
    public function uploadProfilePicture() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        
        if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', 'Please select a valid image file');
            redirect('/jobseeker/profile/edit');
        }
        
        $file = $_FILES['profile_picture'];
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        $errors = validateFileUpload($file, $allowedTypes, $maxSize);
        
        if (!empty($errors)) {
            setFlash('error', implode(', ', $errors));
            redirect('/jobseeker/profile/edit');
        }
        
        $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = uploadFile($file, $uploadDir);
        
        if ($filename) {
            $profile = $this->profileModel->getProfile($user['id']);
            if (!$profile) {
                $this->profileModel->createProfile(['user_id' => $user['id']]);
            }
            $this->profileModel->updateProfilePicture($user['id'], $filename);
            setFlash('success', 'Profile picture updated successfully');
        } else {
            setFlash('error', 'Failed to upload profile picture');
        }
        
        redirect('/jobseeker/profile/edit');
    }
    
    public function uploadResume() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        
        if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', 'Please select a valid resume file');
            redirect('/jobseeker/profile/edit');
        }
        
        $file = $_FILES['resume'];
        $errors = validateFileUpload($file);
        
        if (!empty($errors)) {
            setFlash('error', implode(', ', $errors));
            redirect('/jobseeker/profile/edit');
        }
        
        $filename = uploadFile($file, RESUME_UPLOAD_PATH);
        
        if ($filename) {
            $profile = $this->profileModel->getProfile($user['id']);
            if (!$profile) {
                $this->profileModel->createProfile(['user_id' => $user['id']]);
            }
            $this->profileModel->updateResume($user['id'], $filename);
            setFlash('success', 'Resume uploaded successfully');
        } else {
            setFlash('error', 'Failed to upload resume');
        }
        
        redirect('/jobseeker/profile/edit');
    }
    
    // Work Experience
    public function addWorkExperience() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        
        $data = [
            'user_id' => $user['id'],
            'job_title' => sanitize($_POST['job_title']),
            'company_name' => sanitize($_POST['company_name']),
            'location' => sanitize($_POST['location'] ?? ''),
            'employment_type' => $_POST['employment_type'] ?? 'full_time',
            'start_date' => $_POST['start_date'],
            'end_date' => isset($_POST['is_current']) ? null : ($_POST['end_date'] ?? null),
            'is_current' => isset($_POST['is_current']) ? 1 : 0,
            'description' => sanitize($_POST['description'] ?? ''),
            'achievements' => sanitize($_POST['achievements'] ?? '')
        ];
        
        $this->profileModel->addWorkExperience($data);
        setFlash('success', 'Work experience added successfully');
        redirect('/jobseeker/profile/edit');
    }
    
    public function deleteWorkExperience() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        $id = $_POST['id'];
        
        $this->profileModel->deleteWorkExperience($id, $user['id']);
        setFlash('success', 'Work experience deleted');
        redirect('/jobseeker/profile/edit');
    }
    
    // Education
    public function addEducation() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        
        $data = [
            'user_id' => $user['id'],
            'degree' => sanitize($_POST['degree']),
            'field_of_study' => sanitize($_POST['field_of_study'] ?? ''),
            'institution' => sanitize($_POST['institution']),
            'location' => sanitize($_POST['location'] ?? ''),
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'grade' => sanitize($_POST['grade'] ?? ''),
            'description' => sanitize($_POST['description'] ?? '')
        ];
        
        $this->profileModel->addEducation($data);
        setFlash('success', 'Education added successfully');
        redirect('/jobseeker/profile/edit');
    }
    
    public function deleteEducation() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        $id = $_POST['id'];
        
        $this->profileModel->deleteEducation($id, $user['id']);
        setFlash('success', 'Education deleted');
        redirect('/jobseeker/profile/edit');
    }
    
    // Skills
    public function addSkill() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        
        $data = [
            'user_id' => $user['id'],
            'skill_name' => sanitize($_POST['skill_name']),
            'proficiency' => $_POST['proficiency'] ?? 'intermediate',
            'years_of_experience' => (int)($_POST['years_of_experience'] ?? 0)
        ];
        
        $this->profileModel->addSkill($data);
        setFlash('success', 'Skill added successfully');
        redirect('/jobseeker/profile/edit');
    }
    
    public function deleteSkill() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        $id = $_POST['id'];
        
        $this->profileModel->deleteSkill($id, $user['id']);
        setFlash('success', 'Skill deleted');
        redirect('/jobseeker/profile/edit');
    }
    
    // Certifications
    public function addCertification() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        
        $data = [
            'user_id' => $user['id'],
            'certification_name' => sanitize($_POST['certification_name']),
            'issuing_organization' => sanitize($_POST['issuing_organization']),
            'issue_date' => $_POST['issue_date'] ?? null,
            'expiry_date' => $_POST['expiry_date'] ?? null,
            'credential_id' => sanitize($_POST['credential_id'] ?? ''),
            'credential_url' => sanitize($_POST['credential_url'] ?? ''),
            'description' => sanitize($_POST['description'] ?? '')
        ];
        
        $this->profileModel->addCertification($data);
        setFlash('success', 'Certification added successfully');
        redirect('/jobseeker/profile/edit');
    }
    
    public function deleteCertification() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        $id = $_POST['id'];
        
        $this->profileModel->deleteCertification($id, $user['id']);
        setFlash('success', 'Certification deleted');
        redirect('/jobseeker/profile/edit');
    }
    
    // Languages
    public function addLanguage() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        
        $data = [
            'user_id' => $user['id'],
            'language_name' => sanitize($_POST['language_name']),
            'proficiency' => $_POST['proficiency'] ?? 'professional'
        ];
        
        $this->profileModel->addLanguage($data);
        setFlash('success', 'Language added successfully');
        redirect('/jobseeker/profile/edit');
    }
    
    public function deleteLanguage() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        $id = $_POST['id'];
        
        $this->profileModel->deleteLanguage($id, $user['id']);
        setFlash('success', 'Language deleted');
        redirect('/jobseeker/profile/edit');
    }
    
    // Projects
    public function addProject() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        
        $data = [
            'user_id' => $user['id'],
            'project_name' => sanitize($_POST['project_name']),
            'project_url' => sanitize($_POST['project_url'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'technologies_used' => sanitize($_POST['technologies_used'] ?? ''),
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => isset($_POST['is_ongoing']) ? null : ($_POST['end_date'] ?? null),
            'is_ongoing' => isset($_POST['is_ongoing']) ? 1 : 0
        ];
        
        $this->profileModel->addProject($data);
        setFlash('success', 'Project added successfully');
        redirect('/jobseeker/profile/edit');
    }
    
    public function deleteProject() {
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            setFlash('error', 'Invalid request');
            redirect('/jobseeker/profile/edit');
        }
        
        $user = getCurrentUser();
        $id = $_POST['id'];
        
        $this->profileModel->deleteProject($id, $user['id']);
        setFlash('success', 'Project deleted');
        redirect('/jobseeker/profile/edit');
    }
}
