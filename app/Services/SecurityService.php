<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SecurityService
{
    /**
     * Validate file upload security
     */
    public function validateFileUpload(Request $request, string $fieldName): array
    {
        $file = $request->file($fieldName);

        if (!$file) {
            return ['valid' => false, 'error' => 'No file uploaded'];
        }

        // Check file size (max 20MB for gallery photos)
        if ($file->getSize() > 20 * 1024 * 1024) {
            return ['valid' => false, 'error' => 'File size exceeds 20MB limit'];
        }

        // Define allowed file types
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        if (!in_array($extension, $allowedExtensions)) {
            return ['valid' => false, 'error' => 'Invalid file extension'];
        }

        if (!in_array($mimeType, $allowedMimeTypes)) {
            return ['valid' => false, 'error' => 'Invalid file type'];
        }

        // Additional security: Check file signature
        $fileContent = file_get_contents($file->getRealPath());
        if ($this->hasExecutableSignature($fileContent)) {
            return ['valid' => false, 'error' => 'Potentially malicious file detected'];
        }

        return ['valid' => true, 'file' => $file];
    }

    /**
     * Check for executable file signatures
     */
    private function hasExecutableSignature(string $content): bool
    {
        $maliciousSignatures = [
            "\x4D\x5A", // MZ (PE/DOS executable)
            "<?php",
            "<script",
            "javascript:",
            "vbscript:",
        ];

        foreach ($maliciousSignatures as $signature) {
            if (strpos($content, $signature) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Implement rate limiting for API endpoints
     */
    public function checkRateLimit(Request $request, string $key, int $maxAttempts = 60, int $decayMinutes = 1): bool
    {
        $rateLimitKey = $key . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
            $availableAt = RateLimiter::availableAt($rateLimitKey);
            $retryAfter = $availableAt - time();

            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'key' => $key,
                'retry_after' => $retryAfter
            ]);

            return false;
        }

        RateLimiter::hit($rateLimitKey, $decayMinutes * 60);
        return true;
    }

    /**
     * Sanitize user input to prevent XSS
     */
    public function sanitizeInput(string $input): string
    {
        // Remove script tags and other potentially harmful content
        $input = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi', '', $input);
        $input = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi', '', $input);
        $input = preg_replace('/javascript:/gi', '', $input);
        $input = preg_replace('/vbscript:/gi', '', $input);
        $input = preg_replace('/on\w+\s*=/gi', '', $input);

        return trim($input);
    }

    /**
     * Generate secure random token
     */
    public function generateSecureToken(int $length = 32): string
    {
        return Str::random($length);
    }

    /**
     * Validate password strength
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }

        // Check against common passwords
        $commonPasswords = [
            'password',
            '123456',
            '123456789',
            'qwerty',
            'abc123',
            'password123',
            'admin',
            'letmein',
            'welcome',
            'monkey'
        ];

        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = 'Password is too common, please choose a stronger password';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'strength_score' => $this->calculatePasswordStrength($password)
        ];
    }

    /**
     * Calculate password strength score (0-100)
     */
    private function calculatePasswordStrength(string $password): int
    {
        $score = 0;
        $length = strlen($password);

        // Length score
        if ($length >= 8) $score += 20;
        if ($length >= 12) $score += 10;
        if ($length >= 16) $score += 10;

        // Character variety
        if (preg_match('/[a-z]/', $password)) $score += 15;
        if (preg_match('/[A-Z]/', $password)) $score += 15;
        if (preg_match('/\d/', $password)) $score += 15;
        if (preg_match('/[^A-Za-z0-9]/', $password)) $score += 15;

        return min(100, $score);
    }

    /**
     * Log security events
     */
    public function logSecurityEvent(string $event, array $context = []): void
    {
        Log::channel('security')->warning($event, array_merge($context, [
            'timestamp' => now()->toISOString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => Auth::id(),
        ]));
    }

    /**
     * Check for SQL injection patterns
     */
    public function detectSQLInjection(string $input): bool
    {
        $patterns = [
            '/(\s|^)(union|select|insert|update|delete|drop|create|alter|exec|script)(\s|$)/i',
            '/(\s|^)(or|and)(\s+\d+\s*=\s*\d+|\s+["\'].*["\'])/i',
            '/["\'](\s*;\s*|\s+or\s+|\s+and\s+)/i',
            '/\b(exec|execute|sp_|xp_)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                $this->logSecurityEvent('SQL Injection attempt detected', [
                    'input' => $input,
                    'pattern' => $pattern
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Validate CSRF token
     */
    public function validateCSRF(Request $request): bool
    {
        $sessionToken = $request->session()->token();
        $requestToken = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');

        if (!$sessionToken || !$requestToken) {
            return false;
        }

        return hash_equals($sessionToken, $requestToken);
    }

    /**
     * Generate Content Security Policy headers
     */
    public function getCSPHeaders(): array
    {
        return [
            "Content-Security-Policy" => "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
                "font-src 'self' https://fonts.gstatic.com; " .
                "img-src 'self' data: https:; " .
                "connect-src 'self'; " .
                "frame-ancestors 'none'; " .
                "base-uri 'self'; " .
                "form-action 'self';",
            "X-Content-Type-Options" => "nosniff",
            "X-Frame-Options" => "DENY",
            "X-XSS-Protection" => "1; mode=block",
            "Referrer-Policy" => "strict-origin-when-cross-origin",
        ];
    }
}
