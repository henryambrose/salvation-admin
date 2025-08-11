
<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Family tree display
    Route::get('/member/{id}/family-tree/{type}', [MemberController::class, 'showFamilyTree'])->name('member.family-tree');
    
    // Family tree API routes
    Route::get('/member/{id}/family-tree-data', [MemberController::class, 'getFamilyTreeData']);
    Route::get('/family-tree/search-members', [MemberController::class, 'searchFamilyMembers']);
    Route::post('/family-tree/add-relationship', [MemberController::class, 'addFamilyRelationship']);
    Route::delete('/family-tree/remove-relationship', [MemberController::class, 'removeFamilyRelationship']);
    
    // REMOVED: All debug routes
});