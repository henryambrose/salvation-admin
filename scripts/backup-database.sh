#!/bin/bash

# Database Backup Script for Salvation Admin
# Performs daily MySQL backup with 15-day retention

# Set script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Load environment variables from .env
if [ -f "$PROJECT_ROOT/.env" ]; then
    export $(cat "$PROJECT_ROOT/.env" | grep -v '^#' | grep -v '^$' | xargs)
else
    echo "Error: .env file not found at $PROJECT_ROOT/.env"
    exit 1
fi

# Configuration
BACKUP_DIR="$PROJECT_ROOT/storage/backups/database"
DATE=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_FILE="$BACKUP_DIR/salvation_admin_backup_$DATE.sql.gz"
BACKUP_FILENAME="salvation_admin_backup_$DATE.sql.gz"
LOG_FILE="$PROJECT_ROOT/storage/logs/backup.log"
RETENTION_DAYS=15

# S3 Configuration (set these in your .env file or uncomment and set here)
# AWS_S3_BUCKET="${AWS_BACKUP_BUCKET:-your-bucket-name}"
# AWS_S3_FOLDER="${AWS_BACKUP_FOLDER:-sqlbackup}"
# AWS_REGION="${AWS_REGION:-us-east-1}"
# UPLOAD_TO_S3="${UPLOAD_TO_S3:-true}"
# KEEP_LOCAL_BACKUP="${KEEP_LOCAL_BACKUP:-false}"

AWS_S3_BUCKET="${AWS_BACKUP_BUCKET}"
AWS_S3_FOLDER="${AWS_BACKUP_FOLDER:-sqlbackup}"
AWS_REGION="${AWS_REGION:-us-east-1}"
UPLOAD_TO_S3="${UPLOAD_TO_S3:-true}"
KEEP_LOCAL_BACKUP="${KEEP_LOCAL_BACKUP:-false}"

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"
mkdir -p "$(dirname "$LOG_FILE")"

# Log function
log_message() {
    echo "[$(date +"%Y-%m-%d %H:%M:%S")] $1" | tee -a "$LOG_FILE"
}

log_message "=== Starting database backup ==="

# Check if required variables are set
if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
    log_message "ERROR: Database credentials not found in .env file"
    exit 1
fi

# Create MySQL password file for security (avoids password in command line)
MYSQL_CONFIG_FILE=$(mktemp)
cat > "$MYSQL_CONFIG_FILE" << EOF
[mysqldump]
user=$DB_USERNAME
password=$DB_PASSWORD
host=${DB_HOST:-localhost}
port=${DB_PORT:-3306}
EOF

chmod 600 "$MYSQL_CONFIG_FILE"

# Perform database backup
log_message "Backing up database: $DB_DATABASE"
log_message "Backup file: $BACKUP_FILE"

if mysqldump --defaults-extra-file="$MYSQL_CONFIG_FILE" \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    --add-drop-table \
    --quick \
    "$DB_DATABASE" | gzip > "$BACKUP_FILE"; then

    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    log_message "SUCCESS: Backup completed successfully (Size: $BACKUP_SIZE)"
else
    log_message "ERROR: Backup failed"
    rm -f "$MYSQL_CONFIG_FILE"
    exit 1
fi

# Remove MySQL config file
rm -f "$MYSQL_CONFIG_FILE"

# Upload to S3 if enabled
if [ "$UPLOAD_TO_S3" = "true" ]; then
    if [ -z "$AWS_S3_BUCKET" ]; then
        log_message "WARNING: UPLOAD_TO_S3 is enabled but AWS_BACKUP_BUCKET is not set. Skipping S3 upload."
    else
        log_message "Uploading backup to S3 bucket: s3://$AWS_S3_BUCKET/$AWS_S3_FOLDER/"

        # Check if AWS CLI is installed
        if ! command -v aws &> /dev/null; then
            log_message "ERROR: AWS CLI is not installed. Please install it first."
            log_message "Install: sudo apt-get install awscli -y"
            exit 1
        fi

        # Upload to S3
        S3_PATH="s3://$AWS_S3_BUCKET/$AWS_S3_FOLDER/$BACKUP_FILENAME"
        if aws s3 cp "$BACKUP_FILE" "$S3_PATH" --region "$AWS_REGION"; then
            log_message "SUCCESS: Backup uploaded to S3: $S3_PATH"

            # Delete local backup if configured
            if [ "$KEEP_LOCAL_BACKUP" = "false" ]; then
                log_message "Removing local backup (KEEP_LOCAL_BACKUP=false)"
                rm -f "$BACKUP_FILE"
                log_message "Local backup removed: $BACKUP_FILE"
            else
                log_message "Keeping local backup (KEEP_LOCAL_BACKUP=true)"
            fi
        else
            log_message "ERROR: Failed to upload backup to S3"
            exit 1
        fi

        # Clean up old S3 backups (older than retention days)
        log_message "Cleaning up S3 backups older than $RETENTION_DAYS days..."

        # Calculate cutoff date
        CUTOFF_DATE=$(date -d "$RETENTION_DAYS days ago" +%Y-%m-%d)

        # List and delete old backups from S3
        aws s3 ls "s3://$AWS_S3_BUCKET/$AWS_S3_FOLDER/" --region "$AWS_REGION" | while read -r line; do
            # Extract date and filename from S3 ls output
            file_date=$(echo "$line" | awk '{print $1}')
            file_name=$(echo "$line" | awk '{print $4}')

            if [[ "$file_name" == salvation_admin_backup_*.sql.gz ]]; then
                # Compare dates
                if [[ "$file_date" < "$CUTOFF_DATE" ]]; then
                    log_message "Deleting old S3 backup: $file_name (Date: $file_date)"
                    aws s3 rm "s3://$AWS_S3_BUCKET/$AWS_S3_FOLDER/$file_name" --region "$AWS_REGION"
                fi
            fi
        done

        # Count remaining S3 backups
        S3_BACKUP_COUNT=$(aws s3 ls "s3://$AWS_S3_BUCKET/$AWS_S3_FOLDER/" --region "$AWS_REGION" | grep -c "salvation_admin_backup_.*\.sql\.gz" || echo "0")
        log_message "Total S3 backups remaining: $S3_BACKUP_COUNT"
    fi
fi

# Delete local backups older than retention period (if keeping local backups)
if [ "$KEEP_LOCAL_BACKUP" = "true" ]; then
    log_message "Cleaning up local backups older than $RETENTION_DAYS days..."
    DELETED_COUNT=0

    find "$BACKUP_DIR" -name "salvation_admin_backup_*.sql.gz" -type f -mtime +$RETENTION_DAYS | while read -r old_backup; do
        if [ -f "$old_backup" ]; then
            log_message "Deleting old local backup: $(basename "$old_backup")"
            rm -f "$old_backup"
            DELETED_COUNT=$((DELETED_COUNT + 1))
        fi
    done

    if [ $DELETED_COUNT -gt 0 ]; then
        log_message "Deleted $DELETED_COUNT old local backup(s)"
    else
        log_message "No old local backups to delete"
    fi

    # Count remaining local backups
    BACKUP_COUNT=$(find "$BACKUP_DIR" -name "salvation_admin_backup_*.sql.gz" -type f | wc -l)
    log_message "Total local backups remaining: $BACKUP_COUNT"
fi

log_message "=== Backup process completed ==="
echo ""

exit 0
