<?php

use App\Http\Controllers\CommunityFundController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('community-fund', '/community-fund/index');

    Route::get('community-fund/index', [CommunityFundController::class, 'index'])->name('community-fund.index');
    Route::get('community-fund/create', [CommunityFundController::class, 'create'])->name('community-fund.create');
    Route::post('community-fund', [CommunityFundController::class, 'store'])->name('community-fund.store');
    Route::get('community-fund/{communityFund}/edit', [CommunityFundController::class, 'edit'])->name('community-fund.edit');
    Route::put('community-fund/{communityFund}', [CommunityFundController::class, 'update'])->name('community-fund.update');
    Route::delete('community-fund/{communityFund}', [CommunityFundController::class, 'destroy'])->name('community-fund.destroy');
});
