<?php

use App\Http\Controllers\CityController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('city/export', [CityController::class, 'export'])->name('city.export');
    Route::resource('city', CityController::class);
    Route::get('city/deleted', [CityController::class, 'deleted'])->name('city.deleted');
    Route::post('city/{id}/restore', [CityController::class, 'restore'])->name('city.restore');
}); 