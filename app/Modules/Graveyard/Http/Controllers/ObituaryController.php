<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\ObituaryPage;
use Modules\Graveyard\Models\ObituaryCondolence;
use Modules\Graveyard\Services\ObituaryBackgroundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ObituaryController extends Controller
{
    protected ObituaryBackgroundService $backgroundService;

    public function __construct(ObituaryBackgroundService $backgroundService)
    {
        $this->backgroundService = $backgroundService;
    }

    /**
     * Display the public obituary page
     */
    public function show(string $uuid)
    {
        $obituary = ObituaryPage::where('uuid', $uuid)
            ->where('is_public', true)
            ->where('is_active', true)
            ->with([
                'permanentGraveBooking.validMember',
                'temporaryGraveBooking',
                'condolences' => function ($query) {
                    $query->where('is_approved', true)
                        ->orderBy('created_at', 'desc');
                }
            ])
            ->first();

        if (!$obituary) {
            abort(404, 'Obituary page not found or not public');
        }

        // Increment view count
        $obituary->increment('view_count');

        // Get deceased person's name
        $deceasedName = $this->getDeceasedName($obituary);

        // Get background style using the background service
        $backgroundStyle = [];
        if ($obituary->background_style) {
            $backgroundStyle = $this->backgroundService->getThemeStyle($obituary->background_style);
        }

        // Debug logging with detailed type information
        $canSubmitCondolence = $obituary->allow_condolences; // && $obituary->service_type === 'premium';

        return Inertia::render('Public/Obituary/Show', [
            'obituary' => $obituary,
            'deceasedName' => $deceasedName,
            'condolences' => $obituary->condolences, // Explicitly pass approved condolences
            'canSubmitCondolence' => $canSubmitCondolence,
            'canShareMemory' => $obituary->allow_memory_sharing, // && $obituary->service_type === 'premium',
            'backgroundStyle' => $backgroundStyle, // Pass the processed background style
        ]);
    }

    /**
     * Store a condolence message
     */
    public function storeCondolence(Request $request, string $uuid)
    {
        $obituary = ObituaryPage::where('uuid', $uuid)
            ->where('is_public', true)
            ->where('is_active', true)
            ->first();

        if (!$obituary) {
            return response()->json(['error' => 'Obituary page not found'], 404);
        }

        // Check if condolences are enabled for this obituary
        if (!$obituary->allow_condolences) { // || $obituary->service_type !== 'premium'
            return response()->json(['error' => 'Condolences are not enabled for this obituary page'], 403);
        }

        // Rate limiting - prevent spam
        $key = 'condolence-submission:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => "Too many condolence submissions. Please try again in {$seconds} seconds."
            ], 429);
        }

        // Validate the condolence data
        $validator = Validator::make($request->all(), [
            'visitor_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-\'\.]+$/', // Only letters, spaces, hyphens, apostrophes, dots
            ],
            'visitor_email' => [
                'nullable',
                'email',
                'max:100',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'visitor_phone' => [
                'required',
                'string',
                'regex:/^[6-9]\d{9}$/', // Indian mobile number format: starts with 6,7,8,9 and exactly 10 digits
                function ($attribute, $value, $fail) use ($obituary) {
                    // Check for duplicate phone number for this obituary
                    $exists = ObituaryCondolence::where('obituary_page_id', $obituary->id)
                        ->where('visitor_phone', $value)
                        ->exists();

                    if ($exists) {
                        $fail('A condolence has already been submitted with this phone number for this obituary.');
                    }
                },
            ],
            'relationship' => [
                'nullable',
                'string',
                'max:50',
                'in:family,friend,colleague,neighbor,acquaintance,other'
            ],
            'message' => [
                'required',
                'string',
                'min:10',
                'max:500',
                'regex:/^[^<>{}]*$/' // No HTML/script tags
            ],
        ], [
            'visitor_name.required' => 'Please enter your name',
            'visitor_name.regex' => 'Name can only contain letters, spaces, hyphens, apostrophes, and dots',
            'visitor_email.email' => 'Please enter a valid email address',
            'visitor_phone.required' => 'Phone number is required to submit a condolence',
            'visitor_phone.regex' => 'Please enter a valid Indian mobile number (10 digits starting with 6, 7, 8, or 9)',
            'message.required' => 'Please enter your condolence message',
            'message.min' => 'Your message must be at least 10 characters long',
            'message.max' => 'Your message cannot exceed 500 characters',
            'message.regex' => 'Your message contains invalid characters',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Content filtering for inappropriate content
        $message = $validator->validated()['message'];
        if ($this->containsInappropriateContent($message)) {
            return response()->json([
                'error' => 'Your message contains inappropriate content and cannot be submitted.'
            ], 422);
        }

        // Create the condolence record
        try {
            $condolence = ObituaryCondolence::create([
                'obituary_page_id' => $obituary->id,
                'visitor_name' => $validator->validated()['visitor_name'],
                'visitor_email' => $validator->validated()['visitor_email'] ?? null,
                'visitor_phone' => $validator->validated()['visitor_phone'] ?? null,
                'relationship' => $validator->validated()['relationship'] ?? null,
                'message' => $message,
                'visitor_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'is_approved' => false, // Require admin approval
                'submitted_at' => now(),
            ]);

            // Increment rate limiter
            RateLimiter::hit($key, 300); // 5 minutes

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your condolence. It will be reviewed and published shortly.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save condolence: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to submit condolence. Please try again later.'
            ], 500);
        }
    }

    /**
     * Download QR code for the obituary
     */
    public function downloadQrCode(string $uuid)
    {
        $obituary = ObituaryPage::where('uuid', $uuid)
            ->where('is_public', true)
            ->where('is_active', true)
            ->first();

        if (!$obituary || !$obituary->qr_code_path) {
            abort(404, 'QR code not found');
        }

        // Increment QR scan count
        $obituary->increment('qr_scan_count');

        // Return the QR code file for download
        if (Storage::disk('public')->exists($obituary->qr_code_path)) {
            $filePath = Storage::disk('public')->path($obituary->qr_code_path);
            $fileName = 'obituary-qr-' . $obituary->uuid . '.png';

            return response()->download($filePath, $fileName);
        }

        abort(404, 'QR code file not found');
    }

    /**
     * Preview obituary page (admin only)
     */
    public function preview(string $uuid)
    {
        // Check if user is authenticated
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Authentication required');
        }

        $obituary = ObituaryPage::where('uuid', $uuid)
            ->with([
                'permanentGraveBooking.validMember',
                'temporaryGraveBooking',
                'condolences' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])
            ->first();

        if (!$obituary) {
            abort(404, 'Obituary page not found');
        }

        // Use policy to check preview permission
        $this->authorize('preview', $obituary);

        $deceasedName = $this->getDeceasedName($obituary);

        // Get background style using the background service
        $backgroundStyle = [];
        if ($obituary->background_style) {
            $backgroundStyle = $this->backgroundService->getThemeStyle($obituary->background_style);
        }

        // For preview, show condolences if they would be available publicly
        $wouldShowCondolences = $obituary->allow_condolences; // && $obituary->service_type === 'premium';

        return Inertia::render('Public/Obituary/Show', [
            'obituary' => $obituary,
            'deceasedName' => $deceasedName,
            'condolences' => $obituary->condolences, // Show condolences in preview
            'isPreview' => true,
            'canSubmitCondolence' => $wouldShowCondolences, // Show what it would look like publicly
            'canShareMemory' => $obituary->allow_memory_sharing, // && $obituary->service_type === 'premium',
            'backgroundStyle' => $backgroundStyle, // Pass the processed background style
        ]);
    }

    /**
     * Get deceased person's name from obituary
     */
    private function getDeceasedName(ObituaryPage $obituary): string
    {
        if ($obituary->permanentGraveBooking) {
            $member = $obituary->permanentGraveBooking->validMember;
            return trim($member->first_name . ' ' . $member->last_name);
        }

        if ($obituary->temporaryGraveBooking) {
            $booking = $obituary->temporaryGraveBooking;
            return trim($booking->dead_first_name . ' ' . $booking->dead_last_name);
        }

        return 'Unknown';
    }

    /**
     * Check for inappropriate content in message
     */
    private function containsInappropriateContent(string $message): bool
    {
        // List of inappropriate words/phrases (extend as needed)
        $inappropriateWords = [
            // Add your content filter words here
            'spam',
            'scam',
            'fake',
            'advertisement',
            'buy now',
            'click here',
            // Add profanity or inappropriate terms as needed
        ];

        $lowerMessage = strtolower($message);

        foreach ($inappropriateWords as $word) {
            if (strpos($lowerMessage, strtolower($word)) !== false) {
                return true;
            }
        }

        // Check for excessive capital letters (shouting)
        $upperCount = preg_match_all('/[A-Z]/', $message);
        $totalCount = strlen($message);
        if ($totalCount > 20 && ($upperCount / $totalCount) > 0.7) {
            return true;
        }

        // Check for excessive repetition
        if (preg_match('/(.)\1{10,}/', $message)) {
            return true;
        }

        return false;
    }
}
