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
use Modules\Graveyard\Http\Controllers\RemainsTransferController;
use Modules\Graveyard\Http\Controllers\PaymentController;
use Modules\Graveyard\Http\Controllers\GraveCategoryController;
use Modules\Graveyard\Http\Controllers\AnnualMaintenanceFeeController;
use Illuminate\Support\Facades\Log;


Route::middleware(['web', 'auth',  'nocache'])->prefix('graveyard')->as('graveyard.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('/graves')->name('graves.')->group(function () {
        Route::get('/', [GraveController::class, 'index'])->name('index');
        Route::post('/', [GraveController::class, 'store'])->name('store');
        Route::get('/{grave}', [GraveController::class, 'show'])->name('show');
        Route::put('/{grave}', [GraveController::class, 'update'])->name('update');
        Route::delete('/{grave}', [GraveController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [GraveController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [GraveController::class, 'forceDelete'])->name('force-delete');
    });

    // Niches Management
    Route::prefix('/niches')->name('niches.')->group(function () {
        Route::get('/', [NicheController::class, 'index'])->name('index');
        Route::get('/create', [NicheController::class, 'create'])->name('create');
        Route::post('/', [NicheController::class, 'store'])->name('store');
        Route::get('/search-members', [NicheController::class, 'searchMembers'])->name('search-members');
        Route::get('/{niche}', [NicheController::class, 'show'])->name('show');
        Route::get('/{niche}/edit', [NicheController::class, 'edit'])->name('edit');
        Route::put('/{niche}', [NicheController::class, 'update'])->name('update');
        Route::delete('/{niche}', [NicheController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [NicheController::class, 'restore'])->name('restore');
        Route::get('/{niche}/valid-members-pdf', [NicheController::class, 'downloadValidMembersPdf'])->name('download-valid-members-pdf');
    });

    // Permanent Graves Management
    Route::prefix('/permanent-graves')->name('permanent-graves.')->group(function () {
        Route::get('/', [PermanentGraveController::class, 'index'])->name('index');
        Route::get('/create', [PermanentGraveController::class, 'create'])->name('create');
        Route::post('/', [PermanentGraveController::class, 'store'])->name('store');
        Route::get('/search-members', [PermanentGraveController::class, 'searchMembers'])->name('search-members');
        Route::post('/add-valid-member', [PermanentGraveBookingController::class, 'addValidMember'])->name('add-valid-member');
        Route::post('/release-eligible', [PermanentGraveController::class, 'releaseEligible'])->name('release-eligible');
        Route::get('/{permanentGrave}', [PermanentGraveController::class, 'show'])->name('show');
        Route::get('/{permanentGrave}/edit', [PermanentGraveController::class, 'edit'])->name('edit');
        Route::put('/{permanentGrave}', [PermanentGraveController::class, 'update'])->name('update');
        Route::delete('/{permanentGrave}', [PermanentGraveController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [PermanentGraveController::class, 'restore'])->name('restore');
        Route::get('/{permanentGrave}/valid-members-pdf', [PermanentGraveController::class, 'downloadValidMembersPdf'])->name('download-valid-members-pdf');
    });

    // Temporary Graves Management
    Route::prefix('/temporary-graves')->name('temporary-graves.')->group(function () {
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
    Route::prefix('/valid-members')->name('valid-members.')->group(function () {
        Route::get('/', [ValidMemberController::class, 'index'])->name('index');
        Route::get('/create', [ValidMemberController::class, 'create'])->name('create');
        Route::post('/', [ValidMemberController::class, 'store'])->name('store');
        Route::get('/search-members', [ValidMemberController::class, 'searchMembers'])->name('search-members');
        Route::get('/search-graves', [ValidMemberController::class, 'searchGraves'])->name('search-graves');
        Route::get('/{validMember}', [ValidMemberController::class, 'show'])->name('show');
        Route::get('/{validMember}/edit', [ValidMemberController::class, 'edit'])->name('edit');
        Route::put('/{validMember}', [ValidMemberController::class, 'update'])->name('update');
        Route::delete('/{validMember}', [ValidMemberController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [ValidMemberController::class, 'restore'])->name('restore');
    });

    // Service Types Management
    Route::prefix('/service-types')->name('service-types.')->group(function () {
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
    Route::prefix('/permanent-grave-bookings')->name('permanent-grave-bookings.')->group(function () {
        Route::get('/', [PermanentGraveBookingController::class, 'index'])->name('index');
        Route::get('/create', [PermanentGraveBookingController::class, 'create'])->name('create');
        Route::post('/search-permanent-grave', [PermanentGraveBookingController::class, 'searchPermanentGrave'])->name('search-permanent-grave');
        Route::post('/', [PermanentGraveBookingController::class, 'store'])->name('store');
        Route::get('/{permanentGraveBooking}/edit', [PermanentGraveBookingController::class, 'edit'])->name('edit');
        Route::put('/{permanentGraveBooking}', [PermanentGraveBookingController::class, 'update'])->name('update');
        Route::get('/{permanentGraveBooking}', [PermanentGraveBookingController::class, 'show'])->name('show');
        Route::post('/{permanentGraveBooking}/cancel', [PermanentGraveBookingController::class, 'cancel'])->name('cancel');
        Route::delete('/{permanentGraveBooking}', [PermanentGraveBookingController::class, 'destroy'])->name('destroy');
    });

    // Temporary Grave Booking Management
    Route::prefix('/temporary-grave-bookings')->name('temporary-grave-bookings.')->group(function () {
        Route::get('/', [TemporaryGraveBookingController::class, 'index'])->name('index');
        Route::get('/create', [TemporaryGraveBookingController::class, 'create'])->name('create');
        Route::get('/search-members', [TemporaryGraveBookingController::class, 'searchMembers'])->name('search-members');
        Route::post('/', [TemporaryGraveBookingController::class, 'store'])->name('store');
        Route::get('/eligible-for-transfer', [TemporaryGraveBookingController::class, 'eligibleForTransfer'])->name('eligible-for-transfer');
        Route::get('/{temporaryGraveBooking}', [TemporaryGraveBookingController::class, 'show'])->name('show');
        Route::get('/{temporaryGraveBooking}/edit', [TemporaryGraveBookingController::class, 'edit'])->name('edit');
        Route::put('/{temporaryGraveBooking}', [TemporaryGraveBookingController::class, 'update'])->name('update');
        Route::post('/{temporaryGraveBooking}/confirm', [TemporaryGraveBookingController::class, 'confirm'])->name('confirm');
        Route::post('/{temporaryGraveBooking}/cancel', [TemporaryGraveBookingController::class, 'cancel'])->name('cancel');
        Route::post('/{temporaryGraveBooking}/request-transfer', [TemporaryGraveBookingController::class, 'requestTransfer'])->name('request-transfer');
        Route::delete('/{temporaryGraveBooking}', [TemporaryGraveBookingController::class, 'destroy'])->name('destroy');
    });

    // Remains Transfer Management
    Route::prefix('/remains-transfers')->name('remains-transfers.')->group(function () {
        Route::get('/', [RemainsTransferController::class, 'index'])->name('index');
        Route::get('/create', [RemainsTransferController::class, 'create'])->name('create');
        Route::post('/', [RemainsTransferController::class, 'store'])->name('store');
        Route::get('/statistics', [RemainsTransferController::class, 'statistics'])->name('statistics');
        Route::get('/{remainsTransfer}', [RemainsTransferController::class, 'show'])->name('show');
        Route::put('/{remainsTransfer}', [RemainsTransferController::class, 'update'])->name('update');
        Route::post('/{remainsTransfer}/approve', [RemainsTransferController::class, 'approve'])->name('approve');
        Route::post('/{remainsTransfer}/reject', [RemainsTransferController::class, 'reject'])->name('reject');
        Route::post('/{remainsTransfer}/complete', [RemainsTransferController::class, 'complete'])->name('complete');
        Route::post('/{remainsTransfer}/cancel', [RemainsTransferController::class, 'cancel'])->name('cancel');
    });

    // Payment Management
    Route::prefix('/payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create/maintenance/{graveId}', [PaymentController::class, 'createMaintenancePayment'])->name('create.maintenance');
        Route::post('/maintenance', [PaymentController::class, 'storeMaintenancePayment'])->name('store.maintenance');
        Route::get('/create/{bookingType}/{bookingId}', [PaymentController::class, 'create'])->name('create');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/balance', [PaymentController::class, 'balancePaymentForm'])->name('balance');
        Route::post('/{payment}/balance', [PaymentController::class, 'storeBalancePayment'])->name('balance.store');
        Route::get('/{payment}/receipt', [PaymentController::class, 'generateReceipt'])->name('receipt');
    });

    // Grave Categories Management
    Route::prefix('/grave-categories')->name('grave-categories.')->group(function () {
        Route::get('/', [GraveCategoryController::class, 'index'])->name('index');
        Route::get('/create', [GraveCategoryController::class, 'create'])->name('create');
        Route::post('/', [GraveCategoryController::class, 'store'])->name('store');
        Route::get('/{graveCategory}', [GraveCategoryController::class, 'show'])->name('show');
        Route::get('/{graveCategory}/edit', [GraveCategoryController::class, 'edit'])->name('edit');
        Route::put('/{graveCategory}', [GraveCategoryController::class, 'update'])->name('update');
        Route::delete('/{graveCategory}', [GraveCategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [GraveCategoryController::class, 'restore'])->name('restore');
    });

    // Annual Maintenance Fees Management
    Route::prefix('/annual-maintenance-fees')->name('annual-maintenance-fees.')->group(function () {
        Route::get('/', [AnnualMaintenanceFeeController::class, 'index'])->name('index');
        Route::get('/create', [AnnualMaintenanceFeeController::class, 'create'])->name('create');
        Route::post('/', [AnnualMaintenanceFeeController::class, 'store'])->name('store');
        Route::get('/{annualMaintenanceFee}', [AnnualMaintenanceFeeController::class, 'show'])->name('show');
        Route::get('/{annualMaintenanceFee}/edit', [AnnualMaintenanceFeeController::class, 'edit'])->name('edit');
        Route::put('/{annualMaintenanceFee}', [AnnualMaintenanceFeeController::class, 'update'])->name('update');
        Route::delete('/{annualMaintenanceFee}', [AnnualMaintenanceFeeController::class, 'destroy'])->name('destroy');
    });


    // Permission denied route for graveyard
    Route::get('/graveyard/permission-denied', function () {
        return Inertia::render('errors/PermissionDenied', [
            'message' => request()->get('message', ''),
            'user' => \Illuminate\Support\Facades\Auth::user()
        ]);
    })->name('permission-denied');
});

