<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RelationshipController;

Route::middleware('auth')->group(function () {
    Route::redirect('relationship', '/relationship/index');
    Route::get('relationship/index', [RelationshipController::class, 'index'])->name('relationship.index');
    Route::resource('relationship', RelationshipController::class)->except(['index']);
    Route::post('relationship/{id}/restore', [RelationshipController::class, 'restore'])->name('relationship.restore');
});
