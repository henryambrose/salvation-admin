<?php

use App\Http\Controllers\TownController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('town', '/town/index');

    Route::get('town/index', [TownController::class, 'index'])->name('town.index');
    Route::get('/town/deleted', [TownController::class, 'deleted'])->name('town.deleted');
    Route::post('/town/{id}/restore', [TownController::class, 'restore'])->name('town.restore');
    Route::resource('town', TownController::class)->except(['index']);
});
