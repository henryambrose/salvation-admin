<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PPCHeadController;

Route::middleware('auth')->group(function () {
    Route::redirect('ppc-head', '/ppc-head/index');
    Route::get('ppc-head/index', [PPCHeadController::class, 'index'])->name('ppc-head.index');
    Route::get('ppc-head/export', [PPCHeadController::class, 'export'])->name('ppc-head.export');
    Route::resource('ppc-head', PPCHeadController::class)->except(['index']);
    Route::post('ppc-head/{id}/restore', [PPCHeadController::class, 'restore'])->name('ppc-head.restore');
});
