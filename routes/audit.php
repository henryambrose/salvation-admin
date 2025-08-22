<?php

use Modules\Members\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/logs', [AuditLogController::class, 'index'])->name('logs.index');
        Route::get('/logs/export', [AuditLogController::class, 'export'])->name('logs.export');
        Route::get('/logs/{auditLog}', [AuditLogController::class, 'show'])->name('logs.show');
        Route::get('/logs/table/{tableName}', [AuditLogController::class, 'table'])->name('logs.table');
        Route::get('/logs/record/{tableName}/{recordId}', [AuditLogController::class, 'record'])->name('logs.record');
        Route::get('/logs/user/{userId}', [AuditLogController::class, 'user'])->name('logs.user');
    });
}); 