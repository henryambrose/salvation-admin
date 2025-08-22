<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['web', 'auth'])->prefix('fund')->name('fund.')->group(function () {
    
    // Fund Dashboard
    Route::get('/', function () {
        return Inertia::render('Dashboard/Index');
    })->name('dashboard');
    
    // Annual Contributions Routes
    Route::prefix('annual-contributions')->name('annual-contributions.')->group(function () {
        Route::get('/', function () {
            return Inertia::render('AnnualContributions/Index');
        })->name('index');
        
        Route::get('/create', function () {
            return Inertia::render('AnnualContributions/Create');
        })->name('create');
        
        Route::get('/{id}', function ($id) {
            return Inertia::render('AnnualContributions/Show', ['id' => $id]);
        })->name('show');
        
        Route::get('/{id}/edit', function ($id) {
            return Inertia::render('AnnualContributions/Edit', ['id' => $id]);
        })->name('edit');
    });
    
    // Mass Intentions Routes
    Route::prefix('mass-intentions')->name('mass-intentions.')->group(function () {
        Route::get('/', function () {
            return Inertia::render('MassIntentions/Index');
        })->name('index');
        
        Route::get('/create', function () {
            return Inertia::render('MassIntentions/Create');
        })->name('create');
        
        Route::get('/{id}', function ($id) {
            return Inertia::render('MassIntentions/Show', ['id' => $id]);
        })->name('show');
        
        Route::get('/{id}/edit', function ($id) {
            return Inertia::render('MassIntentions/Edit', ['id' => $id]);
        })->name('edit');
    });
});
