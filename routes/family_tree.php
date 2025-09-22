
<?php

use Modules\Members\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Family tree display
    Route::get('/member/{id}/family-tree/{type}', [MemberController::class, 'showFamilyTree'])->name('member.family-tree');
});
