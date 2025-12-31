# Database Backup Setup Guide

This guide explains how to set up automated daily database backups with a 15-day retention policy.

## Overview

The backup system:
- Creates daily compressed MySQL database backups
- Stores backups in `storage/backups/database/`
- Automatically deletes backups older than 15 days
- Logs all operations to `storage/logs/backup.log`

---

## Windows Setup (Task Scheduler)

### Prerequisites

1. **MySQL Client Tools**: Ensure `mysqldump` is installed and in your PATH
   - Usually located at: `C:\Program Files\MySQL\MySQL Server X.X\bin\`
   - Add to PATH if needed: System Properties → Environment Variables → Path

2. **PowerShell Execution Policy**: Allow script execution
   ```powershell
   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
   ```

### Setup Steps

1. **Test the backup script manually:**
   ```powershell
   cd D:\Projects\salvation-admin
   .\scripts\backup-database.ps1
   ```

2. **Create Windows Task Scheduler Job:**

   **Option A: Using GUI**
   - Open Task Scheduler (Win + R → `taskschd.msc`)
   - Click "Create Task" (not "Create Basic Task")
   - **General Tab:**
     - Name: `Salvation Admin Database Backup`
     - Description: `Daily MySQL database backup with 15-day retention`
     - Run whether user is logged on or not: ✓
     - Run with highest privileges: ✓

   - **Triggers Tab:**
     - Click "New"
     - Begin the task: `On a schedule`
     - Daily, recur every: `1 days`
     - Start time: `02:00:00 AM` (or preferred time)
     - Enabled: ✓

   - **Actions Tab:**
     - Click "New"
     - Action: `Start a program`
     - Program/script: `powershell.exe`
     - Add arguments:
       ```
       -ExecutionPolicy Bypass -File "D:\Projects\salvation-admin\scripts\backup-database.ps1"
       ```
     - Start in: `D:\Projects\salvation-admin`

   - **Conditions Tab:**
     - Uncheck "Start the task only if the computer is on AC power"

   - **Settings Tab:**
     - Allow task to be run on demand: ✓
     - Run task as soon as possible after a scheduled start is missed: ✓

   **Option B: Using PowerShell Command**
   ```powershell
   # Run PowerShell as Administrator
   $action = New-ScheduledTaskAction -Execute 'powershell.exe' `
       -Argument '-ExecutionPolicy Bypass -File "D:\Projects\salvation-admin\scripts\backup-database.ps1"' `
       -WorkingDirectory 'D:\Projects\salvation-admin'

   $trigger = New-ScheduledTaskTrigger -Daily -At 2am

   $settings = New-ScheduledTaskSettingsSet `
       -AllowStartIfOnBatteries `
       -DontStopIfGoingOnBatteries `
       -StartWhenAvailable

   Register-ScheduledTask `
       -TaskName "Salvation Admin Database Backup" `
       -Action $action `
       -Trigger $trigger `
       -Settings $settings `
       -Description "Daily MySQL database backup with 15-day retention" `
       -RunLevel Highest
   ```

3. **Verify the scheduled task:**
   - In Task Scheduler, find the task
   - Right-click → Run
   - Check `storage/logs/backup.log` for success message
   - Verify backup file exists in `storage/backups/database/`

---

## Linux Setup (Cron Job)

### Prerequisites

1. **MySQL Client Tools**: Ensure `mysqldump` is installed
   ```bash
   sudo apt-get install mysql-client  # Debian/Ubuntu
   # or
   sudo yum install mysql             # CentOS/RHEL
   ```

2. **Make script executable:**
   ```bash
   chmod +x scripts/backup-database.sh
   ```

### Setup Steps

1. **Test the backup script manually:**
   ```bash
   cd /path/to/salvation-admin
   ./scripts/backup-database.sh
   ```

2. **Edit crontab:**
   ```bash
   crontab -e
   ```

3. **Add cron job (runs daily at 2 AM):**
   ```bash
   0 2 * * * cd /path/to/salvation-admin && ./scripts/backup-database.sh >> /path/to/salvation-admin/storage/logs/backup-cron.log 2>&1
   ```

   **Cron Schedule Examples:**
   - `0 2 * * *` - Every day at 2:00 AM
   - `0 0 * * *` - Every day at midnight
   - `0 3 * * 0` - Every Sunday at 3:00 AM
   - `0 */6 * * *` - Every 6 hours

4. **Verify cron job is scheduled:**
   ```bash
   crontab -l
   ```

---

## Nginx Configuration (Optional)

If you want to serve backup files via nginx (not recommended for security):

```nginx
location /backups {
    alias /path/to/salvation-admin/storage/backups;
    autoindex on;
    auth_basic "Restricted Access";
    auth_basic_user_file /etc/nginx/.htpasswd;

    # Only allow from specific IPs
    allow 192.168.1.0/24;
    deny all;
}
```

**Better approach**: Use SFTP/SCP to download backups instead of serving via nginx.

---

## Manual Backup

To run a backup manually at any time:

**Windows:**
```powershell
cd D:\Projects\salvation-admin
.\scripts\backup-database.ps1
```

**Linux:**
```bash
cd /path/to/salvation-admin
./scripts/backup-database.sh
```

---

## Restoring from Backup

To restore a database from a backup:

**Windows:**
```powershell
# Decompress the backup
$backupFile = "storage\backups\database\salvation_admin_backup_2025-12-31_02-00-00.sql.gz"
$outputFile = "temp_restore.sql"

# Decompress using .NET
$gzipStream = New-Object System.IO.FileStream($backupFile, [System.IO.FileMode]::Open)
$outputStream = New-Object System.IO.FileStream($outputFile, [System.IO.FileMode]::Create)
$decompressionStream = New-Object System.IO.Compression.GZipStream($gzipStream, [System.IO.Compression.CompressionMode]::Decompress)
$decompressionStream.CopyTo($outputStream)
$decompressionStream.Close()
$gzipStream.Close()
$outputStream.Close()

# Restore the database
mysql -u username -p database_name < temp_restore.sql

# Clean up
Remove-Item temp_restore.sql
```

**Linux:**
```bash
# Decompress and restore in one command
gunzip < storage/backups/database/salvation_admin_backup_2025-12-31_02-00-00.sql.gz | mysql -u username -p database_name
```

---

## Monitoring

### Check backup logs:
```bash
# View last 50 lines of backup log
tail -n 50 storage/logs/backup.log

# View all backups for today
grep "$(date +%Y-%m-%d)" storage/logs/backup.log
```

### List all backups:
**Windows:**
```powershell
Get-ChildItem storage\backups\database\*.sql.gz | Sort-Object LastWriteTime -Descending
```

**Linux:**
```bash
ls -lht storage/backups/database/*.sql.gz
```

### Check backup sizes:
**Windows:**
```powershell
Get-ChildItem storage\backups\database\*.sql.gz | Measure-Object -Property Length -Sum | Select-Object Count,@{Name="TotalSizeMB";Expression={[math]::Round($_.Sum / 1MB, 2)}}
```

**Linux:**
```bash
du -sh storage/backups/database/
```

---

## Customization

To modify backup retention days, edit the script:

**PowerShell** (`scripts/backup-database.ps1`):
```powershell
$RetentionDays = 30  # Change to desired number of days
```

**Bash** (`scripts/backup-database.sh`):
```bash
RETENTION_DAYS=30  # Change to desired number of days
```

---

## Troubleshooting

### mysqldump not found
- Windows: Add MySQL bin directory to PATH
- Linux: Install mysql-client package

### Permission denied
- Windows: Run Task Scheduler as Administrator
- Linux: Ensure script is executable (`chmod +x`)

### .env file not found
- Verify script is run from project root
- Check .env file exists and is readable

### Backup failed
- Check database credentials in `.env`
- Verify MySQL service is running
- Check disk space in `storage/backups/database/`
- Review logs in `storage/logs/backup.log`

---

## Security Recommendations

1. **Encrypt backups** (optional):
   ```bash
   # Encrypt backup
   openssl enc -aes-256-cbc -salt -in backup.sql.gz -out backup.sql.gz.enc -k YOUR_PASSWORD

   # Decrypt backup
   openssl enc -aes-256-cbc -d -in backup.sql.gz.enc -out backup.sql.gz -k YOUR_PASSWORD
   ```

2. **Store backups off-site**:
   - Copy to cloud storage (AWS S3, Azure Blob, Google Cloud Storage)
   - Use rsync to remote server
   - Use backup service (Duplicati, Backblaze)

3. **Restrict file permissions**:
   ```bash
   chmod 600 scripts/backup-database.sh
   chmod 700 storage/backups/database/
   ```

4. **Monitor backup success**:
   - Set up alerts for failed backups
   - Regularly test backup restoration

---

## Support

For issues or questions, refer to the project documentation or contact the development team.
