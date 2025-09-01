<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\Graveyard\Models\Niche;

class NicheController extends Controller
{
    /**
     * Display a listing of niches
     */
    public function index(Request $request)
    {
        $query = Niche::query();

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
                $q->where('niche_no', 'like', "%{$search}%")
                  ->orWhere('sr_no', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
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
        $statuses = ['available', 'occupied', 'reserved', 'maintenance'];

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
        $locations = Niche::distinct()->pluck('location')->filter()->sort()->values();
        $statuses = ['available', 'occupied', 'reserved', 'maintenance'];

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
        $request->validate([
            'niche_no' => 'required|integer|min:1',
            'sr_no' => 'required|integer|min:1',
            'location' => 'required|string|max:100',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'last_occupation_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
            'size_width' => 'nullable|numeric|min:0',
            'size_height' => 'nullable|numeric|min:0',
            'size_depth' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

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
                'remarks' => $request->remarks,
                'size_width' => $request->size_width,
                'size_height' => $request->size_height,
                'size_depth' => $request->size_depth,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            Log::info('Niche created', [
                'id' => $niche->id,
                'location' => $niche->location,
                'niche_no' => $niche->niche_no,
                'sr_no' => $niche->sr_no,
                'created_by' => Auth::id(),
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
        $niche->load(['creator', 'updater']);

        return Inertia::render('PagesGraveyard/Niches/Show', [
            'niche' => $niche,
        ]);
    }

    /**
     * Show the form for editing the specified niche
     */
    public function edit(Niche $niche)
    {
        $locations = Niche::distinct()->pluck('location')->filter()->sort()->values();
        $statuses = ['available', 'occupied', 'reserved', 'maintenance'];

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
        $request->validate([
            'niche_no' => 'required|integer|min:1',
            'sr_no' => 'required|integer|min:1',
            'location' => 'required|string|max:100',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'last_occupation_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
            'size_width' => 'nullable|numeric|min:0',
            'size_height' => 'nullable|numeric|min:0',
            'size_depth' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

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
                'remarks' => $request->remarks,
                'size_width' => $request->size_width,
                'size_height' => $request->size_height,
                'size_depth' => $request->size_depth,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => Auth::id(),
            ]);

            Log::info('Niche updated', [
                'id' => $niche->id,
                'location' => $niche->location,
                'niche_no' => $niche->niche_no,
                'sr_no' => $niche->sr_no,
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
        try {
            // Check if niche is occupied
            if ($niche->status === 'occupied') {
                return back()->withErrors(['error' => 'Cannot delete occupied niche.']);
            }

            $niche->delete();

            Log::info('Niche deleted', [
                'id' => $niche->id,
                'location' => $niche->location,
                'niche_no' => $niche->niche_no,
                'sr_no' => $niche->sr_no,
                'deleted_by' => Auth::id(),
            ]);

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
        try {
            $niche = Niche::onlyTrashed()->findOrFail($id);
            $niche->restore();

            Log::info('Niche restored', [
                'id' => $niche->id,
                'location' => $niche->location,
                'niche_no' => $niche->niche_no,
                'sr_no' => $niche->sr_no,
                'restored_by' => Auth::id(),
            ]);

            return back()->with('success', 'Niche restored successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to restore niche', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to restore niche. Please try again.']);
        }
    }
}
