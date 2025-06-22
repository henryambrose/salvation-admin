<?php

use App\Http\Controllers\StateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('state', '/state/index');

    Route::get('state/index', [StateController::class, 'index'])->name('state.index');
    Route::resource('state', StateController::class)->except(['index']);

});
