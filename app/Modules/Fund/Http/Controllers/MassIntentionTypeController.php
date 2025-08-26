<?php

namespace Modules\Fund\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Fund\Models\MassIntentionType;

class MassIntentionTypeController extends Controller
{
    /**
     * Display a listing of mass intention types
     */
    public function index(Request $request)
    {
        $query = MassIntentionType::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Apply pagination
        $perPage = $request->get('per_page', 15);
        $massIntentionTypes = $query->paginate($perPage);

        return Inertia::render('MassIntentionTypes/Index', [
            'massIntentionTypes' => $massIntentionTypes,
            'filters' => $request->only(['search', 'sort_by', 'sort_order', 'per_page']),
        ]);
    }

    /**
     * Store a newly created mass intention type
     */
    public function store(Request $request)
    {
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
        return Inertia::render('MassIntentionTypes/Show', [
            'massIntentionType' => $massIntentionType,
        ]);
    }

    /**
     * Update the specified mass intention type
     */
    public function update(Request $request, MassIntentionType $massIntentionType)
    {
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
        // Check if it's being used by any mass intentions
        if ($massIntentionType->massIntentions()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete mass intention type as it is being used by mass intentions.']);
        }

        $massIntentionType->delete();

        return back()->with('success', 'Mass intention type deleted successfully.');
    }

    /**
     * Restore the specified mass intention type
     */
    public function restore($id)
    {
        $massIntentionType = MassIntentionType::withTrashed()->findOrFail($id);
        $massIntentionType->restore();

        return back()->with('success', 'Mass intention type restored successfully.');
    }

    /**
     * Permanently delete the specified mass intention type
     */
    public function forceDelete($id)
    {
        $massIntentionType = MassIntentionType::withTrashed()->findOrFail($id);
        $massIntentionType->forceDelete();

        return back()->with('success', 'Mass intention type permanently deleted.');
    }
}
