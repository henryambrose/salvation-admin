<?php

use Modules\Members\Http\Controllers\ExternalMemberController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Define specific routes BEFORE the resource route to prevent conflicts
    Route::get('external-members/export', [ExternalMemberController::class, 'export'])->name('external-members.export');
    Route::get('external-members/search', [ExternalMemberController::class, 'search'])->name('external-members.search');
    Route::get('external-member/family-details/{familyNo}', [ExternalMemberController::class, 'getFamilyDetails'])->name('external-member.family-details');
    Route::post('/external-members/{id}/restore', [ExternalMemberController::class, 'restore'])->name('external-members.restore');

    // Resource route comes last to avoid conflicts
    Route::resource('external-members', ExternalMemberController::class);

    // Route::delete('/external-members/{id}/force-delete', [ExternalMemberController::class, 'forceDelete'])->name('external-members.force-delete');
});
