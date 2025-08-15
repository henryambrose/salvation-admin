<?php

use App\Http\Controllers\Member\ProfileDetailsController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::redirect('member', '/member/index');
    Route::get('member/index', [MemberController::class, 'index'])->name('member.index');
    Route::get('member/export', [MemberController::class, 'export'])->name('member.export');
    
    // Data verification routes with permission middleware
    Route::middleware(['permission:read-data-verification'])->group(function () {
        Route::get('member/data-verification', [MemberController::class, 'dataVerification'])->name('member.data-verification');
    });
    
    // Data verification API routes with update permission
    Route::middleware(['permission:update-data-verification'])->group(function () {
        Route::post('member/bulk-update', [MemberController::class, 'bulkUpdate'])->name('member.bulk-update');
    });
    

    
    Route::resource('member', MemberController::class)->except(['index']);
    // Route::get('member/search-options', [MemberController::class, 'searchOptions'])
    //     ->name('member.search-options');
        Route::post('/member/{id}/restore', [MemberController::class, 'restore'])->name('member.restore');
    // Family numbering system routes
    // Route::post('member/move-family/{familyNo}', [MemberController::class, 'moveFamily'])
    //     ->name('member.move-family');
    Route::post('member/handle-marriage', [MemberController::class, 'handleMarriage'])
        ->name('member.handle-marriage');
    Route::get('member/search-families', [MemberController::class, 'searchFamilies'])
        ->name('member.search-families');
    Route::get('member/church-statistics/{churchCode?}', [MemberController::class, 'getChurchStatistics'])
        ->name('member.church-statistics');
});

