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
    
    <?php if (isset($currentUser) && $currentUser && ($currentUser['status'] ?? 'active') === 'active' && ($currentUser['type'] ?? '') !== USER_TYPE_ADMIN): ?>
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel"><i class="fas fa-flag text-danger"></i> Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reportType" value="">
                    <input type="hidden" id="reportId" value="">
                    <input type="hidden" id="reportCsrfToken" value="<?= getCSRFToken() ?>">
                    <div class="mb-3">
                        <label for="reportMessage" class="form-label">Why are you reporting this?</label>
                        <textarea class="form-control" id="reportMessage" rows="4" placeholder="Please describe the issue in detail (at least 10 characters)..." required></textarea>
                    </div>
                    <div id="reportError" class="alert alert-danger d-none"></div>
                    <div id="reportSuccess" class="alert alert-success d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="submitReportBtn" onclick="submitReport()">
                        <i class="fas fa-flag"></i> Submit Report
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
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
            // Skip forms with no-loader class (like chat)
            if (e.target.classList.contains('no-loader')) {
                return;
            }
            
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
            
            // Email button handler with loader
            document.addEventListener('click', function(e) {
                if (e.target.closest('.contact-email-btn')) {
                    e.preventDefault();
                    var btn = e.target.closest('.contact-email-btn');
                    var href = btn.getAttribute('href');
                    
                    // Show loader
                    btn.disabled = true;
                    var originalHTML = btn.innerHTML;
                    btn.innerHTML = '<span class="btn-loader-spinner" style="border-color: rgba(13, 110, 253, 0.2); border-top-color: #0d6efd; border-right-color: #0d6efd;"></span> Opening email...';
                    
                    // Delay to show loader briefly
                    setTimeout(function() {
                        window.location.href = href;
                        // Restore button after short delay
                        setTimeout(function() {
                            btn.innerHTML = originalHTML;
                            btn.disabled = false;
                        }, 1000);
                    }, 300);
                }
            }, true);
        
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
                
                // Skip email and WhatsApp contact links - they have their own handlers
                if (link.classList.contains('contact-email-btn') || link.classList.contains('contact-whatsapp-btn')) {
                    return;
                }
                
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
        
        // Report functionality
        function openReportModal(type, id) {
            // Find and show loader on the report button that was clicked
            var reportBtns = document.querySelectorAll('.report-btn, [onclick*="openReportModal"]');
            reportBtns.forEach(function(btn) {
                if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes(id)) {
                    btn.disabled = true;
                    btn.dataset.originalHtml = btn.innerHTML;
                    btn.innerHTML = '<span class="btn-loader-spinner" style="border-color: rgba(220,53,69,0.2); border-top-color: #dc3545; border-right-color: #dc3545;"></span> Loading...';
                }
            });
            
            document.getElementById('reportType').value = type;
            document.getElementById('reportId').value = id;
            document.getElementById('reportMessage').value = '';
            document.getElementById('reportError').classList.add('d-none');
            document.getElementById('reportSuccess').classList.add('d-none');
            document.getElementById('submitReportBtn').disabled = false;
            document.getElementById('submitReportBtn').innerHTML = '<i class="fas fa-flag"></i> Submit Report';
            
            // Refresh CSRF token before opening modal - wait for it to complete
            fetch('/csrf/token')
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.token) {
                        document.getElementById('reportCsrfToken').value = data.token;
                    }
                    // Restore report buttons
                    reportBtns.forEach(function(btn) {
                        if (btn.dataset.originalHtml) {
                            btn.innerHTML = btn.dataset.originalHtml;
                            btn.disabled = false;
                            delete btn.dataset.originalHtml;
                        }
                    });
                    // Only show modal after token is ready
                    var modal = new bootstrap.Modal(document.getElementById('reportModal'));
                    modal.show();
                })
                .catch(function(err) { 
                    console.log('CSRF refresh error:', err);
                    // Restore report buttons
                    reportBtns.forEach(function(btn) {
                        if (btn.dataset.originalHtml) {
                            btn.innerHTML = btn.dataset.originalHtml;
                            btn.disabled = false;
                            delete btn.dataset.originalHtml;
                        }
                    });
                    // Still show modal on error, will use existing token
                    var modal = new bootstrap.Modal(document.getElementById('reportModal'));
                    modal.show();
                });
        }
        
        function submitReport() {
            var type = document.getElementById('reportType').value;
            var id = document.getElementById('reportId').value;
            var message = document.getElementById('reportMessage').value.trim();
            var csrfToken = document.getElementById('reportCsrfToken').value;
            var errorDiv = document.getElementById('reportError');
            var successDiv = document.getElementById('reportSuccess');
            var btn = document.getElementById('submitReportBtn');
            
            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');
            
            if (message.length < 10) {
                errorDiv.textContent = 'Please provide at least 10 characters describing the issue.';
                errorDiv.classList.remove('d-none');
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<span class="btn-loader-spinner"></span> Submitting...';
            
            var formData = new FormData();
            formData.append('reported_type', type);
            formData.append('reported_id', id);
            formData.append('message', message);
            formData.append('csrf_token', csrfToken);
            
            fetch('/report/submit', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function(response) { 
                return response.text().then(function(text) {
                    console.log('Server response:', text);
                    try {
                        var data = JSON.parse(text);
                        return {data: data, status: response.status};
                    } catch (e) {
                        console.error('JSON parse error - raw response:', text);
                        console.error('Parse error:', e.message);
                        throw new Error('Server returned invalid response: ' + text.substring(0, 100));
                    }
                });
            })
            .then(function(result) {
                var data = result.data;
                if (result.status !== 200 && !data.success) {
                    errorDiv.textContent = data.error || 'Request failed with status ' + result.status;
                    errorDiv.classList.remove('d-none');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-flag"></i> Submit Report';
                } else if (data.error) {
                    errorDiv.textContent = data.error;
                    errorDiv.classList.remove('d-none');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-flag"></i> Submit Report';
                } else if (data.success) {
                    successDiv.textContent = data.message || 'Report submitted successfully!';
                    successDiv.classList.remove('d-none');
                    setTimeout(function() {
                        bootstrap.Modal.getInstance(document.getElementById('reportModal')).hide();
                    }, 2000);
                } else {
                    errorDiv.textContent = 'Unexpected response: ' + JSON.stringify(data);
                    errorDiv.classList.remove('d-none');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-flag"></i> Submit Report';
                }
            })
            .catch(function(err) {
                console.error('Report error:', err);
                errorDiv.textContent = err.message || 'An error occurred. Please try again.';
                errorDiv.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-flag"></i> Submit Report';
            });
        }
        
        <?php if (isset($currentUser) && $currentUser && $currentUser['type'] !== USER_TYPE_ADMIN): ?>
        // Notification and Chat polling (for non-admin users only)
        (function() {
            var notificationBadge = document.getElementById('notificationBadge');
            var notificationBadgeMobile = document.getElementById('notificationBadgeMobile');
            var chatBadge = document.getElementById('chatBadge');
            var chatBadgeMobile = document.getElementById('chatBadgeMobile');
            var notificationList = document.getElementById('notificationList');
            var notificationListMobile = document.getElementById('notificationListMobile');
            var lastPollTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
            
            function updateNotificationBadge(count) {
                var badges = [notificationBadge, notificationBadgeMobile];
                badges.forEach(function(badge) {
                    if (badge) {
                        if (count > 0) {
                            badge.textContent = count > 99 ? '99+' : count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                });
            }
            
            function updateChatBadge(count) {
                var badges = [chatBadge, chatBadgeMobile];
                badges.forEach(function(badge) {
                    if (badge) {
                        if (count > 0) {
                            badge.textContent = count > 99 ? '99+' : count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                });
            }
            
            function formatTimeAgo(dateString) {
                var date = new Date(dateString);
                var now = new Date();
                var seconds = Math.floor((now - date) / 1000);
                
                if (seconds < 60) return 'Just now';
                if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
                if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
                if (seconds < 604800) return Math.floor(seconds / 86400) + 'd ago';
                return date.toLocaleDateString();
            }
            
            function getNotificationIcon(type) {
                switch(type) {
                    case 'job_selected': return 'fa-trophy text-success';
                    case 'new_job': return 'fa-briefcase text-primary';
                    case 'chat_message': return 'fa-comment text-info';
                    case 'system': return 'fa-cog text-warning';
                    default: return 'fa-bell text-secondary';
                }
            }
            
            function renderNotifications(notifications, unreadCount) {
                var lists = [notificationList, notificationListMobile];
                
                lists.forEach(function(list) {
                    if (!list) return;
                    
                    if (notifications.length === 0) {
                        list.innerHTML = '<div class="text-center py-3 text-muted">No notifications yet</div>';
                        return;
                    }
                    
                    var html = '';
                    notifications.slice(0, 5).forEach(function(n) {
                        var iconClass = getNotificationIcon(n.type);
                        var readClass = n.is_read == 0 ? 'bg-light' : '';
                        html += '<div class="dropdown-item ' + readClass + '" style="white-space: normal; padding: 10px 15px; cursor: pointer;">';
                        html += '<div class="d-flex align-items-start">';
                        html += '<i class="fas ' + iconClass + ' me-2 mt-1"></i>';
                        html += '<div class="flex-grow-1">';
                        html += '<strong style="font-size: 0.9rem;">' + escapeHtml(n.title) + '</strong>';
                        html += '<p class="mb-0 text-muted" style="font-size: 0.8rem;">' + escapeHtml(n.message).substring(0, 50) + '...</p>';
                        html += '<small class="text-muted">' + formatTimeAgo(n.created_at) + '</small>';
                        html += '</div></div></div>';
                    });
                    
                    list.innerHTML = html;
                });
            }
            
            function escapeHtml(text) {
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(text));
                return div.innerHTML;
            }
            
            function loadRecentNotifications() {
                fetch('/notifications/recent')
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        if (data.error) return;
                        updateNotificationBadge(data.unread_count);
                        renderNotifications(data.notifications, data.unread_count);
                    })
                    .catch(function(err) { console.log('Notification fetch error:', err); });
            }
            
            function loadChatUnreadCount() {
                fetch('/chat/unread-count')
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        if (data.error) return;
                        updateChatBadge(data.count);
                    })
                    .catch(function(err) { console.log('Chat count fetch error:', err); });
            }
            
            function pollForUpdates() {
                fetch('/notifications/poll?since=' + encodeURIComponent(lastPollTime))
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        if (data.error) return;
                        updateNotificationBadge(data.unread_count);
                        if (data.timestamp) lastPollTime = data.timestamp;
                        
                        // Show browser notification for new items
                        if (data.notifications && data.notifications.length > 0) {
                            loadRecentNotifications();
                        }
                    })
                    .catch(function(err) { console.log('Poll error:', err); });
            }
            
            // Initial load
            loadRecentNotifications();
            loadChatUnreadCount();
            
            // Poll every 30 seconds
            setInterval(function() {
                pollForUpdates();
                loadChatUnreadCount();
            }, 30000);
            
            // Expose mark all as read function
            window.markAllNotificationsRead = function() {
                fetch('/notifications/mark-all-read', { method: 'POST' })
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        if (data.success) {
                            updateNotificationBadge(0);
                            loadRecentNotifications();
                        }
                    })
                    .catch(function(err) { console.log('Mark read error:', err); });
            };
        })();
        <?php endif; ?>
        
        // Enable click-outside-to-close for all modals and hamburger menu (especially for mobile)
        document.addEventListener('DOMContentLoaded', function() {
            // All Bootstrap modals with default backdrop behavior will close on outside click
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                modal.removeAttribute('data-bs-backdrop');
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        var modalInstance = bootstrap.Modal.getInstance(modal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }
                });
            });
            
            // Close hamburger menu when clicking outside on mobile/tablet
            document.addEventListener('click', function(e) {
                var navbar = document.getElementById('navbarNav');
                var toggler = document.querySelector('.navbar-toggler');
                
                if (navbar && navbar.classList.contains('show')) {
                    // Check if click was outside the navbar and toggler
                    if (!navbar.contains(e.target) && !toggler.contains(e.target)) {
                        // Use getOrCreateInstance to ensure we have a valid instance
                        var bsCollapse = bootstrap.Collapse.getOrCreateInstance(navbar, {toggle: false});
                        bsCollapse.hide();
                    }
                }
            });
            
            // Also close hamburger when clicking on a nav link
            var navLinks = document.querySelectorAll('#navbarNav .nav-link:not(.dropdown-toggle)');
            navLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    var navbar = document.getElementById('navbarNav');
                    if (navbar && navbar.classList.contains('show')) {
                        var bsCollapse = bootstrap.Collapse.getOrCreateInstance(navbar, {toggle: false});
                        bsCollapse.hide();
                    }
                });
            });
        });
    </script>
</body>
</html>
