<?php

use Illuminate\Support\Facades\Route;
use Modules\Members\Http\Controllers\DesignationController;

Route::middleware('auth')->group(function () {
    Route::get('designation/export', [DesignationController::class, 'export'])->name('designation.export');
    Route::resource('designation', DesignationController::class);
    Route::post('designation/{id}/restore', [DesignationController::class, 'restore'])->name('designation.restore');
}); 