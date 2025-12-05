# ConnectWith9 Job Portal

## Overview
ConnectWith9 is a comprehensive job portal application designed to connect job seekers with employers across various industries. Built with Core PHP, it features a robust three-tier user role system (Admin, Employer, Job Seeker), ensuring tailored experiences and secure interactions. The platform prioritizes an intuitive user interface, robust security features including CSRF protection and login throttling, and SEO optimization to maximize visibility. Its core purpose is to streamline the hiring process, offering features from advanced job seeker profiles and resume uploads to real-time chat and notification systems, fostering efficient communication and successful placements.

## User Preferences
I prefer iterative development with clear communication at each stage. Please ask for confirmation before implementing major architectural changes or deleting existing code. I value detailed explanations of complex solutions. Ensure that all new features are thoroughly tested and documented, especially regarding security and data integrity.

## System Architecture

### Core Architecture
The application follows a Model-View-Controller (MVC) architectural pattern, separating business logic, data presentation, and user interaction. It's built with Core PHP 8.2 and utilizes a remote MySQL 8.0 database.

### User Roles
1.  **Admin**: Manages users, moderates jobs, and monitors system activities, including notification management and candidate viewing.
2.  **Employer**: Posts job listings, views and manages applications, searches for candidates, and communicates with selected job seekers.
3.  **Job Seeker**: Browses and applies for jobs, manages a comprehensive professional profile, and tracks application statuses.

### Key Features
*   **Authentication & Authorization**: Secure user login/registration with password hashing, login throttling, and session fingerprinting. Three distinct user roles with role-based access control.
*   **Job Management**: Employers can post jobs, and job seekers can browse and apply. Includes an admin approval workflow for job postings.
*   **Comprehensive Job Seeker Profiles**: Detailed profiles with sections for work experience, education, skills, certifications, languages, and projects. Supports resume and profile picture uploads.
*   **Candidate Management**: Employers can search, filter, and manage candidates, with features for selecting candidates and initiating chat. Admins can also browse all candidates.
*   **Real-time Communication**: Integrated chat system for direct messaging between employers and selected job seekers, including attachment support for various file types (images, documents, PDFs).
*   **Notification System**: Real-time notifications for job selections, new job postings, chat messages, and system announcements, with unread counts and a full notification center.
*   **Password Reset**: Secure token-based password reset functionality with email notifications.
*   **Security Features**: CSRF protection, input sanitization, password hashing (bcrypt), login attempt tracking, and session fingerprinting.
*   **SEO Optimization**: Dynamic meta tags, Schema.org JSON-LD, clean URL structures, Open Graph, and Twitter Card support.
*   **Responsive UI**: Built with Bootstrap 5.3 for a mobile-first, responsive user experience.
*   **Action-Specific Button Loaders**: Visual feedback for form submissions with dynamic loader texts.

### Folder Structure
```
/jobportal/
├── config/
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
├── public/
│   ├── assets/
│   └── uploads/
├── includes/
├── scripts/
└── docs/
```

### UI/UX Design
The application uses Bootstrap 5.3 for its responsive design, ensuring a consistent and user-friendly interface across devices. Custom CSS styling is applied for branding. Icons are provided by Font Awesome 6.4.

## Recent Changes (Dec 2025)
*   **Mobile/Tablet UI Improvements**: Notifications, messages, and admin icons (reports, announcements) now appear outside the hamburger menu on small and medium screens for quick access
*   **Hamburger Menu**: Closes when clicking outside or selecting a nav link on mobile/tablet
*   **WhatsApp Integration**: Uses wa.me API for direct messaging without requiring contact to be saved; proper international phone number formatting
*   **Email Buttons**: Opens email client directly with pre-filled subject and body
*   **Report System**: CSRF token refresh mechanism ensures reports work even after session changes
*   **Modal Dismiss**: All modals close when clicking outside on mobile devices

## External Dependencies
*   **Database**: Remote MySQL 8.0 (Hostinger)
*   **UI Framework**: Bootstrap 5.3
*   **Icons**: Font Awesome 6.4
*   **Web Server**: Apache with mod_rewrite (for clean URLs)