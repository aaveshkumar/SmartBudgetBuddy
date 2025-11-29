<?php
/**
 * Environment Configuration
 * Database credentials and API keys
 * 
 * IMPORTANT: For production, set these values as environment variables
 * and remove the default values below.
 */

// Database Configuration
// Set these as environment variables in production
define('DB_HOST', getenv('DB_HOST') ?: 'srv1642.hstgr.io');
define('DB_NAME', getenv('DB_NAME') ?: 'u647904474_connect9job');
define('DB_USER', getenv('DB_USER') ?: 'u647904474_connect9job');
define('DB_PASS', getenv('DB_PASS') ?: 'Hostinger@1234#');

// Application Settings
define('APP_NAME', 'ConnectWith9 Job Portal');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:5000');
define('APP_ENV', getenv('APP_ENV') ?: 'development'); // development or production

// reCAPTCHA Keys (Add your own keys via environment variables)
define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY') ?: '');
define('RECAPTCHA_SECRET_KEY', getenv('RECAPTCHA_SECRET_KEY') ?: '');

// SMTP Configuration (for email notifications)
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_RESUME_TYPES', ['pdf', 'doc', 'docx', 'txt']);
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');

// Security Settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
