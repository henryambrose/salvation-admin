<?php

use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/country/{id}/restore', [CountryController::class, 'restore']);
    Route::resource('country', CountryController::class);
});
