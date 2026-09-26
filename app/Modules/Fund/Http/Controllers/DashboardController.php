<?php

namespace Modules\Fund\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Fund\Models\FamilyContribution;
use Modules\Fund\Models\FundCategory;
use Modules\Fund\Models\MassIntention;

class DashboardController extends Controller
{
    public function index(): Response
    {
        if (! Auth::user()->can('read-fund-dashboard')) {
            abort(403, 'You do not have permission to access the Fund dashboard.');
        }

        $year = now()->year;

        // Contributions received (paid or partially paid) for the current year
        $received = FamilyContribution::whereIn('status', ['paid', 'partial'])
            ->whereYear('start_date', $year);

        $stats = [
            'annualContributionsTotal' => (float) (clone $received)->sum('amount'),
            'activeMassIntentions' => MassIntention::whereIn('status', ['pending', 'confirmed'])
                ->whereDate('mass_date', '>=', now()->toDateString())
                ->count(),
            'contributingFamilies' => (clone $received)
                ->whereNotNull('family_no')
                ->distinct('family_no')
                ->count('family_no'),
            'activeCategories' => FundCategory::where('is_active', true)->count(),
            'year' => $year,
        ];

        return Inertia::render('FundDashboard', ['stats' => $stats]);
    }
}
