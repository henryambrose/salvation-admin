<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SCCHeadController;
use App\Http\Controllers\PPCHeadController;
use App\Http\Controllers\MemberController;

Route::get('/', function () {
    // return Inertia::render('Welcome');
    return redirect()->route('dashboard');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/roles-permissions', [RolePermissionController::class, 'index'])
        ->name('roles.permissions.index');
    Route::post('/roles-permissions/update', [RolePermissionController::class, 'update'])
        ->name('roles.permissions.update');
    
    // Chat routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/history', [ChatController::class, 'getHistory'])->name('chat.history');
    Route::get('/chat/answer/{uniqueID}', [ChatController::class, 'getSpecificAnswer'])->name('chat.answer');
   


});

Route::get('/api/community/{community}/members', [SCCHeadController::class, 'membersByCommunity']);
Route::get('/api/ppc-community/{community}/members', [PPCHeadController::class, 'membersByCommunity']);
Route::post('/member/{id}/restore', [MemberController::class, 'restore'])->name('member.restore');


require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
require __DIR__.'/member.php';
require __DIR__.'/community.php';
require __DIR__.'/community_fund.php';
require __DIR__.'/zones.php';
require __DIR__.'/blood_group.php';
require __DIR__.'/family_income_range.php';
require __DIR__.'/s_c_c_head.php';
require __DIR__.'/p_p_c_head.php';
require __DIR__.'/country.php';
require __DIR__.'/state.php';
require __DIR__.'/city.php';
require __DIR__.'/town.php';
require __DIR__.'/age_group.php';
require __DIR__.'/relationship.php';
require __DIR__.'/designation.php';
require __DIR__.'/gender.php';
require __DIR__.'/status.php';
require __DIR__.'/cells_and_association.php';
require __DIR__.'/cells_and_association_members.php';
require __DIR__.'/parish.php';
require __DIR__.'/users.php';
require __DIR__.'/clusters.php';
require __DIR__.'/community_clusters.php';