<?php

// use App\Http\Controllers\BloodGroupController;
// use App\Http\Controllers\ZoneController;
use App\Http\Controllers\CatholicCalendarController;
use App\Http\Controllers\SaintsController;
use App\Http\Controllers\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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

// Next Available Numbers API Route
Route::get('/members/next-numbers', [MemberController::class, 'getNextAvailableNumbers']);

// Member Search API Route for Spouse Selection
Route::get('/members/search-spouse', [MemberController::class, 'searchMembers']);

// Parish validation endpoint
Route::post('/validate-parish', function (Illuminate\Http\Request $request) {
    $name = trim($request->name);
    
    if (empty($name)) {
        return response()->json([
            'valid' => true,
            'exists' => false,
            'similar' => []
        ]);
    }
    
    // Check exact match (case-insensitive)
    $exact = \App\Models\Parish::whereRaw('LOWER(name) = ?', [Str::lower($name)])->first();
    if ($exact) {
        return response()->json([
            'valid' => false,
            'exists' => true,
            'message' => "Parish '{$name}' already exists. Please select it from the dropdown.",
            'similar' => []
        ]);
    }
    
    // Check similar names
    $similar = \App\Models\Parish::where(function ($query) use ($name) {
        $query->whereRaw('LOWER(name) LIKE ?', ['%' . Str::lower($name) . '%'])
              ->orWhereRaw('LOWER(name) LIKE ?', ['%' . Str::lower(str_replace(' ', '%', $name)) . '%']);
    })->limit(5)->pluck('name');
        
    return response()->json([
        'valid' => true,
        'exists' => false,
        'similar' => $similar
    ]);
});

// Route::middleware(['auth:sanctum'])->group(function () {
    // Route::apiResource('zone', ZoneController::class);
    // Route::apiResource('blood-group', BloodGroupController::class);
    // Route::apiResource('income-range', IncomeRangeController::class);
    // Route::apiResource('cells-n-associations', CellsAndAssociationsController::class);
    // Route::apiResource('community', CommunityController::class);
    // Route::apiResource('member', MembersController::class);
// });

// Cities API Route for town forms - placed outside middleware groups
Route::get('/cities', function () {
    return \App\Models\City::select('id', 'name')->orderBy('name')->get();
});
