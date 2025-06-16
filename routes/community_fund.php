<?php

use App\Http\Controllers\CommunityFundController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('community-fund', '/community-fund/index');

    Route::get('community-fund/index', [CommunityFundController::class, 'index'])->name('community-fund.index');
    Route::resource('community-fund', CommunityFundController::class)->except(['index']);

});
