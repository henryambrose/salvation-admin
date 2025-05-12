<?php

use App\Http\Controllers\FamilyIncomeRangeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('family-income-range', '/family-income-range/index');

    Route::get('family-income-range/index', [FamilyIncomeRangeController::class, 'index'])->name('family-income-range.index');
    Route::resource('family-income-range', FamilyIncomeRangeController::class)->except(['index']);

});
