<?php

use App\Http\Controllers\BloodGroupController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('blood-group', '/blood-group/index');

    Route::get('blood-group/index', [BloodGroupController::class, 'index'])->name('blood-group.index');
    Route::resource('blood-group', BloodGroupController::class)->except(['index']);

});
