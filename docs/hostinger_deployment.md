# How to Deploy ConnectWith9 Job Portal to Hostinger

This guide will walk you through deploying your ConnectWith9 job portal to Hostinger hosting.

## Prerequisites

✅ You already have:
- Hostinger hosting account
- MySQL database created on Hostinger (srv1642.hstgr.io)
- Database credentials configured

## Deployment Methods

You can choose from three methods. **Method 1 (File Manager)** is the simplest for beginners.

---

## Method 1: Using Hostinger File Manager (Recommended for Beginners)

### Step 1: Prepare Your Files

1. **Download all project files from Replit**:
   - Click the three dots (⋮) menu in Replit
   - Select "Download as zip"
   - OR download individual folders

2. **Files to include**:
   ```
   ✅ app/ (all controllers, models, views)
   ✅ config/ (database.php, env.php, constants.php)
   ✅ includes/ (functions.php, csrf.php, seo.php, email.php)
   ✅ public/ (index.php, .htaccess, assets/, uploads/)
   ✅ docs/ (optional - for documentation)
   
   ❌ .git/ (exclude)
   ❌ .replit, replit.nix (exclude - these are Replit-specific)
   ❌ scripts/ (optional - only needed for initial setup)
   ```

### Step 2: Access Hostinger File Manager

1. Log in to **hPanel** (https://hpanel.hostinger.com)
2. Click **"Websites"** in the left sidebar
3. Click **"Manage"** on your hosting plan
4. In the left sidebar under **"Files"**, click **"File Manager"**

### Step 3: Clear the public_html Directory

1. Navigate to **public_html** folder (this is your web root)
2. Delete the default files (index.html, etc.)
3. Keep public_html open - we'll upload here

### Step 4: Upload Your Application

**Option A: Upload as ZIP (Recommended)**

1. Create a ZIP file of your entire project
2. In File Manager, click **"Upload Files"** (top-right corner)
3. Select your ZIP file
4. After upload completes, right-click the ZIP file
5. Select **"Extract"**
6. Delete the ZIP file after extraction

**Option B: Upload Individual Files**

1. Click **"Upload Files"** button
2. Select files/folders from your computer
3. Wait for upload to complete
4. Note: File Manager has a 256MB per file limit

### Step 5: Organize File Structure

Your public_html should look like this:

```
public_html/
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
├── config/
│   ├── database.php
│   ├── env.php
│   └── constants.php
├── includes/
│   ├── functions.php
│   ├── csrf.php
│   ├── seo.php
│   └── email.php
├── public/
│   ├── assets/
│   ├── uploads/
│   ├── index.php
│   └── .htaccess
├── docs/
└── scripts/
```

**IMPORTANT:** Move the contents of the `public/` folder to the root of `public_html`:

```
public_html/
├── app/
├── config/
├── includes/
├── index.php          ← Moved from public/
├── .htaccess          ← Moved from public/
├── assets/            ← Moved from public/
└── uploads/           ← Moved from public/
```

### Step 6: Update Configuration Files

1. Click on **config/env.php** in File Manager
2. Click **"Edit"** button
3. Update the database settings (already correct, but verify):

```php
// Database Configuration
define('DB_HOST', 'srv1642.hstgr.io');
define('DB_NAME', 'u647904474_connect9job');
define('DB_USER', 'u647904474_connect9job');
define('DB_PASS', 'Hostinger@1234#');

// Update APP_URL to your domain
define('APP_URL', 'https://yourdomain.com');
define('APP_ENV', 'production'); // Change to production
```

4. Click **"Save"**

### Step 7: Set File Permissions

1. Select the **uploads** folder
2. Right-click → **"Permissions"**
3. Set to **755** (or check: Owner: rwx, Group: r-x, Public: r-x)
4. Click **"Apply to subdirectories"**
5. Click **"OK"**

Repeat for:
- **uploads/resumes/** → 755
- **uploads/logos/** → 755

### Step 8: Database Setup

Your database is already created! But if you need to import fresh data:

1. In hPanel, go to **"Databases"** → **"Management"**
2. Find your database: **u647904474_connect9job**
3. Click **"phpMyAdmin"**
4. Select your database from left sidebar
5. Go to **"Import"** tab
6. Click **"Choose File"** → Select `docs/sample_data.sql`
7. Click **"Import"** at the bottom
8. Wait for success message

### Step 9: Enable SSL (HTTPS)

1. In hPanel, go to **"Security"** → **"SSL"**
2. Enable **"Free SSL Certificate"**
3. Wait 5-10 minutes for SSL to activate
4. Force HTTPS by adding to .htaccess (top of file):

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Step 10: Test Your Website

1. Visit your domain: **https://yourdomain.com**
2. Test these pages:
   - Homepage: `/`
   - Browse Jobs: `/jobs`
   - Login: `/login`
   - Register: `/register`

3. Log in with:
   - **Admin**: admin@connectwith9.com / password123
   - **Employer**: employer1@example.com / password123
   - **Job Seeker**: jobseeker1@example.com / password123

---

## Method 2: Using FTP (For Larger Files)

### Step 1: Get FTP Credentials

1. In hPanel, go to **"Files"** → **"FTP Accounts"**
2. Note your **Master FTP Account** details:
   - Hostname
   - Username
   - Port: 21 (FTP) or 22 (SFTP - more secure)

### Step 2: Download FileZilla

1. Download FileZilla from: https://filezilla-project.org/
2. Install on your computer

### Step 3: Connect to Hostinger

1. Open FileZilla
2. At the top, enter:
   - **Host**: your FTP hostname
   - **Username**: your FTP username
   - **Password**: your FTP password
   - **Port**: 21 or 22
3. Click **"Quickconnect"**

### Step 4: Upload Files

1. **Left panel**: Navigate to your project folder on your computer
2. **Right panel**: Navigate to **public_html** folder on server
3. **Delete** default files in public_html
4. **Drag and drop** all your project folders from left to right
5. Wait for upload to complete (no size limit with FTP!)

### Step 5: Reorganize Files

Same as Method 1 - move contents of `public/` to root of `public_html`

---

## Method 3: Using Git (Advanced - For Updates)

### Step 1: Enable SSH Access

1. In hPanel, go to **"Advanced"** → **"SSH Access"**
2. Enable SSH
3. Set a password
4. Note your SSH details (hostname, port 22, username)

### Step 2: Connect via SSH

Use Terminal (Mac/Linux) or PuTTY (Windows):

```bash
ssh your_username@your_hostname -p 22
```

### Step 3: Navigate and Clone

```bash
# Go to web directory
cd domains/yourdomain.com/public_html

# Clone your repository (if you have one)
git clone https://github.com/yourusername/connectwith9.git .

# Or upload files via SFTP
```

---

## Post-Deployment Checklist

After deployment, verify:

- [ ] Website loads at your domain
- [ ] Homepage displays job listings
- [ ] Login page works
- [ ] Register page works
- [ ] Browse jobs shows all listings
- [ ] Admin login works
- [ ] SSL certificate is active (https://)
- [ ] No PHP errors displayed
- [ ] Images and CSS load correctly

---

## Security Checklist (Important!)

Before going live:

- [ ] Change all default user passwords
- [ ] Set `APP_ENV` to `'production'` in config/env.php
- [ ] Disable error display:
  ```php
  // Remove or set to 0 in production
  ini_set('display_errors', 0);
  error_reporting(0);
  ```
- [ ] Set proper file permissions (755 for folders, 644 for files)
- [ ] Enable SSL/HTTPS
- [ ] Keep database credentials secure
- [ ] Add reCAPTCHA keys (optional but recommended)

---

## Troubleshooting

### "Database connection failed"
**Solution**: Verify database credentials in config/env.php. For Hostinger, DB_HOST should be `localhost` if database is on the same server, or `srv1642.hstgr.io` for remote.

### Blank white page
**Solution**: 
1. Enable error display temporarily
2. Check PHP version (should be 8.0+)
3. Verify index.php is in the correct location

### 404 errors on all pages
**Solution**: 
1. Ensure .htaccess file is uploaded
2. Check Apache mod_rewrite is enabled (it should be on Hostinger)
3. Verify RewriteBase is set correctly

### Images/CSS not loading
**Solution**:
1. Check file paths in your code
2. Verify assets folder is uploaded
3. Clear browser cache

### 500 Internal Server Error
**Solution**:
1. Check .htaccess syntax
2. Verify file permissions
3. Check PHP error logs in File Manager

---

## Getting Help

- **Hostinger Support**: 24/7 live chat available in hPanel
- **Knowledge Base**: https://support.hostinger.com
- **Community Forum**: Ask questions in Hostinger community

---

## Maintenance Tips

1. **Regular Backups**: Use Hostinger's backup feature (Files → Backups)
2. **Database Backups**: Export via phpMyAdmin regularly
3. **Monitor Logs**: Check error logs in File Manager
4. **Keep PHP Updated**: Use latest stable PHP version in hPanel
5. **Security Updates**: Monitor and update your code regularly

---

## Your ConnectWith9 Job Portal is Ready! 🚀

Once deployed, your job portal will be live and accessible to users worldwide. Remember to:
- Change all default passwords
- Test all features thoroughly
- Monitor performance and errors
- Keep regular backups

Good luck with your job portal!
