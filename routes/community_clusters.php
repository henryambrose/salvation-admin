<?php

use App\Http\Controllers\CommunityClusterController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('community-clusters', CommunityClusterController::class);
    Route::post('/community-clusters/{id}/restore', [CommunityClusterController::class, 'restore'])->name('community-clusters.restore');
}); 