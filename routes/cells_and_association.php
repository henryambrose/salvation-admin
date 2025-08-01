<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CellsAndAssociationController;

Route::middleware('auth')->group(function () {
    Route::redirect('cells-and-association', '/cells-and-association/index');
    Route::get('cells-and-association/index', [CellsAndAssociationController::class, 'index'])->name('cells-and-association.index');
    Route::resource('cells-and-association', CellsAndAssociationController::class)->except(['index']);
    Route::post('cells-and-association/{id}/restore', [CellsAndAssociationController::class, 'restore'])->name('cells-and-association.restore');
}); 