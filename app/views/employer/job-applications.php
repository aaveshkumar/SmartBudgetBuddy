<?php
$meta = generateMetaTags('Job Applications', 'View applications for ' . $job['title']);
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <h1 class="mb-4">Applications for: <?= htmlspecialchars($job['title']) ?></h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5>Job Details</h5>
            <p class="mb-1"><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
            <p class="mb-1"><strong>Type:</strong> <?= htmlspecialchars($job['type']) ?></p>
            <p class="mb-0"><strong>Total Applications:</strong> <?= count($applications) ?></p>
        </div>
    </div>
    
    <?php if (empty($applications)): ?>
        <div class="alert alert-info">No applications received yet.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Applicant Name</th>
                                <th>Email</th>
                                <th>Applied On</th>
                                <th>Resume</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                            <tr>
                                <td><?= htmlspecialchars($application['applicant_name']) ?></td>
                                <td><?= htmlspecialchars($application['applicant_email']) ?></td>
                                <td><?= formatDate($application['applied_at']) ?></td>
                                <td>
                                    <a href="/public/uploads/resumes/<?= htmlspecialchars($application['resume_path']) ?>" class="btn btn-sm btn-primary" target="_blank" download>
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>
