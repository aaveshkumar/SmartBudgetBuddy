<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php require __DIR__ . '/../common/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-key fa-3x text-primary mb-3"></i>
                            <h2 class="mb-2">Forgot Password?</h2>
                            <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
                        </div>
                        
                        <?php if (hasFlash('error')): ?>
                            <div class="alert alert-danger"><?= getFlash('error') ?></div>
                        <?php endif; ?>
                        
                        <?php if (hasFlash('success')): ?>
                            <div class="alert alert-success"><?= getFlash('success') ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="/forgot-password">
                            <?= csrfField() ?>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Enter your registered email" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                            </button>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p class="mb-0">
                                Remember your password? <a href="/login">Login here</a>
                            </p>
                            <p class="mt-2">
                                Don't have an account? <a href="/register">Register here</a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3 bg-info bg-opacity-10 border-info">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Password Reset Instructions</h6>
                        <ul class="small mb-0">
                            <li>Enter your registered email address</li>
                            <li>Check your inbox for a reset link</li>
                            <li>Click the link within 1 hour (link expires after that)</li>
                            <li>Create a new strong password</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php require __DIR__ . '/../common/footer.php'; ?>
</body>
</html>
