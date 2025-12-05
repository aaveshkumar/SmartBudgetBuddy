<?php
/**
 * Core Helper Functions
 */

// Start session if not already started
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Include CSRF functions (must be after initSession is defined)
require_once __DIR__ . '/csrf.php';

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if user is logged in
function isLoggedIn() {
    initSession();
    return isset($_SESSION['user_id']);
}

// Get current user
function getCurrentUser() {
    initSession();
    if (!isLoggedIn()) {
        return null;
    }
    
    require_once __DIR__ . '/../app/models/User.php';
    $userModel = new User();
    return $userModel->findById($_SESSION['user_id']);
}

// Check if user has specific role
function hasRole($role) {
    $user = getCurrentUser();
    return $user && $user['type'] === $role;
}

// Require authentication
function requireAuth() {
    if (!isLoggedIn()) {
        redirect('/login');
    }
}

// Require specific role
function requireRole($role) {
    requireAuth();
    if (!hasRole($role)) {
        http_response_code(403);
        die('Unauthorized access');
    }
}

// Format date
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

// Generate slug from string
function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

// File upload validation
function validateFileUpload($file, $allowedTypes = ALLOWED_RESUME_TYPES, $maxSize = MAX_FILE_SIZE) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload failed';
        return $errors;
    }
    
    if ($file['size'] > $maxSize) {
        $errors[] = 'File size exceeds maximum limit (' . ($maxSize / 1024 / 1024) . 'MB)';
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        $errors[] = 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes);
    }
    
    return $errors;
}

// Upload file
function uploadFile($file, $destination) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $destination . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }
    
    return false;
}

// Pagination helper
function paginate($totalItems, $itemsPerPage, $currentPage = 1) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'items_per_page' => $itemsPerPage,
        'offset' => $offset
    ];
}

// Flash messages
function setFlash($key, $message) {
    initSession();
    $_SESSION['flash'][$key] = $message;
}

function getFlash($key) {
    initSession();
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

function hasFlash($key) {
    initSession();
    return isset($_SESSION['flash'][$key]);
}

// Get client IP address
function getClientIP() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    }
    
    return $ip;
}

// Session fingerprint for security
function getSessionFingerprint() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip = getClientIP();
    return md5($userAgent . $ip);
}

// Verify session fingerprint
function verifySessionFingerprint() {
    initSession();
    if (!isset($_SESSION['fingerprint'])) {
        $_SESSION['fingerprint'] = getSessionFingerprint();
        return true;
    }
    
    return $_SESSION['fingerprint'] === getSessionFingerprint();
}

// Price formatting
function formatSalary($salary) {
    if (empty($salary)) {
        return 'Not Disclosed';
    }
    return '₹' . $salary . ' per month';
}

// Truncate text
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

// Get base URL
function baseUrl($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:5000';
    $baseUrl = $protocol . '://' . $host;
    return $baseUrl . '/' . ltrim($path, '/');
}

// Asset URL helper
function asset($path) {
    return baseUrl('assets/' . ltrim($path, '/'));
}
