<?php

use Illuminate\Support\Facades\Route;
use Modules\Fund\Http\Controllers\AnnualContributionController;

/*
|--------------------------------------------------------------------------
| Fund Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your Fund module.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

Route::middleware(['auth'])->prefix('fund')->name('fund.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        return inertia('Dashboard');
    })->name('dashboard');

    // Annual Contributions
    Route::resource('annual-contributions', AnnualContributionController::class);
    Route::post('annual-contributions/bulk-update', [AnnualContributionController::class, 'bulkUpdate'])->name('annual-contributions.bulk-update');

    // Mass Intentions (placeholder routes for now)
    Route::get('mass-intentions', function () {
        return inertia('MassIntentions/Index');
    })->name('mass-intentions.index');
    
    Route::get('mass-intentions/create', function () {
        return inertia('MassIntentions/Create');
    })->name('mass-intentions.create');
    
    Route::get('mass-intentions/{id}', function ($id) {
        return inertia('MassIntentions/Show', ['id' => $id]);
    })->name('mass-intentions.show');
    
    Route::get('mass-intentions/{id}/edit', function ($id) {
        return inertia('MassIntentions/Edit', ['id' => $id]);
    })->name('mass-intentions.edit');

    // Permission Denied
    Route::get('permission-denied', function () {
        return inertia('PermissionDenied');
    })->name('permission-denied');

    // API-like endpoints for contribution history and pending amounts
    Route::get('family-contributions/{familyNo}', function ($familyNo) {
        $contributions = \Modules\Fund\Models\FamilyContribution::with(['fundCategory', 'paymentMethod'])
            ->where('family_no', $familyNo)
            ->orderBy('year', 'desc')
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($contribution) {
                return [
                    'id' => $contribution->id,
                    'year' => $contribution->year,
                    'amount' => $contribution->amount,
                    'status' => $contribution->status,
                    'payment_date' => $contribution->payment_date,
                    'category_name' => $contribution->fundCategory->name ?? 'N/A',
                    'payment_method' => $contribution->paymentMethod->name ?? 'N/A',
                    'notes' => $contribution->notes
                ];
            });
        
        $totalContributions = $contributions->count();
        $totalPaid = $contributions->where('status', 'paid')->sum('amount');
        $totalPending = $contributions->whereIn('status', ['pending', 'partial'])->sum('amount');
        
        return response()->json([
            'contributions' => $contributions,
            'total_contributions' => $totalContributions,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending
        ]);
    })->name('family-contributions');

    Route::get('pending-amounts/{familyNo}/{year}', function ($familyNo, $year) {
        // Get all fund categories
        $categories = \Modules\Fund\Models\FundCategory::all();
        
        $pendingAmounts = $categories->map(function ($category) use ($familyNo, $year) {
            // Get contributions for this category and year
            $contributions = \Modules\Fund\Models\FamilyContribution::where('family_no', $familyNo)
                ->where('year', $year)
                ->where('fund_category_id', $category->id)
                ->get();
            
            $paidAmount = $contributions->where('status', 'paid')->sum('amount');
            $partialAmount = $contributions->where('status', 'partial')->sum('amount');
            $pendingAmount = $contributions->where('status', 'pending')->sum('amount');
            
            $totalPaid = $paidAmount + $partialAmount;
            
            return [
                'id' => $category->id,
                'name' => $category->name,
                'year' => $year,
                'paid_amount' => $totalPaid,
                'pending_amount' => $pendingAmount,
                'contributions' => $contributions->count()
            ];
        })->filter(function ($category) {
            return $category['pending_amount'] > 0 || $category['contributions'] > 0;
        })->values();
        
        $totalPending = $pendingAmounts->sum('pending_amount');
        
        return response()->json([
            'pending_amounts' => $pendingAmounts,
            'total_pending' => $totalPending
        ]);
    })->name('pending-amounts');

    // New route for pending amounts by specific category
    Route::get('pending-amounts/{familyNo}/{year}/{categoryId}', function ($familyNo, $year, $categoryId) {
        $category = \Modules\Fund\Models\FundCategory::find($categoryId);
        if (!$category) {
            return response()->json(['pending_amounts' => [], 'total_pending' => 0]);
        }
        
        $contributions = \Modules\Fund\Models\FamilyContribution::where('family_no', $familyNo)
            ->where('year', $year)
            ->where('fund_category_id', $categoryId)
            ->get();
        
        $paidAmount = $contributions->where('status', 'paid')->sum('amount');
        $partialAmount = $contributions->where('status', 'partial')->sum('amount');
        $pendingAmount = $contributions->where('status', 'pending')->sum('amount');
        
        $totalPaid = $paidAmount + $partialAmount;
        
        $pendingAmounts = [
            [
                'id' => $category->id,
                'name' => $category->name,
                'year' => $year,
                'paid_amount' => $totalPaid,
                'pending_amount' => $pendingAmount,
                'contributions' => $contributions->count()
            ]
        ];
        
        $totalPending = $pendingAmount;
        return response()->json(['pending_amounts' => $pendingAmounts, 'total_pending' => $totalPending]);
    })->name('pending-amounts-by-category');
});
