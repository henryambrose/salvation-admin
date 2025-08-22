<?php

use Modules\Members\Http\Controllers\Member\ProfileDetailsController;
use Modules\Members\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {

    
    // Data verification routes with permission middleware
    Route::middleware(['permission:read-data-verification'])->group(function () {
        Route::get('member/data-verification', [MemberController::class, 'dataVerification'])->name('member.data-verification');
    });
    
    // Data verification API routes with update permission
    Route::middleware(['permission:update-data-verification'])->group(function () {
        Route::post('member/bulk-update', [MemberController::class, 'bulkUpdate'])->name('member.bulk-update');
    });
    
    // Family numbering system routes - MUST come BEFORE the resource route
    Route::redirect('member', '/member/index');
    Route::get('member/index', [MemberController::class, 'index'])->name('member.index');
    Route::get('member/export', [MemberController::class, 'export'])->name('member.export');
    Route::get('member/search-families', [MemberController::class, 'searchFamilies'])
        ->name('member.search-families');
    Route::get('member/next-available-numbers', [MemberController::class, 'getNextAvailableNumbers'])
        ->name('member.next-available-numbers');
    Route::get('member/family-details/{familyNo}', [MemberController::class, 'getFamilyDetails'])
        ->name('member.family-details');
    Route::get('member/family-members/{familyNo}', [MemberController::class, 'getMembersByFamily'])
    ->name('member.family-members');
    Route::get('member/church-statistics/{churchCode?}', [MemberController::class, 'getChurchStatistics'])
        ->name('member.church-statistics');
    Route::get('member/search-members', [MemberController::class, 'searchMembers'])
        ->name('member.search-members');
    Route::post('member/handle-marriage', [MemberController::class, 'handleMarriage'])
        ->name('member.handle-marriage');
    Route::resource('member', MemberController::class)->except(['index']);
    Route::post('/member/{id}/restore', [MemberController::class, 'restore'])->name('member.restore');
    Route::get('/member/{id}/details', [MemberController::class, 'getMemberDetails'])->name('member.details');
});

