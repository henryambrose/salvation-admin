<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Graveyard\Http\Controllers\DashboardController;
use Modules\Graveyard\Http\Controllers\CemeteryController;
use Modules\Graveyard\Http\Controllers\SectionController;
use Modules\Graveyard\Http\Controllers\GraveController;
use Modules\Graveyard\Http\Controllers\BurialController;
use Modules\Graveyard\Http\Controllers\MaintenanceController;
use Modules\Graveyard\Http\Controllers\FinanceController;
use Modules\Graveyard\Http\Controllers\VisitorController;
use Modules\Graveyard\Http\Controllers\PermanentGraveController;
use Modules\Graveyard\Http\Controllers\TemporaryGraveController;
use Modules\Graveyard\Http\Controllers\NicheValidMemberController;
use Modules\Graveyard\Http\Controllers\PermanentValidMemberController;
use Modules\Graveyard\Http\Controllers\NicheController;

Route::middleware(['auth', 'verified', 'nocache'])->group(function () {
    
    // Graveyard Dashboard
    Route::get('/graveyard', [DashboardController::class, 'index'])->name('graveyard.dashboard');
    
    // Cemeteries Management
    Route::prefix('graveyard/cemeteries')->name('graveyard.cemeteries.')->group(function () {
        Route::get('/', [CemeteryController::class, 'index'])->name('index');
        Route::post('/', [CemeteryController::class, 'store'])->name('store');
        Route::get('/{cemetery}', [CemeteryController::class, 'show'])->name('show');
        Route::put('/{cemetery}', [CemeteryController::class, 'update'])->name('update');
        Route::delete('/{cemetery}', [CemeteryController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [CemeteryController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [CemeteryController::class, 'forceDelete'])->name('force-delete');
    });
    
    // Sections Management
    Route::prefix('graveyard/sections')->name('graveyard.sections.')->group(function () {
        Route::get('/', [SectionController::class, 'index'])->name('index');
        Route::post('/', [SectionController::class, 'store'])->name('store');
        Route::get('/{section}', [SectionController::class, 'show'])->name('show');
        Route::put('/{section}', [SectionController::class, 'update'])->name('update');
        Route::delete('/{section}', [SectionController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [SectionController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [SectionController::class, 'forceDelete'])->name('force-delete');
    });
    
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
    
    // Burials Management
    Route::prefix('graveyard/burials')->name('graveyard.burials.')->group(function () {
        Route::get('/', [BurialController::class, 'index'])->name('index');
        Route::post('/', [BurialController::class, 'store'])->name('store');
        Route::get('/{burial}', [BurialController::class, 'show'])->name('show');
        Route::put('/{burial}', [BurialController::class, 'update'])->name('update');
        Route::delete('/{burial}', [BurialController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [BurialController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [BurialController::class, 'forceDelete'])->name('force-delete');
    });
    
    // Maintenance Management
    Route::prefix('graveyard/maintenance')->name('graveyard.maintenance.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store');
        Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('show');
        Route::put('/{maintenance}', [MaintenanceController::class, 'update'])->name('update');
        Route::delete('/{maintenance}', [MaintenanceController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [MaintenanceController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [MaintenanceController::class, 'forceDelete'])->name('force-delete');
    });
    
    // Finance Management
    Route::prefix('graveyard/finances')->name('graveyard.finances.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('index');
        Route::post('/', [FinanceController::class, 'store'])->name('store');
        Route::get('/{transaction}', [FinanceController::class, 'show'])->name('show');
        Route::put('/{transaction}', [FinanceController::class, 'update'])->name('update');
        Route::delete('/{transaction}', [FinanceController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [FinanceController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [FinanceController::class, 'forceDelete'])->name('force-delete');
    });
    
    // Visitor Management
    Route::prefix('graveyard/visitors')->name('graveyard.visitors.')->group(function () {
        Route::get('/', [VisitorController::class, 'index'])->name('index');
        Route::post('/', [VisitorController::class, 'store'])->name('store');
        Route::get('/{visitor}', [VisitorController::class, 'show'])->name('show');
        Route::put('/{visitor}', [VisitorController::class, 'update'])->name('update');
        Route::delete('/{visitor}', [VisitorController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [VisitorController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [VisitorController::class, 'forceDelete'])->name('force-delete');
    });

    // Niches Management
    Route::prefix('graveyard/niches')->name('graveyard.niches.')->group(function () {
        Route::get('/', [NicheController::class, 'index'])->name('index');
        Route::get('/create', [NicheController::class, 'create'])->name('create');
        Route::post('/', [NicheController::class, 'store'])->name('store');
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

    // Permanent Valid Member Management
    Route::prefix('graveyard/permanent-valid-members')->name('graveyard.permanent-valid-members.')->group(function () {
        Route::get('/', [PermanentValidMemberController::class, 'index'])->name('index');
        Route::post('/', [PermanentValidMemberController::class, 'store'])->name('store');
        Route::get('/create', [PermanentValidMemberController::class, 'create'])->name('create');
        Route::get('/{permanentValidMember}', [PermanentValidMemberController::class, 'show'])->name('show');
        Route::put('/{permanentValidMember}', [PermanentValidMemberController::class, 'update'])->name('update');
        Route::delete('/{permanentValidMember}', [PermanentValidMemberController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [PermanentValidMemberController::class, 'restore'])->name('restore');
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
    
    // Permission denied route for graveyard
    Route::get('/graveyard/permission-denied', function () {
        return Inertia::render('errors/PermissionDenied', [
            'message' => request()->get('message', ''),
            'user' => auth()->user()
        ]);
    })->name('graveyard.permission-denied');
});
