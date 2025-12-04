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
        
        // Store button states to restore later
        var buttonStates = {};
        
        // Function to restore button after page load
        function restoreButtons() {
            console.log('Restoring buttons after page load');
            for (var btnId in buttonStates) {
                var btn = document.getElementById(btnId);
                if (btn) {
                    btn.innerHTML = buttonStates[btnId];
                    btn.disabled = false;
                    console.log('Restored button:', btnId);
                }
            }
            buttonStates = {};
        }
        
        // Function to add loader to button
        function addLoaderToButton(btn) {
            if (!btn) return;
            if (btn.querySelector('.btn-loader-spinner')) {
                console.log('Spinner already exists');
                return;
            }
            
            console.log('Adding loader to button: ', btn.textContent);
            
            // Save button state if it has an id
            if (btn.id) {
                buttonStates[btn.id] = btn.innerHTML;
            }
            
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
        
        // Restore buttons when page is fully loaded
        window.addEventListener('load', function() {
            console.log('Page fully loaded, restoring buttons');
            restoreButtons();
        });
        
        // Also restore on beforeunload (when form will submit)
        window.addEventListener('beforeunload', function() {
            console.log('Page about to unload');
            // This doesn't restore, but ensures clean state
        });
        
        // Add loader when submit button is CLICKED
        document.addEventListener('click', function(e) {
            if (e.target && e.target.type === 'submit' && !e.target.classList.contains('btn-danger')) {
                console.log('Submit button clicked:', e.target);
                
                // Validate required fields first
                var form = e.target.closest('form');
                if (form) {
                    var requiredFields = form.querySelectorAll('[required]');
                    var isValid = true;
                    
                    requiredFields.forEach(function(field) {
                        if (!field.value || !field.value.trim()) {
                            isValid = false;
                            console.log('Field is empty:', field.name);
                        }
                    });
                    
                    if (!isValid) {
                        console.log('Form validation failed - not submitting');
                        alert('Please fill in all required fields');
                        return false;
                    }
                }
                
                // If validation passes, show loader
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
