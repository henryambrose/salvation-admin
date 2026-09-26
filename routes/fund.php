<?php

use Illuminate\Support\Facades\Route;
use Modules\Fund\Http\Controllers\AnnualContributionController;
use Modules\Fund\Http\Controllers\CommunityContributionController;
use Modules\Fund\Http\Controllers\CommunityContributionTypeController;
use Modules\Fund\Http\Controllers\DashboardController;
use Modules\Fund\Http\Controllers\FundCategoryController;
use Modules\Fund\Http\Controllers\MassIntentionController;
use Modules\Fund\Http\Controllers\MassIntentionTypeController;
use Modules\Fund\Http\Controllers\MassTypeController;
use Modules\Fund\Http\Controllers\PaymentMethodController;

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
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Fund Categories
    Route::get('categories', [FundCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [FundCategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}', [FundCategoryController::class, 'show'])->name('categories.show');
    Route::put('categories/{category}', [FundCategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [FundCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('categories/{id}/restore', [FundCategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{id}/force-delete', [FundCategoryController::class, 'forceDelete'])->name('categories.force-delete');

    // Mass Intention Types
    Route::get('mass-intention-types', [MassIntentionTypeController::class, 'index'])->name('mass-intention-types.index');
    Route::post('mass-intention-types', [MassIntentionTypeController::class, 'store'])->name('mass-intention-types.store');
    Route::get('mass-intention-types/{massIntentionType}', [MassIntentionTypeController::class, 'show'])->name('mass-intention-types.show');
    Route::put('mass-intention-types/{massIntentionType}', [MassIntentionTypeController::class, 'update'])->name('mass-intention-types.update');
    Route::delete('mass-intention-types/{massIntentionType}', [MassIntentionTypeController::class, 'destroy'])->name('mass-intention-types.destroy');
    Route::post('mass-intention-types/{id}/restore', [MassIntentionTypeController::class, 'restore'])->name('mass-intention-types.restore');
    Route::delete('mass-intention-types/{id}/force-delete', [MassIntentionTypeController::class, 'forceDelete'])->name('mass-intention-types.force-delete');

    // Mass Types
    Route::get('mass-types', [MassTypeController::class, 'index'])->name('mass-types.index');
    Route::post('mass-types', [MassTypeController::class, 'store'])->name('mass-types.store');
    Route::get('mass-types/{massType}', [MassTypeController::class, 'show'])->name('mass-types.show');
    Route::put('mass-types/{massType}', [MassTypeController::class, 'update'])->name('mass-types.update');
    Route::delete('mass-types/{massType}', [MassTypeController::class, 'destroy'])->name('mass-types.destroy');
    Route::post('mass-types/{id}/restore', [MassTypeController::class, 'restore'])->name('mass-types.restore');
    Route::delete('mass-types/{id}/force-delete', [MassTypeController::class, 'forceDelete'])->name('mass-types.force-delete');

    // Payment Methods
    Route::get('payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::post('payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::get('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'show'])->name('payment-methods.show');
    Route::put('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
    Route::delete('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');
    Route::post('payment-methods/{id}/restore', [PaymentMethodController::class, 'restore'])->name('payment-methods.restore');
    Route::delete('payment-methods/{id}/force-delete', [PaymentMethodController::class, 'forceDelete'])->name('payment-methods.force-delete');

    // Annual Contributions
    Route::get('annual-contributions/export', [AnnualContributionController::class, 'export'])->name('annual-contributions.export');
    Route::get('annual-contributions/{id}/receipt', [AnnualContributionController::class, 'downloadReceipt'])->name('annual-contributions.receipt');
    Route::resource('annual-contributions', AnnualContributionController::class);
    Route::post('annual-contributions/bulk-update', [AnnualContributionController::class, 'bulkUpdate'])->name('annual-contributions.bulk-update');
    Route::post('annual-contributions/{id}/restore', [AnnualContributionController::class, 'restore'])->name('annual-contributions.restore');

    // Community Contributions
    Route::get('community-contributions/stats', [CommunityContributionController::class, 'getStats'])->name('community-contributions.stats');
    Route::get('community-contributions/{id}/receipt', [CommunityContributionController::class, 'downloadReceipt'])->name('community-contributions.receipt');
    Route::resource('community-contributions', CommunityContributionController::class);
    Route::post('community-contributions/{id}/restore', [CommunityContributionController::class, 'restore'])->name('community-contributions.restore');
    Route::put('community-contributions/{communityContribution}/status', [CommunityContributionController::class, 'updateStatus'])->name('community-contributions.update-status');

    // Community Contribution Types
    Route::get('community-contribution-types', [CommunityContributionTypeController::class, 'index'])->name('community-contribution-types.index');
    Route::post('community-contribution-types', [CommunityContributionTypeController::class, 'store'])->name('community-contribution-types.store');
    Route::get('community-contribution-types/{contributionType}', [CommunityContributionTypeController::class, 'show'])->name('community-contribution-types.show');
    Route::put('community-contribution-types/{contributionType}', [CommunityContributionTypeController::class, 'update'])->name('community-contribution-types.update');
    Route::delete('community-contribution-types/{contributionType}', [CommunityContributionTypeController::class, 'destroy'])->name('community-contribution-types.destroy');
    Route::post('community-contribution-types/{id}/restore', [CommunityContributionTypeController::class, 'restore'])->name('community-contribution-types.restore');
    Route::delete('community-contribution-types/{id}/force-delete', [CommunityContributionTypeController::class, 'forceDelete'])->name('community-contribution-types.force-delete');

    // Mass Intentions
    Route::get('mass-intentions/export', [MassIntentionController::class, 'export'])->name('mass-intentions.export');
    Route::get('mass-intentions/search/members', [MassIntentionController::class, 'searchMembers'])->name('mass-intentions.search-members');
    Route::get('mass-intentions', [MassIntentionController::class, 'index'])->name('mass-intentions.index');
    Route::get('mass-intentions/create', [MassIntentionController::class, 'create'])->name('mass-intentions.create');
    Route::post('mass-intentions', [MassIntentionController::class, 'store'])->name('mass-intentions.store');
    Route::get('mass-intentions/{massIntention}/receipt', [MassIntentionController::class, 'downloadReceipt'])->name('mass-intentions.receipt');
    Route::get('mass-intentions/{massIntention}', [MassIntentionController::class, 'show'])->name('mass-intentions.show');
    Route::get('mass-intentions/{massIntention}/edit', [MassIntentionController::class, 'edit'])->name('mass-intentions.edit');
    Route::put('mass-intentions/{massIntention}', [MassIntentionController::class, 'update'])->name('mass-intentions.update');
    Route::delete('mass-intentions/{massIntention}', [MassIntentionController::class, 'destroy'])->name('mass-intentions.destroy');
    Route::post('mass-intentions/{id}/restore', [MassIntentionController::class, 'restore'])->name('mass-intentions.restore');
    Route::put('mass-intentions/{massIntention}/status', [MassIntentionController::class, 'updateStatus'])->name('mass-intentions.update-status');

    // Permission Denied
    Route::get('permission-denied', function () {
        return inertia('PermissionDenied');
    })->name('permission-denied');

    // API-like endpoints for contribution history and pending amounts
    Route::get('family-contributions/{familyNo}', function ($familyNo) {
        $contributions = \Modules\Fund\Models\FamilyContribution::with(['fundCategory', 'paymentMethod', 'member'])
            ->where('family_no', $familyNo)
            ->orderBy('start_date', 'asc')
            ->orderBy('end_date', 'asc')
            ->get()
            ->map(function ($contribution) {
                $paidBy = $contribution->member_id && $contribution->member
                    ? trim(($contribution->member->first_name ?? '').' '.($contribution->member->last_name ?? ''))
                    : ($contribution->paid_by_name ?? '');

                return [
                    'id' => $contribution->id,
                    'start_date' => $contribution->start_date,
                    'end_date' => $contribution->end_date,
                    'amount' => $contribution->amount,
                    'status' => $contribution->status,
                    'category_name' => $contribution->fundCategory->name ?? 'N/A',
                    'payment_method' => $contribution->paymentMethod->name ?? 'N/A',
                    'paid_by' => $paidBy,
                    'date_of_payment' => $contribution->created_at,
                    'notes' => $contribution->notes,
                ];
            });

        $totalContributions = $contributions->count();
        $totalPaid = $contributions->where('status', 'paid')->sum('amount');
        $totalPending = $contributions->whereIn('status', ['pending', 'partial'])->sum('amount');

        return response()->json([
            'contributions' => $contributions,
            'total_contributions' => $totalContributions,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
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
                'contributions' => $contributions->count(),
            ];
        })->filter(function ($category) {
            return $category['pending_amount'] > 0 || $category['contributions'] > 0;
        })->values();

        $totalPending = $pendingAmounts->sum('pending_amount');

        return response()->json([
            'pending_amounts' => $pendingAmounts,
            'total_pending' => $totalPending,
        ]);
    })->name('pending-amounts');

    // New route for pending amounts by specific category
    Route::get('pending-amounts/{familyNo}/{year}/{categoryId}', function ($familyNo, $year, $categoryId) {
        $category = \Modules\Fund\Models\FundCategory::find($categoryId);
        if (! $category) {
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
                'contributions' => $contributions->count(),
            ],
        ];

        $totalPending = $pendingAmount;

        return response()->json(['pending_amounts' => $pendingAmounts, 'total_pending' => $totalPending]);
    })->name('pending-amounts-by-category');
});
