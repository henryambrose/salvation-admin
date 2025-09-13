<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Graveyard\Http\Controllers\DashboardController;

use Modules\Graveyard\Http\Controllers\GraveController;
use Modules\Graveyard\Http\Controllers\PermanentGraveController;
use Modules\Graveyard\Http\Controllers\TemporaryGraveController;
use Modules\Graveyard\Http\Controllers\NicheController;
use Modules\Graveyard\Http\Controllers\ValidMemberController;
use Modules\Graveyard\Http\Controllers\ServiceTypeController;
use Modules\Graveyard\Http\Controllers\PermanentGraveBookingController;
use Modules\Graveyard\Http\Controllers\TemporaryGraveBookingController;
use Modules\Graveyard\Http\Controllers\NicheTransferController;
use Modules\Graveyard\Http\Controllers\PaymentController;
use Modules\Graveyard\Http\Controllers\GraveCategoryController;
use Modules\Graveyard\Http\Controllers\ObituaryController;
use Modules\Graveyard\Http\Controllers\ObituaryManagementController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::middleware(['auth', 'verified', 'nocache'])->group(function () {

    // Graveyard Dashboard
    Route::get('/graveyard', [DashboardController::class, 'index'])->name('graveyard.dashboard');


    // Graves Management
    Route::prefix('graveyard/graves')->name('graveyard.graves.')->group(function () {
        Route::get('/', [GraveController::class, 'index'])->name('index');
        Route::post('/', [GraveController::class, 'store'])->name('store');
        Route::get('/{grave}', [GraveController::class, 'show'])->name('show');
        Route::put('/{grave}', [GraveController::class, 'update'])->name('update');
        Route::delete('/{grave}', [GraveController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [GraveController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [GraveController::class, 'forceDelete'])->name('force-delete');
    });

    // Niches Management
    Route::prefix('graveyard/niches')->name('graveyard.niches.')->group(function () {
        Route::get('/', [NicheController::class, 'index'])->name('index');
        Route::get('/create', [NicheController::class, 'create'])->name('create');
        Route::post('/', [NicheController::class, 'store'])->name('store');
        Route::get('/search-members', [NicheController::class, 'searchMembers'])->name('search-members');
        Route::get('/{niche}', [NicheController::class, 'show'])->name('show');
        Route::get('/{niche}/edit', [NicheController::class, 'edit'])->name('edit');
        Route::put('/{niche}', [NicheController::class, 'update'])->name('update');
        Route::delete('/{niche}', [NicheController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [NicheController::class, 'restore'])->name('restore');
    });


    // Permanent Graves Management
    Route::prefix('graveyard/permanent-graves')->name('graveyard.permanent-graves.')->group(function () {
        Route::get('/', [PermanentGraveController::class, 'index'])->name('index');
        Route::get('/create', [PermanentGraveController::class, 'create'])->name('create');
        Route::post('/', [PermanentGraveController::class, 'store'])->name('store');
        Route::get('/search-members', [PermanentGraveController::class, 'searchMembers'])->name('search-members');
        Route::post('/add-valid-member', [PermanentGraveBookingController::class, 'addValidMember'])->name('add-valid-member');
        Route::get('/{permanentGrave}', [PermanentGraveController::class, 'show'])->name('show');
        Route::get('/{permanentGrave}/edit', [PermanentGraveController::class, 'edit'])->name('edit');
        Route::put('/{permanentGrave}', [PermanentGraveController::class, 'update'])->name('update');
        Route::delete('/{permanentGrave}', [PermanentGraveController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [PermanentGraveController::class, 'restore'])->name('restore');
    });

    // Temporary Graves Management
    Route::prefix('graveyard/temporary-graves')->name('graveyard.temporary-graves.')->group(function () {
        Route::get('/', [TemporaryGraveController::class, 'index'])->name('index');
        Route::get('/create', [TemporaryGraveController::class, 'create'])->name('create');
        Route::post('/', [TemporaryGraveController::class, 'store'])->name('store');
        Route::get('/search-members', [TemporaryGraveController::class, 'searchMembers'])->name('search-members');
        Route::get('/{temporaryGrave}', [TemporaryGraveController::class, 'show'])->name('show');
        Route::get('/{temporaryGrave}/edit', [TemporaryGraveController::class, 'edit'])->name('edit');
        Route::put('/{temporaryGrave}', [TemporaryGraveController::class, 'update'])->name('update');
        Route::delete('/{temporaryGrave}', [TemporaryGraveController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [TemporaryGraveController::class, 'restore'])->name('restore');
    });

    // Valid Member Management
    Route::prefix('graveyard/valid-members')->name('graveyard.valid-members.')->group(function () {
        Route::get('/', [ValidMemberController::class, 'index'])->name('index');
        Route::get('/create', [ValidMemberController::class, 'create'])->name('create');
        Route::post('/', [ValidMemberController::class, 'store'])->name('store');
        Route::get('/search-members', [ValidMemberController::class, 'searchMembers'])->name('search-members');
        Route::post('/search-graves', [ValidMemberController::class, 'searchGraves'])->name('search-graves');
        Route::get('/{validMember}', [ValidMemberController::class, 'show'])->name('show');
        Route::get('/{validMember}/edit', [ValidMemberController::class, 'edit'])->name('edit');
        Route::put('/{validMember}', [ValidMemberController::class, 'update'])->name('update');
        Route::delete('/{validMember}', [ValidMemberController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [ValidMemberController::class, 'restore'])->name('restore');
    });

    // Service Types Management
    Route::prefix('graveyard/service-types')->name('graveyard.service-types.')->group(function () {
        Route::get('/', [ServiceTypeController::class, 'index'])->name('index');
        Route::get('/create', [ServiceTypeController::class, 'create'])->name('create');
        Route::post('/', [ServiceTypeController::class, 'store'])->name('store');
        Route::get('/{serviceType}', [ServiceTypeController::class, 'show'])->name('show');
        Route::get('/{serviceType}/edit', [ServiceTypeController::class, 'edit'])->name('edit');
        Route::put('/{serviceType}', [ServiceTypeController::class, 'update'])->name('update');
        Route::delete('/{serviceType}', [ServiceTypeController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [ServiceTypeController::class, 'restore'])->name('restore');
        Route::post('/{serviceType}/toggle-active', [ServiceTypeController::class, 'toggleActive'])->name('toggle-active');
        Route::get('/service-types', [ServiceTypeController::class, 'getServiceTypes'])->name('api');
    });

    // Permanent Grave Booking Management
    Route::prefix('graveyard/permanent-grave-bookings')->name('graveyard.permanent-grave-bookings.')->group(function () {
        Route::get('/', [PermanentGraveBookingController::class, 'index'])->name('index');
        Route::get('/create', [PermanentGraveBookingController::class, 'create'])->name('create');
        Route::post('/search-permanent-grave', [PermanentGraveBookingController::class, 'searchPermanentGrave'])->name('search-permanent-grave');
        Route::post('/', [PermanentGraveBookingController::class, 'store'])->name('store');
        Route::get('/{permanentGraveBooking}', [PermanentGraveBookingController::class, 'show'])->name('show');
        Route::post('/{permanentGraveBooking}/cancel', [PermanentGraveBookingController::class, 'cancel'])->name('cancel');
        Route::delete('/{permanentGraveBooking}', [PermanentGraveBookingController::class, 'destroy'])->name('destroy');
    });

    // Temporary Grave Booking Management
    Route::prefix('graveyard/temporary-grave-bookings')->name('graveyard.temporary-grave-bookings.')->group(function () {
        Route::get('/', [TemporaryGraveBookingController::class, 'index'])->name('index');
        Route::get('/create', [TemporaryGraveBookingController::class, 'create'])->name('create');
        Route::get('/search-members', [TemporaryGraveBookingController::class, 'searchMembers'])->name('search-members');
        Route::post('/', [TemporaryGraveBookingController::class, 'store'])->name('store');
        Route::get('/eligible-for-transfer', [TemporaryGraveBookingController::class, 'eligibleForTransfer'])->name('eligible-for-transfer');
        Route::get('/{temporaryGraveBooking}', [TemporaryGraveBookingController::class, 'show'])->name('show');
        Route::post('/{temporaryGraveBooking}/confirm', [TemporaryGraveBookingController::class, 'confirm'])->name('confirm');
        Route::post('/{temporaryGraveBooking}/cancel', [TemporaryGraveBookingController::class, 'cancel'])->name('cancel');
        Route::post('/{temporaryGraveBooking}/request-transfer', [TemporaryGraveBookingController::class, 'requestTransfer'])->name('request-transfer');
    });

    // Niche Transfer Management
    Route::prefix('graveyard/niche-transfers')->name('graveyard.niche-transfers.')->group(function () {
        Route::get('/', [NicheTransferController::class, 'index'])->name('index');
        Route::get('/create', [NicheTransferController::class, 'create'])->name('create');
        Route::post('/', [NicheTransferController::class, 'store'])->name('store');
        Route::get('/statistics', [NicheTransferController::class, 'statistics'])->name('statistics');
        Route::get('/{nicheTransfer}', [NicheTransferController::class, 'show'])->name('show');
        Route::post('/{nicheTransfer}/approve', [NicheTransferController::class, 'approve'])->name('approve');
        Route::post('/{nicheTransfer}/reject', [NicheTransferController::class, 'reject'])->name('reject');
        Route::post('/{nicheTransfer}/complete', [NicheTransferController::class, 'complete'])->name('complete');
        Route::post('/{nicheTransfer}/cancel', [NicheTransferController::class, 'cancel'])->name('cancel');
    });

    // Payment Management
    Route::prefix('graveyard/payments')->name('graveyard.payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create/{bookingType}/{bookingId}', [PaymentController::class, 'create'])->name('create');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/balance', [PaymentController::class, 'balancePaymentForm'])->name('balance');
        Route::post('/{payment}/balance', [PaymentController::class, 'storeBalancePayment'])->name('balance.store');
        Route::get('/{payment}/receipt', [PaymentController::class, 'generateReceipt'])->name('receipt');
    });

    // Grave Categories Management
    Route::prefix('graveyard/grave-categories')->name('graveyard.grave-categories.')->group(function () {
        Route::get('/', [GraveCategoryController::class, 'index'])->name('index');
        Route::get('/create', [GraveCategoryController::class, 'create'])->name('create');
        Route::post('/', [GraveCategoryController::class, 'store'])->name('store');
        Route::get('/{graveCategory}', [GraveCategoryController::class, 'show'])->name('show');
        Route::get('/{graveCategory}/edit', [GraveCategoryController::class, 'edit'])->name('edit');
        Route::put('/{graveCategory}', [GraveCategoryController::class, 'update'])->name('update');
        Route::delete('/{graveCategory}', [GraveCategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [GraveCategoryController::class, 'restore'])->name('restore');
    });

    // Obituary Management (Admin Routes)
    Route::prefix('graveyard/obituaries')->name('graveyard.obituaries.')->group(function () {
        Route::get('/', [ObituaryManagementController::class, 'index'])->name('index');
        Route::get('/create', [ObituaryManagementController::class, 'create'])->name('create');
        Route::post('/', [ObituaryManagementController::class, 'store'])->name('store');
        Route::get('/{obituary}', [ObituaryManagementController::class, 'show'])->name('show');
        Route::get('/{obituary}/edit', [ObituaryManagementController::class, 'edit'])->name('edit');
        Route::put('/{obituary}', [ObituaryManagementController::class, 'update'])->name('update');
        Route::delete('/{obituary}', [ObituaryManagementController::class, 'destroy'])->name('destroy');

        // Condolence management
        Route::get('/condolences/manage', [ObituaryManagementController::class, 'condolences'])->name('condolences.index');
        Route::patch('/condolences/{condolence}/approve', [ObituaryManagementController::class, 'approveCondolence'])->name('condolences.approve');
        Route::patch('/condolences/{condolence}/reject', [ObituaryManagementController::class, 'rejectCondolence'])->name('condolences.reject');

        // Custom QR code generation
        Route::post('/{obituary}/generate-qr', [ObituaryManagementController::class, 'generateCustomQr'])->name('qr.generate');
        
        // Payment processing
        Route::post('/{obituary}/payment', [ObituaryManagementController::class, 'processPayment'])->name('payment.process');
    });

    // Permission denied route for graveyard
    Route::get('/graveyard/permission-denied', function () {
        return Inertia::render('errors/PermissionDenied', [
            'message' => request()->get('message', ''),
            'user' => \Illuminate\Support\Facades\Auth::user()
        ]);
    })->name('graveyard.permission-denied');

});

// Admin QR Download Route (with minimal middleware to avoid response conflicts)
Route::middleware(['auth', 'verified'])->group(function () {
    // QR code download (admin) - without nocache middleware to avoid response conflicts
    Route::get('/graveyard/obituaries/{obituary}/qr-download', [ObituaryManagementController::class, 'downloadQrCode'])->name('graveyard.obituaries.qr.download');
});


// Public Obituary Routes (no auth required) - outside the auth middleware
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
});

// API Routes for integrations
Route::prefix('api/obituary')->name('api.obituary.')->middleware(['auth:sanctum'])->group(function () {
    // Quick create from booking confirmation
    Route::post('/create-from-booking', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'booking_type' => 'required|in:permanent,temporary',
            'booking_id' => 'required|integer',
            'service_type' => 'required|in:basic,premium',
        ]);

        $obituaryService = app(\App\Services\ObituaryService::class);

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
        $obituaryService = app(\App\Services\ObituaryService::class);
        return response()->json($obituaryService->getObituaryStats($obituary));
    })->name('stats');
});
