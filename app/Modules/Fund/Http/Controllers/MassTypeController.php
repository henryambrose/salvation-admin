<?php

namespace Modules\Fund\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Fund\Models\MassType;
use Illuminate\Support\Facades\Log;

class MassTypeController extends Controller
{
    /**
     * Display a listing of mass types
     */
    public function index(Request $request)
    {
        $this->authorize('list-mass-type');

        $query = MassType::query();

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
        $sortBy = $request->get('sort', 'sort_order');
        $sortOrder = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Apply pagination
        $perPage = $request->get('perPage', 15);
        $massTypes = $query->paginate($perPage);

        return Inertia::render('MassTypes/Index', [
            'massTypes' => $massTypes,
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
            'fetchUrl' => route('fund.mass-types.index'),
        ]);
    }

    /**
     * Store a newly created mass type
     */
    public function store(Request $request)
    {
        $this->authorize('create-mass-type');

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
        $this->authorize('read-mass-type');

        return Inertia::render('MassTypes/Show', [
            'massType' => $massType,
        ]);
    }

    /**
     * Update the specified mass type
     */
    public function update(Request $request, MassType $massType)
    {
        $this->authorize('update-mass-type');

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
        $this->authorize('delete-mass-type');

        try {
            $massType->delete();
            return back()->with('success', 'Mass type deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete mass type', [
                'id' => $massType->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Failed to delete mass type: ' . $e->getMessage()]);
        }
    }

    /**
     * Restore the specified mass type
     */
    public function restore($id)
    {
        $this->authorize('restore-mass-type');

        $massType = MassType::onlyTrashed()->findOrFail($id);
        $massType->restore();

        return back()->with('success', 'Mass type restored successfully.');
    }

    /**
     * Permanently delete the specified mass type
     */
    public function forceDelete($id)
    {
        $this->authorize('delete-mass-type');

        $massType = MassType::withTrashed()->findOrFail($id);
        $massType->forceDelete();

        return back()->with('success', 'Mass type permanently deleted.');
    }
}
