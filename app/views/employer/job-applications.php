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
            <button type="button" class="btn btn-primary me-2" onclick="sendSelectionEmail()">
                <i class="fas fa-envelope"></i> Send Email Notification
            </button>
            <?php if ($notification['candidate_phone']): ?>
            <button type="button" class="btn btn-success" onclick="sendSelectionWhatsApp()">
                <i class="fab fa-whatsapp"></i> Send WhatsApp Notification
            </button>
            <?php endif; ?>
        </div>
        <script>
            function sendSelectionEmail() {
                var email = <?= json_encode($notification['candidate_email']) ?>;
                var subject = <?= json_encode($notification['email_subject']) ?>;
                var body = <?= json_encode($notification['email_body']) ?>;
                var mailtoLink = 'mailto:' + encodeURIComponent(email) + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);
                window.location.href = mailtoLink;
            }
            function sendSelectionWhatsApp() {
                var phone = <?= json_encode($notification['candidate_phone']) ?>;
                var message = <?= json_encode($notification['whatsapp_message']) ?>;
                var whatsappLink = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
                window.open(whatsappLink, '_blank');
            }
        </script>
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
                                <th>Status</th>
                                <th>Resume</th>
                                <th>Contact</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $index => $application): ?>
                            <?php
                                $phone = $application['applicant_phone'] ?? '';
                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                $email = $application['applicant_email'];
                                $name = $application['applicant_name'];
                                $jobTitle = $job['title'];
                                $isSelected = ($application['status'] ?? 'pending') === 'selected';
                            ?>
                            <tr class="<?= $isSelected ? 'table-success' : '' ?>">
                                <td><?= htmlspecialchars($name) ?></td>
                                <td><?= htmlspecialchars($email) ?></td>
                                <td><?= $phone ? htmlspecialchars($phone) : '<span class="text-muted">Not provided</span>' ?></td>
                                <td><?= formatDate($application['applied_at']) ?></td>
                                <td>
                                    <?php if ($isSelected): ?>
                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> Selected</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/public/uploads/resumes/<?= htmlspecialchars($application['resume_path']) ?>" class="btn btn-sm btn-primary" target="_blank" download>
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info text-white" onclick="sendEmail<?= $index ?>()" title="Send Email">
                                        <i class="fas fa-envelope"></i> Email
                                    </button>
                                    <?php if ($cleanPhone): ?>
                                    <button type="button" class="btn btn-sm btn-success mt-1" onclick="sendWhatsApp<?= $index ?>()" title="Send WhatsApp">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-secondary mt-1" disabled title="No phone number">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($isSelected): ?>
                                    <a href="/chat/conversation/<?= $application['user_id'] ?>?job_id=<?= $job['id'] ?>" class="btn btn-sm btn-primary mt-1" title="Message Candidate">
                                        <i class="fas fa-comment"></i> Message
                                    </a>
                                    <?php endif; ?>
                                    <script>
                                        function sendEmail<?= $index ?>() {
                                            var email = <?= json_encode($email) ?>;
                                            var subject = <?= json_encode("Regarding your application for $jobTitle") ?>;
                                            var body = <?= json_encode("Dear $name,\n\nThank you for applying for the position of $jobTitle.\n\nBest regards") ?>;
                                            var mailtoLink = 'mailto:' + encodeURIComponent(email) + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);
                                            window.location.href = mailtoLink;
                                        }
                                        <?php if ($cleanPhone): ?>
                                        function sendWhatsApp<?= $index ?>() {
                                            var phone = <?= json_encode($cleanPhone) ?>;
                                            var message = <?= json_encode("Hello $name, I'm reaching out regarding your application for the position of $jobTitle.") ?>;
                                            var whatsappLink = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
                                            window.open(whatsappLink, '_blank');
                                        }
                                        <?php endif; ?>
                                    </script>
                                </td>
                                <td>
                                    <?php if ($isSelected): ?>
                                        <button type="button" class="btn btn-sm btn-outline-success" disabled>
                                            <i class="fas fa-user-check"></i> Selected
                                        </button>
                                    <?php else: ?>
                                        <form action="/employer/jobs/<?= $job['id'] ?>/applications/<?= $application['id'] ?>/select" method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Select this candidate? You will be prompted to send them a notification.')">
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
