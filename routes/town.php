<?php

use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('country', '/country/index');

    Route::get('country/index', [CountryController::class, 'index'])->name('country.index');
    Route::resource('country', CountryController::class)->except(['index']);

});
