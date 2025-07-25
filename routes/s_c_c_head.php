<?php

use App\Http\Controllers\SCCHeadController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('scc-head', '/scc-head/index');

    Route::get('scc-head/index', [SCCHeadController::class, 'index'])->name('scc-head.index');
    Route::resource('scc-head', SCCHeadController::class)->except(['index']);
    Route::post('scc-head/{id}/restore', [SCCHeadController::class, 'restore'])->name('scc-head.restore');
    Route::get('api/community/{communityId}/members', [SCCHeadController::class, 'membersByCommunity']);

});
