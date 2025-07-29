<?php

use App\Http\Controllers\ClusterController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('clusters', ClusterController::class);
    Route::post('/clusters/{id}/restore', [ClusterController::class, 'restore'])->name('cluster.restore');
}); 