<?php

namespace Modules\Graveyard\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObituaryCondolence extends Model
{
    protected $fillable = [
        'obituary_page_id',
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'relationship',
        'message',
        'visitor_ip',
        'user_agent',
        'is_approved',
        'is_rejected',
        'submitted_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_rejected' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function obituaryPage(): BelongsTo
    {
        return $this->belongsTo(ObituaryPage::class);
    }

    public function approve(): void
    {
        $this->update([
            'is_approved' => true,
            'is_rejected' => false
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'is_approved' => false,
            'is_rejected' => true
        ]);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false)->where('is_rejected', false);
    }

    public function scopeRejected($query)
    {
        return $query->where('is_rejected', true);
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_approved) {
            return 'approved';
        } elseif ($this->is_rejected) {
            return 'rejected';
        } else {
            return 'pending';
        }
    }
}
