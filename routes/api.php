<?php

// use App\Http\Controllers\BloodGroupController;
// use App\Http\Controllers\ZoneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::middleware(['auth:sanctum'])->group(function () {
    // Route::apiResource('zone', ZoneController::class);
    // Route::apiResource('blood-group', BloodGroupController::class);
    // Route::apiResource('family-income-range', FamilyIncomeRangeController::class);
    // Route::apiResource('cells-n-associations', CellsAndAssociationsController::class);
    // Route::apiResource('community', CommunityController::class);
    // Route::apiResource('member', MembersController::class);
// });
