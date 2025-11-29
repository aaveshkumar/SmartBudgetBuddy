<?php
/**
 * Email Helper Functions
 */

// Send email using PHP mail()
function sendEmail($to, $subject, $message, $fromName = null) {
    $fromName = $fromName ?? APP_NAME;
    $fromEmail = SMTP_USER;
    
    $headers = "From: $fromName <$fromEmail>\r\n";
    $headers .= "Reply-To: $fromEmail\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Email template wrapper
function emailTemplate($content, $title = '') {
    $title = $title ?: APP_NAME;
    
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f4f4f4; }
        .footer { padding: 10px; text-align: center; font-size: 12px; color: #666; }
        .button { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>$title</h1>
        </div>
        <div class="content">
            $content
        </div>
        <div class="footer">
            <p>&copy; 2025 $title. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
}

// Send welcome email
function sendWelcomeEmail($userEmail, $userName, $userType) {
    $subject = "Welcome to " . APP_NAME;
    
    $content = <<<HTML
<h2>Welcome, $userName!</h2>
<p>Thank you for registering as a <strong>$userType</strong> on our platform.</p>
<p>You can now:</p>
<ul>
HTML;
    
    if ($userType === 'jobseeker') {
        $content .= <<<HTML
    <li>Browse thousands of job opportunities</li>
    <li>Apply for jobs with one click</li>
    <li>Save your favorite jobs</li>
    <li>Track your applications</li>
HTML;
    } else {
        $content .= <<<HTML
    <li>Post job openings</li>
    <li>View applications</li>
    <li>Manage your job listings</li>
    <li>Find the perfect candidates</li>
HTML;
    }
    
    $content .= <<<HTML
</ul>
<p style="text-align: center; margin-top: 30px;">
    <a href="{baseUrl('login')}" class="button">Login Now</a>
</p>
HTML;
    
    $message = emailTemplate($content, APP_NAME);
    return sendEmail($userEmail, $subject, $message);
}

// Send job application notification
function sendApplicationEmail($employerEmail, $jobTitle, $applicantName) {
    $subject = "New Application for $jobTitle";
    
    $content = <<<HTML
<h2>New Job Application Received</h2>
<p>You have received a new application for the position: <strong>$jobTitle</strong></p>
<p>Applicant: <strong>$applicantName</strong></p>
<p style="text-align: center; margin-top: 30px;">
    <a href="{baseUrl('employer/applications')}" class="button">View Application</a>
</p>
HTML;
    
    $message = emailTemplate($content);
    return sendEmail($employerEmail, $subject, $message);
}

// Send job approval notification
function sendJobApprovalEmail($employerEmail, $jobTitle, $status) {
    $subject = $status === 'Approved' ? "Job Approved: $jobTitle" : "Job Status Update: $jobTitle";
    
    $statusMessage = $status === 'Approved' 
        ? "Your job posting has been approved and is now live!"
        : "Your job posting status has been updated to: $status";
    
    $content = <<<HTML
<h2>Job Status Update</h2>
<p>Job Title: <strong>$jobTitle</strong></p>
<p>Status: <strong>$status</strong></p>
<p>$statusMessage</p>
<p style="text-align: center; margin-top: 30px;">
    <a href="{baseUrl('employer/jobs')}" class="button">View Your Jobs</a>
</p>
HTML;
    
    $message = emailTemplate($content);
    return sendEmail($employerEmail, $subject, $message);
}

// Send password reset email
function sendPasswordResetEmail($userEmail, $userName, $resetToken) {
    $subject = "Password Reset Request - " . APP_NAME;
    
    $resetUrl = APP_URL . "/reset-password?token=" . $resetToken;
    
    $content = <<<HTML
<h2>Password Reset Request</h2>
<p>Hello $userName,</p>
<p>We received a request to reset your password. Click the button below to create a new password:</p>
<p style="text-align: center; margin: 30px 0;">
    <a href="$resetUrl" class="button">Reset Password</a>
</p>
<p>Or copy and paste this link into your browser:</p>
<p style="word-break: break-all; background: #fff; padding: 10px; border: 1px solid #ddd;">$resetUrl</p>
<p><strong>This link will expire in 1 hour.</strong></p>
<p>If you didn't request this password reset, please ignore this email. Your password will remain unchanged.</p>
<p>For security reasons, never share this link with anyone.</p>
HTML;
    
    $message = emailTemplate($content, APP_NAME . " - Password Reset");
    return sendEmail($userEmail, $subject, $message);
}
