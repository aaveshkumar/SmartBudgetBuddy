<?php
require_once __DIR__ . '/../models/Report.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/csrf.php';

class ReportController {
    private $reportModel;
    
    public function __construct() {
        $this->reportModel = new Report();
    }
    
    public function submitReport() {
        // Disable any output buffering to prevent headers from being corrupted
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set JSON header at the very beginning
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        
        $user = getCurrentUser();
        if (!$user) {
            echo json_encode(['error' => 'You must be logged in to report']);
            return;
        }
        
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrfToken)) {
            echo json_encode(['error' => 'Security token expired. Please refresh the page and try again.']);
            return;
        }
        
        if ($user['status'] !== 'active') {
            echo json_encode(['error' => 'Your account must be verified to report']);
            return;
        }
        
        $reportedType = sanitize($_POST['reported_type'] ?? '');
        $reportedId = (int)($_POST['reported_id'] ?? 0);
        $message = sanitize($_POST['message'] ?? '');
        
        if (!in_array($reportedType, ['job', 'user'])) {
            echo json_encode(['error' => 'Invalid report type']);
            return;
        }
        
        if ($reportedId <= 0) {
            echo json_encode(['error' => 'Invalid item to report']);
            return;
        }
        
        if (empty($message) || strlen($message) < 10) {
            echo json_encode(['error' => 'Please provide a detailed reason (at least 10 characters)']);
            return;
        }
        
        if ($this->reportModel->hasReported($user['id'], $reportedType, $reportedId)) {
            echo json_encode(['error' => 'You have already reported this item']);
            return;
        }
        
        try {
            $reportId = $this->reportModel->create([
                'reporter_id' => $user['id'],
                'reported_type' => $reportedType,
                'reported_id' => $reportedId,
                'message' => $message
            ]);
            
            if ($reportId) {
                echo json_encode(['success' => true, 'message' => 'Report submitted successfully. Admin will review it.']);
            } else {
                echo json_encode(['error' => 'Failed to submit report. Please try again.']);
            }
        } catch (Exception $e) {
            error_log('Report submission error: ' . $e->getMessage());
            echo json_encode(['error' => 'Database error. Please try again later.']);
        }
    }
}
