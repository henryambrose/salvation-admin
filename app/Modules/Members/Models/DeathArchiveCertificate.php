<?php

namespace Modules\Members\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DeathArchiveCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'folder_path',
        'file_name',
        'reg_year',
        'reg_no',
        'death_year',
        'death_month',
        'death_day',
        'first_name',
        'middle_name',
        'last_name',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'death_year' => 'integer',
        'death_month' => 'integer',
        'death_day' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['full_name', 'file_url', 'formatted_date'];

    /**
     * Get the user who created this record
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get full name accessor
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);
        return implode(' ', $parts);
    }

    /**
     * Get S3 file URL
     */
    public function getS3UrlAttribute(): ?string
    {
        if (!$this->folder_path || !$this->file_name) {
            return null;
        }

        $path = trim($this->folder_path, '/') . '/' . $this->file_name;

        try {
            if (Storage::disk('s3')->exists($path)) {
                return Storage::disk('s3')->url($path);
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * Get download route URL
     */
    public function getFileUrlAttribute(): string
    {
        return route('archive.death.download', $this->id);
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): ?string
    {
        try {
            $date = \Carbon\Carbon::createFromDate(
                $this->death_year,
                $this->death_month,
                $this->death_day
            );
            return $date->format('d F Y');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Scope for searching across all searchable fields
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('middle_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('reg_no', 'like', "%{$search}%")
                ->orWhere('reg_year', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for filtering by year
     */
    public function scopeByYear($query, int $year)
    {
        return $query->where('death_year', $year);
    }

    /**
     * Scope for filtering by month
     */
    public function scopeByMonth($query, int $month)
    {
        return $query->where('death_month', $month);
    }

    /**
     * Scope for filtering by day
     */
    public function scopeByDay($query, int $day)
    {
        return $query->where('death_day', $day);
    }

    /**
     * Check if file exists in S3
     */
    public function fileExists(): bool
    {
        if (!$this->folder_path || !$this->file_name) {
            return false;
        }

        $path = trim($this->folder_path, '/') . '/' . $this->file_name;

        try {
            return Storage::disk('s3')->exists($path);
        } catch (\Exception $e) {
            return false;
        }
    }
}
