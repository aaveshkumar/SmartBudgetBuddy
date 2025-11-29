<?php
$meta = generateMetaTags('Browse Jobs', 'Find your next career opportunity. Browse thousands of jobs across multiple categories.');
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <h1 class="mb-4">Browse Jobs</h1>
    
    <?php if ($error = getFlash('error')): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <form action="/jobs" method="GET" class="card p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" placeholder="Search jobs..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="location" placeholder="Location" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <?php if (empty($jobs)): ?>
            <div class="col-12">
                <div class="alert alert-info">No jobs found. Try adjusting your search criteria.</div>
            </div>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
            <div class="col-md-12 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <h5 class="card-title">
                                    <a href="/jobs/<?= $job['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($job['title']) ?>
                                    </a>
                                </h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-building"></i> <?= htmlspecialchars($job['employer_name']) ?> |
                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?>
                                </p>
                                <p class="mb-2">
                                    <span class="badge bg-info"><?= htmlspecialchars($job['type']) ?></span>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($job['category_name']) ?></span>
                                    <?php if ($job['salary']): ?>
                                        <span class="badge bg-success"><?= formatSalary($job['salary']) ?></span>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text"><?= truncate(strip_tags($job['description']), 150) ?></p>
                            </div>
                            <div class="col-md-3 text-end">
                                <small class="text-muted d-block mb-2">
                                    <i class="fas fa-clock"></i> <?= formatDate($job['created_at']) ?>
                                </small>
                                <a href="/jobs/<?= $job['id'] ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if ($pagination['total_pages'] > 1): ?>
            <div class="col-12">
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['category']) ? '&category=' . $_GET['category'] : '' ?><?= !empty($_GET['location']) ? '&location=' . urlencode($_GET['location']) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
