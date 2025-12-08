<?php

namespace Modules\Graveyard\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Members\Models\User;
use Carbon\Carbon;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payment_reference',
        'payable_type',
        'payable_id',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'concession_amount',
        'payment_status',
        'payment_method_id',
        'transaction_reference',
        'payment_notes',
        'selected_services',
        'service_charges',
        'payment_date',
        'receipt_number',
        'receipt_generated_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'concession_amount' => 'decimal:2',
        'selected_services' => 'array',
        'service_charges' => 'array',
        'payment_date' => \App\Casts\DateString::class,
        'receipt_generated_at' => 'datetime',
    ];

    /**
     * Boot method to generate payment reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_reference)) {
                $payment->payment_reference = static::generatePaymentReference();
            }
        });

        // Auto-calculate balance when amounts change
        static::saving(function ($payment) {
            // Ensure numeric values
            $payment->total_amount = (float) $payment->total_amount;
            $payment->paid_amount = (float) $payment->paid_amount;
            
            $payment->balance_amount = $payment->total_amount - $payment->paid_amount;
            
            // Update payment status based on amounts
            // Special case: Free services (both total and paid amount are 0)
            if ($payment->total_amount == 0 && $payment->paid_amount == 0) {
                $payment->payment_status = 'completed';
            } elseif ($payment->paid_amount == 0) {
                $payment->payment_status = 'pending';
            } elseif ($payment->paid_amount >= $payment->total_amount) {
                $payment->payment_status = 'completed';
            } else {
                $payment->payment_status = 'partial';
            }
        });
    }

    /**
     * Generate unique payment reference
     */
    public static function generatePaymentReference(): string
    {
        do {
            $reference = 'PAY' . date('Y') . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (static::where('payment_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Generate unique receipt number
     */
    public static function generateReceiptNumber(): string
    {
        do {
            $reference = 'RCP' . date('Y') . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (static::where('receipt_number', $reference)->exists());

        return $reference;
    }

    /**
     * Polymorphic relationship to booking
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the payment method
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(\Modules\Fund\Models\PaymentMethod::class, 'payment_method_id');
    }

    /**
     * Get the creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Generate receipt for this payment
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
     * Calculate total from selected services
     */
    public static function calculateTotalFromServices(array $services): float
    {
        $total = 0;
        foreach ($services as $service) {
            $total += ($service['unit_cost'] ?? 0) * ($service['quantity'] ?? 1);
        }
        return $total;
    }

    /**
     * Format currency amount
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return '₹ ' . number_format($this->total_amount, 2);
    }

    /**
     * Format currency amount
     */
    public function getFormattedPaidAmountAttribute(): string
    {
        return '₹ ' . number_format($this->paid_amount, 2);
    }

    /**
     * Format currency amount
     */
    public function getFormattedBalanceAmountAttribute(): string
    {
        return '₹ ' . number_format($this->balance_amount, 2);
    }

    /**
     * Format concession amount
     */
    public function getFormattedConcessionAmountAttribute(): string
    {
        return '₹ ' . number_format($this->concession_amount, 2);
    }

    /**
     * Get payment status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'completed' => 'bg-green-100 text-green-800',
            'partial' => 'bg-blue-100 text-blue-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'refunded' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}