<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\Graveyard\Models\PermanentGrave;
use Modules\Members\Models\Member;
use PDF;

class PermanentGraveController extends Controller
{
    /**
     * Display a listing of permanent graves
     */
    public function index(Request $request)
    {
        $this->authorize('list-permanent-grave');
        $query = PermanentGrave::query()->with(['member', 'maintenancePayments' => function($query) {
            $query->latest()->limit(1);
        }]);

        // Archive logic
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->search($search);
        }

        // Apply filters
        if ($request->filled('block')) {
            $query->byBlock($request->block);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Apply maintenance status filter
        if ($request->filled('maintenance_status')) {
            $query->byMaintenanceStatus($request->maintenance_status);
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'block');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection)
            ->orderBy('row', 'asc')
            ->orderBy('column', 'asc');

        // Handle CSV export
        if ($request->get('export') === 'csv') {
            return $this->exportToCSV($query, $request);
        }

        // Pagination
        $perPage = $request->get('perPage', 10);
        $permanentGraves = $query->paginate($perPage);

        // Calculate pending amounts for paginated results
        foreach ($permanentGraves as $grave) {
            $grave->pending_amount = $grave->calculatePendingAmount();
        }

        // Get filter options
        $blocks = PermanentGrave::distinct()->pluck('block')->filter()->sort()->values();
        $statuses = ['available', 'unavailable'];

        return Inertia::render('PagesGraveyard/PermanentGraves/Index', [
            'data' => $permanentGraves,
            'filters' => $request->only(['search', 'block', 'status', 'is_active', 'maintenance_status', 'sort', 'direction', 'perPage', 'isArchived']),
            'filterOptions' => [
                'blocks' => $blocks,
                'statuses' => $statuses,
            ],
            'fetchUrl' => route('graveyard.permanent-graves.index'),
        ]);
    }

    /**
     * Show the form for creating a new permanent grave
     */
    public function create()
    {
        $this->authorize('create-permanent-grave');
        $blocks = PermanentGrave::distinct()->pluck('block')->filter()->sort()->values();
        $statuses = ['available', 'unavailable'];

        return Inertia::render('PagesGraveyard/PermanentGraves/Create', [
            'blocks' => $blocks,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created permanent grave
     */
    public function store(Request $request)
    {
        $this->authorize('create-permanent-grave');
        $request->validate([
            'block' => 'required|string|max:100',
            'row' => 'required|integer|min:1',
            'column' => 'required|integer|min:1',
            'old_no' => 'nullable|string|max:50',
            'status' => 'required|in:available,unavailable',
            'last_burial_date' => 'nullable|date',
            'owner_name' => 'nullable|string|max:255',
            'member_id' => 'nullable|exists:members,id',
            'contact_no' => 'nullable|string|max:20',
            'remarks' => 'nullable|string|max:1000',
            'plot_size' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'member_type' => 'required|in:member,external',
        ]);

        // Validate mutually exclusive fields
        if ($request->member_type === 'member') {
            if (empty($request->member_id)) {
                return back()->withErrors(['member_id' => 'Member must be selected when member type is Parish Member.']);
            }
            // Clear owner_name if member is selected
            $request->merge(['owner_name' => null]);
        } else {
            if (empty($request->owner_name)) {
                return back()->withErrors(['owner_name' => 'Name is required when member type is Non-Member.']);
            }
            // Clear member_id if non-member is selected
            $request->merge(['member_id' => null]);
        }

        // Check for duplicate grave in same block
        $existingGrave = PermanentGrave::where('block', $request->block)
            ->where('row', $request->row)
            ->where('column', $request->column)
            ->first();

        if ($existingGrave) {
            return back()->withErrors(['column' => 'A grave with this position already exists in the specified block and row.']);
        }

        try {
            $permanentGrave = PermanentGrave::create([
                'block' => $request->block,
                'row' => $request->row,
                'column' => $request->column,
                'old_no' => $request->old_no,
                'status' => $request->status,
                'last_burial_date' => $request->last_burial_date,
                'owner_name' => $request->owner_name,
                'member_id' => $request->member_id,
                'contact_no' => $request->contact_no,
                'remarks' => $request->remarks,
                'plot_size' => $request->plot_size,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('graveyard.permanent-graves.index')
                ->with('success', 'Permanent grave created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create permanent grave', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'Failed to create permanent grave. Please try again.']);
        }
    }

    /**
     * Display the specified permanent grave
     */
    public function show(PermanentGrave $permanentGrave)
    {
        $this->authorize('read-permanent-grave');
        $permanentGrave->load([
            'bookings.validMember.member',
            'creator',
            'updater',
            'validMembers',
        ]);

        return Inertia::render('PagesGraveyard/PermanentGraves/Show', [
            'permanentGrave' => $permanentGrave,
        ]);
    }

    /**
     * Show the form for editing the specified permanent grave
     */
    public function edit(PermanentGrave $permanentGrave)
    {
        $this->authorize('update-permanent-grave');
        $permanentGrave->load(['member', 'member.community']);

        $blocks = PermanentGrave::distinct()->pluck('block')->filter()->sort()->values();
        $statuses = ['available', 'unavailable'];

        return Inertia::render('PagesGraveyard/PermanentGraves/Edit', [
            'permanentGrave' => $permanentGrave,
            'blocks' => $blocks,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified permanent grave
     */
    public function update(Request $request, PermanentGrave $permanentGrave)
    {
        $this->authorize('update-permanent-grave');
        $request->validate([
            'block' => 'required|string|max:100',
            'row' => 'required|integer|min:1',
            'column' => 'required|integer|min:1',
            'old_no' => 'nullable|string|max:50',
            'status' => 'required|in:available,unavailable',
            'last_burial_date' => 'nullable|date',
            'owner_name' => 'nullable|string|max:255',
            'member_id' => 'nullable|exists:members,id',
            'contact_no' => 'nullable|string|max:20',
            'remarks' => 'nullable|string|max:1000',
            'plot_size' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'member_type' => 'required|in:member,external',
        ]);

        // Validate mutually exclusive fields
        if ($request->member_type === 'member') {
            if (empty($request->member_id)) {
                return back()->withErrors(['member_id' => 'Member must be selected when member type is Parish Member.']);
            }
            // Clear owner_name if member is selected
            $request->merge(['owner_name' => null]);
        } else {
            if (empty($request->owner_name)) {
                return back()->withErrors(['owner_name' => 'Name is required when member type is Non-Member.']);
            }
            // Clear member_id if non-member is selected
            $request->merge(['member_id' => null]);
        }

        // Check for duplicate grave in same block (excluding current grave)
        $existingGrave = PermanentGrave::where('block', $request->block)
            ->where('row', $request->row)
            ->where('column', $request->column)
            ->where('id', '!=', $permanentGrave->id)
            ->first();

        if ($existingGrave) {
            return back()->withErrors(['column' => 'A grave with this position already exists in the specified block and row.']);
        }

        try {
            $permanentGrave->update([
                'block' => $request->block,
                'row' => $request->row,
                'column' => $request->column,
                'old_no' => $request->old_no,
                'status' => $request->status,
                'last_burial_date' => $request->last_burial_date,
                'owner_name' => $request->owner_name,
                'member_id' => $request->member_id,
                'contact_no' => $request->contact_no,
                'remarks' => $request->remarks,
                'plot_size' => $request->plot_size,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => Auth::id(),
            ]);


            return redirect()->route('graveyard.permanent-graves.index')
                ->with('success', 'Permanent grave updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update permanent grave', [
                'id' => $permanentGrave->id,
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'Failed to update permanent grave. Please try again.']);
        }
    }

    /**
     * Remove the specified permanent grave
     */
    public function destroy(PermanentGrave $permanentGrave)
    {
        $this->authorize('delete-permanent-grave');
        try {
            // Check if grave has any bookings
            if ($permanentGrave->bookings()->exists()) {
                return back()->withErrors(['error' => 'Cannot delete grave. It has associated burial records.']);
            }

            $permanentGrave->delete();

            return back()->with('success', 'Permanent grave deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete permanent grave', [
                'id' => $permanentGrave->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to delete permanent grave. Please try again.']);
        }
    }

    /**
     * Restore the specified permanent grave
     */
    public function restore($id)
    {
        $this->authorize('restore-permanent-grave');
        try {
            $permanentGrave = PermanentGrave::onlyTrashed()->findOrFail($id);
            $permanentGrave->restore();

            return back()->with('success', 'Permanent grave restored successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to restore permanent grave', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to restore permanent grave. Please try again.']);
        }
    }

    /**
     * Download PDF document showing all valid members for this permanent grave
     */
    public function downloadValidMembersPdf(PermanentGrave $permanentGrave)
    {
        $this->authorize('read-permanent-grave');
        $permanentGrave->load([
            'validMembers.member',
            'validMembers.relationship',
            'validMembers.gender',
            'validMembers.parish',
            'member',
        ]);

        $data = [
            'grave' => $permanentGrave,
            'graveType' => 'Permanent Grave',
            'graveIdentifier' => $permanentGrave->block . '-' . $permanentGrave->row . '-' . $permanentGrave->column,
            'location' => 'Block: ' . $permanentGrave->block . ', Row: ' . $permanentGrave->row . ', Column: ' . $permanentGrave->column,
            'oldNumber' => $permanentGrave->old_no,
            'plotSize' => $permanentGrave->plot_size,
            'registrationDate' => $permanentGrave->created_at?->format('d/m/Y') ?? 'N/A',
            'ownerName' => $permanentGrave->owner_name ?: ($permanentGrave->member ? $permanentGrave->member->first_name . ' ' . $permanentGrave->member->last_name : 'N/A'),
            'contactNo' => $permanentGrave->contact_no ?: ($permanentGrave->member?->contact_no_1 ?? 'N/A'),
            'validMembers' => $permanentGrave->validMembers,
            'generatedDate' => now()->format('d/m/Y H:i A'),
            'generatedBy' => Auth::user()->name ?? 'System',
        ];

        // Generate descriptive filename for the document title
        $filename = 'Valid_Members_Permanent_Grave_' . $permanentGrave->block . '-' . $permanentGrave->row . '-' . $permanentGrave->column . '_' . now()->format('Y-m-d');
        $data['documentTitle'] = $filename;

        // Log PDF data for debugging
        Log::info('Permanent Grave PDF Generation', [
            'grave_id' => $permanentGrave->id,
            'graveIdentifier' => $data['graveIdentifier'],
            'location' => $data['location'],
            'oldNumber' => $data['oldNumber'],
            'plotSize' => $data['plotSize'],
            'validMembers_count' => $permanentGrave->validMembers->count(),
            'ownerName' => $data['ownerName'],
            'contactNo' => $data['contactNo'],
            'filename' => $filename,
        ]);

        // Return view for printing (opens print dialog automatically)
        return view('graveyard.documents.valid-members', $data);
    }

    /**
     * Release all unavailable graves that have passed the 24-month burial wait period
     */
    public function releaseEligible(Request $request)
    {
        $this->authorize('update-permanent-grave');

        $graves = PermanentGrave::where('status', 'unavailable')
            ->whereNotNull('last_burial_date')
            ->get();

        $released = 0;
        foreach ($graves as $grave) {
            $monthsSince = Carbon::parse($grave->last_burial_date)->diffInMonths(now());
            if ($monthsSince >= 24) {
                $grave->update(['status' => 'available']);
                $released++;
            }
        }

        $message = $released > 0
            ? "{$released} grave(s) released to available status."
            : "No graves were eligible for release at this time.";

        return back()->with('success', $message);
    }

    /**
     * Search for members (for AJAX calls)
     */
    public function searchMembers(Request $request)
    {
        $this->authorize('read-permanent-grave');
        $query = $request->get('query');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $members = Member::with(['community'])
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$query%"])
                    ->orWhere('family_no', 'like', "%{$query}%")
                    ->orWhere('contact_no_1', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'first_name' => $member->first_name,
                    'last_name' => $member->last_name,
                    'full_name' => "{$member->first_name} {$member->last_name}",
                    'family_no' => $member->family_no,
                    'contact_no_1' => $member->contact_no_1,
                    'current_add1' => $member->current_add1,
                    'community' => $member->community,
                ];
            });

        return response()->json($members);
    }

    /**
     * Export permanent graves to CSV
     */
    protected function exportToCSV($query, Request $request)
    {
        $graves = $query->get();

        // Calculate pending amounts
        foreach ($graves as $grave) {
            $grave->pending_amount = $grave->calculatePendingAmount();
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="permanent_graves_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function () use ($graves) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Grave Position',
                'Old No',
                'Status',
                'Owner Name',
                'Plot Size',
                'Last Burial Date',
                'Pending Amount',
                'Last Payment Year',
                'Contact No',
                'Remarks'
            ]);

            // Add data rows
            foreach ($graves as $grave) {
                $ownerName = $grave->owner_name ?:
                    ($grave->member ? $grave->member->first_name . ' ' . $grave->member->last_name : '-');

                fputcsv($file, [
                    $grave->block . '-' . $grave->row . '-' . $grave->column,
                    $grave->old_no ?: '-',
                    ucfirst($grave->status),
                    $ownerName,
                    $grave->plot_size ? $grave->plot_size . ' sq ft' : '-',
                    $grave->last_burial_date ? $grave->last_burial_date->format('d/m/Y') : '-',
                    $grave->pending_amount > 0 ? '₹' . number_format($grave->pending_amount, 2) : 'Paid',
                    $grave->last_payment_year ?: '-',
                    $grave->contact_no ?: '-',
                    $grave->remarks ?: '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
