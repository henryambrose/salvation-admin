<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class NotFutureDate implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Allow empty/null values
        }

        try {
            $inputDate = Carbon::parse($value);
            $today = Carbon::today();

            if ($inputDate->gt($today)) {
                $fail('The :attribute cannot be in the future.');
            }
        } catch (\Exception $e) {
            $fail('The :attribute must be a valid date.');
        }
    }
} 