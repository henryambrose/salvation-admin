<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgeGroupController;

Route::middleware('auth')->group(function () {
    Route::redirect('age-group', '/age-group/index');

    Route::get('age-group/index', [AgeGroupController::class, 'index'])->name('age-group.index');
    Route::resource('age-group', AgeGroupController::class)->except(['index']);
});
