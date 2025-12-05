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
require_once __DIR__ . '/../../app/models/Notification.php';
require_once __DIR__ . '/../../app/models/Report.php';

class AdminController {
    private $userModel;
    private $jobModel;
    private $applicationModel;
    private $profileModel;
    private $reportModel;
    
    public function __construct() {
        requireRole(USER_TYPE_ADMIN);
        
        $this->userModel = new User();
        $this->jobModel = new Job();
        $this->applicationModel = new Application();
        $this->profileModel = new JobSeekerProfile();
        $this->reportModel = new Report();
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
    
    // View user details
    public function viewUser($id) {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            setFlash('error', 'User not found');
            redirect('/admin/users');
        }
        
        require __DIR__ . '/../views/admin/view_user.php';
    }
    
    // Update user
    public function updateUser($id) {
        checkCSRF();
        
        $user = $this->userModel->findById($id);
        if (!$user) {
            setFlash('error', 'User not found');
            redirect('/admin/users');
        }
        
        $errors = [];
        $data = [];
        
        // Validate name
        if (empty($_POST['name'])) {
            $errors[] = 'Name is required';
        } else {
            $data['name'] = sanitize($_POST['name']);
        }
        
        // Validate email
        if (empty($_POST['email'])) {
            $errors[] = 'Email is required';
        } else {
            $email = sanitize($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email address';
            } elseif ($this->userModel->emailExists($email, $id)) {
                $errors[] = 'Email already exists';
            } else {
                $data['email'] = $email;
            }
        }
        
        // Update type if provided
        if (!empty($_POST['type']) && in_array($_POST['type'], ['admin', 'employer', 'jobseeker'])) {
            $data['type'] = sanitize($_POST['type']);
        }
        
        // Update verified status if provided
        if (isset($_POST['verified'])) {
            $data['verified'] = (int)$_POST['verified'];
        }
        
        // Update password if provided
        if (!empty($_POST['password'])) {
            if (strlen($_POST['password']) < 6) {
                $errors[] = 'Password must be at least 6 characters';
            } else {
                $data['password'] = $_POST['password'];
            }
        }
        
        if (!empty($errors)) {
            setFlash('error', implode(', ', $errors));
            redirect("/admin/users/$id");
            return;
        }
        
        if ($this->userModel->update($id, $data)) {
            setFlash('success', 'User updated successfully');
        } else {
            setFlash('error', 'Failed to update user');
        }
        
        redirect('/admin/users');
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
    
    // View single job (admin can view regardless of status)
    public function viewJob($id) {
        $job = $this->jobModel->findById($id);
        
        if (!$job) {
            setFlash('error', 'Job not found');
            redirect('/admin/jobs');
        }
        
        require __DIR__ . '/../views/admin/view_job.php';
    }
    
    // Approve job
    public function approveJob($id) {
        checkCSRF();
        
        if ($this->jobModel->update($id, ['status' => JOB_STATUS_APPROVED])) {
            // Get job details for notification
            $job = $this->jobModel->findById($id);
            if ($job) {
                // Send notification to all job seekers about the new job
                $notificationModel = new Notification();
                $jobSeekers = $notificationModel->getJobSeekersByRole();
                $employer = $this->userModel->findById($job['employer_id']);
                $companyName = $employer ? $employer['name'] : 'A company';
                
                foreach ($jobSeekers as $jobSeekerId) {
                    $notificationModel->notifyNewJob($jobSeekerId, $job['id'], $job['title'], $companyName);
                }
            }
            setFlash('success', 'Job approved successfully. All job seekers have been notified.');
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
    
    // Show system notifications page
    public function notifications() {
        $notificationModel = new Notification();
        $systemNotifications = $notificationModel->getSystemNotifications();
        
        $meta = generateMetaTags('System Notifications', 'Send notifications to users');
        require __DIR__ . '/../views/admin/notifications.php';
    }
    
    // Send system notification to employers
    public function sendSystemNotification() {
        checkCSRF();
        
        $title = sanitize($_POST['title'] ?? '');
        $message = sanitize($_POST['message'] ?? '');
        $targetRole = sanitize($_POST['target_role'] ?? 'employer');
        
        if (empty($title) || empty($message)) {
            setFlash('error', 'Title and message are required');
            redirect('/admin/notifications');
        }
        
        $notificationModel = new Notification();
        $count = 0;
        
        if ($targetRole === 'employer' || $targetRole === 'all') {
            $employers = $notificationModel->getEmployersByRole();
            foreach ($employers as $employerId) {
                $notificationModel->notifySystemUpdate($employerId, $title, $message);
                $count++;
            }
        }
        
        if ($targetRole === 'jobseeker' || $targetRole === 'all') {
            $jobSeekers = $notificationModel->getJobSeekersByRole();
            foreach ($jobSeekers as $jobSeekerId) {
                $notificationModel->notifySystemUpdate($jobSeekerId, $title, $message);
                $count++;
            }
        }
        
        setFlash('success', "System notification sent to $count users successfully!");
        redirect('/admin/notifications');
    }
    
    // Delete system notification
    public function deleteSystemNotification() {
        checkCSRF();
        
        $title = $_POST['title'] ?? '';
        $message = $_POST['message'] ?? '';
        
        if (empty($title) || empty($message)) {
            setFlash('error', 'Invalid notification data');
            redirect('/admin/notifications');
        }
        
        $notificationModel = new Notification();
        if ($notificationModel->deleteSystemNotificationsByContent($title, $message)) {
            setFlash('success', 'Notification deleted successfully');
        } else {
            setFlash('error', 'Failed to delete notification');
        }
        
        redirect('/admin/notifications');
    }
    
    // View all reports
    public function reports() {
        $status = sanitize($_GET['status'] ?? '');
        $reports = $this->reportModel->getAll($status ?: null);
        $statusCounts = $this->reportModel->getCountByStatus();
        
        $meta = generateMetaTags('Reports Management', 'View and manage reported content');
        require __DIR__ . '/../views/admin/reports.php';
    }
    
    // View single report with details
    public function viewReport($id) {
        $report = $this->reportModel->findById($id);
        
        if (!$report) {
            setFlash('error', 'Report not found');
            redirect('/admin/reports');
        }
        
        if ($report['reported_type'] === 'job') {
            $reportedItem = $this->reportModel->getReportedJobDetails($report['reported_id']);
        } else {
            $reportedItem = $this->reportModel->getReportedUserDetails($report['reported_id']);
        }
        
        $meta = generateMetaTags('View Report', 'Report details');
        require __DIR__ . '/../views/admin/view_report.php';
    }
    
    // Update report status
    public function updateReport($id) {
        checkCSRF();
        
        $status = sanitize($_POST['status'] ?? '');
        $adminNotes = sanitize($_POST['admin_notes'] ?? '');
        
        if (!in_array($status, ['pending', 'reviewed', 'resolved', 'dismissed'])) {
            setFlash('error', 'Invalid status');
            redirect('/admin/reports');
        }
        
        if ($this->reportModel->updateStatus($id, $status, $adminNotes)) {
            setFlash('success', 'Report status updated successfully');
        } else {
            setFlash('error', 'Failed to update report');
        }
        
        redirect("/admin/reports/$id");
    }
    
    // Delete report
    public function deleteReport($id) {
        checkCSRF();
        
        if ($this->reportModel->delete($id)) {
            setFlash('success', 'Report deleted successfully');
        } else {
            setFlash('error', 'Failed to delete report');
        }
        
        redirect('/admin/reports');
    }
}
