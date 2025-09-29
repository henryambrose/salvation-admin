<?php

namespace Modules\Graveyard\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Graveyard\Models\PermanentGraveBooking;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ObituaryPage extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'uuid',
        'qr_code_path',
        'permanent_grave_booking_id',
        'temporary_grave_booking_id',
        'obituary_plan_id',
        'biography',
        'favorite_memory',
        'achievements',
        'hobbies_interests',
        'notes',
        'profile_image',
        'gallery_images',
        'audio_message',
        'theme_color',
        'background_style',
        'allow_condolences',
        'allow_memory_sharing',
        'is_public',
        'is_active',
        'is_published',
        'published_at',
        'published_by',
        'expires_at',
        'view_count',
        'qr_scan_count',
        'created_by',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'allow_condolences' => 'boolean',
        'allow_memory_sharing' => 'boolean',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'view_count' => 'integer',
        'qr_scan_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($obituaryPage) {
            if (empty($obituaryPage->uuid)) {
                $obituaryPage->uuid = (string) Str::uuid();
            }
        });
    }

    public function permanentGraveBooking(): BelongsTo
    {
        return $this->belongsTo(PermanentGraveBooking::class);
    }

    public function temporaryGraveBooking(): BelongsTo
    {
        return $this->belongsTo(TemporaryGraveBooking::class);
    }

    public function condolences(): HasMany
    {
        return $this->hasMany(ObituaryCondolence::class);
    }

    public function approvedCondolences(): HasMany
    {
        return $this->hasMany(ObituaryCondolence::class)->where('is_approved', true);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ObituaryPayment::class);
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'published_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function obituaryManager(): HasOne
    {
        return $this->hasOne(ObituaryManager::class);
    }

    public function obituaryPlan(): BelongsTo
    {
        return $this->belongsTo(ObituaryPlan::class);
    }

    public function getBookingAttribute()
    {
        return $this->permanentGraveBooking ?: $this->temporaryGraveBooking;
    }

    public function getDeceasedNameAttribute(): string
    {
        $booking = $this->getBookingAttribute();

        if ($booking instanceof PermanentGraveBooking) {
            return $booking->validMember ? $booking->validMember->full_name : 'Unknown';
        }

        if ($booking instanceof TemporaryGraveBooking) {
            return $booking->deceased_full_name;
        }

        return 'Unknown';
    }

    public function getDeceasedDateOfBirthAttribute()
    {
        $booking = $this->getBookingAttribute();

        if ($booking instanceof PermanentGraveBooking) {
            return $booking->validMember?->date_of_birth;
        }

        if ($booking instanceof TemporaryGraveBooking) {
            return $booking->date_of_birth;
        }

        return null;
    }

    public function getDeathDateAttribute()
    {
        $booking = $this->getBookingAttribute();
        return $booking?->died_on;
    }

    public function getBurialDateAttribute()
    {
        $booking = $this->getBookingAttribute();
        return $booking?->buried_on;
    }

    public function getGraveLocationAttribute(): string
    {
        $booking = $this->getBookingAttribute();

        if ($booking instanceof PermanentGraveBooking) {
            $grave = $booking->permanentGrave;
            return $grave ? "Section {$grave->section}, Grave {$grave->grave_no}" : 'Unknown';
        }

        if ($booking instanceof TemporaryGraveBooking) {
            $grave = $booking->temporaryGrave;
            return $grave ? "Section {$grave->section}, Grave {$grave->grave_no}" : 'Unknown';
        }

        return 'Unknown';
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function incrementQrScanCount(): void
    {
        $this->increment('qr_scan_count');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false);
    }

    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    // Helper methods for publish/unpublish actions
    public function publish($userId = null): bool
    {
        return $this->update([
            'is_published' => true,
            'published_at' => now(),
            'published_by' => $userId ?: Auth::id(),
        ]);
    }

    public function unpublish(): bool
    {
        return $this->update([
            'is_published' => false,
            'published_at' => null,
            'published_by' => null,
        ]);
    }

    public function isPublished(): bool
    {
        return $this->is_published;
    }

    public function isDraft(): bool
    {
        return !$this->is_published;
    }

    /**
     * Check if the obituary page has completed payment
     */
    public function hasCompletedPayment(): bool
    {
        // First, check if there are specific obituary payments
        $obituaryPayments = $this->payments;

        if ($obituaryPayments->isNotEmpty()) {
            // If there are obituary-specific payments, all must be completed
            return $obituaryPayments->every(function ($payment) {
                return in_array($payment->payment_status, ['paid', 'completed']);
            });
        }

        // Fallback: If no obituary-specific payments exist, check the associated booking payment
        // This handles cases where obituary pages are included with the burial service
        $booking = $this->getBookingAttribute();
        if ($booking) {
            return in_array($booking->payment_status, ['paid', 'completed']);
        }

        return false;
    }

    /**
     * Check if the obituary can be published (payment completed)
     */
    public function canBePublished(): bool
    {
        return $this->hasCompletedPayment();
    }

    /**
     * Check if the obituary can be accessed publicly (payment completed and published)
     */
    public function canBeAccessedPublicly(): bool
    {
        return $this->hasCompletedPayment() && $this->isPublished() && $this->is_public && $this->is_active;
    }

    /**
     * Get payment status from obituary payments with booking fallback
     */
    public function getPaymentStatus(): ?string
    {
        $obituaryPayments = $this->payments;

        if ($obituaryPayments->isNotEmpty()) {
            // If there are obituary-specific payments, analyze them
            if ($obituaryPayments->every(fn($payment) => in_array($payment->payment_status, ['paid', 'completed']))) {
                return 'completed';
            }

            if ($obituaryPayments->some(fn($payment) => in_array($payment->payment_status, ['paid', 'completed']))) {
                return 'partial';
            }

            return 'pending';
        }

        // Fallback: Return the booking payment status if no obituary payments exist
        $booking = $this->getBookingAttribute();
        return $booking?->payment_status;
    }

    /**
     * Check if premium features are available based on plan or service type
     */
    // public function hasPremiumFeatures(): bool
    // {
    //     // Check if it has a plan with premium features (we could define this logic)
    //     // For now, we'll use the service_type as fallback for backward compatibility
    //     if ($this->obituaryPlan) {
    //         // You could define premium features based on plan cost or specific plan names
    //         // For now, let's consider plans over ₹1000 as premium
    //         return $this->obituaryPlan->cost > 1000;
    //     }

    //     // Fallback to original service_type logic
    //     return $this->service_type === 'premium';
    // }

    /**
     * Check if obituary has expired based on plan duration
     */
    public function hasExpired(): bool
    {
        if ($this->obituaryPlan && !$this->obituaryPlan->isLifetime()) {
            // Check if obituary has expired based on plan duration
            $expiryDate = $this->created_at->addDays($this->obituaryPlan->duration_in_days);
            return $expiryDate->isPast();
        }

        // Fallback to expires_at column
        return $this->expires_at && $this->expires_at->isPast();
    }
}
