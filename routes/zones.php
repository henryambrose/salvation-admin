<?php

use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('zone', '/zone/index');

    Route::get('zone/index', [ZoneController::class, 'index'])->name('zone.index');
    Route::resource('zone', ZoneController::class)->except(['index']);

});
