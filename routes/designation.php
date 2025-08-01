<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesignationController;

Route::middleware('auth')->group(function () {
    Route::redirect('designation', '/designation/index');
    Route::get('designation/index', [DesignationController::class, 'index'])->name('designation.index');
    Route::get('designation/export', [DesignationController::class, 'export'])->name('designation.export');
    Route::resource('designation', DesignationController::class)->except(['index']);
    Route::post('designation/{id}/restore', [DesignationController::class, 'restore'])->name('designation.restore');
}); 