<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Graveyard\Http\Controllers\ObituaryController;
use Modules\Graveyard\Http\Controllers\ObituaryManagementController;
use Modules\Graveyard\Http\Controllers\ObituaryBackgroundThemeController;
use Modules\Graveyard\Http\Controllers\ObituaryManagerController;
use Modules\Graveyard\Http\Controllers\ObituaryPlanController;

// ADMIN ROUTES (Protected with auth middleware)
Route::middleware(['web', 'auth', 'verified', 'nocache'])->group(function () {

    // Obituary Plans Management
    Route::resource('/obituary-plans', ObituaryPlanController::class)
        ->names('obituary-plans');
    Route::post('/obituary-plans/{id}/restore', [ObituaryPlanController::class, 'restore'])
        ->name('obituary-plans.restore');

    // Obituary Background Themes Management
    Route::prefix('/obituary-background-themes')->name('obituary-background-themes.')->group(function () {
        Route::get('/', [ObituaryBackgroundThemeController::class, 'index'])->name('index');
        Route::get('/create', [ObituaryBackgroundThemeController::class, 'create'])->name('create');
        Route::post('/', [ObituaryBackgroundThemeController::class, 'store'])->name('store');
        Route::get('/{theme}', [ObituaryBackgroundThemeController::class, 'show'])->name('show');
        Route::get('/{theme}/edit', [ObituaryBackgroundThemeController::class, 'edit'])->name('edit');
        Route::put('/{theme}', [ObituaryBackgroundThemeController::class, 'update'])->name('update');
        Route::delete('/{theme}', [ObituaryBackgroundThemeController::class, 'destroy'])->name('destroy');
        Route::patch('/{theme}/toggle-status', [ObituaryBackgroundThemeController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{theme}/preview', [ObituaryBackgroundThemeController::class, 'preview'])->name('preview');
    });

    // Obituary Managers Management
    Route::prefix('/obituary-managers')->name('obituary-managers.')->group(function () {
        Route::get('/', [ObituaryManagerController::class, 'index'])->name('index');
        Route::get('/create', [ObituaryManagerController::class, 'create'])->name('create');
        Route::post('/', [ObituaryManagerController::class, 'store'])->name('store');
        Route::get('/{obituaryManager}', [ObituaryManagerController::class, 'show'])->name('show');
        Route::get('/{obituaryManager}/edit', [ObituaryManagerController::class, 'edit'])->name('edit');
        Route::put('/{obituaryManager}', [ObituaryManagerController::class, 'update'])->name('update');
        Route::delete('/{obituaryManager}', [ObituaryManagerController::class, 'destroy'])->name('destroy');
        Route::patch('/{obituaryManager}/toggle-active', [ObituaryManagerController::class, 'toggleActive'])->name('toggle-active');
    });

    // Obituary Management (Admin Routes)
    Route::prefix('/obituaries')->name('obituaries.')->group(function () {
        Route::get('/create', [ObituaryManagementController::class, 'create'])->name('create');
        Route::get('/cleanup', [ObituaryManagementController::class, 'cleanupPage'])->name('cleanup');

        Route::get('/', [ObituaryManagementController::class, 'index'])->name('index');
        Route::post('/', [ObituaryManagementController::class, 'store'])->name('store');
        Route::get('/{obituary}', [ObituaryManagementController::class, 'show'])->name('show');
        Route::get('/{obituary}/edit', [ObituaryManagementController::class, 'edit'])->name('edit');
        Route::put('/{obituary}', [ObituaryManagementController::class, 'update'])->name('update');
        Route::delete('/{obituary}', [ObituaryManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{uuid}/restore', [ObituaryManagementController::class, 'restore'])->name('restore');

        // File cleanup management
        Route::post('/cleanup/scan', [ObituaryManagementController::class, 'scanOrphanedFiles'])->name('cleanup.scan');
        Route::post('/cleanup/execute', [ObituaryManagementController::class, 'executeCleanup'])->name('cleanup.execute');

        // Condolence management
        Route::get('/condolences/manage', [ObituaryManagementController::class, 'condolences'])->name('condolences.manage');
        Route::patch('/condolences/{condolence}/approve', [ObituaryManagementController::class, 'approveCondolence'])->name('condolences.approve');
        Route::patch('/condolences/{condolence}/reject', [ObituaryManagementController::class, 'rejectCondolence'])->name('condolences.reject');

        // Custom QR code generation
        Route::post('/{obituary}/generate-qr', [ObituaryManagementController::class, 'generateCustomQr'])->name('qr.generate');
        Route::get('/{obituary:uuid}/qr-download', [ObituaryManagementController::class, 'downloadQrCode'])->name('qr.download-admin');

        // Payment processing
        Route::post('/{obituary}/payment', [ObituaryManagementController::class, 'processPayment'])->name('payment.process');
        Route::get('/payments/{payment}/receipt', [ObituaryManagementController::class, 'downloadReceipt'])->name('payment.receipt');

        // Image management
        Route::delete('/{obituary}/profile-image', [ObituaryManagementController::class, 'removeProfileImage'])->name('images.remove-profile');
        Route::delete('/{obituary}/gallery-image', [ObituaryManagementController::class, 'removeGalleryImage'])->name('images.remove-gallery');
        Route::delete('/{obituary}/gallery-images-bulk', [ObituaryManagementController::class, 'removeGalleryImagesBulk'])->name('images.remove-gallery-bulk');
        Route::delete('/{obituary}/audio-message', [ObituaryManagementController::class, 'removeAudioMessage'])->name('audio.remove');
        Route::post('/{obituary}/cleanup-files', [ObituaryManagementController::class, 'cleanupOrphanedFiles'])->name('files.cleanup');

        // Expiration management
        Route::post('/{obituary}/extend-expiration', [ObituaryManagementController::class, 'extendExpiration'])->name('expiration.extend');

        // Publishing management
        Route::post('/{obituary}/publish', [ObituaryManagementController::class, 'publish'])->name('publish');
        Route::post('/{obituary}/unpublish', [ObituaryManagementController::class, 'unpublish'])->name('unpublish');

        // Text rephrasing API
        Route::post('/rephrase-text', [ObituaryManagementController::class, 'rephraseText'])->name('rephrase-text');

        // Plan upgrade
        Route::post('/{obituary}/upgrade-to-premium', [ObituaryManagementController::class, 'upgradeToPremium'])->name('upgrade-to-premium');

        // External member management
        Route::post('/{obituary}/grant-external-access', [ObituaryManagementController::class, 'grantExternalAccess'])->name('grant-external-access');
        Route::delete('/{obituary}/revoke-external-access', [ObituaryManagementController::class, 'revokeExternalAccess'])->name('revoke-external-access');
        Route::patch('/{obituary}/toggle-external-access', [ObituaryManagementController::class, 'toggleExternalAccess'])->name('toggle-external-access');
        Route::patch('/{obituary}/reset-external-password', [ObituaryManagementController::class, 'resetExternalPassword'])->name('reset-external-password');
    });
});

// API Routes for integrations
Route::prefix('api/obituary')->name('api.obituary.')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/obituary-background-themes', [ObituaryBackgroundThemeController::class, 'api'])
        ->name('api.obituary-background-themes');

    // Quick create from booking confirmation
    Route::post('/create-from-booking', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'booking_type' => 'required|in:permanent,temporary',
            'booking_id' => 'required|integer',
            'service_type' => 'required|in:basic,premium',
        ]);

        $obituaryService = app(\Modules\Graveyard\Services\ObituaryService::class);

        if ($validated['booking_type'] === 'permanent') {
            $booking = \Modules\Graveyard\Models\PermanentGraveBooking::findOrFail($validated['booking_id']);
        } else {
            $booking = \Modules\Graveyard\Models\TemporaryGraveBooking::findOrFail($validated['booking_id']);
        }

        $obituary = $obituaryService->createObituaryFromBooking($booking, [
            'service_type' => $validated['service_type']
        ]);

        return response()->json([
            'obituary' => $obituary,
            'public_url' => route('obituary.show', $obituary->uuid),
            'qr_code_url' => $obituary->qr_code_path ? asset('storage/' . $obituary->qr_code_path) : null,
        ]);
    })->name('create-from-booking');

    // Get obituary stats
    Route::get('/{obituary}/stats', function (\Modules\Graveyard\Models\ObituaryPage $obituary) {
        $obituaryService = app(\Modules\Graveyard\Services\ObituaryService::class);
        return response()->json($obituaryService->getObituaryStats($obituary));
    })->name('stats');
});

// PUBLIC ROUTES (No authentication required)
Route::prefix('obituary')->name('obituary.')->group(function () {
    // Public obituary page view
    Route::get('/{uuid}', [ObituaryController::class, 'show'])->name('show');

    // Submit condolence (no auth required)
    Route::post('/{uuid}/condolence', [ObituaryController::class, 'storeCondolence'])->name('condolence.store');

    // Download QR code (no auth required but rate limited)
    Route::get('/{uuid}/qr-download', [ObituaryController::class, 'downloadQrCode'])
        ->name('qr.download')
        ->middleware('throttle:10,1'); // 10 downloads per minute

    // Preview (admin only - will be protected in controller)
    Route::get('/{uuid}/preview', [ObituaryController::class, 'preview'])->name('preview');

    // Gallery image viewer with proper favicon and HTML layout
    Route::get('/gallery/{filename}', [ObituaryController::class, 'galleryImage'])->name('gallery.image');
});

// Alternative public obituary route with inline logic (redundant - choose one approach)
Route::get('/obituary/{uuid}', function (string $uuid) {
    $obituary = \Modules\Graveyard\Models\ObituaryPage::with(['permanentGraveBooking.validMember', 'temporaryGraveBooking', 'condolences', 'obituaryPlan'])
        ->where('uuid', $uuid)
        ->first();

    // If obituary doesn't exist at all
    if (!$obituary) {
        return Inertia::render('Public/Obituary/NotFound', [
            'message' => 'The requested obituary page could not be found.',
        ]);
    }

    // Check if obituary is inactive
    if (!$obituary->is_active) {
        return Inertia::render('Public/Obituary/PaymentPending', [
            'message' => 'This obituary page has been deactivated.',
            'paymentStatus' => 'inactive',
            'obituaryName' => $obituary->deceased_name,
            'issueType' => 'inactive'
        ]);
    }

    // Check if obituary is not public
    if (!$obituary->is_public) {
        return Inertia::render('Public/Obituary/PaymentPending', [
            'message' => 'This obituary page is set to private and cannot be accessed publicly.',
            'paymentStatus' => 'private',
            'obituaryName' => $obituary->deceased_name,
            'issueType' => 'private'
        ]);
    }

    // First check payment status - this is the primary blocker
    if (!$obituary->hasCompletedPayment()) {
        $paymentStatus = $obituary->getPaymentStatus();
        $message = match ($paymentStatus) {
            'pending' => 'This obituary page is not available yet. Payment is still pending.',
            'partial' => 'This obituary page is not available yet. Payment is partially completed.',
            null => 'This obituary page is not available yet. No payment information found.',
            default => "This obituary page is not available yet. Payment status: {$paymentStatus}."
        };

        return Inertia::render('Public/Obituary/PaymentPending', [
            'message' => $message,
            'paymentStatus' => $paymentStatus,
            'obituaryName' => $obituary->deceased_name,
            'issueType' => 'payment'
        ]);
    }

    // If payment is complete but not published - this is an admin/review issue
    if (!$obituary->is_published) {
        return Inertia::render('Public/Obituary/PaymentPending', [
            'message' => 'This obituary page is under review and will be published shortly. Payment has been received.',
            'paymentStatus' => 'paid_but_unpublished',
            'obituaryName' => $obituary->deceased_name,
            'issueType' => 'review'
        ]);
    }

    // Check if obituary has expired based on plan duration
    if ($obituary->hasExpired()) {
        return Inertia::render('Public/Obituary/PaymentPending', [
            'message' => 'This obituary page has expired. Please contact the administrator to renew.',
            'paymentStatus' => 'expired',
            'obituaryName' => $obituary->deceased_name,
            'issueType' => 'expired',
            'planName' => $obituary->obituaryPlan?->name
        ]);
    }

    // Increment view count
    $obituary->increment('view_count');

    // Get deceased person's name
    $deceasedName = '';
    if ($obituary->permanentGraveBooking) {
        $member = $obituary->permanentGraveBooking->validMember;
        $deceasedName = trim($member->first_name . ' ' . $member->last_name);
    } elseif ($obituary->temporaryGraveBooking) {
        $booking = $obituary->temporaryGraveBooking;
        $deceasedName = trim($booking->dead_first_name . ' ' . $booking->dead_last_name);
    } else {
        $deceasedName = 'Unknown';
    }

    return Inertia::render('Public/Obituary/Show', [
        'obituary' => $obituary,
        'deceasedName' => $deceasedName,
        'condolences' => $obituary->condolences, // Explicitly pass approved condolences
        'canSubmitCondolence' => $obituary->allow_condolences,
        'canShareMemory' => $obituary->allow_memory_sharing,
        'backgroundStyle' => \Modules\Graveyard\Services\BackgroundService::getBackgroundStyle($obituary->background_style ?: 'plain'),
        'plan' => $obituary->obituaryPlan,
        'hasExpired' => $obituary->hasExpired(),
    ]);
})->name('obituary.show.alternative');

// Public obituary condolence submission (no authentication required)
Route::post('/obituary/{uuid}/condolences', function (string $uuid, \Illuminate\Http\Request $request) {
    $obituary = \Modules\Graveyard\Models\ObituaryPage::where('uuid', $uuid)
        ->where('is_active', true)
        ->where('is_public', true)
        ->where('is_published', true)
        ->where('allow_condolences', true)
        ->firstOrFail();

    // Check if obituary can be accessed publicly (payment completed)
    if (!$obituary->canBeAccessedPublicly()) {
        return response()->json([
            'error' => 'Condolences cannot be submitted. Payment is not completed.',
            'payment_status' => $obituary->getPaymentStatus()
        ], 403);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'message' => 'required|string|max:1000',
    ]);

    \Modules\Graveyard\Models\ObituaryCondolence::create([
        'obituary_page_id' => $obituary->id,
        'name' => $request->name,
        'message' => $request->message,
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

    return response()->json(['message' => 'Condolence submitted successfully']);
})->name('obituary.condolences.store');

// EXTERNAL MANAGER ROUTES (Obituary Manager Authentication)
Route::prefix('obituary/{uuid}/manage')->name('obituary.external.')->group(function () {
    // Login routes (no auth required)
    Route::get('/login', [ObituaryManagerController::class, 'showLogin'])->name('login');
    Route::post('/login', [ObituaryManagerController::class, 'login'])->name('login.submit');
});

// Protected routes (obituary manager auth required)
Route::prefix('obituary/{uuid}/manage')->name('obituary.external.')->middleware(['web', 'auth:external'])->group(function () {
    Route::get('/dashboard', [ObituaryManagerController::class, 'dashboard'])->name('dashboard');
    Route::get('/edit', [ObituaryManagerController::class, 'editObituary'])->name('edit');
    Route::put('/update', [ObituaryManagerController::class, 'updateObituary'])->name('update');
    Route::get('/condolences', [ObituaryManagerController::class, 'condolences'])->name('condolences');
    Route::patch('/condolences/{condolence}/approve', [ObituaryManagerController::class, 'approveCondolence'])->name('condolences.approve');
    Route::patch('/condolences/{condolence}/reject', [ObituaryManagerController::class, 'rejectCondolence'])->name('condolences.reject');
    Route::post('/logout', [ObituaryManagerController::class, 'logout'])->name('logout');
});
