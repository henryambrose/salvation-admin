<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\Graveyard\Models\Niche;
use Modules\Members\Models\Member;
use PDF;

class NicheController extends Controller
{
    /**
     * Display a listing of niches
     */
    public function index(Request $request)
    {
        $this->authorize('list-niche');
        $query = Niche::query()->with('member');

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
        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'location');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection)
            ->orderBy('niche_no', 'asc')
            ->orderBy('sr_no', 'asc');

        // Pagination
        $perPage = $request->get('perPage', 10);
        $niches = $query->paginate($perPage);

        // Get filter options
        $locations = Niche::distinct()->pluck('location')->filter()->sort()->values();
        $statuses = ['available', 'unavailable'];

        return Inertia::render('PagesGraveyard/Niches/Index', [
            'data' => $niches,
            'filters' => $request->only(['search', 'location', 'status', 'is_active', 'sort', 'direction', 'perPage', 'isArchived']),
            'filterOptions' => [
                'locations' => $locations,
                'statuses' => $statuses,
            ],
            'fetchUrl' => route('graveyard.niches.index'),
        ]);
    }

    /**
     * Show the form for creating a new niche
     */
    public function create()
    {
        $this->authorize('create-niche');
        $locations = Niche::distinct()->pluck('location')->filter()->sort()->values();
        $statuses = ['available', 'unavailable'];

        return Inertia::render('PagesGraveyard/Niches/Create', [
            'locations' => $locations,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created niche
     */
    public function store(Request $request)
    {
        $this->authorize('create-niche');
        $request->validate([
            'niche_no' => 'required|integer|min:1',
            'sr_no' => 'required|integer|min:1',
            'location' => 'required|string|max:100',
            'status' => 'required|in:available,unavailable',
            'last_occupation_date' => 'nullable|date',
            'owner_name' => 'nullable|string|max:255',
            'member_id' => 'nullable|exists:members,id',
            'contact_no' => 'nullable|string|max:20',
            'remarks' => 'nullable|string|max:1000',
            'size_width' => 'nullable|numeric|min:0',
            'size_height' => 'nullable|numeric|min:0',
            'size_depth' => 'nullable|numeric|min:0',
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

        // Check for duplicate niche in same location
        $existingNiche = Niche::where('location', $request->location)
            ->where('niche_no', $request->niche_no)
            ->where('sr_no', $request->sr_no)
            ->first();

        if ($existingNiche) {
            return back()->withErrors(['niche_no' => 'A niche with this number already exists in the specified location.']);
        }

        try {
            $niche = Niche::create([
                'niche_no' => $request->niche_no,
                'sr_no' => $request->sr_no,
                'location' => $request->location,
                'status' => $request->status,
                'last_occupation_date' => $request->last_occupation_date,
                'owner_name' => $request->owner_name,
                'member_id' => $request->member_id,
                'contact_no' => $request->contact_no,
                'remarks' => $request->remarks,
                'size_width' => $request->size_width,
                'size_height' => $request->size_height,
                'size_depth' => $request->size_depth,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('graveyard.niches.index')
                ->with('success', 'Niche created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create niche', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'Failed to create niche. Please try again.']);
        }
    }

    /**
     * Display the specified niche
     */
    public function show(Niche $niche)
    {
        $this->authorize('read-niche');
        $niche->load(['member', 'creator', 'updater']);

        return Inertia::render('PagesGraveyard/Niches/Show', [
            'niche' => $niche,
        ]);
    }

    /**
     * Show the form for editing the specified niche
     */
    public function edit(Niche $niche)
    {
        $this->authorize('update-niche');
        $niche->load(['member', 'member.community']);

        $locations = Niche::distinct()->pluck('location')->filter()->sort()->values();
        $statuses = ['available', 'unavailable'];

        return Inertia::render('PagesGraveyard/Niches/Edit', [
            'niche' => $niche,
            'locations' => $locations,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified niche
     */
    public function update(Request $request, Niche $niche)
    {
        $this->authorize('update-niche');
        $request->validate([
            'niche_no' => 'required|integer|min:1',
            'sr_no' => 'required|integer|min:1',
            'location' => 'required|string|max:100',
            'status' => 'required|in:available,unavailable',
            'last_occupation_date' => 'nullable|date',
            'owner_name' => 'nullable|string|max:255',
            'member_id' => 'nullable|exists:members,id',
            'contact_no' => 'nullable|string|max:20',
            'remarks' => 'nullable|string|max:1000',
            'size_width' => 'nullable|numeric|min:0',
            'size_height' => 'nullable|numeric|min:0',
            'size_depth' => 'nullable|numeric|min:0',
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

        // Check for duplicate niche in same location (excluding current niche)
        $existingNiche = Niche::where('location', $request->location)
            ->where('niche_no', $request->niche_no)
            ->where('sr_no', $request->sr_no)
            ->where('id', '!=', $niche->id)
            ->first();

        if ($existingNiche) {
            return back()->withErrors(['niche_no' => 'A niche with this number already exists in the specified location.']);
        }

        try {
            $niche->update([
                'niche_no' => $request->niche_no,
                'sr_no' => $request->sr_no,
                'location' => $request->location,
                'status' => $request->status,
                'last_occupation_date' => $request->last_occupation_date,
                'owner_name' => $request->owner_name,
                'member_id' => $request->member_id,
                'contact_no' => $request->contact_no,
                'remarks' => $request->remarks,
                'size_width' => $request->size_width,
                'size_height' => $request->size_height,
                'size_depth' => $request->size_depth,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('graveyard.niches.index')
                ->with('success', 'Niche updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update niche', [
                'id' => $niche->id,
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'Failed to update niche. Please try again.']);
        }
    }

    /**
     * Remove the specified niche
     */
    public function destroy(Niche $niche)
    {
        $this->authorize('delete-niche');
        try {
            // Check if niche is occupied
            if ($niche->status === 'occupied') {
                return back()->withErrors(['error' => 'Cannot delete occupied niche.']);
            }

            $niche->delete();

            return back()->with('success', 'Niche deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete niche', [
                'id' => $niche->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to delete niche. Please try again.']);
        }
    }

    /**
     * Restore the specified niche
     */
    public function restore($id)
    {
        $this->authorize('restore-niche');
        try {
            $niche = Niche::onlyTrashed()->findOrFail($id);
            $niche->restore();

            return back()->with('success', 'Niche restored successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to restore niche', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to restore niche. Please try again.']);
        }
    }

    /**
     * Download PDF document showing all valid members for this niche
     */
    public function downloadValidMembersPdf(Niche $niche)
    {
        $this->authorize('read-niche');
        $niche->load([
            'validMembers.member',
            'validMembers.relationship',
            'validMembers.gender',
            'validMembers.parish',
            'member',
        ]);

        $data = [
            'grave' => $niche,
            'graveType' => 'Niche',
            'graveIdentifier' => 'N' . $niche->niche_no . '-' . $niche->sr_no,
            'location' => $niche->location,
            'oldNumber' => null,
            'plotSize' => $niche->size_width && $niche->size_height && $niche->size_depth
                ? $niche->size_width . ' x ' . $niche->size_height . ' x ' . $niche->size_depth . ' inches'
                : null,
            'registrationDate' => $niche->created_at?->format('d/m/Y') ?? 'N/A',
            'ownerName' => $niche->owner_name ?: ($niche->member ? $niche->member->first_name . ' ' . $niche->member->last_name : 'N/A'),
            'contactNo' => $niche->contact_no ?: ($niche->member?->contact_no_1 ?? 'N/A'),
            'validMembers' => $niche->validMembers,
            'generatedDate' => now()->format('d/m/Y H:i A'),
            'generatedBy' => Auth::user()->name ?? 'System',
        ];

        // Generate descriptive filename for the document title
        $filename = 'Valid_Members_Niche_N' . $niche->niche_no . '-' . $niche->sr_no . '_' . now()->format('Y-m-d');
        $data['documentTitle'] = $filename;

        // Log PDF data for debugging
        Log::info('Niche PDF Generation', [
            'niche_id' => $niche->id,
            'graveIdentifier' => $data['graveIdentifier'],
            'location' => $data['location'],
            'plotSize' => $data['plotSize'],
            'validMembers_count' => $niche->validMembers->count(),
            'ownerName' => $data['ownerName'],
            'contactNo' => $data['contactNo'],
            'filename' => $filename,
        ]);

        // Return view for printing (opens print dialog automatically)
        return view('graveyard.documents.valid-members', $data);
    }

    /**
     * Search for members (for AJAX calls)
     */
    public function searchMembers(Request $request)
    {
        $this->authorize('read-niche');
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
                    ->orWhere('contact_no_1', 'like', "%{$query}%")
                    ->orWhere('contact_no_2', 'like', "%{$query}%");
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
}
