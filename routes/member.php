<?php

use App\Http\Controllers\Member\ProfileDetailsController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('member', '/member/index');
    Route::get('member/index', [MemberController::class, 'index'])->name('member.index');
    Route::get('member/export', [MemberController::class, 'export'])->name('member.export');
    Route::resource('member', MemberController::class)->except(['index']);
    Route::get('member/search-options', [MemberController::class, 'searchOptions'])
        ->name('member.search-options');
    
    // Family numbering system routes
    Route::post('member/move-family/{familyNo}', [MemberController::class, 'moveFamily'])
        ->name('member.move-family');
    Route::post('member/handle-marriage', [MemberController::class, 'handleMarriage'])
        ->name('member.handle-marriage');
    Route::get('member/search-families', [MemberController::class, 'searchFamilies'])
        ->name('member.search-families');
    Route::get('member/church-statistics/{churchCode?}', [MemberController::class, 'getChurchStatistics'])
        ->name('member.church-statistics');
});

