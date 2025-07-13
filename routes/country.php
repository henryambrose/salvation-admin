<?php

use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('country', '/country/index');

    Route::get('country/index', [CountryController::class, 'index'])->name('country.index');
    Route::get('/country/deleted', [CountryController::class, 'deleted'])->name('country.deleted');
    Route::post('/country/{id}/restore', [CountryController::class, 'restore'])->name('country.restore');
    Route::resource('country', CountryController::class)->except(['index']);
});
