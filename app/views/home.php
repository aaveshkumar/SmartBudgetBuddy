<?php
$meta = generateMetaTags(
    'Find Your Dream Job',
    'Browse thousands of job opportunities across multiple categories. ConnectWith9 - Connecting talent with opportunity.',
    'jobs, careers, employment, job portal, job search'
);

require __DIR__ . '/common/header.php';
?>

<div class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold">Find Your Dream Job Today</h1>
                <p class="lead">Connect with top employers and discover thousands of job opportunities.</p>
                <a href="/jobs" class="btn btn-light btn-lg mt-3">Browse Jobs</a>
                <a href="/register" class="btn btn-outline-light btn-lg mt-3 ms-2">Register Now</a>
            </div>
            <div class="col-lg-6">
                <img src="<?= asset('images/hero.svg') ?>" alt="Job Search" class="img-fluid" onerror="this.style.display='none'">
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center mb-4">Latest Job Opportunities</h2>
        </div>
    </div>
    
    <div class="row">
        <?php foreach ($latestJobs as $job): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/jobs/<?= $job['id'] ?>" class="text-decoration-none">
                            <?= htmlspecialchars($job['title']) ?>
                        </a>
                    </h5>
                    <p class="text-muted mb-2">
                        <i class="fas fa-building"></i> <?= htmlspecialchars($job['employer_name']) ?>
                    </p>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?>
                    </p>
                    <p class="mb-2">
                        <span class="badge bg-info"><?= htmlspecialchars($job['type']) ?></span>
                        <span class="badge bg-secondary"><?= htmlspecialchars($job['category_name']) ?></span>
                    </p>
                    <p class="card-text"><?= truncate(strip_tags($job['description']), 100) ?></p>
                    <a href="/jobs/<?= $job['id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                </div>
                <div class="card-footer text-muted">
                    <small><i class="fas fa-clock"></i> <?= formatDate($job['created_at']) ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-4">
        <a href="/jobs" class="btn btn-outline-primary btn-lg">View All Jobs</a>
    </div>
</div>

<div class="bg-light py-5 mt-5">
    <div class="container">
        <h2 class="text-center mb-5">Browse by Category</h2>
        <div class="row">
            <?php foreach ($categories as $category): ?>
            <div class="col-md-4 col-lg-3 mb-3">
                <a href="/jobs?category=<?= $category['id'] ?>" class="text-decoration-none">
                    <div class="card text-center shadow-sm hover-shadow">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                            <p class="text-muted"><?= $category['job_count'] ?> jobs</p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/common/footer.php'; ?>
