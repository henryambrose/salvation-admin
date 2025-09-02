<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Graveyard\Http\Controllers\DashboardController;

use Modules\Graveyard\Http\Controllers\GraveController;
use Modules\Graveyard\Http\Controllers\PermanentGraveController;
use Modules\Graveyard\Http\Controllers\TemporaryGraveController;
use Modules\Graveyard\Http\Controllers\NicheValidMemberController;
use Modules\Graveyard\Http\Controllers\NicheController;
use Modules\Graveyard\Http\Controllers\ValidMemberController;
use Modules\Graveyard\Http\Controllers\ServiceTypeController;

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

    // Niche Valid Member Management
    Route::prefix('graveyard/niche-valid-members')->name('graveyard.niche-valid-members.')->group(function () {
        Route::get('/', [NicheValidMemberController::class, 'index'])->name('index');
        Route::post('/', [NicheValidMemberController::class, 'store'])->name('store');
        Route::get('/create', [NicheValidMemberController::class, 'create'])->name('create');
        Route::get('/{nicheValidMember}', [NicheValidMemberController::class, 'show'])->name('show');
        Route::put('/{nicheValidMember}', [NicheValidMemberController::class, 'update'])->name('update');
        Route::delete('/{nicheValidMember}', [NicheValidMemberController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [NicheValidMemberController::class, 'restore'])->name('restore');
    });


    // Permanent Graves Management
    Route::prefix('graveyard/permanent-graves')->name('graveyard.permanent-graves.')->group(function () {
        Route::get('/', [PermanentGraveController::class, 'index'])->name('index');
        Route::get('/create', [PermanentGraveController::class, 'create'])->name('create');
        Route::post('/', [PermanentGraveController::class, 'store'])->name('store');
        Route::get('/search-members', [PermanentGraveController::class, 'searchMembers'])->name('search-members');
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
    
    // Permission denied route for graveyard
    Route::get('/graveyard/permission-denied', function () {
        return Inertia::render('errors/PermissionDenied', [
            'message' => request()->get('message', ''),
            'user' => auth()->user()
        ]);
    })->name('graveyard.permission-denied');
});
