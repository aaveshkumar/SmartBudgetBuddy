<?php
/**
 * Authentication Controller
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../app/models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    // Show login form
    public function showLogin() {
        if (isLoggedIn()) {
            $user = getCurrentUser();
            $this->redirectToDashboard($user['type']);
        }
        
        require __DIR__ . '/../views/auth/login.php';
    }
    
    // Handle login
    public function login() {
        checkCSRF();
        
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Check login attempts
        if ($this->isLoginLocked(getClientIP())) {
            setFlash('error', 'Too many login attempts. Please try again in 15 minutes.');
            redirect('/login');
        }
        
        // Validate
        if (empty($email) || empty($password)) {
            setFlash('error', 'All fields are required');
            redirect('/login');
        }
        
        // Verify credentials
        $user = $this->userModel->verifyCredentials($email, $password);
        
        if (!$user) {
            $this->recordLoginAttempt(getClientIP());
            setFlash('error', ERROR_INVALID_CREDENTIALS);
            redirect('/login');
        }
        
        // Create session
        initSession();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['type'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['fingerprint'] = getSessionFingerprint();
        
        // Clear login attempts
        $this->clearLoginAttempts(getClientIP());
        
        setFlash('success', SUCCESS_LOGIN);
        $this->redirectToDashboard($user['type']);
    }
    
    // Show registration form
    public function showRegister() {
        if (isLoggedIn()) {
            $user = getCurrentUser();
            $this->redirectToDashboard($user['type']);
        }
        
        require __DIR__ . '/../views/auth/register.php';
    }
    
    // Handle registration
    public function register() {
        checkCSRF();
        
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $mobile_no = sanitize($_POST['mobile_no'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $type = sanitize($_POST['type'] ?? USER_TYPE_JOBSEEKER);
        
        // Validate
        $errors = [];
        
        if (empty($name) || empty($email) || empty($password)) {
            $errors[] = 'All fields are required';
        }
        
        if (!isValidEmail($email)) {
            $errors[] = 'Invalid email address';
        }
        
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        if ($this->userModel->emailExists($email)) {
            $errors[] = ERROR_EMAIL_EXISTS;
        }
        
        if (!in_array($type, [USER_TYPE_JOBSEEKER, USER_TYPE_EMPLOYER])) {
            $errors[] = 'Invalid user type';
        }
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            redirect('/register');
        }
        
        // Create user
        $userId = $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'type' => $type,
            'mobile_no' => $mobile_no,
            'verified' => 1 // Auto-verify for now
        ]);
        
        if ($userId) {
            setFlash('success', SUCCESS_REGISTER);
            redirect('/login');
        } else {
            setFlash('error', 'Registration failed. Please try again.');
            redirect('/register');
        }
    }
    
    // Logout
    public function logout() {
        initSession();
        session_destroy();
        redirect('/');
    }
    
    // Record login attempt
    private function recordLoginAttempt($ip) {
        $db = getDB();
        $sql = "INSERT INTO login_attempts (ip_address) VALUES (:ip)";
        $stmt = $db->prepare($sql);
        $stmt->execute([':ip' => $ip]);
    }
    
    // Clear login attempts
    private function clearLoginAttempts($ip) {
        $db = getDB();
        $sql = "DELETE FROM login_attempts WHERE ip_address = :ip";
        $stmt = $db->prepare($sql);
        $stmt->execute([':ip' => $ip]);
    }
    
    // Check if login is locked
    private function isLoginLocked($ip) {
        $db = getDB();
        $sql = "SELECT COUNT(*) as attempts FROM login_attempts 
                WHERE ip_address = :ip 
                AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':ip' => $ip]);
        $result = $stmt->fetch();
        
        return $result['attempts'] >= MAX_LOGIN_ATTEMPTS;
    }
    
    // Redirect to appropriate dashboard
    private function redirectToDashboard($userType) {
        switch ($userType) {
            case USER_TYPE_ADMIN:
                redirect('/admin/dashboard');
                break;
            case USER_TYPE_EMPLOYER:
                redirect('/employer/dashboard');
                break;
            case USER_TYPE_JOBSEEKER:
            default:
                redirect('/jobseeker/dashboard');
                break;
        }
    }
    
    // Show forgot password form
    public function showForgotPassword() {
        if (isLoggedIn()) {
            redirect('/');
        }
        
        require __DIR__ . '/../views/auth/forgot-password.php';
    }
    
    // Handle password reset request
    public function requestPasswordReset() {
        checkCSRF();
        
        $email = sanitize($_POST['email'] ?? '');
        
        if (empty($email) || !isValidEmail($email)) {
            setFlash('error', 'Please enter a valid email address');
            redirect('/forgot-password');
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            // Don't reveal if email exists - security best practice
            setFlash('success', 'If an account exists with this email, you will receive a password reset link shortly.');
            redirect('/forgot-password');
        }
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token in database
        $db = getDB();
        
        // Delete any existing tokens for this email
        $sql = "DELETE FROM password_reset_tokens WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        // Insert new token
        $sql = "INSERT INTO password_reset_tokens (email, token, expires_at) 
                VALUES (:email, :token, :expires_at)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':token' => $token,
            ':expires_at' => $expiresAt
        ]);
        
        // Send reset email
        require_once __DIR__ . '/../../includes/email.php';
        sendPasswordResetEmail($user['email'], $user['name'], $token);
        
        setFlash('success', 'If an account exists with this email, you will receive a password reset link shortly.');
        redirect('/forgot-password');
    }
    
    // Show reset password form
    public function showResetPassword() {
        if (isLoggedIn()) {
            redirect('/');
        }
        
        $token = sanitize($_GET['token'] ?? '');
        
        if (empty($token)) {
            setFlash('error', 'Invalid reset link');
            redirect('/forgot-password');
        }
        
        // Verify token exists and hasn't expired
        if (!$this->verifyResetToken($token)) {
            setFlash('error', 'This reset link is invalid or has expired. Please request a new one.');
            redirect('/forgot-password');
        }
        
        require __DIR__ . '/../views/auth/reset-password.php';
    }
    
    // Handle password reset
    public function resetPassword() {
        checkCSRF();
        
        $token = sanitize($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate
        if (empty($password) || empty($confirmPassword)) {
            setFlash('error', 'All fields are required');
            redirect('/reset-password?token=' . $token);
        }
        
        if (strlen($password) < 6) {
            setFlash('error', 'Password must be at least 6 characters');
            redirect('/reset-password?token=' . $token);
        }
        
        if ($password !== $confirmPassword) {
            setFlash('error', 'Passwords do not match');
            redirect('/reset-password?token=' . $token);
        }
        
        // Verify token
        $resetData = $this->getResetTokenData($token);
        
        if (!$resetData) {
            setFlash('error', 'This reset link is invalid or has expired. Please request a new one.');
            redirect('/forgot-password');
        }
        
        // Update password
        $user = $this->userModel->findByEmail($resetData['email']);
        
        if (!$user) {
            setFlash('error', 'User not found');
            redirect('/forgot-password');
        }
        
        $this->userModel->update($user['id'], ['password' => $password]);
        
        // Delete used token
        $db = getDB();
        $sql = "DELETE FROM password_reset_tokens WHERE token = :token";
        $stmt = $db->prepare($sql);
        $stmt->execute([':token' => $token]);
        
        setFlash('success', 'Your password has been reset successfully. You can now login with your new password.');
        redirect('/login');
    }
    
    // Verify reset token is valid
    private function verifyResetToken($token) {
        $data = $this->getResetTokenData($token);
        return $data !== null;
    }
    
    // Get reset token data if valid
    private function getResetTokenData($token) {
        $db = getDB();
        $sql = "SELECT * FROM password_reset_tokens 
                WHERE token = :token 
                AND expires_at > NOW() 
                LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':token' => $token]);
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
}
