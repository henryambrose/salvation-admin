<?php

namespace Modules\Graveyard\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Members\Models\User;
use Modules\Fund\Models\PaymentMethod;

class ObituaryPayment extends Model
{
    protected $fillable = [
        'obituary_page_id',
        'obituary_plan_id',
        'amount',
        'payment_status',
        'payment_reference',
        'receipt_number',
        'receipt_generated_at',
        'payment_method_id',
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
        'receipt_generated_at' => 'datetime',
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

    public function obituaryPlan(): BelongsTo
    {
        return $this->belongsTo(ObituaryPlan::class);
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

    /**
     * Generate receipt number for this payment
     */
    public function generateReceipt(): void
    {
        if (!$this->receipt_number) {
            $this->update([
                'receipt_number' => static::generateReceiptNumber(),
                'receipt_generated_at' => now()
            ]);
        }
    }

    /**
     * Generate a unique receipt number
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = 'OBTR'; // Obituary Receipt
        $date = now()->format('Ymd');
        $lastReceipt = static::whereDate('receipt_generated_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastReceipt ? (int)substr($lastReceipt->receipt_number, -4) + 1 : 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
