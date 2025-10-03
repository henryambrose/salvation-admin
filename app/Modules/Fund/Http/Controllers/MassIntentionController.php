<?php

namespace Modules\Fund\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Fund\Models\MassIntention;
use Modules\Fund\Models\MassIntentionType;
use Modules\Fund\Models\MassType;
use Modules\Members\Models\Member;
use Modules\Fund\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;

class MassIntentionController extends Controller
{
    /**
     * Display a listing of mass intentions
     */
    public function index(Request $request)
    {
        $query = MassIntention::with(['member', 'massIntentionType', 'paymentMethod', 'massType']);

        // Archive logic
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('special_instructions', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('family_no', 'like', "%{$search}%")
                    ->orWhere('external_name', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply mass date filter (for BookIntention page)
        if ($request->filled('mass_date')) {
            $query->where('mass_date', $request->mass_date);
        }

        // Apply mass type filter
        if ($request->filled('mass_type_id')) {
            $query->where('mass_type_id', $request->mass_type_id);
        }

        // Apply start date filter
        if ($request->filled('start_date')) {
            $query->where('mass_date', '>=', $request->start_date);
        }

        // Apply end date filter
        if ($request->filled('end_date')) {
            $query->where('mass_date', '<=', $request->end_date);
        }

        // Apply intention type filter
        if ($request->filled('mass_intention_type_id')) {
            $query->where('mass_intention_type_id', $request->mass_intention_type_id);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'mass_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Apply pagination
        $perPage = $request->get('per_page', 10);
        $massIntentions = $query->paginate($perPage);

        // If this is a request for booked masses (from BookIntention page), return only the data
        if ($request->filled('mass_date') && $request->filled('mass_type_id')) {
            $bookedMasses = $massIntentions->items();

            // Format the data for frontend display
            $formattedMasses = collect($bookedMasses)->map(function ($mass) {
                $displayName = '';
                if ($mass->member) {
                    $displayName = trim($mass->member->first_name . ' ' . $mass->member->last_name) . ' - ' . $mass->member->family_no;
                } else {
                    $displayName = $mass->external_name;
                }

                return [
                    'id' => $mass->id,
                    'display_name' => $displayName,
                    'intention_type_name' => $mass->massIntentionType->name ?? 'N/A',
                    // 'status' => $mass->status, // Commented out - status workflow not implemented yet
                ];
            });

            return response()->json([
                'booked_masses' => $formattedMasses
            ]);
        }

        // Get mass types and intention types for filters
        try {
            $massTypes = MassType::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();
        } catch (\Exception $e) {
            $massTypes = MassType::all();
        }

        try {
            $massIntentionTypes = MassIntentionType::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();
        } catch (\Exception $e) {
            $massIntentionTypes = MassIntentionType::all();
        }
        return Inertia::render('MassIntentions/Index', [
            'massIntentions' => $massIntentions,
            'massTypes' => $massTypes,
            'massIntentionTypes' => $massIntentionTypes,
            'filters' => $request->only(['search', 'status', 'mass_type_id', 'mass_intention_type_id', 'start_date', 'end_date', 'sort_by', 'sort_order', 'per_page', 'isArchived']),
        ]);
    }

    /**
     * Show the form for creating a new mass intention
     */
    public function create()
    {
        try {
            $massTypes = MassType::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();
        } catch (\Exception $e) {
            $massTypes = MassType::all();
        }

        try {
            $intentionTypes = MassIntentionType::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();
        } catch (\Exception $e) {
            $intentionTypes = MassIntentionType::all();
        }

        try {
            $paymentMethods = PaymentMethod::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();
        } catch (\Exception $e) {
            $paymentMethods = PaymentMethod::all();
        }

        return Inertia::render('MassIntentions/Create', [
            'massTypes' => $massTypes,
            'intentionTypes' => $intentionTypes,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Store a newly created mass intention
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_type' => 'required|in:member,external',
            'member_id' => 'nullable|exists:members,id',
            'external_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'mass_date' => 'required|date|after_or_equal:today',
            'mass_type_id' => 'required|exists:mass_types,id',
            'mass_intention_type_id' => 'required|exists:mass_intention_types,id',
            'intention_for' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'special_instructions' => 'nullable|string|max:1000',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        // Validate member information based on type
        if ($request->member_type === 'member' && !$request->member_id) {
            return back()->withErrors(['member_id' => 'Member ID is required for parish members.']);
        }

        if ($request->member_type === 'external' && !$request->external_name) {
            return back()->withErrors(['external_name' => 'Name is required for external.']);
        }

        // Check if the mass type exists and is active
        $massType = MassType::find($request->mass_type_id);
        if (!$massType || !$massType->is_active) {
            return back()->withErrors(['mass_type_id' => 'Selected mass type is not available.']);
        }

        // Check if the intention type exists and is active
        $intentionType = MassIntentionType::find($request->mass_intention_type_id);
        if (!$intentionType || !$intentionType->is_active) {
            return back()->withErrors(['mass_intention_type_id' => 'Selected intention type is not available.']);
        }

        // Create the mass intention
        $massIntention = MassIntention::create([
            'member_id' => $request->member_id,
            'external_name' => $request->member_type === 'external' ? $request->external_name : null,
            'phone' => $request->phone,
            'mass_date' => $request->mass_date,
            'mass_type_id' => $request->mass_type_id,
            'mass_intention_type_id' => $request->mass_intention_type_id,
            'intention_for' => $request->intention_for,
            'amount' => $request->amount,
            'status' => $request->status,
            'special_instructions' => $request->special_instructions,
            'payment_method_id' => $request->payment_method_id,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('fund.mass-intentions.index')
            ->with('success', 'Mass intention booked successfully!');
    }

    /**
     * Display the specified mass intention
     */
    public function show(MassIntention $massIntention)
    {
        $massIntention->load(['member', 'massSchedule', 'intentionType', 'createdBy', 'updatedBy']);

        return Inertia::render('MassIntentions/Show', [
            'massIntention' => $massIntention,
        ]);
    }

    /**
     * Show the form for editing the specified mass intention
     */
    public function edit(MassIntention $massIntention)
    {
        $massIntention->load(['member', 'massIntentionType']);

        // Add members for the dropdown
        $members = Member::orderBy('first_name', 'asc')
            ->orderBy('last_name', 'asc')
            ->get(['id', 'first_name', 'last_name', 'family_no']);

        $massIntentionTypes = MassIntentionType::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get(['id', 'name', 'default_amount', 'description']);

        try {
            $paymentMethods = PaymentMethod::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();
        } catch (\Exception $e) {
            $paymentMethods = PaymentMethod::all();
        }

        return Inertia::render('MassIntentions/Edit', [
            'massIntention' => $massIntention,
            'members' => $members,
            'massIntentionTypes' => $massIntentionTypes,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Update the specified mass intention
     */
    public function update(Request $request, MassIntention $massIntention)
    {
        $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'external_name' => 'nullable|string|max:255',
            'mass_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'mass_intention_type_id' => 'required|exists:mass_intention_types,id',
            'intention_for' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'special_instructions' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $massIntention->update([
            'member_id' => $request->member_id,
            'external_name' => $request->external_name,
            'mass_date' => $request->mass_date,
            'mass_intention_type_id' => $request->mass_intention_type_id,
            'intention_for' => $request->intention_for,
            'amount' => $request->amount,
            'payment_method_id' => $request->payment_method_id,
            'special_instructions' => $request->special_instructions,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('fund.mass-intentions.index')
            ->with('success', 'Mass intention updated successfully.');
    }

    /**
     * Remove the specified mass intention
     */
    public function destroy(MassIntention $massIntention)
    {
        $massIntention->delete();

        return back()->with('success', 'Mass intention deleted successfully.');
    }

    /**
     * Restore the specified mass intention
     */
    public function restore($id)
    {
        $massIntention = MassIntention::onlyTrashed()->findOrFail($id);
        $massIntention->restore();

        return back()->with('success', 'Mass intention restored successfully.');
    }

    /**
     * Update the status of a mass intention
     */
    public function updateStatus(Request $request, MassIntention $massIntention)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $massIntention->update([
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Mass intention status updated successfully.');
    }

    /**
     * Search for members by family number or name
     */
    public function searchMembers(Request $request)
    {
        $query = $request->get('query', '');

        $members = Member::with('community')->alive()->where(function ($q) use ($query) {

            if ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('middle_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('contact_no_1', 'like', "%{$query}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"]);
            }
        })

            ->get(['id', 'first_name', 'middle_name', 'last_name',  'family_no', 'family_no', 'community_id', 'current_add1', 'contact_no_1']);

        // Add a computed 'name' field for frontend compatibility
        $members = $members->map(function ($member) {
            $member->name = trim(implode(' ', array_filter([
                $member->first_name,
                $member->middle_name,
                $member->last_name
            ])));
            // Add phone field for frontend compatibility
            $member->community = $member->community;
            $member->current_add1 = $member->current_add1;
            $member->contact_no_1 = $member->contact_no_1;
            return $member;
        });

        return response()->json($members);
    }

    /**
     * Export mass intentions to CSV
     */
    public function export(Request $request)
    {
        $query = MassIntention::with(['member', 'massIntentionType', 'paymentMethod', 'massType']);

        // Archive logic
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('special_instructions', 'like', "%{$search}%")
                    ->orWhere('external_name', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply mass date filter
        if ($request->filled('mass_date')) {
            $query->where('mass_date', $request->mass_date);
        }

        // Apply mass type filter
        if ($request->filled('mass_type_id')) {
            $query->where('mass_type_id', $request->mass_type_id);
        }

        // Apply start date filter
        if ($request->filled('start_date')) {
            $query->where('mass_date', '>=', $request->start_date);
        }

        // Apply end date filter
        if ($request->filled('end_date')) {
            $query->where('mass_date', '<=', $request->end_date);
        }

        // Apply intention type filter
        if ($request->filled('mass_intention_type_id')) {
            $query->where('mass_intention_type_id', $request->mass_intention_type_id);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'mass_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $massIntentions = $query->get();

        $filename = 'mass-intentions-' . date('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public',
        ];

        $callback = function () use ($massIntentions) {
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
                'Member Name',
                'Non Member Name',
                'Mass Date',
                'Mass Type',
                'Intention Type',
                'Intention For',
                'Amount',
                'Payment Method',
                'Special Instructions',
                'Status',
                'Created At',
                'Updated At'
            ]);

            // CSV Data
            foreach ($massIntentions as $intention) {
                fputcsv($file, [
                    $intention->id,
                    $intention->member ? $intention->member->first_name . ' ' . $intention->member->last_name : 'N/A',
                    $intention->external_name ?? 'N/A',
                    $intention->mass_date,
                    $intention->massType->name ?? 'N/A',
                    $intention->massIntentionType->name ?? 'N/A',
                    $intention->intention_for ?? 'N/A',
                    $intention->amount,
                    $intention->paymentMethod->name ?? 'N/A',
                    $intention->special_instructions ?? 'N/A',
                    $intention->status,
                    $intention->created_at,
                    $intention->updated_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
