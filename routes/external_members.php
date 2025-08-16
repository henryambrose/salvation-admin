<?php

use App\Http\Controllers\ExternalMemberController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('external-members', ExternalMemberController::class);
    Route::get('external-members/search', [ExternalMemberController::class, 'search'])->name('external-members.search');
    Route::get('external-members/export', [ExternalMemberController::class, 'export'])->name('external-members.export');
    Route::post('/external-members/{id}/restore', [ExternalMemberController::class, 'restore'])->name('external-members.restore');
    Route::delete('/external-members/{id}/force-delete', [ExternalMemberController::class, 'forceDelete'])->name('external-members.force-delete');
});
