<?php

use Modules\Members\Http\Controllers\CellsAndAssociationLeaderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('cells-and-association-leaders', '/cells-and-association-leaders/index');
    Route::get('cells-and-association-leaders/index', [CellsAndAssociationLeaderController::class, 'index'])->name('cells-and-association-leaders.index');
    Route::get('cells-and-association-leaders/export', [CellsAndAssociationLeaderController::class, 'export'])->name('cells-and-association-leaders.export');
    Route::resource('cells-and-association-leaders', CellsAndAssociationLeaderController::class)->except(['index']);
    Route::post('cells-and-association-leaders/{id}/restore', [CellsAndAssociationLeaderController::class, 'restore'])->name('cells-and-association-leaders.restore');
    Route::get('api/members/alive', [CellsAndAssociationLeaderController::class, 'getAllMembers'])->name('api.members.alive');
});
