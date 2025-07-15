<?php

use App\Http\Controllers\BloodGroupController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('blood-group', '/blood-group/index');

    Route::get('blood-group/index', [BloodGroupController::class, 'index'])->name('blood-group.index');
    Route::get('blood-group/{bloodGroup}/edit', [BloodGroupController::class, 'edit'])->name('blood-group.edit');
    Route::put('blood-group/{bloodGroup}', [BloodGroupController::class, 'update'])->name('blood-group.update');
    Route::delete('blood-group/{bloodGroup}', [BloodGroupController::class, 'destroy'])->name('blood-group.destroy');
    Route::resource('blood-group', BloodGroupController::class)->except(['index', 'edit', 'update', 'destroy']);
});
