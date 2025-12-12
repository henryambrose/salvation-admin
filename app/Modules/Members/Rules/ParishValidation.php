<?php

namespace Modules\Members\Rules;

use Modules\Members\Models\Parish;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Support\Str;

class ParishValidation implements ValidationRule, DataAwareRule
{
    protected $fieldName;
    protected $data = [];
    protected $originalValue;

    public function __construct($fieldName = null, $originalValue = null)
    {
        $this->fieldName = $fieldName;
        $this->originalValue = $originalValue;
    }

    /**
     * Set the data under validation.
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
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

        // Get the corresponding parish_id field name
        // e.g., if attribute is 'baptism_parish', look for 'baptism_parish_id'
        $parishIdField = $attribute . '_id';

        // If a parish_id is set, it means the user selected from dropdown
        // In this case, skip validation as we WANT to use the existing parish
        if (!empty($this->data[$parishIdField])) {
            return; // Skip validation when parish is selected from dropdown
        }

        $parishName = trim($value);

        // Skip validation if the value hasn't changed (editing existing member)
        // This prevents validation errors when editing a member who already has this parish
        if (!empty($this->originalValue) && Str::lower($parishName) === Str::lower(trim($this->originalValue))) {
            return; // Skip validation when value is unchanged
        }

        // Note: Duplicate parish validation removed - users can enter custom parish names
        // even if they exist in the dropdown. The dropdown is for convenience only.

        // Check for similar names (fuzzy matching) - informational only
        $similarParishes = Parish::where(function ($query) use ($parishName) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . Str::lower($parishName) . '%'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%' . Str::lower(str_replace(' ', '%', $parishName)) . '%'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%' . Str::lower(str_replace(['St.', 'St '], 'Saint ', $parishName)) . '%'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%' . Str::lower(str_replace('Saint ', 'St. ', $parishName)) . '%']);
        })->limit(5)->get();

        // Note: Similar parish warnings removed - users have full freedom to enter custom parish names
        // The validation now only checks for format and length requirements

        // Additional validation for parish name format
        if (strlen($parishName) < 3) {
            $fail('Parish name must be at least 3 characters long.');

            return;
        }

        if (strlen($parishName) > 255) {
            $fail('Parish name cannot exceed 255 characters.');

            return;
        }

        // Check for invalid characters - allow most printable characters except control characters
        // This allows for multilingual parish names and common punctuation
        if (! preg_match('/^[\p{L}\p{N}\s\p{P}]+$/u', $parishName)) {
            $fail('Parish name contains invalid characters. Please use only letters, numbers, spaces, and common punctuation marks.');

            return;
        }

        // Additional check to prevent obviously problematic characters
        if (preg_match('/[<>{}[\]\\|]/', $parishName)) {
            $fail('Parish name contains invalid characters. Please avoid using <, >, {, }, [, ], \\, or | characters.');

            return;
        }
    }
}
