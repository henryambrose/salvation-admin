<?php

use Illuminate\Support\Facades\Route;
use Modules\Members\Http\Controllers\CellsAndAssociationMemberController;

Route::middleware('auth')->group(function () {
    Route::get('cells-and-association-members/export', [CellsAndAssociationMemberController::class, 'export'])->name('cells-and-association-members.export');
    Route::resource('cells-and-association-members', CellsAndAssociationMemberController::class);
    Route::post('cells-and-association-members/{id}/restore', [CellsAndAssociationMemberController::class, 'restore'])->name('cells-and-association-members.restore');
    // API routes moved to routes/api.php
}); 