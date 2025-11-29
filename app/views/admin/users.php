<?php
$meta = generateMetaTags('Manage Users - Admin', 'Manage platform users');
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <h1 class="mb-4">Manage Users</h1>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <form action="/admin/users" method="GET" class="row g-2">
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="jobseeker" <?= (isset($_GET['type']) && $_GET['type'] === 'jobseeker') ? 'selected' : '' ?>>Job Seekers</option>
                        <option value="employer" <?= (isset($_GET['type']) && $_GET['type'] === 'employer') ? 'selected' : '' ?>>Employers</option>
                        <option value="admin" <?= (isset($_GET['type']) && $_GET['type'] === 'admin') ? 'selected' : '' ?>>Admins</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Type</th>
                            <th>Verified</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['mobile_no'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-<?= $user['type'] === 'admin' ? 'danger' : ($user['type'] === 'employer' ? 'primary' : 'secondary') ?>">
                                    <?= ucfirst($user['type']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['verified']): ?>
                                    <i class="fas fa-check-circle text-success"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle text-danger"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= formatDate($user['created_at']) ?></td>
                            <td>
                                <a href="/admin/users/<?= $user['id'] ?>" class="btn btn-sm btn-info">View</a>
                                <?php if ($user['type'] !== 'admin'): ?>
                                    <form action="/admin/users/<?= $user['id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                <?php endif; ?>
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
                            <a class="page-link" href="?page=<?= $i ?><?= !empty($_GET['type']) ? '&type=' . $_GET['type'] : '' ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>">
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
