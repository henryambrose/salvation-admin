<?php

namespace Modules\Members\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\Members\Models\User;

class FamilyPhoto extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'family_no',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'uploaded_by',
        'uploaded_at',
        'is_active',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Get the user who uploaded this photo
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get all members that belong to this family
     */
    public function familyMembers(): HasMany
    {
        return $this->hasMany(Member::class, 'family_no', 'family_no');
    }

    /**
     * Get the head of the family (first member created with this family_no)
     */
    public function familyHead(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'family_no', 'family_no');
    }

    /**
     * Get the full URL for the family photo
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Get human readable file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Check if the photo file exists on disk
     */
    public function fileExists(): bool
    {
        return $this->file_path && Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Delete the photo file from storage
     */
    public function deleteFile(): bool
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->delete($this->file_path);
        }

        return true;
    }

    /**
     * Scope to get only active photos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get photos by family number
     */
    public function scopeByFamily($query, string $familyNo)
    {
        return $query->where('family_no', $familyNo);
    }

    /**
     * Scope to get photos uploaded by a specific user
     */
    public function scopeByUploader($query, int $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // When deleting a photo, also delete the file from storage
        static::deleting(function ($photo) {
            $photo->deleteFile();
        });
    }
}