<?php

use App\Http\Controllers\Member\ProfileDetailsController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;
Route::get('member/exportxls', [MemberController::class, 'exportxls'])->name('member.exportxls');
Route::middleware('auth')->group(function () {
    Route::redirect('member', '/member/index');
    Route::get('member/index', [MemberController::class, 'index'])->name('member.index');
    Route::resource('member', MemberController::class)->except(['index']);
    Route::get('member/search-options', [MemberController::class, 'searchOptions'])
        ->name('member.search-options');
    
});

