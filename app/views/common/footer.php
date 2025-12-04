    </main>
    
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-briefcase"></i> ConnectWith9</h5>
                    <p>Your trusted job portal connecting job seekers with employers.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/jobs" class="text-white-50">Browse Jobs</a></li>
                        <li><a href="/register" class="text-white-50">Register</a></li>
                        <li><a href="/login" class="text-white-50">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <p class="text-white-50">
                        Email: info@connectwith9.com<br>
                        Phone: +91 1234567890
                    </p>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="text-center">
                <p class="mb-0">&copy; 2025 ConnectWith9. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        // Add loader to all submit buttons when form is submitted
        document.addEventListener('submit', function(e) {
            var btn = e.target.querySelector('button[type="submit"]');
            if (btn && !btn.classList.contains('btn-danger')) {
                // Create spinner
                var spinner = document.createElement('span');
                spinner.className = 'btn-spinner';
                spinner.innerHTML = '';
                // Insert at beginning of button
                if (btn.firstChild) {
                    btn.insertBefore(spinner, btn.firstChild);
                } else {
                    btn.appendChild(spinner);
                }
                btn.disabled = true;
            }
        }, true);
    </script>
</body>
</html>
