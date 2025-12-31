# AWS S3 Backup Setup Guide

This guide explains how to configure automated database backups to AWS S3.

## Prerequisites

1. **AWS Account** with S3 access
2. **S3 Bucket** created (or will create one)
3. **AWS CLI** installed on your Ubuntu server
4. **IAM User** with S3 permissions

---

## Step 1: Install AWS CLI

```bash
# Update package list
sudo apt-get update

# Install AWS CLI
sudo apt-get install awscli -y

# Verify installation
aws --version
```

---

## Step 2: Create S3 Bucket (if needed)

### Option A: Using AWS Console
1. Go to https://console.aws.amazon.com/s3/
2. Click "Create bucket"
3. Bucket name: `your-backup-bucket-name` (must be globally unique)
4. Region: Select your preferred region (e.g., `us-east-1`)
5. Block all public access: **Enable** (recommended for security)
6. Versioning: Optional (recommended for extra protection)
7. Encryption: Enable server-side encryption (recommended)
8. Click "Create bucket"

### Option B: Using AWS CLI
```bash
# Replace with your bucket name and region
BUCKET_NAME="salvation-admin-backups"
REGION="us-east-1"

# Create bucket
aws s3 mb s3://$BUCKET_NAME --region $REGION

# Enable versioning (optional but recommended)
aws s3api put-bucket-versioning \
    --bucket $BUCKET_NAME \
    --versioning-configuration Status=Enabled

# Enable encryption (recommended)
aws s3api put-bucket-encryption \
    --bucket $BUCKET_NAME \
    --server-side-encryption-configuration '{
        "Rules": [{
            "ApplyServerSideEncryptionByDefault": {
                "SSEAlgorithm": "AES256"
            }
        }]
    }'
```

---

## Step 3: Create IAM User with S3 Permissions

### Option A: Using AWS Console

1. **Create IAM User:**
   - Go to https://console.aws.amazon.com/iam/
   - Users → Add users
   - Username: `salvation-admin-backup`
   - Access type: **Programmatic access** ✓
   - Click "Next: Permissions"

2. **Attach Policy:**
   - Click "Attach existing policies directly"
   - Search for `AmazonS3FullAccess` (or create custom policy below)
   - Click "Next" through the remaining steps
   - Click "Create user"

3. **Save Credentials:**
   - **Access Key ID**: Copy and save securely
   - **Secret Access Key**: Copy and save securely (you won't see this again!)

### Option B: Custom IAM Policy (More Secure)

Create a custom policy with minimum required permissions:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AllowBackupBucketAccess",
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::your-backup-bucket-name",
                "arn:aws:s3:::your-backup-bucket-name/*"
            ]
        }
    ]
}
```

Replace `your-backup-bucket-name` with your actual bucket name.

---

## Step 4: Configure AWS CLI on Ubuntu Server

```bash
# Run AWS configuration
aws configure

# Enter your credentials when prompted:
# AWS Access Key ID: [your-access-key-id]
# AWS Secret Access Key: [your-secret-access-key]
# Default region name: us-east-1 (or your bucket's region)
# Default output format: json
```

### Verify AWS CLI Configuration

```bash
# Test S3 access
aws s3 ls

# Test bucket access
aws s3 ls s3://your-backup-bucket-name/

# Create test folder
aws s3 mb s3://your-backup-bucket-name/sqlbackup/
```

---

## Step 5: Update .env File

Add these variables to your `.env` file:

```bash
# AWS S3 Backup Configuration
AWS_BACKUP_BUCKET=your-backup-bucket-name
AWS_BACKUP_FOLDER=sqlbackup
AWS_REGION=us-east-1
UPLOAD_TO_S3=true
KEEP_LOCAL_BACKUP=false
```

### Configuration Options:

- **AWS_BACKUP_BUCKET**: Your S3 bucket name
- **AWS_BACKUP_FOLDER**: Folder inside bucket (default: `sqlbackup`)
- **AWS_REGION**: AWS region (e.g., `us-east-1`, `eu-west-1`)
- **UPLOAD_TO_S3**: `true` to enable S3 upload, `false` to disable
- **KEEP_LOCAL_BACKUP**:
  - `true` = Keep backups both locally AND on S3
  - `false` = Only keep backups on S3 (delete local after upload)

---

## Step 6: Test the Backup Script

```bash
cd /path/to/salvation-admin

# Make script executable
chmod +x scripts/backup-database.sh

# Run manually to test
./scripts/backup-database.sh

# Check logs
tail -f storage/logs/backup.log
```

### Expected Output:

```
[2025-12-31 02:00:00] === Starting database backup ===
[2025-12-31 02:00:01] Backing up database: salvation_admin_db
[2025-12-31 02:00:05] SUCCESS: Backup completed successfully (Size: 25M)
[2025-12-31 02:00:05] Uploading backup to S3 bucket: s3://your-bucket/sqlbackup/
[2025-12-31 02:00:10] SUCCESS: Backup uploaded to S3: s3://your-bucket/sqlbackup/salvation_admin_backup_2025-12-31_02-00-00.sql.gz
[2025-12-31 02:00:10] Removing local backup (KEEP_LOCAL_BACKUP=false)
[2025-12-31 02:00:10] Local backup removed
[2025-12-31 02:00:11] Cleaning up S3 backups older than 15 days...
[2025-12-31 02:00:12] Total S3 backups remaining: 1
[2025-12-31 02:00:12] === Backup process completed ===
```

---

## Step 7: Verify S3 Upload

```bash
# List files in S3 bucket
aws s3 ls s3://your-backup-bucket-name/sqlbackup/

# Expected output:
# 2025-12-31 02:00:10   26214400 salvation_admin_backup_2025-12-31_02-00-00.sql.gz

# Download backup to verify (optional)
aws s3 cp s3://your-backup-bucket-name/sqlbackup/salvation_admin_backup_2025-12-31_02-00-00.sql.gz ./test-backup.sql.gz

# Test decompression
gunzip -t ./test-backup.sql.gz
```

---

## Step 8: Set Up Cron Job

```bash
# Edit crontab
crontab -e

# Add this line (runs daily at 2 AM)
0 2 * * * cd /var/www/salvation-admin && ./scripts/backup-database.sh >> /var/www/salvation-admin/storage/logs/backup-cron.log 2>&1
```

---

## Monitoring & Management

### View S3 Backups

```bash
# List all backups in S3
aws s3 ls s3://your-backup-bucket-name/sqlbackup/ --recursive --human-readable

# Count backups
aws s3 ls s3://your-backup-bucket-name/sqlbackup/ | grep "salvation_admin_backup" | wc -l

# Get total size of all backups
aws s3 ls s3://your-backup-bucket-name/sqlbackup/ --recursive --summarize
```

### Download Specific Backup

```bash
# Download latest backup
LATEST_BACKUP=$(aws s3 ls s3://your-backup-bucket-name/sqlbackup/ | grep "salvation_admin_backup" | sort | tail -n 1 | awk '{print $4}')
aws s3 cp s3://your-backup-bucket-name/sqlbackup/$LATEST_BACKUP ./latest-backup.sql.gz

# Download specific backup
aws s3 cp s3://your-backup-bucket-name/sqlbackup/salvation_admin_backup_2025-12-31_02-00-00.sql.gz ./backup.sql.gz
```

### Restore from S3 Backup

```bash
# 1. Download backup from S3
aws s3 cp s3://your-backup-bucket-name/sqlbackup/salvation_admin_backup_2025-12-31_02-00-00.sql.gz ./restore.sql.gz

# 2. Decompress and restore
gunzip < restore.sql.gz | mysql -u username -p database_name

# Or in one command:
aws s3 cp s3://your-backup-bucket-name/sqlbackup/salvation_admin_backup_2025-12-31_02-00-00.sql.gz - | gunzip | mysql -u username -p database_name
```

---

## S3 Lifecycle Policy (Optional)

For additional cost savings, set up S3 Lifecycle policies:

1. **Move to Glacier after 30 days** (cheaper storage for old backups)
2. **Delete after 90 days** (permanent deletion)

### Using AWS Console:
1. Go to S3 → Your Bucket → Management → Lifecycle
2. Create lifecycle rule
3. Add transitions:
   - After 30 days: Transition to Glacier
   - After 90 days: Delete

### Using AWS CLI:
```bash
# Create lifecycle policy file
cat > lifecycle-policy.json << 'EOF'
{
    "Rules": [
        {
            "Id": "BackupRetentionPolicy",
            "Filter": {
                "Prefix": "sqlbackup/"
            },
            "Status": "Enabled",
            "Transitions": [
                {
                    "Days": 30,
                    "StorageClass": "GLACIER"
                }
            ],
            "Expiration": {
                "Days": 90
            }
        }
    ]
}
EOF

# Apply lifecycle policy
aws s3api put-bucket-lifecycle-configuration \
    --bucket your-backup-bucket-name \
    --lifecycle-configuration file://lifecycle-policy.json
```

---

## Cost Estimation

### S3 Standard Storage (us-east-1):
- **Storage**: $0.023 per GB/month
- **Requests**: $0.005 per 1,000 PUT requests
- **Data Transfer**: Free (within AWS), $0.09/GB (to internet)

### Example Cost (assuming 100MB daily backups):
- **Daily backup size**: 100MB = 0.1GB
- **Monthly backups (15 days retention)**: 1.5GB
- **Monthly storage cost**: 1.5GB × $0.023 = **$0.03/month**
- **Monthly PUT requests**: 30 × $0.005/1000 = **$0.0002/month**
- **Total**: **~$0.03/month** (negligible)

With Glacier after 30 days:
- Glacier storage: $0.004 per GB/month (~82% savings)

---

## Security Best Practices

1. **Enable MFA Delete** on S3 bucket (prevents accidental deletion)
2. **Enable S3 Bucket Versioning** (recovery from accidental overwrites)
3. **Use IAM roles** instead of access keys (for EC2 instances)
4. **Encrypt backups** before upload (optional extra layer)
5. **Enable S3 access logging** (audit trail)
6. **Restrict bucket access** with bucket policies
7. **Regular restore tests** (verify backups are valid)

### Example Bucket Policy (Restrict by IP):
```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AllowFromSpecificIP",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:*",
            "Resource": [
                "arn:aws:s3:::your-backup-bucket-name",
                "arn:aws:s3:::your-backup-bucket-name/*"
            ],
            "Condition": {
                "IpAddress": {
                    "aws:SourceIp": "your.server.ip.address/32"
                }
            }
        }
    ]
}
```

---

## Troubleshooting

### "Unable to locate credentials"
```bash
# Verify AWS credentials are configured
aws configure list

# Re-configure if needed
aws configure
```

### "Access Denied" errors
- Check IAM user has correct permissions
- Verify bucket name is correct
- Check bucket region matches AWS CLI region

### "Bucket does not exist"
```bash
# List all buckets to verify name
aws s3 ls

# Check bucket in specific region
aws s3 ls --region us-east-1
```

### Upload fails silently
```bash
# Check AWS CLI debug output
aws s3 cp file.txt s3://bucket/file.txt --debug

# Check network connectivity
ping s3.amazonaws.com
```

---

## Support

For AWS-specific issues:
- AWS Support: https://console.aws.amazon.com/support/
- AWS CLI Documentation: https://docs.aws.amazon.com/cli/
- S3 Documentation: https://docs.aws.amazon.com/s3/

For backup script issues:
- Check `storage/logs/backup.log`
- Run script manually with debug: `bash -x scripts/backup-database.sh`
