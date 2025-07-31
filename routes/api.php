<?php

// use App\Http\Controllers\BloodGroupController;
// use App\Http\Controllers\ZoneController;
use App\Http\Controllers\CatholicCalendarController;
use App\Http\Controllers\SaintsController;
use App\Http\Controllers\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Catholic Calendar and Saints API Routes
Route::get('/catholic-calendar', [CatholicCalendarController::class, 'index']);
Route::get('/saints', [SaintsController::class, 'index']);
Route::get('/saints/saint-of-the-day', [SaintsController::class, 'saintOfTheDay']);
Route::get('/saints/image', [SaintsController::class, 'getSaintImageByName']);

// Community Members API Route
Route::get('/community/{community}/members', [MemberController::class, 'getMembersByCommunity']);

// Family Members API Route
Route::get('/family/{familyNo}/members', [MemberController::class, 'getMembersByFamily']);

// Family Search API Route
Route::get('/families/search', [MemberController::class, 'searchFamilies']);

// Route::middleware(['auth:sanctum'])->group(function () {
    // Route::apiResource('zone', ZoneController::class);
    // Route::apiResource('blood-group', BloodGroupController::class);
    // Route::apiResource('family-income-range', FamilyIncomeRangeController::class);
    // Route::apiResource('cells-n-associations', CellsAndAssociationsController::class);
    // Route::apiResource('community', CommunityController::class);
    // Route::apiResource('member', MembersController::class);
// });
