# AWS EC2 Setup Guide for Salvation Admin

This guide will help you set up an AWS EC2 instance for the Salvation Admin Laravel application.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Step 1: Launch EC2 Instance](#step-1-launch-ec2-instance)
- [Step 2: Connect to EC2](#step-2-connect-to-ec2)
- [Step 3: Install Dependencies](#step-3-install-dependencies)
- [Step 4: Install MySQL](#step-4-install-mysql)
- [Step 5: Install and Configure Nginx](#step-5-install-and-configure-nginx)
- [Step 6: Deploy Application](#step-6-deploy-application)
- [Step 7: Configure S3 for File Storage](#step-7-configure-s3-for-file-storage)
- [Step 8: Install Chromium for PDF Generation](#step-8-install-chromium-for-pdf-generation)
- [Step 9: Configure Queue Workers](#step-9-configure-queue-workers)
- [Step 10: SSL Certificate (Optional)](#step-10-ssl-certificate-optional)
- [Step 11: Configure Firewall](#step-11-configure-firewall)
- [Troubleshooting](#troubleshooting)

---

## Prerequisites

- AWS Account
- Domain name (optional but recommended)
- SSH key pair for EC2 access
- Basic knowledge of Linux commands

---

## Step 1: Launch EC2 Instance

### 1.1 Create EC2 Instance

1. Go to **AWS Console** → **EC2** → **Launch Instance**
2. **Name**: `salvation-admin-server`
3. **AMI**: Ubuntu Server 22.04 LTS (HVM), SSD Volume Type
4. **Instance Type**: `t2.medium` or `t3.medium` (minimum recommended)
   - 2 vCPUs, 4GB RAM
   - For production, consider `t3.large` or higher
5. **Key Pair**: Create or select an existing key pair
   - Download and save the `.pem` file securely

### 1.2 Configure Storage

- **Root Volume**: 30 GB gp3 (minimum)
- For production, consider 50-100 GB

### 1.3 Configure Security Group

Create a security group with these rules:

| Type | Protocol | Port | Source | Description |
|------|----------|------|--------|-------------|
| SSH | TCP | 22 | My IP | SSH access |
| HTTP | TCP | 80 | 0.0.0.0/0 | Web traffic |
| HTTPS | TCP | 443 | 0.0.0.0/0 | Secure web traffic |
| MySQL | TCP | 3306 | Security Group ID | Database (optional, for RDS) |

### 1.4 Launch Instance

Click **Launch Instance** and wait for it to start.

### 1.5 Allocate Elastic IP (Recommended)

1. Go to **EC2** → **Elastic IPs**
2. Click **Allocate Elastic IP address**
3. Associate it with your instance

---

## Step 2: Connect to EC2

### 2.1 Set Key Permissions

```bash
# On your local machine
chmod 400 /path/to/your-key.pem
```

### 2.2 SSH into Instance

```bash
ssh -i /path/to/your-key.pem ubuntu@<your-elastic-ip>
```

### 2.3 Update System

```bash
sudo apt update
sudo apt upgrade -y
```

---

## Step 3: Install Dependencies

### 3.1 Install PHP 8.4

```bash
# Install software-properties-common first
sudo apt install -y software-properties-common

# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y

# Update package list
sudo apt update

# Install PHP 8.4 and required extensions
sudo apt install -y php8.4-fpm php8.4-cli php8.4-common \
  php8.4-mysql php8.4-zip php8.4-gd php8.4-mbstring \
  php8.4-curl php8.4-xml php8.4-bcmath php8.4-intl \
  php8.4-redis php8.4-soap php8.4-imagick

# Verify installation
php -v

# Check PHP-FPM status
sudo systemctl status php8.4-fpm
```

**Note:** If you need PHP 8.2 instead (e.g., for compatibility), replace all instances of `8.4` with `8.2` above, and see the **Composer Lock File Issue** section in troubleshooting.

### 3.2 Install Composer

```bash
# Download and install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verify installation
composer --version
```

### 3.3 Install Node.js and NPM

```bash
# Remove old Node.js version if exists
sudo apt remove -y nodejs npm

# Download and install Node.js 20.x (LTS) from NodeSource
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -

# Install Node.js (includes npm)
sudo apt install -y nodejs

# Verify installation
node -v    # Should show v20.x.x
npm -v     # Should show 10.x.x or higher

# Install build tools (optional, for native modules)
sudo apt install -y build-essential
```

### 3.4 Install Git

```bash
sudo apt install -y git
git --version
```

---

## Step 4: Install MySQL

### 4.1 Install MySQL Server

```bash
sudo apt install -y mysql-server

# Secure MySQL installation
sudo mysql_secure_installation
```

Answer the prompts:
- Set root password: **Yes** (choose a strong password)
- Remove anonymous users: **Yes**
- Disallow root login remotely: **Yes**
- Remove test database: **Yes**
- Reload privilege tables: **Yes**

### 4.2 Create Database and User

```bash
sudo mysql -u root -p
```

In MySQL console:

```sql
-- Create database
CREATE DATABASE salvation_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'salvation_user'@'localhost' IDENTIFIED BY 'your_strong_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON salvation_admin.* TO 'salvation_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Exit
EXIT;
```

### 4.3 Configure MySQL (Optional)

Edit MySQL configuration:

```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Recommended settings:

```ini
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
```

Restart MySQL:

```bash
sudo systemctl restart mysql
```

---

## Step 5: Install and Configure Nginx

### 5.1 Install Nginx

```bash
sudo apt install -y nginx

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### 5.2 Configure Nginx for Laravel

Create site configuration:

```bash
sudo nano /etc/nginx/sites-available/salvation-admin
```

Add this configuration:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/html/salvation-admin/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Increase upload size for certificate files
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5.3 Enable Site

```bash
# Create symlink
sudo ln -s /etc/nginx/sites-available/salvation-admin /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

---

## Step 6: Deploy Application

### 6.1 Create Application Directory

```bash
sudo mkdir -p /var/www/html
cd /var/www/html
```

### 6.2 Clone Repository

#### Option A: Using SSH Key (Recommended)

```bash
# Generate SSH key on server
ssh-keygen -t ed25519 -C "your-email@example.com"
# Press Enter to accept default location
# Press Enter twice for no passphrase (or set a passphrase)

# Display the public key
cat ~/.ssh/id_ed25519.pub

# Copy the output and add it to GitHub:
# 1. Go to GitHub → Settings → SSH and GPG keys
# 2. Click "New SSH key"
# 3. Paste the key and save

# Clone repository using SSH
cd /var/www/html
sudo git clone git@github.com:henryambrose/salvation-admin.git
cd salvation-admin
```

#### Option B: Using Personal Access Token

```bash
# Create a Personal Access Token on GitHub:
# 1. Go to GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
# 2. Click "Generate new token (classic)"
# 3. Give it a name (e.g., "EC2 Server")
# 4. Select scopes: repo (full control)
# 5. Click "Generate token"
# 6. Copy the token (shown only once!)

# Clone repository using HTTPS with token
cd /var/www/html
sudo git clone https://henryambrose:<YOUR_TOKEN>@github.com/henryambrose/salvation-admin.git
cd salvation-admin

# Or use Git credential helper to store token
git config --global credential.helper store
git clone https://github.com/henryambrose/salvation-admin.git
# Enter username: henryambrose
# Enter password: <YOUR_TOKEN>
```

#### Option C: Upload Files via SCP/SFTP

If you prefer not to use Git on the server, upload files directly:

```bash
# On your local machine:
scp -i your-key.pem -r /path/to/salvation-admin ubuntu@your-server-ip:/tmp/
# Then on server:
sudo mv /tmp/salvation-admin /var/www/html/
```

### 6.3 Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/html/salvation-admin

# Set directory permissions
sudo find /var/www/html/salvation-admin -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/html/salvation-admin -type f -exec chmod 644 {} \;

# Make storage and bootstrap/cache writable
sudo chmod -R 775 /var/www/html/salvation-admin/storage
sudo chmod -R 775 /var/www/html/salvation-admin/bootstrap/cache
```

### 6.4 Install Dependencies

```bash
cd /var/www/html/salvation-admin

# Install PHP dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader

# Temporarily change ownership for npm
sudo chown -R ubuntu:ubuntu /var/www/html/salvation-admin

# Install Node dependencies and build assets
npm install
npm run build

# Restore ownership to www-data
sudo chown -R www-data:www-data /var/www/html/salvation-admin

# Ensure storage directories are writable
sudo chmod -R 775 storage bootstrap/cache
```

### 6.5 Configure Environment

```bash
# Copy .env file
sudo cp .env.example .env
sudo nano .env
```

Update these values in `.env`:

```env
APP_NAME=Salvation
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=salvation_admin
DB_USERNAME=salvation_user
DB_PASSWORD=your_strong_password

# S3 Configuration (see Step 7)
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name

# Chrome Configuration
CHROME_PATH=/usr/bin/chromium-browser
CHROME_NO_SANDBOX=true

# Queue Configuration
QUEUE_CONNECTION=database

# Cache Configuration
CACHE_STORE=database
SESSION_DRIVER=database
```

### 6.6 Generate Application Key

```bash
sudo -u www-data php artisan key:generate
```

### 6.7 Run Migrations

```bash
# Run migrations
sudo -u www-data php artisan migrate --force

# Run seeders
sudo -u www-data php artisan db:seed --force
```

### 6.8 Create Storage Link

```bash
sudo -u www-data php artisan storage:link
```

### 6.9 Optimize Application

```bash
cd /var/www/html/salvation-admin

# Cache configuration for better performance
sudo -u www-data php artisan optimize
```

---

## Step 7: Configure S3 for File Storage

### 7.1 Create S3 Bucket

1. Go to **AWS Console** → **S3**
2. Click **Create bucket**
3. **Bucket name**: `salvation-admin-files` (must be globally unique)
4. **Region**: Select your preferred region
5. **Block Public Access**: Keep all enabled (for security)
6. Click **Create bucket**

### 7.2 Create IAM User for S3 Access

1. Go to **AWS Console** → **IAM** → **Users**
2. Click **Add users**
3. **User name**: `salvation-admin-s3`
4. **Access type**: Programmatic access
5. **Permissions**: Attach policy directly
6. Select **AmazonS3FullAccess** (or create custom policy)
7. Click **Create user**
8. **Save Access Key ID and Secret Access Key** (shown only once!)

### 7.3 Update .env File

```bash
sudo nano /var/www/html/salvation-admin/.env
```

Add S3 credentials:

```env
AWS_ACCESS_KEY_ID=your_access_key_id
AWS_SECRET_ACCESS_KEY=your_secret_access_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=salvation-admin-files
```

### 7.4 Clear Config Cache

```bash
cd /var/www/html/salvation-admin
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan config:cache
```

---

## Step 8: Install Chromium for PDF Generation

### 8.1 Install Chromium Browser

```bash
sudo apt update
sudo apt install -y chromium-browser
```

### 8.2 Install Puppeteer Dependencies

```bash
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

### 8.3 Install Chrome for Puppeteer (Alternative)

```bash
cd /var/www/html/salvation-admin

# Create Puppeteer cache directory
sudo mkdir -p /var/www/.cache/puppeteer
sudo chown -R www-data:www-data /var/www/.cache

# Install Chrome as www-data user
sudo -u www-data npx puppeteer browsers install chrome
```

### 8.4 Verify Installation

```bash
chromium-browser --version
# or
sudo -u www-data npx puppeteer browsers list
```

---

## Step 9: Configure Queue Workers

### 9.1 Create Supervisor Configuration

```bash
sudo apt install -y supervisor
```

Create worker configuration:

```bash
sudo nano /etc/supervisor/conf.d/salvation-admin-worker.conf
```

Add this configuration:

```ini
[program:salvation-admin-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/salvation-admin/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/salvation-admin/storage/logs/worker.log
stopwaitsecs=3600
```

### 9.2 Start Queue Workers

```bash
# Reread supervisor configuration
sudo supervisorctl reread

# Update supervisor
sudo supervisorctl update

# Start workers
sudo supervisorctl start salvation-admin-worker:*

# Check status
sudo supervisorctl status
```

### 9.3 Manage Workers

```bash
# Restart workers
sudo supervisorctl restart salvation-admin-worker:*

# Stop workers
sudo supervisorctl stop salvation-admin-worker:*

# View logs
sudo tail -f /var/www/html/salvation-admin/storage/logs/worker.log
```

---

## Step 10: SSL Certificate with Let's Encrypt

### Prerequisites

Before setting up SSL, ensure:
1. ✅ Your domain is pointing to your EC2 Elastic IP
2. ✅ DNS records are propagated (check with `nslookup your-domain.com`)
3. ✅ Firewall allows ports 80 and 443
4. ✅ Nginx is running and serving your application

### 10.1 Install Certbot

```bash
# Install Certbot and Nginx plugin
sudo apt update
sudo apt install -y certbot python3-certbot-nginx

# Verify installation
certbot --version
```

### 10.2 Verify Nginx Configuration

Before running Certbot, ensure your Nginx config has the correct server_name:

```bash
# Check your Nginx configuration
sudo nano /etc/nginx/sites-available/salvation-admin
```

Make sure it has:
```nginx
server_name your-domain.com www.your-domain.com;
```

Test Nginx configuration:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

### 10.3 Obtain SSL Certificate

```bash
# For a single domain
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Example with actual domain:
# sudo certbot --nginx -d salvationadmin.com -d www.salvationadmin.com
```

**Follow the prompts:**
1. **Email address**: Enter your email (for renewal notifications)
2. **Terms of Service**: Type 'Y' to agree
3. **Share email with EFF**: Type 'Y' or 'N' (optional)
4. **Redirect HTTP to HTTPS**: Select option 2 (Recommended)

**What Certbot does automatically:**
- Creates SSL certificates in `/etc/letsencrypt/live/your-domain.com/`
- Modifies your Nginx configuration to use SSL
- Sets up automatic certificate renewal
- Configures HTTP to HTTPS redirect (if selected)

### 10.4 Verify SSL Certificate

```bash
# Check certificate details
sudo certbot certificates

# View Nginx configuration (should now include SSL)
sudo cat /etc/nginx/sites-available/salvation-admin | grep ssl

# Test in browser
# Visit: https://your-domain.com
```

### 10.5 Test Auto-Renewal

Certbot automatically sets up renewal via systemd timer. Test it:

```bash
# Dry run renewal (doesn't actually renew)
sudo certbot renew --dry-run

# Check renewal timer status
sudo systemctl status certbot.timer

# Check when certificates expire
sudo certbot certificates
```

**Renewal happens automatically:**
- Certificates are valid for 90 days
- Certbot checks twice daily for expiring certificates
- Automatically renews certificates within 30 days of expiration

### 10.6 Manual Renewal (if needed)

```bash
# Manually renew all certificates
sudo certbot renew

# Renew and reload Nginx
sudo certbot renew --nginx

# Force renewal (even if not expiring)
sudo certbot renew --force-renewal
```

### 10.7 Troubleshooting SSL Issues

**Issue: Domain validation fails**
```bash
# Check DNS records
nslookup your-domain.com
dig your-domain.com

# Make sure port 80 is accessible
curl -I http://your-domain.com

# Check Nginx is running
sudo systemctl status nginx
```

**Issue: Certificate not working**
```bash
# Check certificate files exist
sudo ls -la /etc/letsencrypt/live/your-domain.com/

# Check Nginx SSL configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

**Issue: Renewal fails**
```bash
# Check renewal logs
sudo cat /var/log/letsencrypt/letsencrypt.log

# Manually renew with verbose output
sudo certbot renew --dry-run -v
```

### 10.8 SSL Best Practices

After setting up SSL, add these security headers to Nginx:

```bash
sudo nano /etc/nginx/sites-available/salvation-admin
```

Add inside the `server` block (after the SSL lines Certbot added):

```nginx
# SSL Security Headers
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

Test and reload:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

### 10.9 Update Laravel .env File

After SSL is working, update your `.env`:

```bash
sudo nano /var/www/html/salvation-admin/.env
```

Change:
```env
APP_URL=https://your-domain.com
SESSION_SECURE_COOKIE=true
```

Clear config cache:
```bash
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan config:cache
```

### 10.10 Test SSL Configuration

Test your SSL setup:
- **SSL Labs**: https://www.ssllabs.com/ssltest/analyze.html?d=your-domain.com
- **Security Headers**: https://securityheaders.com/?q=your-domain.com

**Expected SSL Labs Grade**: A or A+

---

## Step 11: Configure Firewall

### 11.1 Enable UFW Firewall

```bash
# Allow OpenSSH
sudo ufw allow OpenSSH

# Allow HTTP and HTTPS
sudo ufw allow 'Nginx Full'

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

---

## Post-Installation Tasks

### 1. Set Up Scheduled Tasks (Cron)

```bash
sudo crontab -e -u www-data
```

Add this line:

```cron
* * * * * cd /var/www/html/salvation-admin && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Configure Log Rotation

```bash
sudo nano /etc/logrotate.d/salvation-admin
```

Add:

```
/var/www/html/salvation-admin/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    sharedscripts
}
```

### 3. Set Up Monitoring

Consider installing:
- **New Relic** for application monitoring
- **CloudWatch** for AWS resource monitoring
- **Sentry** for error tracking

### 4. Configure Backups

#### Prerequisites: Install and Configure AWS CLI

**Install AWS CLI:**

```bash
# Download and install AWS CLI
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
sudo apt-get update && sudo apt-get install unzip -y
unzip awscliv2.zip
sudo ./aws/install
aws --version
rm -rf aws awscliv2.zip
```

**Configure IAM Role (Recommended for EC2):**

1. **Create IAM Role in AWS Console:**
   - Go to **IAM** → **Roles** → **Create role**
   - Select **AWS service** → **EC2**
   - Add permission: **AmazonS3FullAccess** (or custom policy below)
   - Role name: `salvation-ec2-s3-backup-role`
   - Create role

2. **Custom Policy (More Secure) - Optional:**
   ```json
   {
     "Version": "2012-10-17",
     "Statement": [
       {
         "Effect": "Allow",
         "Action": [
           "s3:PutObject",
           "s3:GetObject",
           "s3:ListBucket",
           "s3:DeleteObject"
         ],
         "Resource": [
           "arn:aws:s3:::salvation-files",
           "arn:aws:s3:::salvation-files/*"
         ]
       }
     ]
   }
   ```

3. **Attach Role to EC2 Instance:**
   - Go to **EC2** → **Instances** → Select your instance
   - **Actions** → **Security** → **Modify IAM role**
   - Select `salvation-ec2-s3-backup-role`
   - Click **Update IAM role**

4. **Verify:**
   ```bash
   aws sts get-caller-identity
   aws s3 ls s3://salvation-files/
   ```

#### Database Backup Script with S3 Upload

**Get Database Credentials:**

Check your `.env` file for the correct database credentials:

```bash
grep DB_ /var/www/html/salvation-admin/.env
```

**Create Backup Script:**

```bash
sudo nano /usr/local/bin/backup-db.sh
```

Add the following script (update DB credentials from your .env):

```bash
#!/bin/bash

# Configuration
BACKUP_DIR="/var/backups/mysql"
S3_BUCKET="salvation-files"
S3_FOLDER="mysql-backup"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="salvation_admin_$DATE.sql.gz"
LOCAL_BACKUP="$BACKUP_DIR/$BACKUP_FILE"

# Database credentials (get from .env file)
DB_USER="root"
DB_PASSWORD=""  # Empty if no password, or add your password
DB_NAME="salvation_admin"

# Create backup directory
mkdir -p $BACKUP_DIR

# Create MySQL backup
echo "Creating MySQL backup..."
if [ -z "$DB_PASSWORD" ]; then
    mysqldump -u $DB_USER $DB_NAME | gzip > $LOCAL_BACKUP
else
    mysqldump -u $DB_USER -p"$DB_PASSWORD" $DB_NAME | gzip > $LOCAL_BACKUP
fi

# Check if backup was successful
if [ $? -eq 0 ] && [ -f $LOCAL_BACKUP ]; then
    BACKUP_SIZE=$(du -h $LOCAL_BACKUP | cut -f1)
    echo "✓ Backup created: $LOCAL_BACKUP ($BACKUP_SIZE)"

    # Upload to S3
    echo "Uploading to S3..."
    aws s3 cp $LOCAL_BACKUP s3://$S3_BUCKET/$S3_FOLDER/$BACKUP_FILE

    if [ $? -eq 0 ]; then
        echo "✓ Uploaded to s3://$S3_BUCKET/$S3_FOLDER/$BACKUP_FILE"
    else
        echo "✗ ERROR: Failed to upload to S3"
        exit 1
    fi
else
    echo "✗ ERROR: Failed to create MySQL backup"
    exit 1
fi

# Clean up local backups older than 7 days
echo "Cleaning up old local backups..."
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete

# Clean up S3 backups older than 30 days
echo "Cleaning up old S3 backups..."
CUTOFF_DATE=$(date -d "30 days ago" +%Y%m%d)
aws s3 ls s3://$S3_BUCKET/$S3_FOLDER/ | while read -r line; do
    FILE_DATE=$(echo $line | awk '{print $4}' | grep -oP '\d{8}' | head -1)
    FILE_NAME=$(echo $line | awk '{print $4}')

    if [ ! -z "$FILE_DATE" ] && [ "$FILE_DATE" -lt "$CUTOFF_DATE" ]; then
        echo "Deleting old backup: $FILE_NAME"
        aws s3 rm s3://$S3_BUCKET/$S3_FOLDER/$FILE_NAME
    fi
done

echo "✓ Backup process completed successfully!"
```

**Make Script Executable:**

```bash
sudo chmod +x /usr/local/bin/backup-db.sh
```

**Test the Backup Script:**

```bash
sudo bash /usr/local/bin/backup-db.sh
```

**Add to Crontab for Daily Backups:**

```bash
sudo crontab -e
```

Add this line (runs daily at 2 AM):

```cron
0 2 * * * /usr/local/bin/backup-db.sh >> /var/log/mysql-backup.log 2>&1
```

**Monitor Backup Logs:**

```bash
# View real-time logs
tail -f /var/log/mysql-backup.log

# View backup history
ls -lh /var/backups/mysql/

# View S3 backups
aws s3 ls s3://salvation-files/mysql-backup/
```

#### Optional: S3 Lifecycle Policy

Instead of script-based cleanup, use S3 lifecycle policy:

```bash
# Create lifecycle policy file
cat > s3-lifecycle-policy.json <<'EOF'
{
  "Rules": [
    {
      "Id": "DeleteOldBackups",
      "Status": "Enabled",
      "Filter": {
        "Prefix": "mysql-backup/"
      },
      "Expiration": {
        "Days": 30
      }
    }
  ]
}
EOF

# Apply lifecycle policy
aws s3api put-bucket-lifecycle-configuration \
    --bucket salvation-files \
    --lifecycle-configuration file://s3-lifecycle-policy.json
```

---

## Troubleshooting

### Issue: Composer Lock File Incompatible with PHP Version

**Error:** `Your lock file does not contain a compatible set of packages`

**Solution Option 1: Upgrade to PHP 8.4 (Recommended)**

```bash
# Install PHP 8.4
sudo apt install -y php8.4-fpm php8.4-cli php8.4-common \
  php8.4-mysql php8.4-zip php8.4-gd php8.4-mbstring \
  php8.4-curl php8.4-xml php8.4-bcmath php8.4-intl \
  php8.4-redis php8.4-soap php8.4-imagick

# Remove old PHP 8.2 (if installed)
sudo apt remove -y php8.2-*
sudo apt purge -y php8.2-fpm php8.2-common
sudo apt autoremove -y

# Verify PHP 8.4 is active
php -v
sudo systemctl status php8.4-fpm

# Update Nginx to use PHP 8.4
sudo nano /etc/nginx/sites-available/salvation-admin
# Change: fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;

# Test Nginx configuration
sudo nginx -t

# Restart services
sudo systemctl restart nginx php8.4-fpm

# Now install composer dependencies
cd /var/www/html/salvation-admin
sudo -u www-data composer install --no-dev --optimize-autoloader
```

**Solution Option 2: Regenerate Lock File for PHP 8.2**

```bash
# Delete the existing lock file
cd /var/www/html/salvation-admin
sudo rm composer.lock

# Update composer dependencies to match PHP 8.2
sudo -u www-data composer update --no-dev --optimize-autoloader

# Commit the new lock file to your repository
git add composer.lock
git commit -m "Update composer.lock for PHP 8.2 compatibility"
git push
```

### Issue: 502 Bad Gateway

**Solution:**

```bash
# Check PHP-FPM status (use your PHP version)
sudo systemctl status php8.4-fpm

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm

# Check Nginx error logs
sudo tail -f /var/log/nginx/error.log
```

### Issue: Permission Denied / Failed to open stream

**Error:** `file_put_contents(...): Failed to open stream: Permission denied`

**Solution:**

```bash
cd /var/www/html/salvation-admin

# Fix ownership
sudo chown -R www-data:www-data storage bootstrap/cache

# Fix permissions
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Clear cached views
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan cache:clear

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm

# If still getting errors, use more permissive permissions
sudo chmod -R 777 storage
sudo chmod -R 777 bootstrap/cache
```

### Issue: Database Connection Error

**Solution:**

```bash
# Check MySQL status
sudo systemctl status mysql

# Test connection
mysql -u salvation_user -p salvation_admin

# Check .env database credentials
sudo nano /var/www/html/salvation-admin/.env
```

### Issue: PDF Generation Fails

**Solution:**

```bash
# Check Chromium installation
chromium-browser --version

# Install missing dependencies
sudo apt install -y chromium-browser chromium-chromedriver

# Verify Chrome path in .env
grep CHROME_PATH /var/www/html/salvation-admin/.env

# Clear config cache
sudo -u www-data php artisan config:clear
```

### Issue: Queue Jobs Not Processing

**Solution:**

```bash
# Check supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart salvation-admin-worker:*

# Check worker logs
sudo tail -f /var/www/html/salvation-admin/storage/logs/worker.log
```

### View Application Logs

```bash
# Laravel logs
sudo tail -f /var/www/html/salvation-admin/storage/logs/laravel.log

# Nginx access logs
sudo tail -f /var/log/nginx/access.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

---

## Security Best Practices

1. **Keep system updated:**
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

2. **Disable root login:**
   ```bash
   sudo nano /etc/ssh/sshd_config
   # Set: PermitRootLogin no
   sudo systemctl restart sshd
   ```

3. **Configure fail2ban:**
   ```bash
   sudo apt install fail2ban
   sudo systemctl enable fail2ban
   ```

4. **Regular backups:**
   - Set up automated database backups
   - Backup S3 files regularly
   - Test restore procedures

5. **Monitor logs:**
   - Set up log monitoring
   - Configure alerts for errors
   - Review access logs regularly

---

## Deployment Checklist

- [ ] EC2 instance launched and configured
- [ ] Elastic IP assigned
- [ ] Security groups configured
- [ ] SSH access working
- [ ] PHP 8.2 installed and configured
- [ ] Composer installed
- [ ] Node.js and npm installed
- [ ] MySQL installed and database created
- [ ] Nginx installed and configured
- [ ] Application deployed
- [ ] .env file configured
- [ ] Dependencies installed
- [ ] Migrations run
- [ ] S3 configured and tested
- [ ] Chromium installed for PDF generation
- [ ] Queue workers configured and running
- [ ] SSL certificate installed (optional)
- [ ] Firewall enabled and configured
- [ ] Cron jobs configured
- [ ] Log rotation configured
- [ ] Backups configured
- [ ] Application tested and working

---

## Useful Commands

```bash
# Restart all services
sudo systemctl restart nginx php8.4-fpm mysql
sudo supervisorctl restart all

# Clear all caches
cd /var/www/html/salvation-admin
sudo -u www-data php artisan optimize:clear

# Deploy latest code
cd /var/www/html/salvation-admin
sudo -u www-data git pull origin main

# Install dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo chown -R ubuntu:ubuntu .
npm install && npm run build
sudo chown -R www-data:www-data .

# Run migrations and optimize
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan optimize

# Restart services
sudo systemctl restart php8.4-fpm nginx
sudo supervisorctl restart all

# Check system resources
df -h              # Disk usage
free -h            # Memory usage
top                # CPU and processes (press 'q' to quit)

# Monitor logs
sudo tail -f /var/www/html/salvation-admin/storage/logs/laravel.log
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/php8.4-fpm.log
```

---

## Support

For issues or questions:
- Check Laravel logs: `/var/www/html/salvation-admin/storage/logs/laravel.log`
- Check Nginx logs: `/var/log/nginx/error.log`
- Check application documentation: `CLAUDE.md`

---

## Summary

This guide covers:
- ✅ EC2 instance setup with Ubuntu 22.04
- ✅ PHP 8.4 + all required extensions
- ✅ MySQL database configuration
- ✅ Nginx web server setup
- ✅ Node.js 20.x for frontend assets
- ✅ S3 file storage integration
- ✅ Chromium for PDF generation
- ✅ Queue workers with Supervisor
- ✅ SSL certificates (optional)
- ✅ Security and optimization

**Last Updated:** January 2026
