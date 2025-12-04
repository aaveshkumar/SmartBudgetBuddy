<?php
$meta = generateMetaTags('Manage Jobs - Admin', 'Manage job postings');
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <h1 class="mb-4">Manage Jobs</h1>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <form action="/admin/jobs" method="GET" class="row g-2">
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="Pending" <?= (isset($_GET['status']) && $_GET['status'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="Approved" <?= (isset($_GET['status']) && $_GET['status'] === 'Approved') ? 'selected' : '' ?>>Approved</option>
                        <option value="Rejected" <?= (isset($_GET['status']) && $_GET['status'] === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Employer</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?= $job['id'] ?></td>
                            <td><?= htmlspecialchars($job['title']) ?></td>
                            <td><?= htmlspecialchars($job['employer_name']) ?></td>
                            <td><?= htmlspecialchars($job['location']) ?></td>
                            <td>
                                <span class="badge bg-<?= $job['status'] === 'Approved' ? 'success' : ($job['status'] === 'Pending' ? 'warning' : 'danger') ?>">
                                    <?= $job['status'] ?>
                                </span>
                            </td>
                            <td><?= formatDate($job['created_at']) ?></td>
                            <td>
                                <a href="/admin/jobs/<?= $job['id'] ?>" class="btn btn-sm btn-info">View</a>
                                <?php if ($job['status'] === 'Pending'): ?>
                                    <form action="/admin/jobs/<?= $job['id'] ?>/approve" method="POST" class="d-inline">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="/admin/jobs/<?= $job['id'] ?>/reject" method="POST" class="d-inline">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-sm btn-warning">Reject</button>
                                    </form>
                                <?php endif; ?>
                                <form action="/admin/jobs/<?= $job['id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($pagination['total_pages'] > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= !empty($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
