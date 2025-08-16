<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SCCHeadController;

Route::middleware('auth')->group(function () {
    Route::redirect('scc-head', '/scc-head/index');
    Route::get('scc-head/index', [SCCHeadController::class, 'index'])->name('scc-head.index');
    Route::get('scc-head/export', [SCCHeadController::class, 'export'])->name('scc-head.export');
    Route::resource('scc-head', SCCHeadController::class)->except(['index']);
    Route::post('scc-head/{id}/restore', [SCCHeadController::class, 'restore'])->name('scc-head.restore');
});
