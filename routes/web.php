<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

// Protected routes
Route::middleware(['auth', 'verified', 'nocache'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Super Admin routes
    Route::middleware(['role:super admin'])->group(function () {
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
    
    // Role management routes
    Route::get('/roles-permissions/users', [RolePermissionController::class, 'users'])
        ->name('roles.permissions.users');
    Route::post('/roles-permissions/assign-role', [RolePermissionController::class, 'assignRole'])
        ->name('roles.permissions.assign-role');
    Route::post('/roles-permissions/remove-role', [RolePermissionController::class, 'removeRole'])
        ->name('roles.permissions.remove-role');
    Route::post('/roles-permissions/update-user-permissions', [RolePermissionController::class, 'updateUserPermissions'])
        ->name('roles.permissions.update-user-permissions');
    
    // API routes for PPC Head and SCC Head member filtering
    Route::middleware('auth')->group(function () {
        Route::get('api/ppc-community/{communityId}/members', [App\Http\Controllers\PPCHeadController::class, 'membersByCommunity']);
        Route::get('api/community/{communityId}/members', [App\Http\Controllers\SCCHeadController::class, 'membersByCommunity']);
    });
});

// Include feature-specific route files
require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
require __DIR__.'/member.php';
require __DIR__.'/family_tree.php';
require __DIR__.'/community.php';
require __DIR__.'/community_fund.php';
require __DIR__.'/zones.php';
require __DIR__.'/blood_group.php';
require __DIR__.'/income_range.php';
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
require __DIR__.'/audit.php';
require __DIR__.'/external_members.php';