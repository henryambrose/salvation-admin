<?php

use App\Http\Controllers\PPCHeadController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('ppc-head', '/ppc-head/index');

    Route::get('ppc-head/index', [PPCHeadController::class, 'index'])->name('ppc-head.index');
    Route::resource('ppc-head', PPCHeadController::class)->except(['index']);

});
