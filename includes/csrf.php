<?php
/**
 * CSRF Protection
 */

// Generate CSRF token
function generateCSRFToken() {
    initSession();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Get CSRF token
function getCSRFToken() {
    initSession();
    return $_SESSION['csrf_token'] ?? generateCSRFToken();
}

// Verify CSRF token
function verifyCSRFToken($token) {
    initSession();
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Render CSRF input field
function csrfField() {
    $token = getCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// Check CSRF token from POST request
function checkCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($token)) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
    }
}
