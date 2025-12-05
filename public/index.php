<?php
/**
 * Front Controller - Routes all requests
 */

// Error reporting for development
if (defined('APP_ENV') && APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Load configuration and utilities
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/seo.php';

// Initialize session
initSession();

// Get the request URI and method
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Remove query string and trim slashes
$path = strtok($requestUri, '?');
$path = trim($path, '/');

// Simple router
try {
    // Homepage
    if (empty($path) || $path === 'index.php') {
        require_once __DIR__ . '/../app/models/Job.php';
        require_once __DIR__ . '/../app/models/Category.php';
        
        $jobModel = new Job();
        $categoryModel = new Category();
        
        $latestJobs = $jobModel->getLatest(6);
        $categories = $categoryModel->getAll();
        
        require __DIR__ . '/../app/views/home.php';
        exit;
    }
    
    // Authentication routes
    if ($path === 'login') {
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        
        if ($requestMethod === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        exit;
    }
    
    if ($path === 'register') {
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        
        if ($requestMethod === 'POST') {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        exit;
    }
    
    if ($path === 'logout') {
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        exit;
    }
    
    if ($path === 'forgot-password') {
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        
        if ($requestMethod === 'POST') {
            $controller->requestPasswordReset();
        } else {
            $controller->showForgotPassword();
        }
        exit;
    }
    
    if ($path === 'reset-password') {
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        
        if ($requestMethod === 'POST') {
            $controller->resetPassword();
        } else {
            $controller->showResetPassword();
        }
        exit;
    }
    
    // Jobs routes
    if ($path === 'jobs') {
        require_once __DIR__ . '/../app/controllers/JobController.php';
        $controller = new JobController();
        $controller->index();
        exit;
    }
    
    if (preg_match('/^jobs\/(\d+)$/', $path, $matches)) {
        require_once __DIR__ . '/../app/controllers/JobController.php';
        $controller = new JobController();
        $controller->show($matches[1]);
        exit;
    }
    
    if (preg_match('/^jobs\/(\d+)\/apply$/', $path, $matches)) {
        require_once __DIR__ . '/../app/controllers/JobController.php';
        $controller = new JobController();
        $controller->apply($matches[1]);
        exit;
    }
    
    // Notification routes
    if (preg_match('/^notifications(.*)/', $path, $matches)) {
        require_once __DIR__ . '/../app/controllers/NotificationController.php';
        $controller = new NotificationController();
        $action = trim($matches[1], '/');
        
        if (empty($action)) {
            $controller->index();
        } elseif ($action === 'count') {
            $controller->getUnreadCount();
        } elseif ($action === 'recent') {
            $controller->getRecent();
        } elseif ($action === 'poll') {
            $controller->poll();
        } elseif ($action === 'mark-all-read' && $requestMethod === 'POST') {
            $controller->markAllAsRead();
        } elseif (preg_match('/^(\d+)\/read$/', $action, $m) && $requestMethod === 'POST') {
            $controller->markAsRead($m[1]);
        } elseif (preg_match('/^(\d+)\/delete$/', $action, $m) && $requestMethod === 'POST') {
            $controller->delete($m[1]);
        }
        exit;
    }
    
    // Chat routes
    if (preg_match('/^chat(.*)/', $path, $matches)) {
        require_once __DIR__ . '/../app/controllers/ChatController.php';
        $controller = new ChatController();
        $action = trim($matches[1], '/');
        
        if (empty($action)) {
            $controller->index();
        } elseif ($action === 'unread-count') {
            $controller->getUnreadCount();
        } elseif (preg_match('/^start\/(\d+)$/', $action, $m)) {
            $controller->startConversation($m[1]);
        } elseif (preg_match('/^(\d+)$/', $action, $m)) {
            $controller->conversation($m[1]);
        } elseif (preg_match('/^(\d+)\/send$/', $action, $m) && $requestMethod === 'POST') {
            $controller->sendMessage($m[1]);
        } elseif (preg_match('/^(\d+)\/messages$/', $action, $m)) {
            $controller->getMessages($m[1]);
        }
        exit;
    }
    
    // CSRF token refresh endpoint
    if ($path === 'csrf/token') {
        header('Content-Type: application/json');
        echo json_encode(['token' => getCSRFToken()]);
        exit;
    }
    
    // Report route
    if ($path === 'report/submit' && $requestMethod === 'POST') {
        require_once __DIR__ . '/../app/controllers/ReportController.php';
        $controller = new ReportController();
        $controller->submitReport();
        exit;
    }
    
    // Admin routes
    if (preg_match('/^admin\/(.+)/', $path, $matches)) {
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        $controller = new AdminController();
        $action = $matches[1];
        
        if ($action === 'dashboard') {
            $controller->dashboard();
        } elseif ($action === 'users') {
            $controller->users();
        } elseif (preg_match('/^users\/(\d+)$/', $action, $m)) {
            $controller->viewUser($m[1]);
        } elseif (preg_match('/^users\/(\d+)\/update$/', $action, $m)) {
            $controller->updateUser($m[1]);
        } elseif (preg_match('/^users\/(\d+)\/delete$/', $action, $m)) {
            $controller->deleteUser($m[1]);
        } elseif ($action === 'jobs') {
            $controller->jobs();
        } elseif (preg_match('/^jobs\/(\d+)$/', $action, $m)) {
            $controller->viewJob($m[1]);
        } elseif (preg_match('/^jobs\/(\d+)\/approve$/', $action, $m)) {
            $controller->approveJob($m[1]);
        } elseif (preg_match('/^jobs\/(\d+)\/reject$/', $action, $m)) {
            $controller->rejectJob($m[1]);
        } elseif (preg_match('/^jobs\/(\d+)\/delete$/', $action, $m)) {
            $controller->deleteJob($m[1]);
        } elseif ($action === 'candidates') {
            $controller->candidates();
        } elseif (preg_match('/^candidates\/(\d+)$/', $action, $m)) {
            $controller->viewCandidate($m[1]);
        } elseif ($action === 'notifications') {
            $controller->notifications();
        } elseif ($action === 'notifications/send' && $requestMethod === 'POST') {
            $controller->sendSystemNotification();
        } elseif ($action === 'notifications/delete' && $requestMethod === 'POST') {
            $controller->deleteSystemNotification();
        } elseif ($action === 'reports') {
            $controller->reports();
        } elseif (preg_match('/^reports\/(\d+)$/', $action, $m)) {
            $controller->viewReport($m[1]);
        } elseif (preg_match('/^reports\/(\d+)\/update$/', $action, $m) && $requestMethod === 'POST') {
            $controller->updateReport($m[1]);
        } elseif (preg_match('/^reports\/(\d+)\/delete$/', $action, $m) && $requestMethod === 'POST') {
            $controller->deleteReport($m[1]);
        }
        exit;
    }
    
    // Employer routes
    if (preg_match('/^employer\/(.+)/', $path, $matches)) {
        require_once __DIR__ . '/../app/controllers/EmployerController.php';
        $controller = new EmployerController();
        $action = $matches[1];
        
        if ($action === 'dashboard') {
            $controller->dashboard();
        } elseif ($action === 'jobs') {
            $controller->jobs();
        } elseif ($action === 'post-job') {
            if ($requestMethod === 'POST') {
                $controller->postJob();
            } else {
                $controller->showPostJob();
            }
        } elseif (preg_match('/^jobs\/(\d+)\/applications$/', $action, $m)) {
            $controller->applications($m[1]);
        } elseif (preg_match('/^jobs\/(\d+)\/applications\/(\d+)\/select$/', $action, $m)) {
            $controller->selectCandidate($m[1], $m[2]);
        } elseif (preg_match('/^jobs\/(\d+)\/delete$/', $action, $m)) {
            $controller->deleteJob($m[1]);
        } elseif ($action === 'candidates') {
            $controller->candidates();
        } elseif (preg_match('/^candidates\/(\d+)$/', $action, $m)) {
            $controller->viewCandidate($m[1]);
        }
        exit;
    }
    
    // Jobseeker routes
    if (preg_match('/^jobseeker\/(.+)/', $path, $matches)) {
        require_once __DIR__ . '/../app/controllers/JobSeekerController.php';
        $controller = new JobSeekerController();
        $action = $matches[1];
        
        if ($action === 'dashboard') {
            $controller->dashboard();
        } elseif ($action === 'applications') {
            $controller->applications();
        } elseif ($action === 'profile') {
            $controller->showProfile();
        } elseif ($action === 'profile/edit') {
            $controller->editProfile();
        } elseif ($action === 'profile/update-basic' && $requestMethod === 'POST') {
            $controller->updateBasicInfo();
        } elseif ($action === 'profile/upload-picture' && $requestMethod === 'POST') {
            $controller->uploadProfilePicture();
        } elseif ($action === 'profile/upload-resume' && $requestMethod === 'POST') {
            $controller->uploadResume();
        } elseif ($action === 'profile/add-work-experience' && $requestMethod === 'POST') {
            $controller->addWorkExperience();
        } elseif ($action === 'profile/delete-work-experience' && $requestMethod === 'POST') {
            $controller->deleteWorkExperience();
        } elseif ($action === 'profile/add-education' && $requestMethod === 'POST') {
            $controller->addEducation();
        } elseif ($action === 'profile/delete-education' && $requestMethod === 'POST') {
            $controller->deleteEducation();
        } elseif ($action === 'profile/add-skill' && $requestMethod === 'POST') {
            $controller->addSkill();
        } elseif ($action === 'profile/delete-skill' && $requestMethod === 'POST') {
            $controller->deleteSkill();
        } elseif ($action === 'profile/add-certification' && $requestMethod === 'POST') {
            $controller->addCertification();
        } elseif ($action === 'profile/delete-certification' && $requestMethod === 'POST') {
            $controller->deleteCertification();
        } elseif ($action === 'profile/add-language' && $requestMethod === 'POST') {
            $controller->addLanguage();
        } elseif ($action === 'profile/delete-language' && $requestMethod === 'POST') {
            $controller->deleteLanguage();
        } elseif ($action === 'profile/add-project' && $requestMethod === 'POST') {
            $controller->addProject();
        } elseif ($action === 'profile/delete-project' && $requestMethod === 'POST') {
            $controller->deleteProject();
        }
        exit;
    }
    
    // 404 Not Found
    http_response_code(404);
    echo '<h1>404 - Page Not Found</h1>';
    echo '<p><a href="/">Return to homepage</a></p>';
    
} catch (Exception $e) {
    http_response_code(500);
    if (APP_ENV === 'development') {
        echo '<h1>Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>500 - Internal Server Error</h1>';
        echo '<p>Something went wrong. Please try again later.</p>';
    }
}
