<?php

namespace Modules\Fund\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Fund\Models\FamilyContribution;
use Modules\Fund\Models\FundCategory;
use Modules\Fund\Models\PaymentMethod;
use Modules\Members\Models\Member;
use Illuminate\Support\Facades\DB;

class AnnualContributionController extends Controller
{
    public function index(Request $request)
    {
        $query = FamilyContribution::with(['member', 'fundCategory', 'paymentMethod'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('family_no')) {
            $query->where('family_no', 'like', '%' . $request->family_no . '%');
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('fund_category_id')) {
            $query->where('fund_category_id', $request->fund_category_id);
        }

        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $contributions = $query->paginate(15)->withQueryString();

        // Get summary statistics
        $currentYear = date('Y');
        $stats = $this->getContributionStats($currentYear);

        // Get filter options
        $filterOptions = $this->getFilterOptions();

        return Inertia::render('AnnualContributions/Index', [
            'contributions' => $contributions,
            'stats' => $stats,
            'filterOptions' => $filterOptions,
            'filters' => $request->only([
                'family_no', 'year', 'status', 'fund_category_id', 
                'payment_method_id', 'member_id', 'date_from', 'date_to'
            ])
        ]);
    }

    private function getContributionStats($year)
    {
        $stats = FamilyContribution::selectRaw('
            COUNT(DISTINCT family_no) as total_families,
            COUNT(*) as total_contributions,
            SUM(amount) as total_amount,
            COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = "partial" THEN 1 END) as partial_count,
            COUNT(CASE WHEN status = "paid" THEN 1 END) as paid_count
        ')
        ->where('year', $year)
        ->first();

        // Calculate monthly average
        $monthlyStats = FamilyContribution::selectRaw('
            MONTH(payment_date) as month,
            SUM(amount) as monthly_amount
        ')
        ->where('year', $year)
        ->where('status', '!=', 'cancelled')
        ->groupBy('month')
        ->get();

        $monthlyAverage = $monthlyStats->count() > 0 
            ? $monthlyStats->avg('monthly_amount') 
            : 0;

        return [
            'total_families' => $stats->total_families ?? 0,
            'total_contributions' => $stats->total_contributions ?? 0,
            'total_amount' => $stats->total_amount ?? 0,
            'pending_count' => $stats->pending_count ?? 0,
            'partial_count' => $stats->partial_count ?? 0,
            'paid_count' => $stats->paid_count ?? 0,
            'monthly_average' => round($monthlyAverage, 2),
            'current_year' => $year
        ];
    }

    private function getFilterOptions()
    {
        return [
            'years' => FamilyContribution::distinct()->pluck('year')->sort()->values(),
            'statuses' => [
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'partial', 'label' => 'Partial'],
                ['value' => 'paid', 'label' => 'Paid'],
                ['value' => 'cancelled', 'label' => 'Cancelled'],
                ['value' => 'refunded', 'label' => 'Refunded'],
            ],
            'fund_categories' => FundCategory::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name']),
            'payment_methods' => PaymentMethod::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name']),
        ];
    }

    public function create()
    {
        $filterOptions = $this->getFilterOptions();
        
        return Inertia::render('AnnualContributions/Create', [
            'filterOptions' => $filterOptions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'family_no' => 'required|string|max:50',
            'years' => 'required|array|min:1',
            'years.*' => 'integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'fund_category_id' => 'nullable|exists:fund_categories,id',
            'payment_date' => 'required|date',
            'status' => 'required|in:pending,partial,paid,cancelled,refunded',
            'member_id' => 'nullable|exists:members,id',
            'paid_by_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $years = $validated['years'];
        $totalAmount = $validated['amount'];
        $totalYears = count($years);
        
        // Calculate split amounts for each year
        $baseAmount = floor($totalAmount / $totalYears);
        $remainder = $totalAmount % $totalYears;

        // Create contributions for each year
        $contributions = [];
        foreach ($years as $index => $year) {
            // First year(s) get the extra amount if there's a remainder
            $yearAmount = $index < $remainder ? $baseAmount + 1 : $baseAmount;
            
            $contributionData = [
                'family_no' => $validated['family_no'],
                'year' => $year,
                'amount' => $yearAmount,
                'payment_method_id' => $validated['payment_method_id'],
                'fund_category_id' => $validated['fund_category_id'],
                'payment_date' => $validated['payment_date'],
                'status' => $validated['status'],
                'member_id' => $validated['member_id'],
                'paid_by_name' => $validated['paid_by_name'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ];
            
            $contributions[] = FamilyContribution::create($contributionData);
        }

        $message = $totalYears > 1 
            ? "Created {$totalYears} contributions with total amount ₹{$totalAmount} split equally between years: " . implode(', ', $years)
            : 'Contribution created successfully.';

        return redirect()->route('fund.annual-contributions.index')
            ->with('success', $message);
    }

    public function show($id)
    {
        $contribution = FamilyContribution::with(['member', 'fundCategory', 'paymentMethod'])
            ->findOrFail($id);

        return Inertia::render('AnnualContributions/Show', [
            'contribution' => $contribution
        ]);
    }

    public function edit($id)
    {
        $contribution = FamilyContribution::findOrFail($id);
        $filterOptions = $this->getFilterOptions();

        return Inertia::render('AnnualContributions/Edit', [
            'contribution' => $contribution,
            'filterOptions' => $filterOptions
        ]);
    }

    public function update(Request $request, $id)
    {
        $contribution = FamilyContribution::findOrFail($id);

        $validated = $request->validate([
            'family_no' => 'required|string|max:50',
            'years' => 'required|array|min:1',
            'years.*' => 'integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'fund_category_id' => 'nullable|exists:fund_categories,id',
            'payment_date' => 'required|date',
            'status' => 'required|in:pending,partial,paid,cancelled,refunded',
            'member_id' => 'nullable|exists:members,id',
            'paid_by_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $years = $validated['years'];
        $totalAmount = $validated['amount'];
        $totalYears = count($years);
        
        // Delete the existing contribution
        $contribution->delete();
        
        // Calculate split amounts for each year
        $baseAmount = floor($totalAmount / $totalYears);
        $remainder = $totalAmount % $totalYears;

        // Create new contributions for each year
        $contributions = [];
        foreach ($years as $index => $year) {
            // First year(s) get the extra amount if there's a remainder
            $yearAmount = $index < $remainder ? $baseAmount + 1 : $baseAmount;
            
            $contributionData = [
                'family_no' => $validated['family_no'],
                'year' => $year,
                'amount' => $yearAmount,
                'payment_method_id' => $validated['payment_method_id'],
                'fund_category_id' => $validated['fund_category_id'],
                'payment_date' => $validated['payment_date'],
                'status' => $validated['status'],
                'member_id' => $validated['member_id'],
                'paid_by_name' => $validated['paid_by_name'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ];
            
            $contributions[] = FamilyContribution::create($contributionData);
        }

        $message = $totalYears > 1 
            ? "Updated to {$totalYears} contributions with total amount ₹{$totalAmount} split equally between years: " . implode(', ', $years)
            : 'Contribution updated successfully.';

        return redirect()->route('fund.annual-contributions.index')
            ->with('success', $message);
    }

    public function destroy($id)
    {
        $contribution = FamilyContribution::findOrFail($id);
        $contribution->delete();

        return redirect()->route('fund.annual-contributions.index')
            ->with('success', 'Contribution deleted successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:family_contributions,id',
            'status' => 'required|in:pending,partial,paid,cancelled,refunded',
        ]);

        FamilyContribution::whereIn('id', $validated['ids'])
            ->update([
                'status' => $validated['status'],
                'updated_by' => auth()->id()
            ]);

        return back()->with('success', 'Contributions updated successfully.');
    }
}
