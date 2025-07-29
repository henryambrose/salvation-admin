<?php

use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Route::redirect('zone', '/zone/index');

    // Route::get('zone/index', [ZoneController::class, 'index'])->name('zone.index');
    // Route::get('zone/{zone}/edit', [ZoneController::class, 'edit'])->name('zone.edit');
    // Route::put('zone/{zone}', [ZoneController::class, 'update'])->name('zone.update');
    // Route::delete('zone/{zone}', [ZoneController::class, 'destroy'])->name('zone.destroy');
    // Route::resource('zone', ZoneController::class)->except(['index', 'edit', 'update', 'destroy']);
    Route::resource('zone', ZoneController::class);
    Route::post('/zone/{id}/restore', [ZoneController::class, 'restore'])->name('zone.restore');
});
