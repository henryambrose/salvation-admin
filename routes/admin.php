<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PermissionCategoryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // Permission Categories Management
        Route::resource('permission-categories', PermissionCategoryController::class);
        Route::patch('permission-categories/{category}/toggle-active', [PermissionCategoryController::class, 'toggleActive'])
            ->name('permission-categories.toggle-active');
    });
});


