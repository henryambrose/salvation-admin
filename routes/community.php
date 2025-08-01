<?php

use App\Http\Controllers\CommunityController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('community', '/community/index');

    Route::get('community/index', [CommunityController::class, 'index'])->name('community.index');
    Route::get('community/export', [CommunityController::class, 'export'])->name('community.export');
    Route::resource('community', CommunityController::class)->except(['index']);
    Route::post('/community/{id}/restore', [CommunityController::class, 'restore'])->name('community.restore');
});
