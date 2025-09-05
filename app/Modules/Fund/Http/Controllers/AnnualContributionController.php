<?php

namespace Modules\Fund\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Fund\Models\FamilyContribution;
use Modules\Fund\Models\FundCategory;
use Modules\Fund\Models\PaymentMethod;
use Modules\Members\Models\Member;
use Illuminate\Support\Facades\Log;

class AnnualContributionController extends Controller
{
    public function index(Request $request)
    {
        $query = FamilyContribution::with(['member', 'fundCategory', 'paymentMethod'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('family_no', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Handle archived filter
        if ($request->get('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply filters
        if ($request->filled('family_no')) {
            $query->where('family_no', 'like', '%' . $request->family_no . '%');
        }

        // Optional: filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        if ($request->filled('category_id')) {
            $query->where('fund_category_id', $request->category_id);
        }

        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Apply pagination
        $perPage = $request->get('per_page', 10);
        $annualContributions = $query->paginate($perPage);

        // Get filter options
        $filterOptions = $this->getFilterOptions();

        return Inertia::render('AnnualContributions/Index', [
            'annualContributions' => $annualContributions,
            // Years removed from filter options
            'categories' => $filterOptions['fund_categories'],
            'paymentMethods' => $filterOptions['payment_methods'],
            'filters' => $request->only([
                'search',
                'family_no',
                'start_date',
                'end_date',
                'category_id',
                'payment_method_id',
                'per_page',
                'isArchived'
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
            // Years removed
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
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'fund_category_id' => 'nullable|exists:fund_categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,partial,paid,cancelled,refunded',
            'member_id' => 'nullable|exists:members,id',
            'paid_by_name' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        FamilyContribution::create([
            'family_no' => $validated['family_no'],
            'amount' => $validated['amount'],
            'payment_method_id' => $validated['payment_method_id'],
            'fund_category_id' => $validated['fund_category_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
            'member_id' => $validated['member_id'],
            'paid_by_name' => $validated['paid_by_name'],
            'contact_no' => $validated['contact_no'],
            'notes' => $validated['notes'],
            'created_by' => auth()->id,
            'updated_by' => auth()->id,
        ]);

        return redirect()->route('fund.annual-contributions.index')
            ->with('success', 'Contribution created successfully.');
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

        // Get all members for the dropdown
        $members = Member::orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        return Inertia::render('AnnualContributions/Edit', [
            'contribution' => $contribution,
            'filterOptions' => $filterOptions,
            'members' => $members
        ]);
    }

    public function update(Request $request, $id)
    {
        $contribution = FamilyContribution::findOrFail($id);

        $validated = $request->validate([
            'family_no' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'fund_category_id' => 'nullable|exists:fund_categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,partial,paid,cancelled,refunded',
            'member_id' => 'nullable|exists:members,id',
            'paid_by_name' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        // Update the existing contribution
        $contribution->update([
            'family_no' => $validated['family_no'],
            'amount' => $validated['amount'],
            'payment_method_id' => $validated['payment_method_id'],
            'fund_category_id' => $validated['fund_category_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
            'member_id' => $validated['member_id'],
            'paid_by_name' => $validated['paid_by_name'],
            'contact_no' => $validated['contact_no'],
            'notes' => $validated['notes'],
            'updated_by' => auth()->id,
        ]);

        return redirect()->route('fund.annual-contributions.index')
            ->with('success', 'Contribution updated successfully.');
    }

    public function destroy($id)
    {
        $contribution = FamilyContribution::findOrFail($id);
        $contribution->delete();

        return back()->with('success', 'Contribution deleted successfully.');
    }

    public function restore($id)
    {
        $contribution = FamilyContribution::onlyTrashed()->findOrFail($id);
        $contribution->restore();

        return back()->with('success', 'Contribution restored successfully.');
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
                'updated_by' => auth()->id
            ]);

        return back()->with('success', 'Contributions updated successfully.');
    }

    public function export(Request $request)
    {
        Log::info('Export method called with parameters:', $request->all());

        $query = FamilyContribution::with(['member', 'fundCategory', 'paymentMethod'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('family_no', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Handle archived filter
        if ($request->get('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply filters
        if ($request->filled('family_no')) {
            $query->where('family_no', 'like', '%' . $request->family_no . '%');
        }

        // Optional: filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        if ($request->filled('category_id')) {
            $query->where('fund_category_id', $request->category_id);
        }

        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        $contributions = $query->get();

        Log::info('Export query returned ' . $contributions->count() . ' records');

        $filename = 'annual-contributions-' . date('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public',
        ];

        $callback = function () use ($contributions) {
            // Clear any output buffers
            if (ob_get_level()) {
                ob_end_clean();
            }

            $file = fopen('php://output', 'w');

            // Add BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");

            // CSV Headers
            fputcsv($file, [
                'ID',
                'Family Number',
                'Amount',
                'Start Date',
                'End Date',
                'Status',
                'Fund Category',
                'Payment Method',
                'Member Name',
                'Paid By Name',
                'Contact Number',
                'Notes',
                'Created At',
                'Updated At'
            ]);

            // CSV Data
            foreach ($contributions as $contribution) {
                fputcsv($file, [
                    $contribution->id,
                    $contribution->family_no,
                    $contribution->amount,
                    $contribution->start_date,
                    $contribution->end_date,
                    $contribution->status,
                    $contribution->fundCategory->name ?? 'N/A',
                    $contribution->paymentMethod->name ?? 'N/A',
                    $contribution->member ? $contribution->member->first_name . ' ' . $contribution->member->last_name : 'N/A',
                    $contribution->paid_by_name ?? 'N/A',
                    $contribution->contact_no ?? 'N/A',
                    $contribution->notes ?? 'N/A',
                    $contribution->created_at,
                    $contribution->updated_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
