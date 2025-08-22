<?php

use Modules\Members\Http\Controllers\BloodGroupController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('blood-group', BloodGroupController::class);
    Route::post('/blood-group/{id}/restore', [BloodGroupController::class, 'restore'])->name('blood-group.restore');
});
