<?php

use Modules\Members\Http\Controllers\CommunityClusterController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('community-clusters/export', [CommunityClusterController::class, 'export'])->name('community-clusters.export');
    Route::post('/community-clusters/{id}/restore', [CommunityClusterController::class, 'restore'])->name('community-clusters.restore');
    Route::resource('community-clusters', CommunityClusterController::class);
}); 