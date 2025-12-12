<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Members\Models\Parish;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Parish validation endpoint
Route::middleware(['web', 'auth'])->post('/validate-parish', function (Request $request) {
    $parishName = trim($request->input('name', ''));
    $originalValue = trim($request->input('original', ''));

    if (empty($parishName)) {
        return response()->json([
            'valid' => false,
            'exists' => false,
            'similar' => [],
            'message' => 'Parish name is required.'
        ]);
    }

    // Skip validation if the value hasn't changed (editing existing member)
    if (!empty($originalValue) && Str::lower($parishName) === Str::lower($originalValue)) {
        return response()->json([
            'valid' => true,
            'exists' => false,
            'similar' => [],
            'message' => 'Using existing parish value.'
        ]);
    }

    // Check for similar names (fuzzy matching) - show as suggestions, not errors
    $similarParishes = Parish::where(function ($query) use ($parishName) {
        $query->whereRaw('LOWER(name) LIKE ?', ['%' . Str::lower($parishName) . '%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . Str::lower(str_replace(' ', '%', $parishName)) . '%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . Str::lower(str_replace(['St.', 'St '], 'Saint ', $parishName)) . '%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . Str::lower(str_replace('Saint ', 'St. ', $parishName)) . '%']);
    })->limit(5)->pluck('name')->toArray();

    if (count($similarParishes) > 0) {
        return response()->json([
            'valid' => true,
            'exists' => false,
            'similar' => $similarParishes,
            'message' => 'Similar parishes found. Consider using one of these or enter a different name.'
        ]);
    }

    return response()->json([
        'valid' => true,
        'exists' => false,
        'similar' => [],
        'message' => 'Parish name is valid.'
    ]);
});

