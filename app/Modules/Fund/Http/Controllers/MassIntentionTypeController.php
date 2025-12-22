<?php

namespace Modules\Fund\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Fund\Models\MassIntentionType;
use Illuminate\Support\Facades\Log;

class MassIntentionTypeController extends Controller
{
    /**
     * Display a listing of mass intention types
     */
    public function index(Request $request)
    {
        $this->authorize('list-mass-intention-type');

        $query = MassIntentionType::query();

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
        $massIntentionTypes = $query->paginate($perPage);

        return Inertia::render('MassIntentionTypes/Index', [
            'massIntentionTypes' => $massIntentionTypes,
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
            'fetchUrl' => route('fund.mass-intention-types.index'),
        ]);
    }

    /**
     * Store a newly created mass intention type
     */
    public function store(Request $request)
    {
        $this->authorize('create-mass-intention-type');

        $request->validate([
            'name' => 'required|string|max:255|unique:mass_intention_types,name',
            'description' => 'nullable|string|max:1000',
            'default_amount' => 'required|numeric|min:0.01',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $massIntentionType = MassIntentionType::create([
            'name' => $request->name,
            'description' => $request->description,
            'default_amount' => $request->default_amount,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Mass intention type created successfully.');
    }

    /**
     * Display the specified mass intention type
     */
    public function show(MassIntentionType $massIntentionType)
    {
        $this->authorize('read-mass-intention-type');

        return Inertia::render('MassIntentionTypes/Show', [
            'massIntentionType' => $massIntentionType,
        ]);
    }

    /**
     * Update the specified mass intention type
     */
    public function update(Request $request, MassIntentionType $massIntentionType)
    {
        $this->authorize('update-mass-intention-type');

        $request->validate([
            'name' => 'required|string|max:255|unique:mass_intention_types,name,' . $massIntentionType->id,
            'description' => 'nullable|string|max:1000',
            'default_amount' => 'required|numeric|min:0.01',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $massIntentionType->update([
            'name' => $request->name,
            'description' => $request->description,
            'default_amount' => $request->default_amount,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Mass intention type updated successfully.');
    }

    /**
     * Remove the specified mass intention type
     */
    public function destroy(MassIntentionType $massIntentionType)
    {
        $this->authorize('delete-mass-intention-type');

        try {
            $massIntentionType->delete();
            return back()->with('success', 'Mass intention type deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete mass intention type', [
                'id' => $massIntentionType->id,
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Failed to delete mass intention type: ' . $e->getMessage()]);
        }
    }

    /**
     * Restore the specified mass intention type
     */
    public function restore($id)
    {
        $this->authorize('restore-mass-intention-type');

        $massIntentionType = MassIntentionType::onlyTrashed()->findOrFail($id);
        $massIntentionType->restore();

        return back()->with('success', 'Mass intention type restored successfully.');
    }

    /**
     * Permanently delete the specified mass intention type
     */
    public function forceDelete($id)
    {
        $this->authorize('delete-mass-intention-type');

        $massIntentionType = MassIntentionType::withTrashed()->findOrFail($id);
        $massIntentionType->forceDelete();

        return back()->with('success', 'Mass intention type permanently deleted.');
    }
}
