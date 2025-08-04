<?php

namespace App\Rules;

use App\Models\Parish;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class ParishValidation implements ValidationRule
{
    protected $fieldName;

    public function __construct($fieldName = null)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Allow empty values
        }

        $parishName = trim($value);
        
        // Check for exact match (case-insensitive)
        $exactMatch = Parish::whereRaw('LOWER(name) = ?', [Str::lower($parishName)])->first();
        
        if ($exactMatch) {
            $fail("Parish '{$parishName}' already exists. Please select it from the dropdown instead.");
            return;
        }

        // Check for similar names (fuzzy matching)
        $similarParishes = Parish::where(function ($query) use ($parishName) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . Str::lower($parishName) . '%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%' . Str::lower(str_replace(' ', '%', $parishName)) . '%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%' . Str::lower(str_replace(['St.', 'St '], 'Saint ', $parishName)) . '%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%' . Str::lower(str_replace('Saint ', 'St. ', $parishName)) . '%']);
        })->limit(5)->get();

        if ($similarParishes->count() > 0) {
            $similarNames = $similarParishes->pluck('name')->implode(', ');
            $fail("Similar parishes found: {$similarNames}. Please check if you meant one of these or use a different name.");
            return;
        }

        // Additional validation for parish name format
        if (strlen($parishName) < 3) {
            $fail('Parish name must be at least 3 characters long.');
            return;
        }

        if (strlen($parishName) > 255) {
            $fail('Parish name cannot exceed 255 characters.');
            return;
        }

        // Check for invalid characters
        if (!preg_match('/^[a-zA-Z0-9\s\-\'\.\(\)]+$/', $parishName)) {
            $fail('Parish name contains invalid characters. Only letters, numbers, spaces, hyphens, apostrophes, dots, and parentheses are allowed.');
            return;
        }
    }
}
