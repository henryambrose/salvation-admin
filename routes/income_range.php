<?php

use App\Http\Controllers\IncomeRangeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('income-range', '/income-range/index');

    Route::get('income-range/index', [IncomeRangeController::class, 'index'])->name('income-range.index');
Route::resource('income-range', IncomeRangeController::class)->except(['index']);
Route::post('/income-range/{id}/restore', [IncomeRangeController::class, 'restore'])->name('income-range.restore');

});
