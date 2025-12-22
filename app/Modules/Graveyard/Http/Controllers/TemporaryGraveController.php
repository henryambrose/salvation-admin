<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\Graveyard\Models\TemporaryGrave;
use Modules\Graveyard\Models\GraveCategories;
use Modules\Members\Models\Member;

class TemporaryGraveController extends Controller
{
    /**
     * Display a listing of temporary graves
     */
    public function index(Request $request)
    {
        $this->authorize('list-temporary-grave');

        $query = TemporaryGrave::query()->with(['member', 'graveCategory']);

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
        if ($request->filled('section')) {
            $query->bySection($request->section);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('grave_category_id')) {
            $query->where('grave_category_id', $request->grave_category_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'section');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection)
            ->orderBy('row_no', 'asc')
            ->orderBy('grave_no', 'asc');

        // Pagination
        $perPage = $request->get('perPage', 10);
        $temporaryGraves = $query->paginate($perPage);

        // Get filter options
        $sections = TemporaryGrave::distinct()->pluck('section')->filter()->sort()->values();
        $statuses = ['available', 'unavailable'];
        $graveCategories = GraveCategories::orderBy('name')->get(['id', 'name']);

        return Inertia::render('PagesGraveyard/TemporaryGraves/Index', [
            'data' => $temporaryGraves,
            'filters' => $request->only(['search', 'section', 'status', 'grave_category_id', 'is_active', 'sort', 'direction', 'perPage', 'isArchived']),
            'filterOptions' => [
                'sections' => $sections,
                'statuses' => $statuses,
                'graveCategories' => $graveCategories,
            ],
            'fetchUrl' => route('graveyard.temporary-graves.index'),
        ]);
    }

    /**
     * Show the form for creating a new temporary grave
     */
    public function create()
    {
        $this->authorize('create-temporary-grave');

        $sections = TemporaryGrave::distinct()->pluck('section')->filter()->sort()->values();
        $statuses = ['available', 'unavailable'];
        $graveCategories = GraveCategories::orderBy('name')->get(['id', 'name']);

        return Inertia::render('PagesGraveyard/TemporaryGraves/Create', [
            'sections' => $sections,
            'statuses' => $statuses,
            'graveCategories' => $graveCategories,
        ]);
    }

    /**
     * Store a newly created temporary grave
     */
    public function store(Request $request)
    {
        $this->authorize('create-temporary-grave');

        $request->validate([
            'section' => 'required|string|max:100',
            'row_no' => 'required|integer|min:1',
            'grave_no' => 'required|integer|min:1',
            'old_no' => 'nullable|string|max:50',
            'status' => 'required|in:available,unavailable',
            'last_burial_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
            'plot_size' => 'nullable|numeric|min:0',
            'owner_name' => 'nullable|string|max:255',
            'member_id' => 'nullable|exists:members,id',
            'grave_category_id' => 'nullable|exists:grave_categories,id',
            'contact_no' => 'nullable|string|max:20',
            'is_active' => 'boolean',
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

        // Check for duplicate grave in same section
        $existingGrave = TemporaryGrave::where('section', $request->section)
            ->where('row_no', $request->row_no)
            ->where('grave_no', $request->grave_no)
            ->first();

        if ($existingGrave) {
            return back()->withErrors(['grave_no' => 'A grave with this number already exists in the specified section and row.']);
        }

        try {
            $temporaryGrave = TemporaryGrave::create([
                'section' => $request->section,
                'row_no' => $request->row_no,
                'grave_no' => $request->grave_no,
                'old_no' => $request->old_no,
                'status' => $request->status,
                'last_burial_date' => $request->last_burial_date,
                'remarks' => $request->remarks,
                'plot_size' => $request->plot_size,
                'owner_name' => $request->owner_name,
                'member_id' => $request->member_id,
                'grave_category_id' => $request->grave_category_id,
                'contact_no' => $request->contact_no,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('graveyard.temporary-graves.index')
                ->with('success', 'Temporary grave created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create temporary grave', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'Failed to create temporary grave. Please try again.']);
        }
    }

    /**
     * Display the specified temporary grave
     */
    public function show(TemporaryGrave $temporaryGrave)
    {
        $this->authorize('read-temporary-grave');

        $temporaryGrave->load(['bookings.member', 'creator', 'updater']);

        return Inertia::render('PagesGraveyard/TemporaryGraves/Show', [
            'temporaryGrave' => $temporaryGrave,
        ]);
    }

    /**
     * Show the form for editing the specified temporary grave
     */
    public function edit(TemporaryGrave $temporaryGrave)
    {
        $this->authorize('update-temporary-grave');

        $temporaryGrave->load(['member', 'member.community', 'graveCategory']);

        $sections = TemporaryGrave::distinct()->pluck('section')->filter()->sort()->values();
        $statuses = ['available', 'unavailable'];
        $graveCategories = GraveCategories::orderBy('name')->get(['id', 'name']);

        return Inertia::render('PagesGraveyard/TemporaryGraves/Edit', [
            'temporaryGrave' => $temporaryGrave,
            'sections' => $sections,
            'statuses' => $statuses,
            'graveCategories' => $graveCategories,
        ]);
    }

    /**
     * Update the specified temporary grave
     */
    public function update(Request $request, TemporaryGrave $temporaryGrave)
    {
        $this->authorize('update-temporary-grave');

        $request->validate([
            'section' => 'required|string|max:100',
            'row_no' => 'required|integer|min:1',
            'grave_no' => 'required|integer|min:1',
            'old_no' => 'nullable|string|max:50',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'last_burial_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
            'plot_size' => 'nullable|numeric|min:0',
            'owner_name' => 'nullable|string|max:255',
            'member_id' => 'nullable|exists:members,id',
            'grave_category_id' => 'nullable|exists:grave_categories,id',
            'contact_no' => 'nullable|string|max:20',
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

        // Check for duplicate grave in same section (excluding current grave)
        $existingGrave = TemporaryGrave::where('section', $request->section)
            ->where('row_no', $request->row_no)
            ->where('grave_no', $request->grave_no)
            ->where('id', '!=', $temporaryGrave->id)
            ->first();

        if ($existingGrave) {
            return back()->withErrors(['grave_no' => 'A grave with this number already exists in the specified section and row.']);
        }

        try {
            $temporaryGrave->update([
                'section' => $request->section,
                'row_no' => $request->row_no,
                'grave_no' => $request->grave_no,
                'old_no' => $request->old_no,
                'status' => $request->status,
                'last_burial_date' => $request->last_burial_date,
                'remarks' => $request->remarks,
                'plot_size' => $request->plot_size,
                'owner_name' => $request->owner_name,
                'member_id' => $request->member_id,
                'grave_category_id' => $request->grave_category_id,
                'contact_no' => $request->contact_no,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('graveyard.temporary-graves.index')
                ->with('success', 'Temporary grave updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update temporary grave', [
                'id' => $temporaryGrave->id,
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'Failed to update temporary grave. Please try again.']);
        }
    }

    /**
     * Remove the specified temporary grave
     */
    public function destroy($id)
    {
        $this->authorize('delete-temporary-grave');

        try {
            $temporaryGrave = TemporaryGrave::findOrFail($id);

            // Check if grave has any bookings
            if ($temporaryGrave->bookings()->exists()) {
                return back()->withErrors(['error' => 'Cannot delete grave. It has associated burial records.']);
            }

            $temporaryGrave->delete();

            return back()->with('success', 'Temporary grave deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete temporary grave', [
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to delete temporary grave. Please try again.']);
        }
    }

    /**
     * Restore the specified temporary grave
     */
    public function restore($id)
    {
        $this->authorize('restore-temporary-grave');

        try {
            $temporaryGrave = TemporaryGrave::onlyTrashed()->findOrFail($id);
            $temporaryGrave->restore();

            return back()->with('success', 'Temporary grave restored successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to restore temporary grave', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to restore temporary grave. Please try again.']);
        }
    }

    /**
     * Search members for temporary graves
     */
    public function searchMembers(Request $request)
    {
        $this->authorize('read-temporary-grave');

        $query = $request->get('query');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $members = Member::with('community')
            ->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
            ->orWhere('family_no', 'like', "%{$query}%")
            ->orWhere('contact_no_1', 'like', "%{$query}%")
            ->orWhere('contact_no_2', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'full_name' => $member->first_name . ' ' . $member->last_name,
                    'first_name' => $member->first_name,
                    'last_name' => $member->last_name,
                    'family_no' => $member->family_no,
                    'contact_no_1' => $member->contact_no_1,
                    'current_add1' => $member->current_add1,
                    'community' => $member->community,
                ];
            });

        return response()->json($members);
    }
}
