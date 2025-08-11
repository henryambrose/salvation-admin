<?php

use App\Http\Controllers\CatholicCalendarController;
use App\Http\Controllers\SaintsController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ExternalMemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

// Public API routes
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Catholic Calendar and Saints API
Route::prefix('catholic')->group(function () {
    Route::get('/calendar', [CatholicCalendarController::class, 'index']);
    Route::get('/saints', [SaintsController::class, 'index']);
    Route::get('/saints/saint-of-the-day', [SaintsController::class, 'saintOfTheDay']);
    Route::get('/saints/image', [SaintsController::class, 'getSaintImageByName']);
});

// Member and Family API
Route::prefix('members')->group(function () {
    Route::get('/next-numbers', [MemberController::class, 'getNextAvailableNumbers']);
    Route::get('/search-spouse', [MemberController::class, 'searchMembers']);
    Route::get('/community/{community}', [MemberController::class, 'getMembersByCommunity']);
});

Route::prefix('families')->group(function () {
    Route::get('/{familyNo}/members', [MemberController::class, 'getMembersByFamily']);
    Route::get('/search', [MemberController::class, 'searchFamilies']);
    Route::get('/{familyNo}/details', [MemberController::class, 'getFamilyDetails']);
});

// Family Numbers Search API
Route::get('/family-numbers/search', [ExternalMemberController::class, 'searchFamilyNumbers']);

// Family Tree API
Route::prefix('family-tree')->group(function () {
    Route::get('/member/{id}/data', [MemberController::class, 'getFamilyTreeData']);
    Route::get('/search-members', [MemberController::class, 'searchFamilyMembers']);
    Route::post('/add-relationship', [MemberController::class, 'addFamilyRelationship']);
    Route::delete('/remove-relationship', [MemberController::class, 'removeFamilyRelationship']);
});

// External Members API
Route::prefix('external-members')->group(function () {
    Route::get('/search-all', [ExternalMemberController::class, 'searchAll']);
    Route::get('/{id}', [ExternalMemberController::class, 'getDetails']);
});

// Utility API routes
Route::get('/cities', function () {
    return \App\Models\City::select('id', 'name')->orderBy('name')->get();
});

Route::post('/validate-parish', function (Request $request) {
    $name = trim($request->name);
    
    if (empty($name)) {
        return response()->json(['valid' => true, 'exists' => false, 'similar' => []]);
    }
    
    $exact = \App\Models\Parish::whereRaw('LOWER(name) = ?', [Str::lower($name)])->first();
    if ($exact) {
        return response()->json([
            'valid' => false,
            'exists' => true,
            'message' => "Parish '{$name}' already exists. Please select it from the dropdown.",
            'similar' => []
        ]);
    }
    
    $similar = \App\Models\Parish::where(function ($query) use ($name) {
        $query->whereRaw('LOWER(name) LIKE ?', ['%' . Str::lower($name) . '%'])
              ->orWhereRaw('LOWER(name) LIKE ?', ['%' . Str::lower(str_replace(' ', '%', $name)) . '%']);
    })->limit(5)->pluck('name');
        
    return response()->json(['valid' => true, 'exists' => false, 'similar' => $similar]);
});
