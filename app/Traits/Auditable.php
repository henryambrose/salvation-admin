<?php

namespace App\Traits;

use Modules\Members\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     */
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->auditActivity('CREATE');
        });

        static::updated(function ($model) {
            $model->auditActivity('UPDATE');
        });

        static::deleted(function ($model) {
            $model->auditActivity('DELETE');
        });
    }

    /**
     * Create an audit log entry for the model.
     */
    protected function auditActivity(string $action)
    {
        // Skip audit logging if in console (migrations, seeders, etc.)
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        $oldValues = null;
        $newValues = null;

        switch ($action) {
            case 'CREATE':
                $newValues = $this->getAuditableAttributes();
                break;

            case 'UPDATE':
                $oldValues = $this->getOriginal();
                $newValues = $this->getAuditableAttributes();

                // Only log if there are actual changes
                if (empty($this->getDirty())) {
                    return;
                }

                // Filter to only changed attributes
                $changedAttributes = array_keys($this->getDirty());
                $oldValues = array_intersect_key($oldValues, array_flip($changedAttributes));
                $newValues = array_intersect_key($newValues, array_flip($changedAttributes));
                break;

            case 'DELETE':
                $oldValues = $this->getAuditableAttributes();
                break;
        }

        // Create audit log entry
        AuditLog::create([
            'table_name' => $this->getTable(),
            'action' => $action,
            'record_id' => $this->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => Auth::id(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get the auditable attributes for the model.
     */
    protected function getAuditableAttributes(): array
    {
        $attributes = $this->getAttributes();

        // Remove sensitive attributes that shouldn't be audited
        $hiddenAttributes = array_merge(
            $this->getHidden(),
            ['password', 'remember_token', 'password_confirmation', 'current_password']
        );

        foreach ($hiddenAttributes as $hidden) {
            unset($attributes[$hidden]);
        }

        // Convert timestamps to string format for consistency
        foreach ($attributes as $key => $value) {
            if ($value instanceof \DateTime) {
                $attributes[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        return $attributes;
    }

    /**
     * Get audit logs for this model instance.
     */
    public function auditLogs()
    {
        return AuditLog::where('table_name', $this->getTable())
                      ->where('record_id', $this->getKey())
                      ->orderBy('created_at', 'desc')
                      ->get();
    }

    /**
     * Get the latest audit log for this model instance.
     */
    public function latestAuditLog()
    {
        return AuditLog::where('table_name', $this->getTable())
                      ->where('record_id', $this->getKey())
                      ->orderBy('created_at', 'desc')
                      ->first();
    }
}