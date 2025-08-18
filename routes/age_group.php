<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgeGroupController;

Route::middleware('auth')->group(function () {
    Route::resource('age-group', AgeGroupController::class);
    Route::post('age-group/{id}/restore', [AgeGroupController::class, 'restore'])->name('age-group.restore');
});
