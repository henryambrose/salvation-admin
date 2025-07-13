<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // return Inertia::render('Welcome');
    return redirect()->route('dashboard');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'can:update-role-permissions'])->group(function () {
    Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles.permissions.index');
    Route::post('/roles-permissions/update', [RolePermissionController::class, 'update'])->name('roles.permissions.update');
});

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
require __DIR__.'/town.php';
require __DIR__.'/users.php';
require __DIR__.'/auth.php';
