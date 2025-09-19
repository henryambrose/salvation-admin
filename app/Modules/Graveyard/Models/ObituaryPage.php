<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Graveyard\Models\PermanentGraveBooking;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use Illuminate\Support\Facades\Auth;

class ObituaryPage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'qr_code_path',
        'permanent_grave_booking_id',
        'temporary_grave_booking_id',
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
        'service_type',
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
     * Check if the associated booking has completed payment
     */
    public function hasCompletedPayment(): bool
    {
        $booking = $this->getBookingAttribute();

        if (!$booking) {
            return false;
        }

        // Check payment status - 'paid' for bookings, 'completed' for some payment records
        return in_array($booking->payment_status, ['paid', 'completed']);
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
     * Get payment status from associated booking
     */
    public function getPaymentStatus(): ?string
    {
        $booking = $this->getBookingAttribute();
        return $booking?->payment_status;
    }
}
