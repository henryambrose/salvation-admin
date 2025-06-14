<?php

use App\Http\Controllers\CommunityController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('community', '/community/index');

    Route::get('community/index', [CommunityController::class, 'index'])->name('community.index');
    Route::resource('community', CommunityController::class)->except(['index']);

});
