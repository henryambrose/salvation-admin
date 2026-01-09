# PDF Generation Troubleshooting Guide

This guide documents the setup and troubleshooting steps for PDF generation using Spatie Laravel PDF (Browsershot/Puppeteer/Chrome) on Ubuntu servers.

## Table of Contents
- [Overview](#overview)
- [System Requirements](#system-requirements)
- [Initial Setup](#initial-setup)
- [Common Issues and Solutions](#common-issues-and-solutions)
- [Chrome Arguments Explained](#chrome-arguments-explained)
- [Verification Steps](#verification-steps)

---

## Overview

The application uses **Spatie Laravel PDF** package which relies on:
- **Browsershot** - PHP wrapper for Puppeteer
- **Puppeteer** - Node.js library for controlling Chrome/Chromium
- **Chrome/Chromium** - Headless browser for rendering PDFs

PDF generation happens in these locations:
1. `app/Modules/Members/Services/CertificateGenerationService.php` - Certificate generation
2. `app/Modules/Members/Http/Controllers/MemberController.php` - Member details PDF
3. `app/Modules/Members/Http/Controllers/BaptismRecordController.php` - Baptism certificates
4. `app/Modules/Members/Http/Controllers/DeathRecordController.php` - Death/Burial certificates
5. `app/Modules/Members/Http/Controllers/MarriageRecordController.php` - Marriage certificates

---

## System Requirements

### Server Environment
- Ubuntu 22.04 LTS (or similar)
- PHP 8.2+ with FPM
- Node.js 20.x
- NPM 10.x+

### Required Packages
```bash
# Chrome/Chromium dependencies
sudo apt install -y \
    fonts-liberation \
    libasound2 \
    libatk-bridge2.0-0 \
    libatk1.0-0 \
    libgbm1 \
    libgtk-3-0 \
    libnspr4 \
    libnss3 \
    libx11-xcb1 \
    libxcomposite1 \
    libxdamage1 \
    libxrandr2 \
    xdg-utils
```

---

## Initial Setup

### Option 1: Install Google Chrome (Recommended)

Google Chrome is more stable on Linux servers than Chromium or Puppeteer's Chrome.

```bash
# Download and install Google Chrome
cd /tmp
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
sudo apt install -y ./google-chrome-stable_current_amd64.deb

# Verify installation
google-chrome --version
which google-chrome
# Output: /usr/bin/google-chrome
```

### Option 2: Install Puppeteer's Chrome

```bash
cd /var/www/html/salvation-admin

# Create Puppeteer cache directory
sudo mkdir -p /var/www/.cache/puppeteer
sudo chown -R www-data:www-data /var/www/.cache

# Install Chrome for Puppeteer
sudo -u www-data npx puppeteer browsers install chrome

# Verify installation
sudo -u www-data npx puppeteer browsers list
```

### Create Required Directories

Chrome needs these directories when running as www-data:

```bash
# Create Chrome config directories
sudo mkdir -p /var/www/.local/share/applications
sudo mkdir -p /var/www/.config
sudo mkdir -p /tmp/chrome-user-data

# Set ownership and permissions
sudo chown -R www-data:www-data /var/www/.local
sudo chown -R www-data:www-data /var/www/.config
sudo chown -R www-data:www-data /tmp/chrome-user-data
sudo chmod -R 755 /var/www/.local
sudo chmod -R 755 /var/www/.config
sudo chmod 777 /tmp/chrome-user-data
```

### Configure Environment Variables

Add to `/var/www/html/salvation-admin/.env`:

```env
# Chrome Configuration
PUPPETEER_EXECUTABLE_PATH=/usr/bin/google-chrome
# OR if using Puppeteer's Chrome:
# PUPPETEER_EXECUTABLE_PATH=/var/www/.cache/puppeteer/chrome/linux-143.0.7499.169/chrome-linux64/chrome
```

### Clear Caches and Restart

```bash
cd /var/www/html/salvation-admin
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan optimize:clear
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx
```

---

## Common Issues and Solutions

### Issue 1: "Could not find Chrome"

**Error:**
```
Error: Could not find Chrome (ver. 143.0.7499.169)
```

**Solution:**
1. Install Chrome/Chromium (see Initial Setup)
2. Set `PUPPETEER_EXECUTABLE_PATH` in `.env`
3. Verify the path exists: `ls -la /usr/bin/google-chrome`

---

### Issue 2: "chrome_crashpad_handler: --database is required"

**Error:**
```
chrome_crashpad_handler: --database is required
recvmsg: Connection reset by peer (104)
```

**Solution:**
Add these Chrome arguments (already configured in the codebase):
```php
'--disable-crash-reporter',
'--disable-breakpad',
'--user-data-dir=/tmp/chrome-user-data',
'--crash-dumps-dir=/tmp/chrome-user-data'
```

Make sure `/tmp/chrome-user-data` exists with proper permissions.

---

### Issue 3: "Permission denied" - Cannot create directories

**Error:**
```
mkdir: cannot create directory '/var/www/.local': Permission denied
touch: cannot touch '/var/www/.local/share/applications/mimeapps.list'
```

**Solution:**
```bash
# Create directories with proper ownership
sudo mkdir -p /var/www/.local/share/applications
sudo mkdir -p /var/www/.config
sudo chown -R www-data:www-data /var/www/.local
sudo chown -R www-data:www-data /var/www/.config
sudo chmod -R 755 /var/www/.local
sudo chmod -R 755 /var/www/.config
```

---

### Issue 4: Snap Chromium Issues

**Error:**
```
update.go:85: cannot change mount namespace...
```

**Solution:**
Snap packages have sandboxing issues. Uninstall and use Google Chrome instead:

```bash
# Remove snap chromium
sudo snap remove chromium

# Install Google Chrome (see Initial Setup)
```

---

### Issue 5: Git Safe Directory Warning

**Error:**
```
fatal: detected dubious ownership in repository
```

**Solution:**
```bash
sudo git config --global --add safe.directory /var/www/html/salvation-admin
```

---

## Chrome Arguments Explained

The application uses these Chrome arguments for server environments:

```php
[
    '--disable-setuid-sandbox',    // Disable setuid sandbox (needed for www-data user)
    '--disable-dev-shm-usage',     // Overcome limited shared memory
    '--disable-gpu',               // Disable GPU hardware acceleration
    '--no-sandbox',                // Disable Chrome sandbox (needed for containers/servers)
    '--disable-crash-reporter',    // Disable crash reporting system
    '--disable-breakpad',          // Disable breakpad crash handler
    '--disable-extensions',        // Disable Chrome extensions
    '--disable-sync',              // Disable Google sync
    '--no-first-run',              // Skip first run wizards
    '--no-zygote',                 // Disable zygote process (reduces process management overhead)
    '--single-process',            // Run in single process mode (more stable on servers)
    '--disable-background-networking',  // Disable background network requests
    '--disable-default-apps',      // Disable default apps
    '--mute-audio',                // Mute audio output
    '--user-data-dir=/tmp/chrome-user-data',      // Set user data directory
    '--crash-dumps-dir=/tmp/chrome-user-data'     // Set crash dumps directory
]
```

### Why These Arguments?

- **Security/Sandbox:** `--no-sandbox`, `--disable-setuid-sandbox` - Chrome's sandbox doesn't work well when running as www-data user
- **Performance:** `--single-process`, `--no-zygote` - Reduces overhead, more stable on servers
- **Resource Usage:** `--disable-dev-shm-usage`, `--disable-gpu` - Prevents memory/GPU issues
- **Crashpad Fix:** `--disable-crash-reporter`, `--disable-breakpad`, `--crash-dumps-dir` - Prevents crashpad handler errors
- **Permissions Fix:** `--user-data-dir` - Specifies writable location for Chrome config files

---

## Verification Steps

### 1. Verify Chrome Installation

```bash
# For Google Chrome
google-chrome --version
which google-chrome

# For Puppeteer's Chrome
sudo -u www-data npx puppeteer browsers list
```

### 2. Verify Directory Permissions

```bash
# Check directories exist with correct permissions
ls -la /var/www/.local
ls -la /var/www/.config
ls -la /tmp/chrome-user-data

# All should be owned by www-data:www-data
```

### 3. Verify Code Configuration

```bash
# Check if user-data-dir argument is present
grep "user-data-dir" app/Modules/Members/Services/CertificateGenerationService.php

# Should output:
# '--user-data-dir=/tmp/chrome-user-data',
```

### 4. Check Environment Variables

```bash
# Verify PUPPETEER_EXECUTABLE_PATH is set
grep PUPPETEER_EXECUTABLE_PATH .env

# Should output path to Chrome executable
```

### 5. Test PDF Generation

1. Log into the application
2. Navigate to Certificates → Generate Certificate
3. Try generating a PDF
4. If error occurs, check logs:

```bash
# Laravel logs
tail -f /var/www/html/salvation-admin/storage/logs/laravel.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.4-fpm.log
```

---

## Deployment Checklist

When deploying to a new server or after major updates:

- [ ] Install Google Chrome or Chromium with dependencies
- [ ] Create required directories (`/var/www/.local`, `/var/www/.config`, `/tmp/chrome-user-data`)
- [ ] Set proper ownership (www-data:www-data) and permissions (755/777)
- [ ] Configure `PUPPETEER_EXECUTABLE_PATH` in `.env`
- [ ] Clear all Laravel caches (`php artisan optimize:clear`)
- [ ] Restart PHP-FPM (`systemctl restart php8.4-fpm`)
- [ ] Test PDF generation from UI
- [ ] Check logs for any errors

---

## Advanced Troubleshooting

### Enable Debug Mode

To get more detailed error information:

```bash
# Edit .env
APP_DEBUG=true
LOG_LEVEL=debug

# Clear config cache
sudo -u www-data php artisan config:clear

# Watch logs in real-time
tail -f storage/logs/laravel.log
```

### Test Chrome Directly

Test if Chrome can run as www-data:

```bash
# Try running Chrome as www-data user
sudo -u www-data /usr/bin/google-chrome \
  --headless \
  --disable-gpu \
  --no-sandbox \
  --dump-dom \
  --user-data-dir=/tmp/chrome-user-data \
  https://example.com

# Should output HTML content if working
```

### Check Process Limits

Ensure www-data user can create processes:

```bash
# Check current limits
sudo -u www-data bash -c 'ulimit -a'

# If limits are too low, edit /etc/security/limits.conf
# Add:
www-data soft nofile 65536
www-data hard nofile 65536
```

---

## Related Documentation

- [Spatie Laravel PDF Documentation](https://spatie.be/docs/laravel-pdf)
- [Puppeteer Troubleshooting](https://pptr.dev/troubleshooting)
- [Chrome Headless Documentation](https://developers.google.com/web/updates/2017/04/headless-chrome)
- [Browsershot Documentation](https://github.com/spatie/browsershot)

---

## Change Log

### 2026-01-09
- Initial setup and troubleshooting guide created
- Documented Chrome installation and configuration
- Added comprehensive Chrome arguments with explanations
- Documented all common errors and solutions

---

## Support

If you encounter issues not covered in this guide:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Nginx logs: `/var/log/nginx/error.log`
3. Check PHP-FPM logs: `/var/log/php8.4-fpm.log`
4. Verify Chrome can run: `sudo -u www-data /usr/bin/google-chrome --version`
5. Test with debug mode enabled

For persistent issues, verify all directories exist with correct permissions and all Chrome arguments are properly configured in the code.
