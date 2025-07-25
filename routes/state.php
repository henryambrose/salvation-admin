<?php

use App\Http\Controllers\StateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('state', '/state/index');

    Route::get('state/index', [StateController::class, 'index'])->name('state.index');
    Route::get('/state/deleted', [StateController::class, 'deleted'])->name('state.deleted');
    Route::post('/state/{id}/restore', [App\Http\Controllers\StateController::class, 'restore'])->name('state.restore');
    Route::resource('state', StateController::class)->except(['index']);
});
