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
                        <li class="nav-item">
                            <a class="nav-link" href="/logout">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-light text-primary" href="/register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="py-4">
