// ConnectWith9 Job Portal JavaScript

// Button Loader Functions
const addButtonLoader = (button) => {
    if (button.querySelector('.btn-spinner')) return;
    const spinner = document.createElement('span');
    spinner.className = 'btn-spinner';
    button.prepend(spinner);
    button.disabled = true;
};

const removeButtonLoader = (button) => {
    const spinner = button.querySelector('.btn-spinner');
    if (spinner) spinner.remove();
    button.disabled = false;
};

document.addEventListener('DOMContentLoaded', function() {
    // Add loader to form submit buttons
    document.querySelectorAll('form').forEach(form => {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            form.addEventListener('submit', function(e) {
                const isDelete = submitBtn.classList.contains('btn-danger') || submitBtn.classList.contains('btn-sm');
                if (!isDelete) {
                    addButtonLoader(submitBtn);
                }
            });
        }
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
    
    // Confirm delete actions
    const deleteForms = document.querySelectorAll('form[onsubmit*="confirm"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
    
    // Job search autocomplete (if search input exists)
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput && window.location.pathname.includes('jobs')) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length < 2) return;
            
            searchTimeout = setTimeout(() => {
                fetch(`/api/jobs/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        // You can implement autocomplete dropdown here
                        console.log('Search results:', data);
                    })
                    .catch(error => console.error('Search error:', error));
            }, 300);
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('form[action]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            // Email validation
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (field.value && !emailRegex.test(field.value)) {
                    isValid = false;
                    field.classList.add('is-invalid');
                }
            });
            
            // Password confirmation
            const password = form.querySelector('input[name="password"]');
            const confirmPassword = form.querySelector('input[name="confirm_password"]');
            
            if (password && confirmPassword && password.value !== confirmPassword.value) {
                isValid = false;
                confirmPassword.classList.add('is-invalid');
                alert('Passwords do not match');
                e.preventDefault();
                return false;
            }
        });
    });
    
    // File upload validation
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
            
            if (file.size > maxSize) {
                alert('File size must not exceed 5MB');
                e.target.value = '';
                return;
            }
            
            if (!allowedTypes.includes(file.type)) {
                alert('Only PDF, DOC, DOCX, and TXT files are allowed');
                e.target.value = '';
                return;
            }
        });
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
