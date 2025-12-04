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
        console.log('Footer script loading - setting up form loader');
        
        // Inject animation styles
        if (!document.getElementById('spinner-styles')) {
            var style = document.createElement('style');
            style.id = 'spinner-styles';
            style.innerHTML = `
                @keyframes spinLoader {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                .btn-loader-spinner {
                    display: inline-block;
                    width: 16px;
                    height: 16px;
                    border: 3px solid rgba(255, 255, 255, 0.2);
                    border-top-color: white;
                    border-right-color: white;
                    border-radius: 50%;
                    animation: spinLoader 0.8s linear infinite;
                    margin-right: 10px;
                    vertical-align: middle;
                }
            `;
            document.head.appendChild(style);
            console.log('Spinner styles injected');
        }
        
        // Function to add loader to button
        function addLoaderToButton(btn) {
            if (!btn) return;
            if (btn.querySelector('.btn-loader-spinner')) {
                console.log('Spinner already exists');
                return;
            }
            
            console.log('Adding loader to button: ', btn.textContent);
            // Save original text
            var originalText = btn.textContent;
            btn.dataset.originalText = originalText;
            
            // Create spinner
            var spinner = document.createElement('span');
            spinner.className = 'btn-loader-spinner';
            spinner.innerHTML = '';
            
            // Clear button text and add spinner + text
            btn.innerHTML = '';
            btn.appendChild(spinner);
            btn.appendChild(document.createTextNode('Processing...'));
            btn.disabled = true;
            console.log('Loader added successfully to button with text');
        }
        
        // Add loader when submit button is CLICKED
        document.addEventListener('click', function(e) {
            if (e.target && e.target.type === 'submit' && !e.target.classList.contains('btn-danger')) {
                console.log('Submit button clicked:', e.target);
                addLoaderToButton(e.target);
            }
        }, true);
        
        // Also add loader on form submission (for keyboard enter)
        document.addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            var btn = e.target.querySelector('button[type="submit"]');
            addLoaderToButton(btn);
        }, true);
    </script>
</body>
</html>
