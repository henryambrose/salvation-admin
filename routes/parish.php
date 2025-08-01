<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParishController;

Route::middleware('auth')->group(function () {
  Route::redirect('parish', '/parish/index');

  Route::get('parish/index', [ParishController::class, 'index'])->name('parish.index');
  Route::get('parish/export', [ParishController::class, 'export'])->name('parish.export');
  Route::resource('parish', ParishController::class)->except(['index']);
  Route::post('parish/{id}/restore', [ParishController::class, 'restore'])->name('parish.restore');
});
