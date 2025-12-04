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
                        <?php if (isset($currentUser) && $currentUser): ?>
                            <li><a href="/logout" class="text-white-50">Logout</a></li>
                        <?php else: ?>
                            <li><a href="/register" class="text-white-50">Register</a></li>
                            <li><a href="/login" class="text-white-50">Login</a></li>
                        <?php endif; ?>
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
        // Inject animation styles for button loader and page loader
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
                
                /* Page Loader Overlay Styles */
                .page-loader-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: transparent;
                    z-index: 9999;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                .page-loader-content {
                    text-align: center;
                }
                .page-loader-spinner {
                    width: 50px;
                    height: 50px;
                    border: 4px solid rgba(13, 110, 253, 0.3);
                    border-top-color: #0d6efd;
                    border-radius: 50%;
                    animation: spinLoader 0.8s linear infinite;
                    margin: 0 auto 10px;
                }
                .page-loader-text {
                    color: #0d6efd;
                    font-size: 14px;
                    font-weight: 600;
                    margin: 0;
                }
            `;
            document.head.appendChild(style);
        }
        
        // Determine loader text based on button
        function getLoaderText(btn) {
            if (!btn) return 'Processing...';
            
            var btnText = btn.textContent.trim().toLowerCase();
            var classList = btn.className;
            
            // Check button class for action
            if (classList.includes('btn-danger')) return 'Deleting...';
            if (classList.includes('btn-warning')) return 'Updating...';
            if (classList.includes('btn-success')) return 'Saving...';
            if (classList.includes('btn-info')) return 'Loading...';
            
            // Check button text for action
            if (btnText.includes('search')) return 'Searching...';
            if (btnText.includes('delete')) return 'Deleting...';
            if (btnText.includes('save')) return 'Saving...';
            if (btnText.includes('update')) return 'Updating...';
            if (btnText.includes('submit')) return 'Submitting...';
            if (btnText.includes('register')) return 'Registering...';
            if (btnText.includes('login')) return 'Loading...';
            if (btnText.includes('approve')) return 'Approving...';
            if (btnText.includes('reject')) return 'Rejecting...';
            if (btnText.includes('apply')) return 'Applying...';
            if (btnText.includes('post')) return 'Posting...';
            if (btnText.includes('publish')) return 'Publishing...';
            if (btnText.includes('cancel')) return 'Canceling...';
            if (btnText.includes('confirm')) return 'Confirming...';
            
            return 'Processing...';
        }
        
        // Add loader on form submission
        document.addEventListener('submit', function(e) {
            var btn = e.target.querySelector('button[type="submit"]');
            
            if (!btn) return;
            
            // Check if already has loader
            if (btn.querySelector('.btn-loader-spinner')) {
                return;
            }
            
            // Create and add spinner
            var spinner = document.createElement('span');
            spinner.className = 'btn-loader-spinner';
            var loaderText = getLoaderText(btn);
            
            // Save original content
            var originalHTML = btn.innerHTML;
            btn.dataset.loaderOriginal = originalHTML;
            
            // Replace with spinner + text
            btn.innerHTML = '';
            btn.appendChild(spinner);
            btn.appendChild(document.createTextNode(' ' + loaderText));
            btn.disabled = true;
        }, true);
        
        // Page loader for navigation links
        (function() {
            var pageLoader = document.getElementById('pageLoader');
            var loaderText = pageLoader ? pageLoader.querySelector('.page-loader-text') : null;
            
            function showPageLoader(text) {
                if (pageLoader) {
                    if (loaderText) loaderText.textContent = text || 'Loading...';
                    pageLoader.style.display = 'flex';
                }
            }
            
            function hidePageLoader() {
                if (pageLoader) {
                    pageLoader.style.display = 'none';
                }
            }
            
            // Hide loader when page loads (for back/forward navigation)
            window.addEventListener('pageshow', hidePageLoader);
            
            // Get loader text based on link text
            function getNavLoaderText(linkText) {
                var text = linkText.toLowerCase().trim();
                if (text.includes('dashboard')) return 'Loading Dashboard...';
                if (text.includes('jobs') || text.includes('browse jobs')) return 'Loading Jobs...';
                if (text.includes('candidates')) return 'Loading Candidates...';
                if (text.includes('profile')) return 'Loading Profile...';
                if (text.includes('applications')) return 'Loading Applications...';
                if (text.includes('login')) return 'Loading...';
                if (text.includes('register')) return 'Loading...';
                if (text.includes('logout')) return 'Logging out...';
                if (text.includes('home')) return 'Loading...';
                if (text.includes('post job')) return 'Loading...';
                if (text.includes('users')) return 'Loading Users...';
                return 'Loading...';
            }
            
            // Add click handler to navbar and footer links
            document.addEventListener('click', function(e) {
                var link = e.target.closest('a');
                
                if (!link) return;
                
                // Skip if it's a dropdown toggle, hash link, or external link
                if (link.getAttribute('data-bs-toggle') === 'dropdown') return;
                if (link.getAttribute('href') === '#') return;
                if (link.getAttribute('target') === '_blank') return;
                
                var href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
                
                // Check if link is in navbar or footer quick links
                var isNavLink = link.closest('.navbar') !== null;
                var isFooterLink = link.closest('footer') !== null;
                var isDropdownItem = link.classList.contains('dropdown-item');
                
                if (isNavLink || isFooterLink || isDropdownItem) {
                    var linkText = link.textContent.trim();
                    showPageLoader(getNavLoaderText(linkText));
                }
            }, true);
        })();
        
        // Button loader for specific action buttons to prevent double-click
        (function() {
            var loadingButtons = [
                'manage users', 'manage jobs', 'review jobs', 'view jobs',
                'browse jobs', 'register now', 'view details', 'view all jobs'
            ];
            
            function shouldShowButtonLoader(btnText) {
                var text = btnText.toLowerCase().trim();
                return loadingButtons.some(function(loadBtn) {
                    return text === loadBtn || text.includes(loadBtn);
                });
            }
            
            document.addEventListener('click', function(e) {
                var btn = e.target.closest('a.btn, button.btn');
                
                if (!btn) return;
                
                // Skip if already loading
                if (btn.classList.contains('btn-loading')) return;
                if (btn.querySelector('.btn-loader-spinner')) return;
                
                var btnText = btn.textContent.trim();
                
                if (shouldShowButtonLoader(btnText)) {
                    // Prevent double click
                    btn.classList.add('btn-loading');
                    
                    // Save original content
                    var originalHTML = btn.innerHTML;
                    btn.dataset.loaderOriginal = originalHTML;
                    
                    // Create spinner
                    var spinner = document.createElement('span');
                    spinner.className = 'btn-loader-spinner';
                    
                    // Replace content
                    btn.innerHTML = '';
                    btn.appendChild(spinner);
                    btn.appendChild(document.createTextNode(' Loading...'));
                    
                    // Disable pointer events
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '0.7';
                }
            }, true);
            
            // Reset buttons when page shows (for back/forward navigation)
            window.addEventListener('pageshow', function() {
                document.querySelectorAll('.btn-loading').forEach(function(btn) {
                    if (btn.dataset.loaderOriginal) {
                        btn.innerHTML = btn.dataset.loaderOriginal;
                        delete btn.dataset.loaderOriginal;
                    }
                    btn.classList.remove('btn-loading');
                    btn.style.pointerEvents = '';
                    btn.style.opacity = '';
                });
            });
        })();
    </script>
</body>
</html>
