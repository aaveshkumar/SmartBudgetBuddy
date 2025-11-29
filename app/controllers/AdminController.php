<?php
/**
 * Admin Controller
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../app/models/User.php';
require_once __DIR__ . '/../../app/models/Job.php';
require_once __DIR__ . '/../../app/models/Application.php';
require_once __DIR__ . '/../../app/models/JobSeekerProfile.php';

class AdminController {
    private $userModel;
    private $jobModel;
    private $applicationModel;
    private $profileModel;
    
    public function __construct() {
        requireRole(USER_TYPE_ADMIN);
        
        $this->userModel = new User();
        $this->jobModel = new Job();
        $this->applicationModel = new Application();
        $this->profileModel = new JobSeekerProfile();
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
        
        require __DIR__ . '/../views/admin/candidates.php';
    }
    
    // View candidate full profile
    public function viewCandidate($userId) {
        $user = $this->userModel->findById($userId);
        
        if (!$user || $user['type'] !== USER_TYPE_JOBSEEKER) {
            setFlash('error', 'Candidate not found');
            redirect('/admin/candidates');
        }
        
        $data = $this->profileModel->getCompleteProfile($userId);
        $data['user'] = $user;
        
        require __DIR__ . '/../views/admin/candidate_profile.php';
    }
    
    // Admin dashboard
    public function dashboard() {
        $stats = [
            'total_users' => $this->userModel->count(),
            'jobseekers' => $this->userModel->count(['type' => USER_TYPE_JOBSEEKER]),
            'employers' => $this->userModel->count(['type' => USER_TYPE_EMPLOYER]),
            'total_jobs' => $this->jobModel->count(),
            'pending_jobs' => $this->jobModel->count(['status' => JOB_STATUS_PENDING]),
            'approved_jobs' => $this->jobModel->count(['status' => JOB_STATUS_APPROVED]),
        ];
        
        $recentJobs = $this->jobModel->getAll(['limit' => 10]);
        
        require __DIR__ . '/../views/admin/dashboard.php';
    }
    
    // Manage users
    public function users() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $filters = [
            'limit' => 20,
            'offset' => ($page - 1) * 20
        ];
        
        if (!empty($_GET['type'])) {
            $filters['type'] = sanitize($_GET['type']);
        }
        
        if (!empty($_GET['search'])) {
            $filters['search'] = sanitize($_GET['search']);
        }
        
        $users = $this->userModel->getAll($filters);
        $totalUsers = $this->userModel->count($filters);
        $pagination = paginate($totalUsers, 20, $page);
        
        require __DIR__ . '/../views/admin/users.php';
    }
    
    // Delete user
    public function deleteUser($id) {
        checkCSRF();
        
        if ($this->userModel->delete($id)) {
            setFlash('success', 'User deleted successfully');
        } else {
            setFlash('error', 'Failed to delete user');
        }
        
        redirect('/admin/users');
    }
    
    // Manage jobs
    public function jobs() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $filters = [
            'limit' => 20,
            'offset' => ($page - 1) * 20
        ];
        
        if (!empty($_GET['status'])) {
            $filters['status'] = sanitize($_GET['status']);
        }
        
        $jobs = $this->jobModel->getAll($filters);
        $totalJobs = $this->jobModel->count($filters);
        $pagination = paginate($totalJobs, 20, $page);
        
        require __DIR__ . '/../views/admin/jobs.php';
    }
    
    // Approve job
    public function approveJob($id) {
        checkCSRF();
        
        if ($this->jobModel->update($id, ['status' => JOB_STATUS_APPROVED])) {
            setFlash('success', 'Job approved successfully');
        } else {
            setFlash('error', 'Failed to approve job');
        }
        
        redirect('/admin/jobs');
    }
    
    // Reject job
    public function rejectJob($id) {
        checkCSRF();
        
        if ($this->jobModel->update($id, ['status' => JOB_STATUS_REJECTED])) {
            setFlash('success', 'Job rejected successfully');
        } else {
            setFlash('error', 'Failed to reject job');
        }
        
        redirect('/admin/jobs');
    }
    
    // Delete job
    public function deleteJob($id) {
        checkCSRF();
        
        if ($this->jobModel->delete($id)) {
            setFlash('success', 'Job deleted successfully');
        } else {
            setFlash('error', 'Failed to delete job');
        }
        
        redirect('/admin/jobs');
    }
}
