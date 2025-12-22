<?php

namespace Modules\Fund\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Fund\Models\FamilyContribution;
use Modules\Fund\Models\FundCategory;
use Modules\Fund\Models\PaymentMethod;
use Modules\Members\Models\Member;
use Illuminate\Support\Facades\Auth; // For Auth

class AnnualContributionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list-annual-contribution');

        $query = FamilyContribution::with(['member', 'fundCategory', 'paymentMethod']);

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

        // Filter by date range - find contributions created within the selected range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // Both dates provided - find contributions created within the filter period
            $query->whereDate('created_at', '>=', $request->start_date)
                  ->whereDate('created_at', '<=', $request->end_date);
        } elseif ($request->filled('start_date')) {
            // Only start date - find contributions created on or after this date
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            // Only end date - find contributions created on or before this date
            $query->whereDate('created_at', '<=', $request->end_date);
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
        $this->authorize('create-annual-contribution');

        $filterOptions = $this->getFilterOptions();

        return Inertia::render('AnnualContributions/Create', [
            'filterOptions' => $filterOptions
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create-annual-contribution');

        $validated = $request->validate([
            'family_no' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'transaction_reference' => 'nullable|string|max:255',
            'fund_category_id' => 'nullable|exists:fund_categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,partial,paid,cancelled,refunded',
            'member_id' => 'nullable|exists:members,id',
            'paid_by_name' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $contribution = FamilyContribution::create([
            'family_no' => $validated['family_no'],
            'amount' => $validated['amount'],
            'payment_method_id' => $validated['payment_method_id'],
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'fund_category_id' => $validated['fund_category_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
            'member_id' => $validated['member_id'],
            'paid_by_name' => $validated['paid_by_name'],
            'contact_no' => $validated['contact_no'],
            'notes' => $validated['notes'],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('fund.annual-contributions.index')
            ->with('success', 'Contribution created successfully.')
            ->with('receipt_id', $contribution->id)
            ->with('receipt_url', route('fund.annual-contributions.receipt', $contribution->id));
    }

    public function show($id)
    {
        $this->authorize('read-annual-contribution');

        $contribution = FamilyContribution::with(['member', 'fundCategory', 'paymentMethod'])
            ->findOrFail($id);

        return Inertia::render('AnnualContributions/Show', [
            'contribution' => $contribution
        ]);
    }

    public function edit($id)
    {
        $this->authorize('update-annual-contribution');

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
        $this->authorize('update-annual-contribution');

        $contribution = FamilyContribution::findOrFail($id);

        $validated = $request->validate([
            'family_no' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'transaction_reference' => 'nullable|string|max:255',
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
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'fund_category_id' => $validated['fund_category_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
            'member_id' => $validated['member_id'],
            'paid_by_name' => $validated['paid_by_name'],
            'contact_no' => $validated['contact_no'],
            'notes' => $validated['notes'],
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('fund.annual-contributions.index')
            ->with('success', 'Contribution updated successfully.');
    }

    public function destroy($id)
    {
        $this->authorize('delete-annual-contribution');

        $contribution = FamilyContribution::findOrFail($id);
        $contribution->delete();

        return back()->with('success', 'Contribution deleted successfully.');
    }

    public function restore($id)
    {
        $this->authorize('restore-annual-contribution');

        $contribution = FamilyContribution::onlyTrashed()->findOrFail($id);
        $contribution->restore();

        return back()->with('success', 'Contribution restored successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $this->authorize('update-annual-contribution');

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:family_contributions,id',
            'status' => 'required|in:pending,partial,paid,cancelled,refunded',
        ]);

        FamilyContribution::whereIn('id', $validated['ids'])
            ->update([
                'status' => $validated['status'],
                'updated_by' => Auth::id()
            ]);

        return back()->with('success', 'Contributions updated successfully.');
    }

    public function downloadReceipt($id)
    {
        $this->authorize('read-annual-contribution');

        $contribution = FamilyContribution::with(['member', 'fundCategory', 'paymentMethod'])
            ->findOrFail($id);

        $memberName = $contribution->member
            ? trim($contribution->member->first_name . ' ' . $contribution->member->last_name)
            : ($contribution->paid_by_name ?? 'N/A');

        $receiptData = [
            'receipt_no' => 'AC-' . str_pad($contribution->id, 6, '0', STR_PAD_LEFT),
            'date' => now()->format('d/m/Y'),
            'family_no' => $contribution->family_no,
            'received_from' => $memberName,
            'amount' => number_format($contribution->amount, 2),
            'amount_words' => $this->numberToWords($contribution->amount),
            'payment_method' => $contribution->paymentMethod->name ?? 'N/A',
            'category' => $contribution->fundCategory->name ?? 'Annual Contribution',
            'period' => date('d/m/Y', strtotime($contribution->start_date)) . ' to ' . date('d/m/Y', strtotime($contribution->end_date)),
            'status' => ucfirst($contribution->status),
            'notes' => $contribution->notes ?? '',
            'created_at' => $contribution->created_at->format('d/m/Y H:i A'),
        ];

        return view('fund.receipts.annual-contribution', $receiptData);
    }

    private function numberToWords($number)
    {
        $amount = number_format($number, 2, '.', '');
        list($rupees, $paise) = explode('.', $amount);

        $words = '';
        if ($rupees > 0) {
            $words = $this->convertNumberToWords((int)$rupees) . ' Rupees';
        }
        if ($paise > 0) {
            $words .= ($words ? ' and ' : '') . $this->convertNumberToWords((int)$paise) . ' Paise';
        }

        return $words ?: 'Zero Rupees';
    }

    private function convertNumberToWords($number)
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

        if ($number < 10) {
            return $ones[$number];
        } elseif ($number < 20) {
            return $teens[$number - 10];
        } elseif ($number < 100) {
            return $tens[intval($number / 10)] . ' ' . $ones[$number % 10];
        } elseif ($number < 1000) {
            return $ones[intval($number / 100)] . ' Hundred ' . $this->convertNumberToWords($number % 100);
        } elseif ($number < 100000) {
            return $this->convertNumberToWords(intval($number / 1000)) . ' Thousand ' . $this->convertNumberToWords($number % 1000);
        } elseif ($number < 10000000) {
            return $this->convertNumberToWords(intval($number / 100000)) . ' Lakh ' . $this->convertNumberToWords($number % 100000);
        } else {
            return $this->convertNumberToWords(intval($number / 10000000)) . ' Crore ' . $this->convertNumberToWords($number % 10000000);
        }
    }

    public function export(Request $request)
    {
        $this->authorize('list-annual-contribution');

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

        // Filter by date range - find contributions created within the selected range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // Both dates provided - find contributions created within the filter period
            $query->whereDate('created_at', '>=', $request->start_date)
                  ->whereDate('created_at', '<=', $request->end_date);
        } elseif ($request->filled('start_date')) {
            // Only start date - find contributions created on or after this date
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            // Only end date - find contributions created on or before this date
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('category_id')) {
            $query->where('fund_category_id', $request->category_id);
        }

        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        $contributions = $query->get();

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
                'Received Date',
               
            ]);

            // CSV Data
            foreach ($contributions as $contribution) {
                fputcsv($file, [
                    $contribution->id,
                    $contribution->family_no,
                    $contribution->amount,
                    $contribution->start_date ? date('d/m/Y', strtotime($contribution->start_date)) : 'N/A',
                    $contribution->end_date ? date('d/m/Y', strtotime($contribution->end_date)) : 'N/A',
                    $contribution->status,
                    $contribution->fundCategory->name ?? 'N/A',
                    $contribution->paymentMethod->name ?? 'N/A',
                    $contribution->member ? $contribution->member->first_name . ' ' . $contribution->member->last_name : 'N/A',
                    $contribution->paid_by_name ?? 'N/A',
                    $contribution->contact_no ?? 'N/A',
                    $contribution->notes ?? 'N/A',
                    $contribution->created_at ? $contribution->created_at->format('d/m/Y H:i') : 'N/A',

                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
