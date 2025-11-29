# Password Reset Functionality Guide

## Overview

The ConnectWith9 Job Portal includes a complete password reset functionality that allows users (Admin, Employer, Job Seeker) to reset their passwords via email.

## Features

✅ **Secure token-based reset** - Tokens expire after 1 hour
✅ **Email notifications** - Automated password reset emails  
✅ **Works for all user types** - Admin, Employer, and Job Seeker
✅ **Rate limiting prevention** - Uses existing login throttling system
✅ **User-friendly interface** - Beautiful forms with password strength indicator
✅ **Security best practices** - Doesn't reveal if email exists in system

## Setup Instructions

### Step 1: Create Database Table

Run this command to add the password_reset_tokens table:

```bash
php scripts/add_password_reset.php
```

Or manually execute this SQL:

```sql
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(100) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Step 2: Configure Email Settings

Update your email configuration in `config/env.php`:

```php
// SMTP Configuration (for email notifications)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_PORT', 587);
```

**For Gmail:**
1. Enable 2-Step Verification
2. Go to Google Account → Security → App passwords
3. Generate an app password
4. Use that password in SMTP_PASS

**For other email providers:**
- **SendGrid**: smtp.sendgrid.net
- **Mailgun**: smtp.mailgun.org
- **Amazon SES**: email-smtp.region.amazonaws.com

### Step 3: Test the Feature

1. Go to `/login`
2. Click "Forgot Password?"
3. Enter a registered email address
4. Check your inbox for the reset link
5. Click the link and set a new password
6. Login with your new password

## User Flow

### Password Reset Request

1. User visits `/forgot-password`
2. User enters their registered email
3. System generates a secure token (64 characters)
4. Token is stored in database with 1-hour expiration
5. Email with reset link is sent to user
6. Success message displayed (doesn't reveal if email exists)

### Password Reset Completion

1. User clicks reset link from email
2. System validates token (checks existence and expiration)
3. User enters new password (minimum 6 characters)
4. Password strength indicator shows security level
5. Password is hashed and updated in database
6. Used token is deleted
7. User redirected to login with success message

## Security Features

### Token Security
- **Random generation**: Uses `bin2hex(random_bytes(32))` for cryptographically secure tokens
- **Unique constraint**: Database ensures no duplicate tokens
- **One-time use**: Token deleted after successful password reset
- **Time-limited**: Expires after 1 hour

### Email Validation
- Checks email format validity
- Verifies email exists in database (internal check only)
- Doesn't reveal to attacker if email is registered

### Password Validation
- Minimum 6 characters (configurable)
- Must match confirmation field
- Strength indicator encourages strong passwords
- Hashed using bcrypt (PASSWORD_DEFAULT)

### Protection Against Attacks
- **Token expiration**: Prevents old tokens from working
- **Single use**: Token deleted after reset
- **No email enumeration**: Same message whether email exists or not
- **CSRF protection**: All forms protected with CSRF tokens

## File Structure

```
app/
├── controllers/
│   └── AuthController.php          # Password reset methods added
├── views/
│   └── auth/
│       ├── login.php              # Updated with "Forgot Password?" link
│       ├── forgot-password.php    # Request reset form
│       └── reset-password.php     # New password form
public/
└── index.php                      # Routes for /forgot-password and /reset-password
includes/
└── email.php                      # sendPasswordResetEmail() function
scripts/
└── add_password_reset.php         # Database migration script
docs/
└── password_reset_migration.sql   # SQL for manual migration
```

## Routes

| Route | Method | Description |
|-------|--------|-------------|
| `/forgot-password` | GET | Show password reset request form |
| `/forgot-password` | POST | Process password reset request |
| `/reset-password?token=xxx` | GET | Show new password form |
| `/reset-password` | POST | Process password update |

## Email Template

The password reset email includes:
- Personalized greeting with user's name
- Clear call-to-action button
- Backup link (in case button doesn't work)
- Expiration notice (1 hour)
- Security reminder (ignore if didn't request)

## Controller Methods

### AuthController.php

```php
// Show forgot password form
public function showForgotPassword()

// Handle password reset request
public function requestPasswordReset()

// Show reset password form (with token validation)
public function showResetPassword()

// Handle password reset
public function resetPassword()

// Verify reset token is valid
private function verifyResetToken($token)

// Get reset token data if valid
private function getResetTokenData($token)
```

## Database Schema

```sql
password_reset_tokens
├── id (INT, AUTO_INCREMENT, PRIMARY KEY)
├── email (VARCHAR 100, NOT NULL)
├── token (VARCHAR 100, NOT NULL, UNIQUE)
├── expires_at (DATETIME, NOT NULL)
├── created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
├── INDEX idx_email (email)
├── INDEX idx_token (token)
└── INDEX idx_expires (expires_at)
```

## Testing Checklist

- [ ] Password reset table created successfully
- [ ] SMTP settings configured correctly
- [ ] "Forgot Password?" link appears on login page
- [ ] Forgot password page loads at `/forgot-password`
- [ ] Form validates email address
- [ ] Email sent successfully to registered user
- [ ] Reset link contains valid token
- [ ] Reset link expires after 1 hour
- [ ] Reset password page validates token
- [ ] Password strength indicator works
- [ ] Password confirmation validation works
- [ ] Password updated in database
- [ ] Can login with new password
- [ ] Used token is deleted from database
- [ ] Invalid/expired tokens show error message

## Troubleshooting

### Email Not Sending

**Problem**: User doesn't receive reset email

**Solutions**:
1. Check SMTP credentials in `config/env.php`
2. Verify SMTP port (587 for TLS, 465 for SSL)
3. Check spam/junk folder
4. Test with a simple PHP mail script
5. Check server error logs
6. Consider using email service (SendGrid, Mailgun)

### Token Invalid/Expired

**Problem**: "This reset link is invalid or has expired"

**Solutions**:
1. Request a new reset link
2. Check database clock vs server clock
3. Verify token wasn't already used
4. Check token hasn't expired (1 hour limit)

### Password Not Updating

**Problem**: Password reset succeeds but can't login

**Solutions**:
1. Verify password is being hashed correctly
2. Check User model update() method
3. Clear browser cookies/cache
4. Verify correct email being used

## Customization

### Change Token Expiration

In `AuthController::requestPasswordReset()`:

```php
// Change from 1 hour to 30 minutes
$expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

// Or 24 hours
$expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
```

### Customize Email Template

Edit `includes/email.php` → `sendPasswordResetEmail()`:

```php
function sendPasswordResetEmail($userEmail, $userName, $resetToken) {
    // Customize subject
    $subject = "Your Custom Subject Here";
    
    // Customize email content
    $content = <<<HTML
<h2>Your Custom Heading</h2>
<p>Your custom message...</p>
HTML;
    
    // Rest of the code...
}
```

### Change Password Requirements

In `AuthController::resetPassword()`:

```php
// Change minimum password length
if (strlen($password) < 8) {  // Changed from 6 to 8
    setFlash('error', 'Password must be at least 8 characters');
    redirect('/reset-password?token=' . $token);
}

// Add complexity requirements
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
    setFlash('error', 'Password must contain uppercase, lowercase, and number');
    redirect('/reset-password?token=' . $token);
}
```

## Production Deployment

Before deploying to production:

1. ✅ Test password reset flow completely
2. ✅ Configure production SMTP settings
3. ✅ Set APP_ENV to 'production' in config/env.php
4. ✅ Use environment variables for SMTP credentials
5. ✅ Enable HTTPS for secure token transmission
6. ✅ Set up email monitoring/logging
7. ✅ Test with real email accounts
8. ✅ Configure SPF/DKIM for better email delivery
9. ✅ Set up email rate limiting if needed
10. ✅ Monitor reset token usage for abuse

## Support & Maintenance

### Cleanup Old Tokens

Add a cron job to clean up expired tokens:

```bash
# Run daily at 2 AM
0 2 * * * php /path/to/scripts/cleanup_tokens.php
```

Create `scripts/cleanup_tokens.php`:

```php
<?php
require_once __DIR__ . '/../config/database.php';
$db = getDB();
$db->exec("DELETE FROM password_reset_tokens WHERE expires_at < NOW()");
```

### Monitor Reset Requests

Track password reset requests in admin panel:

```sql
SELECT DATE(created_at) as date, COUNT(*) as reset_requests
FROM password_reset_tokens
GROUP BY DATE(created_at)
ORDER BY date DESC
LIMIT 30;
```

## FAQs

**Q: How long is the reset link valid?**  
A: 1 hour by default (customizable)

**Q: Can a user request multiple reset links?**  
A: Yes, but only the latest token will work (old tokens are deleted)

**Q: Does it work for all user types?**  
A: Yes, Admin, Employer, and Job Seeker can all reset passwords

**Q: What if email service is down?**  
A: User won't receive email but can retry. Consider implementing email queuing for production.

**Q: Is the feature mobile-friendly?**  
A: Yes, all forms are responsive and work on mobile devices

---

**Your password reset feature is now ready to use!** 🎉
