<?php

namespace Modules\Graveyard\Models;

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
        'submitted_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function obituaryPage(): BelongsTo
    {
        return $this->belongsTo(ObituaryPage::class);
    }

    public function approve(): void
    {
        $this->update(['is_approved' => true]);
    }

    public function reject(): void
    {
        $this->update(['is_approved' => false]);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }
}
