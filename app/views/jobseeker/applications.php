<?php
$meta = generateMetaTags('My Applications', 'View all your job applications');
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <h1 class="mb-4">My Applications</h1>
    
    <?php if (empty($applications)): ?>
        <div class="alert alert-info">
            You haven't applied to any jobs yet. <a href="/jobs" class="alert-link">Browse Jobs</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($applications as $app): ?>
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($app['job_title']) ?></h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-building"></i> <?= htmlspecialchars($app['employer_name']) ?> |
                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($app['location']) ?>
                        </p>
                        <p class="mb-2">
                            <span class="badge bg-info"><?= htmlspecialchars($app['type']) ?></span>
                            <span class="badge bg-secondary"><?= htmlspecialchars($app['category_name']) ?></span>
                        </p>
                        <p class="text-muted mb-0">
                            <small>Applied on: <?= formatDate($app['applied_at']) ?></small>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
            <div class="col-12">
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
