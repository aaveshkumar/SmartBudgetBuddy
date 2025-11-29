<?php
/**
 * Application Constants
 */

// User Types
define('USER_TYPE_JOBSEEKER', 'jobseeker');
define('USER_TYPE_EMPLOYER', 'employer');
define('USER_TYPE_ADMIN', 'admin');

// Job Types
define('JOB_TYPE_FULLTIME', 'Full-time');
define('JOB_TYPE_PARTTIME', 'Part-time');
define('JOB_TYPE_CONTRACT', 'Contract');
define('JOB_TYPE_INTERNSHIP', 'Internship');

// Job Status
define('JOB_STATUS_PENDING', 'Pending');
define('JOB_STATUS_APPROVED', 'Approved');
define('JOB_STATUS_REJECTED', 'Rejected');

// Pagination
define('JOBS_PER_PAGE', 20);
define('APPLICATIONS_PER_PAGE', 20);

// SEO
define('META_DESCRIPTION_LENGTH', 155);
define('META_TITLE_LENGTH', 60);

// Error Messages
define('ERROR_INVALID_CREDENTIALS', 'Invalid email or password');
define('ERROR_EMAIL_EXISTS', 'Email already registered');
define('ERROR_UNAUTHORIZED', 'Unauthorized access');
define('ERROR_INVALID_FILE', 'Invalid file type or size');

// Success Messages
define('SUCCESS_REGISTER', 'Registration successful! Please login.');
define('SUCCESS_LOGIN', 'Login successful!');
define('SUCCESS_JOB_POSTED', 'Job posted successfully and pending approval');
define('SUCCESS_APPLICATION', 'Application submitted successfully');

// Upload Paths (relative to public directory)
define('RESUME_UPLOAD_PATH', __DIR__ . '/../public/uploads/resumes/');
define('PROFILE_UPLOAD_PATH', __DIR__ . '/../public/uploads/profiles/');
define('LOGO_UPLOAD_PATH', __DIR__ . '/../public/uploads/logos/');
