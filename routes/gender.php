<?php

use Illuminate\Support\Facades\Route;
use Modules\Members\Http\Controllers\GenderController;

Route::middleware('auth')->group(function () {
    Route::redirect('gender', '/gender/index');
    Route::get('gender/index', [GenderController::class, 'index'])->name('gender.index');
    Route::resource('gender', GenderController::class)->except(['index']);
    Route::post('gender/{id}/restore', [GenderController::class, 'restore'])->name('gender.restore');
}); 