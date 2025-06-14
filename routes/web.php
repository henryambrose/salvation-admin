<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // return Inertia::render('Welcome');
    return redirect()->route('dashboard');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/member.php';
require __DIR__.'/community.php';
require __DIR__.'/zones.php';
require __DIR__.'/blood_group.php';
require __DIR__.'/family_income_range.php';
require __DIR__.'/s_c_c_head.php';
require __DIR__.'/auth.php';
