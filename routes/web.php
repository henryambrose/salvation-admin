<?php

use Modules\Members\Http\Controllers\DashboardController;
use Modules\Members\Http\Controllers\RolePermissionController;
use Modules\Members\Http\Controllers\ChatController;
use Modules\Members\Http\Controllers\CatholicCalendarController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

// CSRF token refresh route - MUST be before other routes
Route::get('/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF token refreshed'], 200);
})->middleware('web');

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
    Route::post('/roles-permissions/create-role', [RolePermissionController::class, 'createRole'])
        ->name('roles.permissions.create-role');
        
    // API routes for PPC Head and SCC Head member filtering
    Route::middleware('auth')->group(function () {
        Route::get('api/ppc-community/{communityId}/members', [Modules\Members\Http\Controllers\PPCHeadController::class, 'membersByCommunity']);
        Route::get('api/community/{communityId}/members', [Modules\Members\Http\Controllers\SCCHeadController::class, 'membersByCommunity']);
    });
});

// Include feature-specific route files
require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
require __DIR__.'/member.php';
require __DIR__.'/family_tree.php';
require __DIR__.'/community.php';
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

// Public API routes (after all other routes to avoid conflicts)
Route::get('/api/catholic-calendar', [CatholicCalendarController::class, 'index']);

// Temporary test routes (remove in production)
Route::get('/test-419', function() {
    abort(419);
});

Route::get('/test-404', function() {
    abort(404);
});

Route::get('/test-500', function() {
    abort(500);
});