# Database Audit System Setup

## Overview
This audit system uses **database triggers** to automatically log all CRUD operations with user IDs. It's the most reliable approach as it works even if the application fails.

## File Structure

### 1. Migration File
**Location:** `database/migrations/2025_08_05_184227_create_audit_logs_table.php`
- Creates `audit_logs` table
- Sets up triggers for `members` table as example
- Includes proper indexes for performance

### 2. Helper Class
**Location:** `app/Helpers/AuditHelper.php`
- Manages user session for triggers
- Provides utility methods for audit logs
- Handles user ID tracking

### 3. Middleware
**Location:** `app/Http/Middleware/AuditMiddleware.php`
- Automatically sets user ID for each request
- Ensures triggers can access current user

### 4. Model
**Location:** `app/Models/AuditLog.php`
- Eloquent model for audit logs
- Includes scopes for filtering
- Provides change tracking

### 5. Controller
**Location:** `app/Http/Controllers/AuditLogController.php`
- View and manage audit logs
- Filter by table, action, user, record

### 6. Artisan Command
**Location:** `app/Console/Commands/GenerateAuditTriggers.php`
- Generate triggers for any table
- Reusable for multiple tables

## Setup Instructions

### Step 1: Run Migration
```bash
php artisan migrate
```

### Step 2: Register Middleware
Add to `app/Http/Kernel.php` in the `$middleware` array:
```php
protected $middleware = [
    // ... other middleware
    \App\Http\Middleware\AuditMiddleware::class,
];
```

### Step 3: Generate Triggers for Tables
```bash
# For members table (already done in migration)
php artisan audit:generate-triggers members

# For other tables
php artisan audit:generate-triggers parishes
php artisan audit:generate-triggers users
php artisan audit:generate-triggers communities
```

## How It Works

### 1. User Session Tracking
```php
// Middleware automatically sets user ID
AuditHelper::setCurrentUserId();
```

### 2. Database Triggers
```sql
-- Automatically fires on INSERT/UPDATE/DELETE
CREATE TRIGGER members_audit_insert
AFTER INSERT ON members
FOR EACH ROW
EXECUTE FUNCTION audit_members_insert();
```

### 3. Audit Log Entry
```json
{
  "id": 1,
  "table_name": "members",
  "action": "CREATE",
  "record_id": 123,
  "new_values": {"name": "John Doe", "email": "john@example.com"},
  "user_id": 5,
  "ip_address": "192.168.1.1",
  "created_at": "2025-08-05 18:42:27"
}
```

## Usage Examples

### View All Audit Logs
```php
use App\Models\AuditLog;

// Get all logs
$logs = AuditLog::with('user')->paginate(50);

// Filter by table
$memberLogs = AuditLog::forTable('members')->get();

// Filter by user
$userLogs = AuditLog::forUser(5)->get();

// Filter by action
$createLogs = AuditLog::forAction('CREATE')->get();
```

### View Changes for a Record
```php
// Get all changes for member ID 123
$changes = AuditLog::forTable('members')
    ->forRecord(123)
    ->orderBy('created_at', 'desc')
    ->get();

// Get what changed in each update
foreach ($changes as $log) {
    if ($log->action === 'UPDATE') {
        $changes = $log->changes; // Returns only changed fields
        // $changes = [
        //     'name' => ['from' => 'John', 'to' => 'Johnny'],
        //     'email' => ['from' => 'john@old.com', 'to' => 'john@new.com']
        // ];
    }
}
```

### Web Interface
Visit these routes to view audit logs:
- `/audit-logs` - All audit logs with filters
- `/audit-logs/table/{tableName}` - Logs for specific table
- `/audit-logs/user/{userId}` - Logs for specific user
- `/audit-logs/{tableName}/{recordId}` - Logs for specific record

## Database Schema

### audit_logs Table
```sql
CREATE TABLE audit_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    table_name VARCHAR(100) NOT NULL,
    action ENUM('CREATE', 'UPDATE', 'DELETE') NOT NULL,
    record_id BIGINT,
    old_values JSON,
    new_values JSON,
    user_id BIGINT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_table_action (table_name, action),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
);
```

## Performance Considerations

### 1. Indexes
- Composite index on `(table_name, action)` for filtering
- Index on `user_id` for user-specific queries
- Index on `created_at` for date-based queries

### 2. JSON Storage
- `old_values` and `new_values` stored as JSON
- Efficient for storing complex data structures
- Can be queried using JSON functions

### 3. Partitioning (Optional)
For large datasets, consider partitioning by date:
```sql
ALTER TABLE audit_logs PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027)
);
```

## Security Features

### 1. User Tracking
- Automatically captures user ID from session
- Includes IP address and user agent
- Links to users table for additional info

### 2. Data Integrity
- Triggers fire even if application fails
- No application-level dependencies
- Complete audit trail

### 3. Access Control
- Audit logs can be protected by middleware
- Role-based access to audit data
- Sensitive data can be masked in logs

## Troubleshooting

### Triggers Not Firing
1. Check if middleware is registered
2. Verify user is authenticated
3. Check database permissions
4. Review trigger functions

### Performance Issues
1. Add missing indexes
2. Consider partitioning for large tables
3. Archive old audit logs
4. Optimize JSON queries

### Missing User IDs
1. Ensure user is logged in
2. Check session configuration
3. Verify trigger function logic
4. Review middleware execution order

## Extending the System

### Add New Tables
```bash
php artisan audit:generate-triggers new_table_name
```

### Custom Triggers
Modify the trigger functions in the migration or command to add custom logic.

### Additional Fields
Add new columns to `audit_logs` table and update trigger functions accordingly.

### Integration with Other Systems
- Export audit logs to external systems
- Real-time notifications for sensitive changes
- Integration with SIEM systems 