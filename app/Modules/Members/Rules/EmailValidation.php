<?php

namespace Modules\Members\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailValidation implements ValidationRule
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

        // Basic email format validation
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('The :attribute must be a valid email address.');

            return;
        }

        // Additional validation checks
        $email = strtolower(trim($value));

        // Check email length
        if (strlen($email) > 254) {
            $fail('The :attribute must not exceed 254 characters.');

            return;
        }

        // Split email into local and domain parts
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            $fail('The :attribute must contain exactly one @ symbol.');

            return;
        }

        $localPart = $parts[0];
        $domain = $parts[1];

        // Validate local part
        if (strlen($localPart) > 64) {
            $fail('The local part of :attribute must not exceed 64 characters.');

            return;
        }

        if (strlen($localPart) === 0) {
            $fail('The local part of :attribute cannot be empty.');

            return;
        }

        // Check for valid characters in local part
        if (! preg_match('/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+$/', $localPart)) {
            $fail('The local part of :attribute contains invalid characters.');

            return;
        }

        // Check for consecutive dots in local part
        if (strpos($localPart, '..') !== false) {
            $fail('The local part of :attribute cannot contain consecutive dots.');

            return;
        }

        // Check if local part starts or ends with dot
        if ($localPart[0] === '.' || $localPart[strlen($localPart) - 1] === '.') {
            $fail('The local part of :attribute cannot start or end with a dot.');

            return;
        }

        // Validate domain
        if (strlen($domain) > 253) {
            $fail('The domain part of :attribute must not exceed 253 characters.');

            return;
        }

        if (strlen($domain) === 0) {
            $fail('The domain part of :attribute cannot be empty.');

            return;
        }

        // Check for valid domain format
        if (! preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/', $domain)) {
            $fail('The domain part of :attribute contains invalid characters or format.');

            return;
        }

        // Check for consecutive dots in domain
        if (strpos($domain, '..') !== false) {
            $fail('The domain part of :attribute cannot contain consecutive dots.');

            return;
        }

        // Check if domain starts or ends with dot
        if ($domain[0] === '.' || $domain[strlen($domain) - 1] === '.') {
            $fail('The domain part of :attribute cannot start or end with a dot.');

            return;
        }

        // Check for valid TLD (Top Level Domain)
        $tld = substr($domain, strrpos($domain, '.') + 1);
        if (strlen($tld) < 2) {
            $fail('The domain part of :attribute must have a valid top-level domain (at least 2 characters).');

            return;
        }

        // Check for common disposable email domains (optional - you can customize this list)
        $disposableDomains = [
            '10minutemail.com', 'guerrillamail.com', 'mailinator.com', 'tempmail.org',
            'throwaway.email', 'yopmail.com', 'temp-mail.org', 'sharklasers.com',
        ];

        if (in_array($domain, $disposableDomains)) {
            $fail('The :attribute domain is not allowed. Please use a valid email address.');

            return;
        }
    }
}
