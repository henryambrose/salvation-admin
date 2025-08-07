
<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Family tree display
    Route::get('/member/{id}/family-tree', [MemberController::class, 'showFamilyTree'])->name('member.family-tree');
    
    // Family tree API routes
    Route::get('/member/{id}/family-tree-data', [MemberController::class, 'getFamilyTreeData']);
    Route::get('/family-tree/search-members', [MemberController::class, 'searchFamilyMembers']);
    Route::post('/family-tree/add-relationship', [MemberController::class, 'addFamilyRelationship']);
    Route::delete('/family-tree/remove-relationship', [MemberController::class, 'removeFamilyRelationship']);
    
    // Debug routes
    Route::get('/member/{id}/debug-family-links', [MemberController::class, 'debugFamilyLinks']);
    Route::get('/member/{id}/debug-relationship-suggestions', [MemberController::class, 'debugRelationshipSuggestions']);
    Route::get('/debug-member/{memberNo}', [MemberController::class, 'debugSpecificMember']);
    Route::get('/debug-member-by-id/{id}', [MemberController::class, 'debugMemberById']);
});