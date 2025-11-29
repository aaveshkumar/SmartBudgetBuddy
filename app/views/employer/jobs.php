<?php
$meta = generateMetaTags('My Jobs', 'View and manage your job postings');
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <h1 class="mb-4">My Jobs</h1>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?= htmlspecialchars($job['title']) ?></td>
                            <td><?= htmlspecialchars($job['location']) ?></td>
                            <td><span class="badge bg-info"><?= $job['type'] ?></span></td>
                            <td>
                                <span class="badge bg-<?= $job['status'] === 'Approved' ? 'success' : ($job['status'] === 'Pending' ? 'warning' : 'danger') ?>">
                                    <?= $job['status'] ?>
                                </span>
                            </td>
                            <td><?= formatDate($job['created_at']) ?></td>
                            <td>
                                <a href="/employer/jobs/<?= $job['id'] ?>/applications" class="btn btn-sm btn-primary">Applications</a>
                                <?php if ($job['status'] === 'Approved'): ?>
                                    <a href="/jobs/<?= $job['id'] ?>" class="btn btn-sm btn-info" target="_blank">View</a>
                                <?php endif; ?>
                                <form action="/employer/jobs/<?= $job['id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
