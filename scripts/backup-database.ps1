# Database Backup Script for Salvation Admin (PowerShell)
# Performs daily MySQL backup with 15-day retention

# Configuration
$ProjectRoot = Split-Path -Parent $PSScriptRoot
$EnvFile = Join-Path $ProjectRoot ".env"
$BackupDir = Join-Path $ProjectRoot "storage\backups\database"
$LogFile = Join-Path $ProjectRoot "storage\logs\backup.log"
$Date = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
$BackupFile = Join-Path $BackupDir "salvation_admin_backup_$Date.sql.gz"
$RetentionDays = 15

# Create directories if they don't exist
New-Item -ItemType Directory -Force -Path $BackupDir | Out-Null
New-Item -ItemType Directory -Force -Path (Split-Path $LogFile) | Out-Null

# Function to log messages
function Log-Message {
    param([string]$Message)
    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $LogEntry = "[$Timestamp] $Message"
    Write-Host $LogEntry
    Add-Content -Path $LogFile -Value $LogEntry
}

Log-Message "=== Starting database backup ==="

# Load .env file
if (-Not (Test-Path $EnvFile)) {
    Log-Message "ERROR: .env file not found at $EnvFile"
    exit 1
}

# Parse .env file
$EnvVars = @{}
Get-Content $EnvFile | ForEach-Object {
    if ($_ -match '^\s*([^#][^=]*)\s*=\s*(.*)$') {
        $key = $matches[1].Trim()
        $value = $matches[2].Trim() -replace '^["'']|["'']$'
        $EnvVars[$key] = $value
    }
}

# Extract database credentials
$DbHost = if ($EnvVars["DB_HOST"]) { $EnvVars["DB_HOST"] } else { "localhost" }
$DbPort = if ($EnvVars["DB_PORT"]) { $EnvVars["DB_PORT"] } else { "3306" }
$DbDatabase = $EnvVars["DB_DATABASE"]
$DbUsername = $EnvVars["DB_USERNAME"]
$DbPassword = $EnvVars["DB_PASSWORD"]

# Validate credentials
if (-Not $DbDatabase -or -Not $DbUsername) {
    Log-Message "ERROR: Database credentials not found in .env file"
    exit 1
}

Log-Message "Backing up database: $DbDatabase"
Log-Message "Backup file: $BackupFile"

# Create temporary MySQL config file
$MysqlConfigFile = [System.IO.Path]::GetTempFileName()
@"
[mysqldump]
user=$DbUsername
password=$DbPassword
host=$DbHost
port=$DbPort
"@ | Out-File -FilePath $MysqlConfigFile -Encoding ASCII

# Perform database backup
try {
    # Run mysqldump and compress with 7-Zip or built-in compression
    $TempSqlFile = Join-Path $BackupDir "temp_backup_$Date.sql"

    # Execute mysqldump
    $mysqldumpPath = "mysqldump"  # Assumes mysqldump is in PATH
    $arguments = @(
        "--defaults-extra-file=$MysqlConfigFile",
        "--single-transaction",
        "--routines",
        "--triggers",
        "--events",
        "--add-drop-table",
        "--quick",
        "--result-file=$TempSqlFile",
        $DbDatabase
    )

    $process = Start-Process -FilePath $mysqldumpPath -ArgumentList $arguments -NoNewWindow -Wait -PassThru

    if ($process.ExitCode -eq 0) {
        # Compress the SQL file using .NET compression
        $sqlContent = [System.IO.File]::ReadAllBytes($TempSqlFile)
        $gzipStream = New-Object System.IO.FileStream($BackupFile, [System.IO.FileMode]::Create)
        $compressionStream = New-Object System.IO.Compression.GZipStream($gzipStream, [System.IO.Compression.CompressionMode]::Compress)
        $compressionStream.Write($sqlContent, 0, $sqlContent.Length)
        $compressionStream.Close()
        $gzipStream.Close()

        # Remove temp SQL file
        Remove-Item $TempSqlFile -Force

        $BackupSize = (Get-Item $BackupFile).Length / 1MB
        $BackupSizeFormatted = "{0:N2} MB" -f $BackupSize
        Log-Message "SUCCESS: Backup completed successfully (Size: $BackupSizeFormatted)"
    } else {
        Log-Message "ERROR: mysqldump failed with exit code $($process.ExitCode)"
        Remove-Item $MysqlConfigFile -Force -ErrorAction SilentlyContinue
        Remove-Item $TempSqlFile -Force -ErrorAction SilentlyContinue
        exit 1
    }
} catch {
    Log-Message "ERROR: Backup failed - $($_.Exception.Message)"
    Remove-Item $MysqlConfigFile -Force -ErrorAction SilentlyContinue
    Remove-Item $TempSqlFile -Force -ErrorAction SilentlyContinue
    exit 1
}

# Remove MySQL config file
Remove-Item $MysqlConfigFile -Force

# Delete backups older than retention period
Log-Message "Cleaning up backups older than $RetentionDays days..."
$CutoffDate = (Get-Date).AddDays(-$RetentionDays)
$OldBackups = Get-ChildItem -Path $BackupDir -Filter "salvation_admin_backup_*.sql.gz" | Where-Object { $_.LastWriteTime -lt $CutoffDate }

$DeletedCount = 0
foreach ($OldBackup in $OldBackups) {
    Log-Message "Deleting old backup: $($OldBackup.Name)"
    Remove-Item $OldBackup.FullName -Force
    $DeletedCount++
}

if ($DeletedCount -gt 0) {
    Log-Message "Deleted $DeletedCount old backup(s)"
} else {
    Log-Message "No old backups to delete"
}

# Count remaining backups
$BackupCount = (Get-ChildItem -Path $BackupDir -Filter "salvation_admin_backup_*.sql.gz").Count
Log-Message "Total backups remaining: $BackupCount"

Log-Message "=== Backup process completed ==="

exit 0
