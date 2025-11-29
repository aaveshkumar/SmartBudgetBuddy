# ConnectWith9 Job Portal - Setup Guide

## Environment Setup

### Database Configuration

The application uses a remote MySQL database hosted on Hostinger. For security, you should set database credentials as environment variables:

#### Option 1: Using Environment Variables (Recommended for Production)

Set the following environment variables:

```bash
export DB_HOST="srv1642.hstgr.io"
export DB_NAME="u647904474_connect9job"
export DB_USER="u647904474_connect9job"
export DB_PASS="Hostinger@1234#"
```

#### Option 2: Default Configuration (Development)

The application will use the default credentials specified in `config/env.php` if environment variables are not set. 

**IMPORTANT**: Before deploying to production:
1. Remove the default credential values from `config/env.php`
2. Set all credentials as environment variables
3. Never commit credentials to version control

### Database Tables Setup

Run the setup script to create all necessary tables and insert sample data:

```bash
php scripts/setup_database.php
```

This will create:
- Users table
- Categories table
- Technologies table
- Education levels table
- Jobs table
- Applications table
- Saved jobs table
- Blog table
- SEO logs table
- Login attempts table (for security)

### IP Whitelisting

If you encounter "Access denied" errors when connecting to the remote database, you may need to whitelist your IP address:

1. Log in to your Hostinger control panel
2. Navigate to MySQL Databases → Remote MySQL
3. Add your server's IP address to the allowed list

### Default User Accounts

After running the setup script, you can log in with these accounts:

**Admin Account:**
- Email: admin@connectwith9.com
- Password: password123

**Employer Account:**
- Email: employer1@example.com
- Password: password123

**Job Seeker Account:**
- Email: jobseeker1@example.com
- Password: password123

**IMPORTANT**: Change these passwords immediately in production!

## Running the Application

Start the PHP built-in development server:

```bash
php -S 0.0.0.0:5000 -t public
```

Access the application at: http://localhost:5000

## File Permissions

Ensure the uploads directory is writable:

```bash
chmod -R 755 public/uploads
chmod -R 755 logs
```

## Optional Features Setup

### reCAPTCHA (Spam Prevention)

1. Get reCAPTCHA keys from https://www.google.com/recaptcha/admin
2. Set environment variables:

```bash
export RECAPTCHA_SITE_KEY="your_site_key_here"
export RECAPTCHA_SECRET_KEY="your_secret_key_here"
```

### Email Notifications

To enable email notifications (job applications, approvals, etc.):

1. Set up SMTP credentials:

```bash
export SMTP_HOST="smtp.gmail.com"
export SMTP_USER="your-email@gmail.com"
export SMTP_PASS="your-app-password"
export SMTP_PORT="587"
```

2. For Gmail, you'll need to create an "App Password":
   - Go to Google Account settings
   - Security → 2-Step Verification
   - App passwords → Generate new password

## Security Checklist

Before going to production:

- [ ] Remove default credentials from config/env.php
- [ ] Set all sensitive values as environment variables
- [ ] Change all default user passwords
- [ ] Enable HTTPS
- [ ] Set APP_ENV to 'production'
- [ ] Enable reCAPTCHA
- [ ] Configure email notifications
- [ ] Set up regular database backups
- [ ] Review and update .htaccess security headers
- [ ] Test file upload restrictions
- [ ] Review and test CSRF protection
- [ ] Test login throttling mechanism

## Troubleshooting

### Database Connection Failed

**Error**: "Database connection failed. Please try again later."

**Solutions**:
1. Verify database credentials are correct
2. Check if your IP is whitelisted on Hostinger
3. Ensure the database server is accessible
4. Verify network connectivity

### File Upload Errors

**Error**: "Failed to upload resume"

**Solutions**:
1. Check `public/uploads/resumes/` directory exists and is writable
2. Verify PHP upload settings in php.ini:
   - `upload_max_filesize = 5M`
   - `post_max_size = 8M`
3. Ensure file types match allowed types (PDF, DOC, DOCX, TXT)

### Clean URLs Not Working

**Error**: 404 errors on all pages except homepage

**Solutions**:
1. Ensure Apache mod_rewrite is enabled
2. Verify .htaccess files are being read
3. Check Apache configuration allows .htaccess overrides
4. Restart Apache after changes

## Production Deployment

### Recommended Hosting Requirements

- PHP 7.4 or higher (8.2 recommended)
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- HTTPS support
- Minimum 512MB RAM
- 1GB disk space

### Deployment Steps

1. Upload all files except `.git/` directory
2. Set environment variables on your hosting platform
3. Run database setup script
4. Set proper file permissions
5. Configure SSL certificate
6. Test all functionality
7. Change all default passwords
8. Enable error logging (disable display_errors in production)

## Support

For issues or questions:
- Check the main README.md documentation
- Review error logs in `logs/` directory
- Ensure all dependencies are properly installed
