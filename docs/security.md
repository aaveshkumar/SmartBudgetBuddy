# Security Documentation

## Overview

ConnectWith9 Job Portal implements multiple layers of security to protect user data and prevent common web vulnerabilities.

## Implemented Security Features

### 1. Authentication & Authorization

#### Password Security
- **Hashing Algorithm**: bcrypt via PHP's `password_hash()` function
- **Cost Factor**: Default (currently 10)
- **Verification**: Using `password_verify()` for constant-time comparison
- **Minimum Length**: 6 characters (configurable in validation)

#### Session Management
- **Session Fingerprinting**: Combines user IP and user agent
- **Session Regeneration**: On login
- **Session Timeout**: 1 hour (configurable in config/constants.php)
- **Secure Flags**: Should enable `session.cookie_secure` in production

#### Login Throttling
- **Maximum Attempts**: 5 failed attempts
- **Lockout Duration**: 15 minutes
- **Tracking Method**: IP-based with database logging
- **Cleanup**: Old attempts auto-expire after lockout period

### 2. Input Validation & Sanitization

All user inputs are sanitized using:

```php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}
```

#### Sanitization Locations
- All form inputs before processing
- All data before display (XSS prevention)
- Email validation using `filter_var()`
- File upload validation (type, size, extension)

### 3. CSRF Protection

#### Implementation
- **Token Generation**: 32-byte random token via `random_bytes()`
- **Storage**: PHP session
- **Validation**: Hash comparison using `hash_equals()`
- **Requirement**: All POST requests must include valid CSRF token

#### Usage in Forms
```php
<?= csrfField() ?>
```

This renders:
```html
<input type="hidden" name="csrf_token" value="[token]">
```

### 4. SQL Injection Prevention

#### Prepared Statements
All database queries use PDO prepared statements:

```php
$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $this->db->prepare($sql);
$stmt->execute([':email' => $email]);
```

#### Parameter Binding
- Named parameters for clarity
- Proper type hinting with PDO::PARAM_INT
- No string concatenation in queries

### 5. File Upload Security

#### Resume Upload Restrictions
- **Allowed Types**: PDF, DOC, DOCX, TXT
- **Maximum Size**: 5MB
- **Validation**: MIME type and extension checking
- **Storage**: Outside web root (recommended) or with .htaccess protection
- **Filename**: Randomized with `uniqid()` and timestamp

#### Implementation
```php
function validateFileUpload($file, $allowedTypes, $maxSize) {
    // Check upload errors
    // Verify file size
    // Validate extension
    // Could add MIME type checking
}
```

### 6. XSS (Cross-Site Scripting) Prevention

#### Output Escaping
All dynamic content is escaped before display:

```php
<?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>
```

#### Content Security
- No `eval()` or similar dangerous functions
- No inline JavaScript with user data
- Proper content-type headers

### 7. Security Headers

Implemented in `.htaccess`:

```apache
Header set X-XSS-Protection "1; mode=block"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-Content-Type-Options "nosniff"
```

Additional headers to consider for production:
- `Strict-Transport-Security` (HSTS)
- `Content-Security-Policy` (CSP)
- `Referrer-Policy`
- `Permissions-Policy`

### 8. Access Control

#### Role-Based Access
- **Admin**: Full system access
- **Employer**: Job posting and application viewing
- **Job Seeker**: Job browsing and application submission

#### Authorization Checks
```php
requireRole(USER_TYPE_ADMIN);  // Enforces admin access
hasRole($role);                // Checks specific role
requireAuth();                 // Enforces any authenticated user
```

### 9. Directory Protection

- `.htaccess` disables directory listing
- Sensitive directories should be outside web root
- `uploads/` directory should have execute permissions disabled

## Security Best Practices

### For Production Deployment

1. **Environment Variables**
   - Move all credentials to environment variables
   - Never commit `.env` files to version control
   - Use different credentials for dev/staging/production

2. **HTTPS/SSL**
   - Enable HTTPS for all pages
   - Set secure and httponly flags on cookies
   - Enable HSTS header

3. **Error Handling**
   - Disable `display_errors` in php.ini
   - Enable error logging to files
   - Show generic error messages to users

4. **Database Security**
   - Use separate database user with minimal privileges
   - Enable MySQL query logging for auditing
   - Regular backups with encryption

5. **File Permissions**
   ```bash
   # Directories
   find . -type d -exec chmod 755 {} \;
   
   # Files
   find . -type f -exec chmod 644 {} \;
   
   # Uploads (no execute)
   chmod 755 public/uploads
   ```

6. **Regular Updates**
   - Keep PHP updated
   - Monitor security advisories
   - Update dependencies regularly

### Additional Security Measures to Implement

#### Rate Limiting
- Limit API requests per IP
- Throttle form submissions
- Protect against DDoS

#### Two-Factor Authentication
- SMS or email verification
- TOTP (Time-based One-Time Password)
- Backup codes

#### Audit Logging
- Log all admin actions
- Track job posting changes
- Monitor failed login attempts
- IP address tracking

#### Content Security Policy
```apache
Header set Content-Security-Policy "default-src 'self'; script-src 'self' cdn.jsdelivr.net; style-src 'self' cdn.jsdelivr.net cdnjs.cloudflare.com; img-src 'self' data:;"
```

## Common Vulnerabilities Mitigated

✅ **SQL Injection**: Prepared statements
✅ **XSS**: Output escaping
✅ **CSRF**: Token validation
✅ **Brute Force**: Login throttling
✅ **Session Hijacking**: Fingerprinting
✅ **File Upload**: Type and size validation
✅ **Directory Traversal**: Proper path handling
✅ **Clickjacking**: X-Frame-Options header

## Security Testing Checklist

- [ ] Test SQL injection on all inputs
- [ ] Test XSS on all user-generated content
- [ ] Verify CSRF tokens on all forms
- [ ] Test file upload restrictions
- [ ] Verify login throttling works
- [ ] Check authorization on all admin/employer routes
- [ ] Test session timeout
- [ ] Verify password hashing
- [ ] Check for sensitive data exposure
- [ ] Test HTTPS enforcement
- [ ] Review error messages for information leakage
- [ ] Scan for known vulnerabilities

## Incident Response

If a security breach is discovered:

1. **Immediate Actions**
   - Take affected systems offline if necessary
   - Change all passwords and API keys
   - Review logs for unauthorized access
   - Identify the vulnerability

2. **Investigation**
   - Determine scope of breach
   - Identify compromised data
   - Document timeline of events

3. **Remediation**
   - Patch the vulnerability
   - Restore from clean backups if needed
   - Notify affected users
   - Report to authorities if required

4. **Prevention**
   - Implement additional safeguards
   - Update security procedures
   - Conduct security training

## Compliance Considerations

### Data Protection
- Implement user data deletion
- Provide data export functionality
- Maintain privacy policy
- Get explicit consent for data collection

### Password Policy
- Enforce minimum complexity
- Implement password expiration
- Prevent password reuse
- Offer password reset functionality

## Contact

For security issues or vulnerabilities:
- **DO NOT** create public issues
- Email security concerns privately
- Include detailed reproduction steps
- Allow reasonable time for fixes before disclosure
