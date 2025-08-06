<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuditHelper
{
    /**
     * Set the current user ID for database triggers
     */
    public static function setCurrentUserId(): void
    {
        $userId = Auth::id();
        
        if ($userId) {
            // Store user ID in sessions table for triggers to access
            // Use CONNECTION_ID() for older MySQL versions
            $connectionId = DB::select('SELECT CONNECTION_ID() as id')[0]->id;
            
            // Create a temporary table to store the user ID for this connection
            DB::statement("CREATE TEMPORARY TABLE IF NOT EXISTS temp_user_sessions (
                connection_id INT PRIMARY KEY,
                user_id BIGINT UNSIGNED,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Insert or update the user ID for this connection
            DB::statement("INSERT INTO temp_user_sessions (connection_id, user_id) 
                          VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE user_id = ?, created_at = CURRENT_TIMESTAMP", 
                          [$connectionId, $userId, $userId]);
        }
    }

    /**
     * Clear the current user ID
     */
    public static function clearCurrentUserId(): void
    {
        // For MySQL, we don't need to clear anything as we're using SESSION_ID()
    }

    /**
     * Get audit logs for a specific table and record
     */
    public static function getAuditLogs(string $tableName, int $recordId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = DB::table('audit_logs')
            ->where('table_name', $tableName)
            ->orderBy('created_at', 'desc');

        if ($recordId) {
            $query->where('record_id', $recordId);
        }

        return $query->get();
    }

    /**
     * Get audit logs for a specific user
     */
    public static function getUserAuditLogs(int $userId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return DB::table('audit_logs')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
} 