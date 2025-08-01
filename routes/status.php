<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatusController;

Route::middleware('auth')->group(function () {
    Route::redirect('status', '/status/index');
    Route::get('status/index', [StatusController::class, 'index'])->name('status.index');
    Route::resource('status', StatusController::class)->except(['index']);
    Route::post('status/{id}/restore', [StatusController::class, 'restore'])->name('status.restore');
}); 