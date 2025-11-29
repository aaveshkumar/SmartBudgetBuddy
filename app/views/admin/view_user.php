<?php
$meta = generateMetaTags('View/Edit User - Admin', 'View and edit user details');
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>View & Edit User</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="/admin/users" class="btn btn-secondary">Back to Users</a>
        </div>
    </div>
    
    <?php if ($error = getFlash('error')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Complete Account Information</h5>
        </div>
        <div class="card-body">
            <form action="/admin/users/<?= $user['id'] ?>/update" method="POST">
                <?= csrfField() ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">User Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="jobseeker" <?= $user['type'] === 'jobseeker' ? 'selected' : '' ?>>Job Seeker</option>
                            <option value="employer" <?= $user['type'] === 'employer' ? 'selected' : '' ?>>Employer</option>
                            <option value="admin" <?= $user['type'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="verified" class="form-label">Verification Status</label>
                        <select class="form-select" id="verified" name="verified" required>
                            <option value="0" <?= !$user['verified'] ? 'selected' : '' ?>>Not Verified</option>
                            <option value="1" <?= $user['verified'] ? 'selected' : '' ?>>Verified</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="password" class="form-label">Change Password (Leave empty to keep current)</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password (min. 6 characters)">
                        <small class="form-text text-muted">If you change the password, enter at least 6 characters.</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-muted mb-2"><strong>Account Created:</strong> <?= formatDate($user['created_at']) ?></p>
                        <p class="text-muted mb-3"><strong>User ID:</strong> <?= $user['id'] ?></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success btn-lg">Save Changes</button>
                        <a href="/admin/users" class="btn btn-secondary btn-lg">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
