<?php

use Modules\Members\Http\Controllers\StatusController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('status', '/status/index');
    Route::get('status/index', [StatusController::class, 'index'])->name('status.index');
    Route::resource('status', StatusController::class)->except(['index']);
    Route::post('status/{id}/restore', [StatusController::class, 'restore'])->name('status.restore');
}); 