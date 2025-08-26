<?php

namespace Modules\Fund\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Fund\Models\MassType;

class MassTypeController extends Controller
{
    /**
     * Display a listing of mass types
     */
    public function index(Request $request)
    {
        $query = MassType::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Apply pagination
        $perPage = $request->get('per_page', 15);
        $massTypes = $query->paginate($perPage);

        return Inertia::render('MassTypes/Index', [
            'massTypes' => $massTypes,
            'filters' => $request->only(['search', 'sort_by', 'sort_order', 'per_page']),
        ]);
    }

    /**
     * Store a newly created mass type
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:mass_types,name',
            'description' => 'nullable|string|max:1000',
            'default_time' => 'required|date_format:H:i',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $massType = MassType::create([
            'name' => $request->name,
            'description' => $request->description,
            'default_time' => $request->default_time,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Mass type created successfully.');
    }

    /**
     * Display the specified mass type
     */
    public function show(MassType $massType)
    {
        return Inertia::render('MassTypes/Show', [
            'massType' => $massType,
        ]);
    }

    /**
     * Update the specified mass type
     */
    public function update(Request $request, MassType $massType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:mass_types,name,' . $massType->id,
            'description' => 'nullable|string|max:1000',
            'default_time' => 'required|date_format:H:i',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $massType->update([
            'name' => $request->name,
            'description' => $request->description,
            'default_time' => $request->default_time,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Mass type updated successfully.');
    }

    /**
     * Remove the specified mass type
     */
    public function destroy(MassType $massType)
    {
        // Check if it's being used by any mass schedules or intentions
        if ($massType->massSchedules()->count() > 0 || $massType->massIntentions()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete mass type as it is being used by mass schedules or intentions.']);
        }

        $massType->delete();

        return back()->with('success', 'Mass type deleted successfully.');
    }

    /**
     * Restore the specified mass type
     */
    public function restore($id)
    {
        $massType = MassType::withTrashed()->findOrFail($id);
        $massType->restore();

        return back()->with('success', 'Mass type restored successfully.');
    }

    /**
     * Permanently delete the specified mass type
     */
    public function forceDelete($id)
    {
        $massType = MassType::withTrashed()->findOrFail($id);
        $massType->forceDelete();

        return back()->with('success', 'Mass type permanently deleted.');
    }
}
