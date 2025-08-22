<?php

use Modules\Members\Http\Controllers\StateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::post('/state/{id}/restore', [App\Http\Controllers\StateController::class, 'restore'])->name('state.restore');
    Route::resource('state', StateController::class);
});
