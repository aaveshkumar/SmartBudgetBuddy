# ConnectWith9 Job Portal

## Overview
A comprehensive job portal application built with Core PHP featuring three user roles (Admin, Employer, Job Seeker). The platform connects job seekers with employers through an intuitive interface with robust security features and SEO optimization.

## Current State
- **Version**: 1.0.0 MVP
- **Status**: ✅ **FULLY OPERATIONAL**
- **Database**: Remote MySQL (Hostinger) - Connected & Configured
- **PHP Version**: 8.2
- **Server**: Running on port 5000

## Recent Changes (December 5, 2025)
- ✅ **ADMIN NOTIFICATION MANAGEMENT**
  - Admin now sees a "Create Notification" icon (bullhorn) in navbar instead of notification bell
  - Admin can create system notifications for new features/announcements
  - Admin can view all sent system notifications with recipient counts
  - Admin can delete notifications (removes from all users)
  - Disabled notification/chat polling for admin users
- ✅ **ENHANCED CHAT SYSTEM & EMPLOYER FEATURES**
  - **Chat Access Control**:
    - Job seekers cannot access conversations until employer sends first message
    - Both page view and AJAX polling endpoints secured
    - Clear messaging explaining first-message policy to job seekers
  - **Employer Candidates Page Improvements**:
    - "Selected" status badge indicator for selected candidates
    - Shows which job each candidate was selected for
    - Direct "Message" button to chat with selected candidates
  - **Message Templates for Employers**:
    - Interview Invitation template
    - Follow Up template
    - Next Steps template
    - Request Documents template
    - Welcome Message template
    - Templates auto-populate with candidate name and job title
    - Expandable textarea for longer messages

## Recent Changes (December 4, 2025)
- ✅ **COMPLETE NOTIFICATION & CHAT SYSTEM IMPLEMENTED**
  - **Database**: Created `notifications`, `conversations`, and `conversation_messages` tables
  - **Notification Features**:
    - Real-time notification bell with unread count badge in header
    - AJAX polling every 10 seconds for near real-time updates
    - Notification types: job_selected, new_job, system, chat_message
    - Mark as read/unread and delete notifications
    - Full notifications page with list view
  - **Chat System**:
    - Automatic conversation creation when employer selects candidate
    - Real-time messaging with AJAX polling
    - Chat icon with unread message count in header
    - Full conversation interface with message history
    - Access control: Only employer and selected candidate can chat
  - **Admin Features**:
    - System notifications page at /admin/notifications
    - Send notifications to all employers, job seekers, or all users
    - Job approval automatically notifies all job seekers of new jobs
  - **Integration**:
    - EmployerController::selectCandidate creates notification + conversation
    - AdminController::approveJob broadcasts new job notifications
- ✅ **CRITICAL FIX: Database Setup Complete** - All tables created and operational
  - Executed `php scripts/setup_database.php` to create core tables (users, jobs, categories, applications, etc.)
  - Executed `php scripts/add_jobseeker_profiles.php` to create profile tables (profiles, work_experiences, education, skills, certifications, languages, projects)
  - Added sample profile data for 2 job seekers with skills
- ✅ **Created Missing View File: `app/views/admin/candidate_profile.php`**
  - Fixed 404 error when accessing /admin/candidates/{id}
  - Complete profile display with all sections: personal info, work experience, education, skills, certifications, languages, projects
  - Resume download link and profile picture display
  - Proper error handling for incomplete profiles
- ✅ **Implemented Action-Specific Button Loaders**
  - Added intelligent loader text detection: Searching, Deleting, Saving, Updating, Loading, Approving, Rejecting, Applying, Posting, etc.
  - Spinning CSS animation with proper styling
  - Automatic loader restoration after page load
  - Works on all form submissions across the entire application

## Recent Changes (October 31, 2025)
- ✅ **Implemented comprehensive job seeker profile system** with advanced features
  - Created 7 new database tables: profiles, work_experiences, education, skills, certifications, languages, projects
  - Built complete profile management forms with dynamic add/remove sections
  - Added file upload for resume (PDF/DOCX) and profile picture (JPG/PNG)
  - Implemented admin candidate filtering by location, skills, experience, availability
  - Created employer candidate matching system to find qualified job seekers
  - Added profile completeness tracking with all professional details
- ✅ Implemented complete password reset functionality with email notifications
- ✅ Created password_reset_tokens database table with secure token management
- ✅ Added beautiful "Forgot Password?" and "Reset Password" pages with password strength indicator
- ✅ Integrated token-based password reset system (1-hour expiration, one-time use)
- ✅ Added comprehensive password reset documentation and setup guide
- ✅ Fixed missing hasFlash() helper function for flash message detection
- ✅ Added "Forgot Password?" link to login page for better user experience

## Recent Changes (October 19, 2025)
- ✅ Fixed security vulnerability: Moved database credentials to environment variables
- ✅ User whitelisted Replit IP on Hostinger database
- ✅ Fixed database setup script to properly execute SQL statements
- ✅ Successfully connected to remote Hostinger database
- ✅ All database tables created and sample data inserted (15 statements executed)
- ✅ Application fully tested and operational
- ✅ Verified homepage, login, and job browsing pages working correctly
- ✅ Created comprehensive setup and security documentation

## Recent Changes (October 18, 2025)
- Initial project setup with complete folder structure
- Implemented MVC architecture with controllers, models, and views
- Created authentication system with three user roles
- Built job posting and application management system
- Added admin panel for user and job moderation
- Implemented employer dashboard for job management
- Created jobseeker dashboard for application tracking
- Added security features (CSRF protection, login throttling, input validation)
- Implemented SEO features (meta tags, Schema.org JSON-LD, clean URLs)
- Set up responsive Bootstrap UI with custom styling

## Project Architecture

### Folder Structure
```
/jobportal/
├── config/          # Database and environment configuration
├── app/
│   ├── controllers/ # Business logic controllers
│   ├── models/      # Database models
│   └── views/       # HTML templates
├── public/          # Public web root
│   ├── assets/      # CSS, JS, images
│   └── uploads/     # User uploads (resumes, logos)
├── includes/        # Utility functions
├── scripts/         # Setup and maintenance scripts
└── docs/            # Documentation
```

### User Roles
1. **Admin**: User management, job moderation, system monitoring
2. **Employer**: Post jobs, view applications, manage listings
3. **Job Seeker**: Browse jobs, apply for positions, track applications

### Key Features
- User authentication with password hashing
- **Password reset functionality** with email notifications (token-based, 1-hour expiration)
- Job posting with admin approval workflow
- Resume upload with file validation (PDF, DOC, DOCX, TXT, max 5MB)
- AJAX job search functionality
- SEO-optimized pages with dynamic meta tags
- CSRF protection on all forms
- Login throttling (5 attempts, 15-minute lockout)
- Session fingerprinting for security
- Clean URLs via mod_rewrite
- Responsive Bootstrap 5 design

## Database Configuration

### Remote MySQL (Hostinger)
- **Host**: srv1642.hstgr.io
- **Database**: u647904474_connect9job
- **User**: u647904474_connect9job
- **Note**: IP whitelisting may be required for remote access

### Database Setup
Run the setup script to create tables and sample data:
```bash
php scripts/setup_database.php
```

### Default Login Credentials
- **Admin**: admin@connectwith9.com / password123
- **Employer**: employer1@example.com / password123
- **Job Seeker**: jobseeker1@example.com / password123

## Running the Application

The application runs on PHP's built-in web server on port 5000:
```bash
php -S 0.0.0.0:5000 -t public
```

Access the application at: http://localhost:5000

## Development Notes

### Security Considerations
- All user inputs are sanitized with `htmlspecialchars()` and `strip_tags()`
- CSRF tokens required for all POST requests
- Password hashing using PHP's `password_hash()` with bcrypt
- Login attempt tracking to prevent brute force attacks
- Session fingerprinting based on IP and user agent
- File upload validation for resume submissions

### SEO Implementation
- Dynamic meta tags for all pages
- Schema.org JSON-LD markup for job listings
- Clean URL structure via .htaccess
- Open Graph and Twitter Card meta tags
- Breadcrumb navigation support

### Future Enhancements (Phase 2)
- reCAPTCHA integration for spam prevention
- Automated cron jobs (sitemap generation, search engine pinging, backups)
- Email notifications via SMTP
- Blog/content management system
- Resume parser functionality
- Advanced job matching algorithm
- Fake employer detection
- Keyword suggestions

## File Upload Paths
- **Resumes**: `public/uploads/resumes/`
- **Logos**: `public/uploads/logos/`

## Navigation & Menu Integration

### Job Seeker Menu
**Top Navigation:**
- Dashboard
- My Profile (NEW)
- Browse Jobs

**User Dropdown:**
- My Profile (NEW) - View/edit professional profile
- My Applications - Track job applications
- Logout

**Dashboard Quick Actions:**
- Browse Jobs
- View My Applications
- My Profile (NEW) - Quick access to profile
- Edit Profile (NEW) - Directly edit profile

### Employer Menu
**Top Navigation:**
- Dashboard
- Post Job
- Find Candidates (NEW)

**User Dropdown:**
- My Jobs - Manage job postings
- Browse Candidates (NEW) - Search & filter candidates
- Logout

**Dashboard Quick Actions:**
- Post New Job
- View All Jobs
- Find Candidates (NEW) - Search qualified candidates

### Admin Menu
**Top Navigation:**
- Dashboard
- Candidates (NEW)

**User Dropdown:**
- Manage Users
- Manage Jobs
- Browse Candidates (NEW)
- Logout

## API Endpoints

### Authentication
- POST `/login` - User authentication
- POST `/register` - New user registration
- GET `/forgot-password` - Show password reset request form
- POST `/forgot-password` - Process password reset request
- GET `/reset-password` - Show new password form (requires token)
- POST `/reset-password` - Process password update

### Job Browsing & Applications
- GET `/jobs` - Browse job listings
- GET `/jobs/{id}` - View job details
- POST `/jobs/{id}/apply` - Submit job application

### Job Seeker Profile (NEW)
- GET `/jobseeker/profile` - View profile
- GET `/jobseeker/profile/edit` - Edit profile form
- POST `/jobseeker/profile/update-basic` - Update basic information
- POST `/jobseeker/profile/upload-picture` - Upload profile picture
- POST `/jobseeker/profile/upload-resume` - Upload resume
- POST `/jobseeker/profile/add-work-experience` - Add work experience
- POST `/jobseeker/profile/delete-work-experience/{id}` - Delete work experience
- POST `/jobseeker/profile/add-education` - Add education
- POST `/jobseeker/profile/delete-education/{id}` - Delete education
- POST `/jobseeker/profile/add-skill` - Add skill
- POST `/jobseeker/profile/delete-skill/{id}` - Delete skill
- POST `/jobseeker/profile/add-certification` - Add certification
- POST `/jobseeker/profile/delete-certification/{id}` - Delete certification
- POST `/jobseeker/profile/add-language` - Add language
- POST `/jobseeker/profile/delete-language/{id}` - Delete language
- POST `/jobseeker/profile/add-project` - Add project
- POST `/jobseeker/profile/delete-project/{id}` - Delete project

### Candidate Search (NEW)
- GET `/admin/candidates` - Browse/filter all candidates (Admin)
- GET `/admin/candidates/{id}` - View candidate profile (Admin)
- GET `/employer/candidates` - Browse/filter candidates (Employer)
- GET `/employer/candidates/{id}` - View candidate profile (Employer)

## Tech Stack
- **Backend**: Core PHP 8.2
- **Database**: MySQL 8.0 (PDO)
- **Frontend**: HTML5, CSS3, JavaScript
- **UI Framework**: Bootstrap 5.3
- **Icons**: Font Awesome 6.4
- **Web Server**: Apache with mod_rewrite

## Maintenance
- Review pending jobs in admin panel regularly
- Monitor failed login attempts
- Backup database and uploads directory
- Clean up expired jobs periodically
- Update security patches
