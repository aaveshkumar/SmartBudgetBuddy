<?php
/**
 * Job Controller
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/seo.php';
require_once __DIR__ . '/../../app/models/Job.php';
require_once __DIR__ . '/../../app/models/Category.php';
require_once __DIR__ . '/../../app/models/Application.php';

class JobController {
    private $jobModel;
    private $categoryModel;
    private $applicationModel;
    
    public function __construct() {
        $this->jobModel = new Job();
        $this->categoryModel = new Category();
        $this->applicationModel = new Application();
    }
    
    // List all jobs
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $filters = [
            'status' => JOB_STATUS_APPROVED,
            'limit' => JOBS_PER_PAGE,
            'offset' => ($page - 1) * JOBS_PER_PAGE
        ];
        
        if (!empty($_GET['category'])) {
            $filters['category_id'] = (int)$_GET['category'];
        }
        
        if (!empty($_GET['type'])) {
            $filters['type'] = sanitize($_GET['type']);
        }
        
        if (!empty($_GET['location'])) {
            $filters['location'] = sanitize($_GET['location']);
        }
        
        if (!empty($_GET['search'])) {
            $filters['search'] = sanitize($_GET['search']);
        }
        
        $jobs = $this->jobModel->getAll($filters);
        $totalJobs = $this->jobModel->count(['status' => JOB_STATUS_APPROVED]);
        $pagination = paginate($totalJobs, JOBS_PER_PAGE, $page);
        
        $categories = $this->categoryModel->getAll();
        
        require __DIR__ . '/../views/jobs/index.php';
    }
    
    // Show single job
    public function show($id) {
        $job = $this->jobModel->findById($id);
        
        if (!$job || $job['status'] !== JOB_STATUS_APPROVED) {
            http_response_code(404);
            die('Job not found');
        }
        
        // Check if user has applied
        $hasApplied = false;
        if (isLoggedIn()) {
            $user = getCurrentUser();
            $hasApplied = $this->applicationModel->hasApplied($id, $user['id']);
        }
        
        require __DIR__ . '/../views/jobs/show.php';
    }
    
    // Apply for job
    public function apply($jobId) {
        requireAuth();
        checkCSRF();
        
        $user = getCurrentUser();
        
        // Only job seekers can apply
        if ($user['type'] !== USER_TYPE_JOBSEEKER) {
            setFlash('error', 'Only job seekers can apply for jobs');
            redirect('/jobs/' . $jobId);
        }
        
        // Check if job exists
        $job = $this->jobModel->findById($jobId);
        if (!$job || $job['status'] !== JOB_STATUS_APPROVED) {
            setFlash('error', 'Job not found');
            redirect('/jobs');
        }
        
        // Check if already applied
        if ($this->applicationModel->hasApplied($jobId, $user['id'])) {
            setFlash('error', 'You have already applied for this job');
            redirect('/jobs/' . $jobId);
        }
        
        // Validate file upload
        if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', 'Please upload your resume');
            redirect('/jobs/' . $jobId);
        }
        
        $errors = validateFileUpload($_FILES['resume']);
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            redirect('/jobs/' . $jobId);
        }
        
        // Upload resume
        $resumePath = uploadFile($_FILES['resume'], UPLOAD_PATH . 'resumes/');
        
        if (!$resumePath) {
            setFlash('error', 'Failed to upload resume');
            redirect('/jobs/' . $jobId);
        }
        
        // Create application
        $applicationId = $this->applicationModel->create($jobId, $user['id'], $resumePath);
        
        if ($applicationId) {
            setFlash('success', SUCCESS_APPLICATION);
        } else {
            setFlash('error', 'Application failed. Please try again.');
        }
        
        redirect('/jobs/' . $jobId);
    }
    
    // Search jobs (AJAX)
    public function search() {
        header('Content-Type: application/json');
        
        $query = sanitize($_GET['q'] ?? '');
        
        if (strlen($query) < 2) {
            echo json_encode([]);
            exit;
        }
        
        $results = $this->jobModel->search($query, 10);
        echo json_encode($results);
    }
}
