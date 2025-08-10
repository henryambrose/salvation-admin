<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'table_name',
        'action',
        'record_id',
        'old_values',
        'new_values',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the changes made in this audit log
     */
    public function getChangesAttribute(): array
    {
        if ($this->action === 'CREATE') {
            return $this->new_values ?? [];
        }

        if ($this->action === 'UPDATE') {
            $changes = [];
            $oldValues = $this->old_values ?? [];
            $newValues = $this->new_values ?? [];

            foreach ($newValues as $key => $newValue) {
                if (isset($oldValues[$key]) && $oldValues[$key] !== $newValue) {
                    $changes[$key] = [
                        'from' => $oldValues[$key],
                        'to' => $newValue,
                    ];
                }
            }

            return $changes;
        }

        return $this->old_values ?? [];
    }

    /**
     * Scope to filter by table name
     */
    public function scopeForTable($query, string $tableName)
    {
        return $query->where('table_name', $tableName);
    }

    /**
     * Scope to filter by action
     */
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by record ID
     */
    public function scopeForRecord($query, int $recordId)
    {
        return $query->where('record_id', $recordId);
    }
}
