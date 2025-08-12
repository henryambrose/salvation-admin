<?php

use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('zone', ZoneController::class);
    Route::post('/zone/{id}/restore', [ZoneController::class, 'restore'])->name('zone.restore');
});
