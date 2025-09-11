<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Modules\Fund\Models\PaymentMethod;

class ObituaryPayment extends Model
{
    protected $fillable = [
        'obituary_page_id',
        'amount',
        'service_type',
        'payment_status',
        'payment_reference',
        'payment_method_id',
        'payment_method',
        'paid_amount',
        'payment_date',
        'notes',
        'created_by',
        'updated_by',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function obituaryPage(): BelongsTo
    {
        return $this->belongsTo(ObituaryPage::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function markAsPaid(): void
    {
        $this->update(['payment_status' => 'completed']);
    }

    public function markAsFailed(): void
    {
        $this->update(['payment_status' => 'failed']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
