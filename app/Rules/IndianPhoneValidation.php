<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IndianPhoneValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Allow empty/null values
        }

        // Remove all non-digit characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $value);

        // Indian Mobile Number: 10 digits starting with 6, 7, 8, 9
        $mobilePattern = '/^[6-9]\d{9}$/';

        // Indian Landline Number: 10-11 digits (with or without STD code)
        // STD codes: 2-4 digits + 6-8 digits local number
        $landlinePattern = '/^(?:[2-4]\d{1,3})?\d{6,8}$/';

        // Check if it's a valid mobile number
        if (preg_match($mobilePattern, $cleanNumber)) {
            return; // Valid mobile number
        }

        // Check if it's a valid landline number
        if (preg_match($landlinePattern, $cleanNumber) && strlen($cleanNumber) >= 10 && strlen($cleanNumber) <= 11) {
            return; // Valid landline number
        }

        // If neither mobile nor landline pattern matches
        $fail('The :attribute must be a valid Indian mobile number (10 digits starting with 6, 7, 8, 9) or landline number (10-11 digits with STD code).');
    }
}
