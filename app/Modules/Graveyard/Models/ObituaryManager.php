<?php

namespace Modules\Graveyard\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ObituaryManager extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'obituary_managers';

    protected $fillable = [
        'obituary_page_id',
        'name',
        'email',
        'password',
        'is_active',
        'access_granted_by',
        'access_granted_at',
        'last_login_at',
        'login_attempts',
        'blocked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'access_granted_at' => 'datetime',
        'last_login_at' => 'datetime',
        'blocked_until' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    public function obituaryPage(): BelongsTo
    {
        return $this->belongsTo(ObituaryPage::class);
    }

    public function accessGrantedBy(): BelongsTo
    {
        return $this->belongsTo(\Modules\Members\Models\User::class, 'access_granted_by');
    }

    /**
     * Check if obituary manager can access the obituary
     */
    public function canAccess(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->blocked_until && $this->blocked_until->isFuture()) {
            return false;
        }

        return true;
    }

    /**
     * Block access for a specified duration (in minutes)
     */
    public function blockAccess(int $minutes = 60): void
    {
        $this->update([
            'blocked_until' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Increment login attempts
     */
    public function incrementLoginAttempts(): void
    {
        $this->increment('login_attempts');

        // Block after 5 failed attempts
        if ($this->login_attempts >= 5) {
            $this->blockAccess(30); // Block for 30 minutes
        }
    }

    /**
     * Reset login attempts on successful login
     */
    public function resetLoginAttempts(): void
    {
        $this->update([
            'login_attempts' => 0,
            'blocked_until' => null,
            'last_login_at' => now(),
        ]);
    }
}