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

        // Check if it starts with 0 or 1 (invalid Aadhar)
        if (in_array($cleanAadhar[0], ['0', '1'])) {
            $fail('The :attribute cannot start with 0 or 1.');
            return;
        }

        // Validate using Verhoeff algorithm (simplified check)
        if (!$this->isValidAadhar($cleanAadhar)) {
            $fail('The :attribute appears to be invalid. Please check the number.');
            return;
        }
    }

    /**
     * Simplified Aadhar validation using Verhoeff algorithm
     */
    private function isValidAadhar(string $aadhar): bool
    {
        // Verhoeff algorithm tables
        $multiplication = [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
            [2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
            [3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
            [4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
            [5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
            [6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
            [7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
            [8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
            [9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
        ];

        $permutation = [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
            [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
            [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
            [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
            [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
            [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
            [7, 0, 4, 6, 9, 1, 3, 2, 5, 8]
        ];

        $inverse = [0, 4, 3, 2, 1, 5, 6, 7, 8, 9];

        // Convert string to array of integers
        $digits = array_map('intval', str_split($aadhar));

        // Check if we have exactly 12 digits
        if (count($digits) !== 12) {
            return false;
        }

        // Apply Verhoeff algorithm
        $c = 0;
        for ($i = 0; $i < 11; $i++) {
            $c = $multiplication[$c][$permutation[($i + 1) % 8][$digits[10 - $i]]];
        }

        return $inverse[$c] === $digits[11];
    }
} 