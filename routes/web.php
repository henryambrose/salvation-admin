<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SCCHeadController;
use App\Http\Controllers\PPCHeadController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ExternalMemberController;

Route::get('/', function () {
    // return Inertia::render('Welcome');
    return redirect()->route('dashboard');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'role:super admin'])->group(function () {
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

Route::get('/roles-permissions/users', [RolePermissionController::class, 'users'])->name('roles.permissions.users');
Route::post('/roles-permissions/assign-role', [RolePermissionController::class, 'assignRole'])->name('roles.permissions.assign-role');
Route::post('/roles-permissions/remove-role', [RolePermissionController::class, 'removeRole'])->name('roles.permissions.remove-role');
Route::post('/roles-permissions/assign-role-from-group', [RolePermissionController::class, 'assignRoleFromGroup'])
    ->name('roles.permissions.assign-role-from-group');
Route::post('/roles-permissions/update-user-permissions', [RolePermissionController::class, 'updateUserPermissions'])
    ->name('roles.permissions.update-user-permissions');

Route::get('/api/community/{community}/members', [SCCHeadController::class, 'membersByCommunity']);
Route::get('/api/ppc-community/{community}/members', [PPCHeadController::class, 'membersByCommunity']);
Route::post('/member/{id}/restore', [MemberController::class, 'restore'])->name('member.restore');

// Next available numbers route
Route::get('/members/next-numbers', [MemberController::class, 'getNextAvailableNumbers'])
    ->middleware(['auth'])
    ->name('members.next-numbers');

// Test route for debugging
Route::get('/test-next-numbers', function() {
    try {
        $churchCode = config('app.church_code', 'SAL');
        $numberingService = new \App\Services\FamilyNumberingService($churchCode);
        
        $nextFamilyGroup = $numberingService->generateFamilyGroupNumber();
        $nextFamilyNo = $numberingService->generateMemberNumberInFamily($nextFamilyGroup);
        $nextMemberNo = $numberingService->generateMemberNumber();
        
        return response()->json([
            'success' => true,
            'next_family_no' => $nextFamilyNo,
            'next_member_no' => $nextMemberNo,
            'family_group' => $nextFamilyGroup,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->middleware(['auth']);

// User routes
// Route::middleware('auth')->group(function () {
//     Route::get('users', [UserController::class, 'index'])->name('users.index');
//     Route::get('users/create', [UserController::class, 'create'])->name('users.create');
//     Route::post('users', [UserController::class, 'store'])->name('users.store');
//     Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
//     Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
//     Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
//     Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
//     Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    
//     // Test route for UserController2
//     // Route::get('users2', [UserController2::class, 'index'])->name('users2.index');
// });

// External Members Routes
Route::middleware('auth')->group(function () {
    Route::resource('external-members', ExternalMemberController::class);
    Route::get('/external-members/by-family', [ExternalMemberController::class, 'getByFamily'])->name('external-members.by-family');
});

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

Route::get('/debug-user/{email}', [RolePermissionController::class, 'debugUser']);