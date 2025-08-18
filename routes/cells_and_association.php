<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CellsAndAssociationController;

Route::middleware('auth')->group(function () {
    Route::resource('cells-and-association', CellsAndAssociationController::class);
    Route::post('cells-and-association/{id}/restore', [CellsAndAssociationController::class, 'restore'])->name('cells-and-association.restore');
}); 