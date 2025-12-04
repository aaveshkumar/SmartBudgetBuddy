<?php
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/csrf.php';

class NotificationController {
    private $notificationModel;
    
    public function __construct() {
        $this->notificationModel = new Notification();
    }
    
    public function index() {
        $user = getCurrentUser();
        if (!$user) {
            redirect('/login');
        }
        
        $notifications = $this->notificationModel->getByUserId($user['id']);
        $unreadCount = $this->notificationModel->getUnreadCount($user['id']);
        
        $meta = generateMetaTags('Notifications', 'View your notifications');
        require __DIR__ . '/../views/notifications/index.php';
    }
    
    public function getUnreadCount() {
        header('Content-Type: application/json');
        
        $user = getCurrentUser();
        if (!$user) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        
        $count = $this->notificationModel->getUnreadCount($user['id']);
        echo json_encode(['count' => (int)$count]);
    }
    
    public function getRecent() {
        header('Content-Type: application/json');
        
        $user = getCurrentUser();
        if (!$user) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        
        $notifications = $this->notificationModel->getByUserId($user['id'], 5);
        $unreadCount = $this->notificationModel->getUnreadCount($user['id']);
        
        echo json_encode([
            'notifications' => $notifications,
            'unread_count' => (int)$unreadCount
        ]);
    }
    
    public function poll() {
        header('Content-Type: application/json');
        
        $user = getCurrentUser();
        if (!$user) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        
        $since = $_GET['since'] ?? date('Y-m-d H:i:s', strtotime('-5 minutes'));
        $notifications = $this->notificationModel->getUnreadSince($user['id'], $since);
        $unreadCount = $this->notificationModel->getUnreadCount($user['id']);
        
        echo json_encode([
            'notifications' => $notifications,
            'unread_count' => (int)$unreadCount,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function markAsRead($id) {
        header('Content-Type: application/json');
        
        $user = getCurrentUser();
        if (!$user) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        
        $success = $this->notificationModel->markAsRead($id, $user['id']);
        echo json_encode(['success' => $success]);
    }
    
    public function markAllAsRead() {
        header('Content-Type: application/json');
        
        $user = getCurrentUser();
        if (!$user) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        
        $success = $this->notificationModel->markAllAsRead($user['id']);
        echo json_encode(['success' => $success]);
    }
    
    public function delete($id) {
        $user = getCurrentUser();
        if (!$user) {
            redirect('/login');
        }
        
        $this->notificationModel->delete($id, $user['id']);
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            setFlash('success', 'Notification deleted');
            redirect('/notifications');
        }
    }
}
