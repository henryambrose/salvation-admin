<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AadharValidation implements ValidationRule
{
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

        // Remove any spaces, dashes, or other separators
        $cleanAadhar = preg_replace('/[^0-9]/', '', $value);

        // Check if it's exactly 12 digits
        if (strlen($cleanAadhar) !== 12) {
            $fail('The :attribute must be exactly 12 digits.');

            return;
        }

        // Check if it's all zeros (invalid Aadhar)
        if ($cleanAadhar === '000000000000') {
            $fail('The :attribute cannot be all zeros.');

            return;
        }

        // Note: Aadhar numbers CAN start with 0 or 1
        // The previous rule was incorrect

        // Basic pattern check (12 digits, not all same)
        if (preg_match('/^(\d)\1{11}$/', $cleanAadhar)) {
            $fail('The :attribute cannot be all repeated digits.');

            return;
        }
    }
}
