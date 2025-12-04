<?php
$meta = generateMetaTags('Job Applications', 'View applications for ' . $job['title']);
require __DIR__ . '/../common/header.php';
?>

<div class="container">
    <h1 class="mb-4">Applications for: <?= htmlspecialchars($job['title']) ?></h1>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if ($error = getFlash('error')): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['selection_notification'])): ?>
        <?php $notification = $_SESSION['selection_notification']; unset($_SESSION['selection_notification']); ?>
        <div class="alert alert-info">
            <h5><i class="fas fa-bell"></i> Send Selection Notification to <?= htmlspecialchars($notification['candidate_name']) ?></h5>
            <p class="mb-2">Click below to notify the candidate about their selection:</p>
            <a href="<?= $notification['email_link'] ?>" class="btn btn-primary me-2" target="_blank">
                <i class="fas fa-envelope"></i> Send Email Notification
            </a>
            <?php if ($notification['whatsapp_link']): ?>
            <a href="<?= $notification['whatsapp_link'] ?>" class="btn btn-success" target="_blank">
                <i class="fab fa-whatsapp"></i> Send WhatsApp Notification
            </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
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
                                <th>Phone</th>
                                <th>Applied On</th>
                                <th>Resume</th>
                                <th>Contact</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                            <?php
                                $phone = $application['applicant_phone'] ?? '';
                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                $email = $application['applicant_email'];
                                $name = $application['applicant_name'];
                                $jobTitle = $job['title'];
                                
                                $emailSubject = rawurlencode("Regarding your application for $jobTitle");
                                $emailBody = rawurlencode("Dear $name,\n\nThank you for applying for the position of $jobTitle.\n\nBest regards");
                                
                                $whatsappMessage = rawurlencode("Hello $name, I'm reaching out regarding your application for the position of $jobTitle.");
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($name) ?></td>
                                <td><?= htmlspecialchars($email) ?></td>
                                <td><?= $phone ? htmlspecialchars($phone) : '<span class="text-muted">Not provided</span>' ?></td>
                                <td><?= formatDate($application['applied_at']) ?></td>
                                <td>
                                    <a href="/public/uploads/resumes/<?= htmlspecialchars($application['resume_path']) ?>" class="btn btn-sm btn-primary" target="_blank" download>
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($email) ?>?subject=<?= $emailSubject ?>&body=<?= $emailBody ?>" class="btn btn-sm btn-info text-white" title="Send Email">
                                        <i class="fas fa-envelope"></i> Email
                                    </a>
                                    <?php if ($cleanPhone): ?>
                                    <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $whatsappMessage ?>" class="btn btn-sm btn-success mt-1" target="_blank" title="Send WhatsApp">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </a>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-secondary mt-1" disabled title="No phone number">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </button>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($application['status']) && $application['status'] === 'selected'): ?>
                                        <span class="badge bg-success"><i class="fas fa-check"></i> Selected</span>
                                    <?php else: ?>
                                        <form action="/employer/jobs/<?= $job['id'] ?>/applications/<?= $application['id'] ?>/select" method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Select this candidate? An automated message will be sent to notify them.')">
                                                <i class="fas fa-user-check"></i> Select
                                            </button>
                                        </form>
                                    <?php endif; ?>
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
