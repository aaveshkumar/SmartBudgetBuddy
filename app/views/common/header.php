<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/seo.php';

initSession();
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    if (isset($meta)) {
        renderMetaTags($meta);
    } else {
        echo '<title>' . APP_NAME . '</title>';
    }
    ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <?php if (isset($schema)): ?>
    <script type="application/ld+json">
    <?= $schema ?>
    </script>
    <?php endif; ?>
</head>
<body>
    <!-- Page Loader Overlay -->
    <div id="pageLoader" class="page-loader-overlay" style="display: none;">
        <div class="page-loader-content">
            <div class="page-loader-spinner"></div>
            <p class="page-loader-text">Loading...</p>
        </div>
    </div>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-briefcase"></i> ConnectWith9
            </a>
            <?php if ($currentUser && $currentUser['type'] !== USER_TYPE_ADMIN): ?>
            <!-- Mobile/Tablet icons (outside hamburger) - visible on small AND medium screens -->
            <div class="d-flex align-items-center d-lg-none order-lg-2 me-2">
                <!-- Chat Icon (Mobile/Tablet) -->
                <a class="nav-link position-relative text-white px-2" href="/chat" title="Messages">
                    <i class="fas fa-comments"></i>
                    <span id="chatBadgeMobile" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.6rem;">
                        0
                    </span>
                </a>
                
                <!-- Notification Bell (Mobile/Tablet) -->
                <div class="dropdown">
                    <a class="nav-link position-relative text-white px-2 dropdown-toggle" href="#" id="notificationDropdownMobile" role="button" data-bs-toggle="dropdown" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span id="notificationBadgeMobile" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.6rem;">
                            0
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 280px; max-height: 350px; overflow-y: auto;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <span><strong>Notifications</strong></span>
                            <button type="button" class="btn btn-sm btn-link text-primary p-0" onclick="markAllNotificationsRead()">Mark all read</button>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div id="notificationListMobile">
                            <div class="text-center py-3 text-muted">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-primary text-center" href="/notifications">View All Notifications</a>
                    </div>
                </div>
            </div>
            <?php elseif ($currentUser && $currentUser['type'] === USER_TYPE_ADMIN): ?>
            <!-- Admin icons outside hamburger on mobile/tablet - closer to hamburger -->
            <div class="d-flex align-items-center d-lg-none order-lg-2 ms-auto" style="margin-right: 5px;">
                <!-- Reports Icon (Admin Mobile/Tablet) -->
                <a class="nav-link text-white px-1" href="/admin/reports" title="Reports" style="margin-right: 5px;">
                    <i class="fas fa-flag"></i>
                </a>
                
                <!-- Announcements Icon (Admin Mobile/Tablet) -->
                <a class="nav-link text-white px-1" href="/admin/notifications" title="System Notifications" style="margin-right: 5px;">
                    <i class="fas fa-bullhorn"></i>
                </a>
            </div>
            <?php endif; ?>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/jobs">Browse Jobs</a>
                    </li>
                    <?php if ($currentUser): ?>
                        <?php if ($currentUser['type'] === USER_TYPE_ADMIN): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/dashboard">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/candidates">Candidates</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/reports"><i class="fas fa-flag"></i> Reports</a>
                            </li>
                        <?php elseif ($currentUser['type'] === USER_TYPE_EMPLOYER): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/employer/dashboard">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/employer/post-job">Post Job</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/employer/candidates">Find Candidates</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/jobseeker/dashboard">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/jobseeker/profile">My Profile</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($currentUser['type'] !== USER_TYPE_ADMIN): ?>
                        <!-- Chat Icon (Desktop only) -->
                        <li class="nav-item d-none d-lg-block">
                            <a class="nav-link position-relative" href="/chat" title="Messages">
                                <i class="fas fa-comments"></i>
                                <span id="chatBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.6rem;">
                                    0
                                </span>
                            </a>
                        </li>
                        
                        <!-- Notification Bell (Desktop only) -->
                        <li class="nav-item dropdown d-none d-lg-block">
                            <a class="nav-link position-relative dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" title="Notifications">
                                <i class="fas fa-bell"></i>
                                <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.6rem;">
                                    0
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <div class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span><strong>Notifications</strong></span>
                                    <button type="button" class="btn btn-sm btn-link text-primary p-0" onclick="markAllNotificationsRead()">Mark all read</button>
                                </div>
                                <div class="dropdown-divider"></div>
                                <div id="notificationList">
                                    <div class="text-center py-3 text-muted">
                                        <i class="fas fa-spinner fa-spin"></i> Loading...
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-primary text-center" href="/notifications">View All Notifications</a>
                            </div>
                        </li>
                        <?php else: ?>
                        <!-- Create Notification Icon (for admin only) -->
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/notifications" title="Create Notification">
                                <i class="fas fa-bullhorn"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?= htmlspecialchars($currentUser['name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if ($currentUser['type'] === USER_TYPE_JOBSEEKER): ?>
                                    <li><a class="dropdown-item" href="/jobseeker/profile"><i class="fas fa-id-card"></i> My Profile</a></li>
                                    <li><a class="dropdown-item" href="/jobseeker/applications"><i class="fas fa-file-alt"></i> My Applications</a></li>
                                    <li><a class="dropdown-item" href="/chat"><i class="fas fa-comments"></i> Messages</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php elseif ($currentUser['type'] === USER_TYPE_EMPLOYER): ?>
                                    <li><a class="dropdown-item" href="/employer/jobs"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                                    <li><a class="dropdown-item" href="/employer/candidates"><i class="fas fa-users"></i> Browse Candidates</a></li>
                                    <li><a class="dropdown-item" href="/chat"><i class="fas fa-comments"></i> Messages</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php elseif ($currentUser['type'] === USER_TYPE_ADMIN): ?>
                                    <li><a class="dropdown-item" href="/admin/users"><i class="fas fa-users"></i> Manage Users</a></li>
                                    <li><a class="dropdown-item" href="/admin/jobs"><i class="fas fa-briefcase"></i> Manage Jobs</a></li>
                                    <li><a class="dropdown-item" href="/admin/candidates"><i class="fas fa-user-graduate"></i> Browse Candidates</a></li>
                                    <li><a class="dropdown-item" href="/admin/reports"><i class="fas fa-flag"></i> Reports</a></li>
                                    <li><a class="dropdown-item" href="/admin/notifications"><i class="fas fa-bullhorn"></i> System Notifications</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-light text-primary ms-2" style="background-color: white !important;" href="/register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="py-4">
