<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CellsAndAssociationMemberController;

Route::middleware('auth')->group(function () {
    Route::redirect('cells-and-association-members', '/cells-and-association-members/index');
    Route::get('cells-and-association-members/index', [CellsAndAssociationMemberController::class, 'index'])->name('cells-and-association-members.index');
    Route::get('cells-and-association-members/export', [CellsAndAssociationMemberController::class, 'export'])->name('cells-and-association-members.export');
    Route::resource('cells-and-association-members', CellsAndAssociationMemberController::class)->except(['index']);
    Route::post('cells-and-association-members/{id}/restore', [CellsAndAssociationMemberController::class, 'restore'])->name('cells-and-association-members.restore');
    Route::get('api/members/search', [CellsAndAssociationMemberController::class, 'searchMembers'])->name('api.members.search');
    Route::get('api/members/{id}', [CellsAndAssociationMemberController::class, 'getMemberById'])->name('api.members.getById');
    Route::get('api/members/{id}/cell-associations', [CellsAndAssociationMemberController::class, 'getMemberCellAssociations'])->name('api.members.cellAssociations');
}); 