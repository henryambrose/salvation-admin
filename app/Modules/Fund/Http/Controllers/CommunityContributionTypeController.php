<?php

namespace Modules\Fund\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Fund\Models\CommunityContributionType;
use Illuminate\Support\Facades\Log;

class CommunityContributionTypeController extends Controller
{
    /**
     * Display a listing of community contribution types
     */
    public function index(Request $request)
    {
        $this->authorize('list-community-contribution-type');

        $query = CommunityContributionType::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Handle archived filter
        if ($request->get('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Apply pagination
        $perPage = $request->get('perPage', 15);
        $contributionTypes = $query->paginate($perPage);

        return Inertia::render('CommunityContributionTypes/Index', [
            'contributionTypes' => $contributionTypes,
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
            'fetchUrl' => route('fund.community-contribution-types.index'),
        ]);
    }

    /**
     * Store a newly created community contribution type
     */
    public function store(Request $request)
    {
        $this->authorize('create-community-contribution-type');

        $request->validate([
            'name' => 'required|string|max:255|unique:community_contribution_types,name',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $contributionType = CommunityContributionType::create([
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Community contribution type created successfully.');
    }

    /**
     * Display the specified community contribution type
     */
    public function show(CommunityContributionType $contributionType)
    {
        $this->authorize('read-community-contribution-type');

        return Inertia::render('CommunityContributionTypes/Show', [
            'contributionType' => $contributionType,
        ]);
    }

    /**
     * Update the specified community contribution type
     */
    public function update(Request $request, CommunityContributionType $contributionType)
    {
        $this->authorize('update-community-contribution-type');

        $request->validate([
            'name' => 'required|string|max:255|unique:community_contribution_types,name,' . $contributionType->id,
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $contributionType->update([
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Community contribution type updated successfully.');
    }

    /**
     * Remove the specified community contribution type
     */
    public function destroy(CommunityContributionType $contributionType)
    {
        $this->authorize('delete-community-contribution-type');

        try {
            $contributionType->delete();
            return back()->with('success', 'Community contribution type deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete community contribution type', [
                'id' => $contributionType->id,
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Failed to delete community contribution type: ' . $e->getMessage()]);
        }
    }

    /**
     * Restore the specified community contribution type
     */
    public function restore($id)
    {
        $this->authorize('restore-community-contribution-type');

        $contributionType = CommunityContributionType::onlyTrashed()->findOrFail($id);
        $contributionType->restore();

        return back()->with('success', 'Community contribution type restored successfully.');
    }

    /**
     * Permanently delete the specified community contribution type
     */
    public function forceDelete($id)
    {
        $this->authorize('delete-community-contribution-type');

        $contributionType = CommunityContributionType::withTrashed()->findOrFail($id);
        $contributionType->forceDelete();

        return back()->with('success', 'Community contribution type permanently deleted.');
    }
}