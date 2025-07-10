<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('users/index', [UserController::class, 'index'])->name('users.index');
    Route::resource('users', UserController::class)->except(['index']);
});