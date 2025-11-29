<?php
/**
 * Employer Controller
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../app/models/Job.php';
require_once __DIR__ . '/../../app/models/Category.php';
require_once __DIR__ . '/../../app/models/Application.php';
require_once __DIR__ . '/../../app/models/JobSeekerProfile.php';
require_once __DIR__ . '/../../app/models/User.php';

class EmployerController {
    private $jobModel;
    private $categoryModel;
    private $applicationModel;
    private $profileModel;
    private $userModel;
    
    public function __construct() {
        requireRole(USER_TYPE_EMPLOYER);
        
        $this->jobModel = new Job();
        $this->categoryModel = new Category();
        $this->applicationModel = new Application();
        $this->profileModel = new JobSeekerProfile();
        $this->userModel = new User();
    }
    
    // View and filter candidates
    public function candidates() {
        $filters = [];
        
        if (!empty($_GET['city'])) {
            $filters['city'] = sanitize($_GET['city']);
        }
        
        if (!empty($_GET['state'])) {
            $filters['state'] = sanitize($_GET['state']);
        }
        
        if (!empty($_GET['skill'])) {
            $filters['skill'] = sanitize($_GET['skill']);
        }
        
        if (!empty($_GET['min_experience'])) {
            $filters['min_experience'] = (int)$_GET['min_experience'];
        }
        
        if (!empty($_GET['max_experience'])) {
            $filters['max_experience'] = (int)$_GET['max_experience'];
        }
        
        if (!empty($_GET['availability'])) {
            $filters['availability'] = sanitize($_GET['availability']);
        }
        
        $candidates = $this->profileModel->searchCandidates($filters);
        
        require __DIR__ . '/../views/employer/candidates.php';
    }
    
    // View candidate full profile
    public function viewCandidate($userId) {
        $user = $this->userModel->findById($userId);
        
        if (!$user || $user['type'] !== USER_TYPE_JOBSEEKER) {
            setFlash('error', 'Candidate not found');
            redirect('/employer/candidates');
        }
        
        $data = $this->profileModel->getCompleteProfile($userId);
        $data['user'] = $user;
        
        require __DIR__ . '/../views/employer/candidate_profile.php';
    }
    
    // Employer dashboard
    public function dashboard() {
        $user = getCurrentUser();
        
        $stats = [
            'total_jobs' => $this->jobModel->count(['employer_id' => $user['id']]),
            'approved_jobs' => $this->jobModel->count([
                'employer_id' => $user['id'],
                'status' => JOB_STATUS_APPROVED
            ]),
            'pending_jobs' => $this->jobModel->count([
                'employer_id' => $user['id'],
                'status' => JOB_STATUS_PENDING
            ]),
        ];
        
        $recentJobs = $this->jobModel->getAll([
            'employer_id' => $user['id'],
            'limit' => 10
        ]);
        
        require __DIR__ . '/../views/employer/dashboard.php';
    }
    
    // List employer's jobs
    public function jobs() {
        $user = getCurrentUser();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        $jobs = $this->jobModel->getAll([
            'employer_id' => $user['id'],
            'limit' => JOBS_PER_PAGE,
            'offset' => ($page - 1) * JOBS_PER_PAGE
        ]);
        
        $totalJobs = $this->jobModel->count(['employer_id' => $user['id']]);
        $pagination = paginate($totalJobs, JOBS_PER_PAGE, $page);
        
        require __DIR__ . '/../views/employer/jobs.php';
    }
    
    // Show post job form
    public function showPostJob() {
        $categories = $this->categoryModel->getAll();
        require __DIR__ . '/../views/employer/post-job.php';
    }
    
    // Handle post job
    public function postJob() {
        checkCSRF();
        
        $user = getCurrentUser();
        
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $location = sanitize($_POST['location'] ?? '');
        $salary = sanitize($_POST['salary'] ?? '');
        $type = sanitize($_POST['type'] ?? JOB_TYPE_FULLTIME);
        
        // Validate
        $errors = [];
        
        if (empty($title) || empty($description) || empty($location)) {
            $errors[] = 'All required fields must be filled';
        }
        
        if ($categoryId <= 0) {
            $errors[] = 'Please select a category';
        }
        
        if (!in_array($type, [JOB_TYPE_FULLTIME, JOB_TYPE_PARTTIME, JOB_TYPE_CONTRACT, JOB_TYPE_INTERNSHIP])) {
            $errors[] = 'Invalid job type';
        }
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            redirect('/employer/post-job');
        }
        
        // Create job
        $jobId = $this->jobModel->create([
            'title' => $title,
            'description' => $description,
            'category_id' => $categoryId,
            'employer_id' => $user['id'],
            'location' => $location,
            'salary' => $salary,
            'type' => $type,
            'status' => JOB_STATUS_PENDING
        ]);
        
        if ($jobId) {
            setFlash('success', SUCCESS_JOB_POSTED);
            redirect('/employer/jobs');
        } else {
            setFlash('error', 'Failed to post job. Please try again.');
            redirect('/employer/post-job');
        }
    }
    
    // View applications for a job
    public function applications($jobId = null) {
        $user = getCurrentUser();
        
        if ($jobId) {
            // View applications for specific job
            $job = $this->jobModel->findById($jobId);
            
            if (!$job || $job['employer_id'] != $user['id']) {
                setFlash('error', ERROR_UNAUTHORIZED);
                redirect('/employer/dashboard');
            }
            
            $applications = $this->applicationModel->getByJob($jobId);
            require __DIR__ . '/../views/employer/job-applications.php';
        } else {
            // View all applications
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $applications = $this->applicationModel->getByEmployer(
                $user['id'],
                APPLICATIONS_PER_PAGE,
                ($page - 1) * APPLICATIONS_PER_PAGE
            );
            
            require __DIR__ . '/../views/employer/applications.php';
        }
    }
    
    // Delete job
    public function deleteJob($id) {
        checkCSRF();
        
        $user = getCurrentUser();
        $job = $this->jobModel->findById($id);
        
        if (!$job || $job['employer_id'] != $user['id']) {
            setFlash('error', ERROR_UNAUTHORIZED);
            redirect('/employer/jobs');
        }
        
        if ($this->jobModel->delete($id)) {
            setFlash('success', 'Job deleted successfully');
        } else {
            setFlash('error', 'Failed to delete job');
        }
        
        redirect('/employer/jobs');
    }
}
