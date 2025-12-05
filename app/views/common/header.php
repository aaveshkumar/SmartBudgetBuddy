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
                        <!-- Chat Icon -->
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="/chat" title="Messages">
                                <i class="fas fa-comments"></i>
                                <span id="chatBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.6rem;">
                                    0
                                </span>
                            </a>
                        </li>
                        
                        <!-- Notification Bell (for non-admin users) -->
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" title="Notifications">
                                <i class="fas fa-bell"></i>
                                <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.6rem;">
                                    0
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span><strong>Notifications</strong></span>
                                    <button type="button" class="btn btn-sm btn-link text-primary p-0" onclick="markAllNotificationsRead()">Mark all read</button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <div id="notificationList">
                                    <li class="text-center py-3 text-muted">
                                        <i class="fas fa-spinner fa-spin"></i> Loading...
                                    </li>
                                </div>
                                <li><hr class="dropdown-divider"></li>
                                <li class="text-center">
                                    <a class="dropdown-item text-primary" href="/notifications">View All Notifications</a>
                                </li>
                            </ul>
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
