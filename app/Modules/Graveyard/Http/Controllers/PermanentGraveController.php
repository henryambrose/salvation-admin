<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\Graveyard\Models\PermanentGrave;

class PermanentGraveController extends Controller
{
    /**
     * Display a listing of permanent graves
     */
    public function index(Request $request)
    {
        $query = PermanentGrave::query();

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
        $permanentGraves = $query->paginate($perPage);

        // Get filter options
        $sections = PermanentGrave::distinct()->pluck('section')->filter()->sort()->values();
        $statuses = ['available', 'occupied', 'reserved', 'maintenance'];

        return Inertia::render('PagesGraveyard/PermanentGraves/Index', [
            'data' => $permanentGraves,
            'filters' => $request->only(['search', 'section', 'status', 'is_active', 'sort', 'direction', 'perPage', 'isArchived']),
            'filterOptions' => [
                'sections' => $sections,
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
        $sections = PermanentGrave::distinct()->pluck('section')->filter()->sort()->values();
        $statuses = ['available', 'occupied', 'reserved', 'maintenance'];

        return Inertia::render('PagesGraveyard/PermanentGraves/Create', [
            'sections' => $sections,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created permanent grave
     */
    public function store(Request $request)
    {
        $request->validate([
            'section' => 'required|string|max:100',
            'row_no' => 'required|integer|min:1',
            'grave_no' => 'required|integer|min:1',
            'oldno' => 'nullable|string|max:50',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'last_burial_date' => 'nullable|date',
            'owner_name' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'plot_size' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Check for duplicate grave in same section
        $existingGrave = PermanentGrave::where('section', $request->section)
            ->where('row_no', $request->row_no)
            ->where('grave_no', $request->grave_no)
            ->first();

        if ($existingGrave) {
            return back()->withErrors(['grave_no' => 'A grave with this number already exists in the specified section and row.']);
        }

        try {
            $permanentGrave = PermanentGrave::create([
                'section' => $request->section,
                'row_no' => $request->row_no,
                'grave_no' => $request->grave_no,
                'oldno' => $request->oldno,
                'status' => $request->status,
                'last_burial_date' => $request->last_burial_date,
                'owner_name' => $request->owner_name,
                'remarks' => $request->remarks,
                'plot_size' => $request->plot_size,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            Log::info('Permanent grave created', [
                'id' => $permanentGrave->id,
                'section' => $permanentGrave->section,
                'grave_no' => $permanentGrave->grave_no,
                'created_by' => Auth::id(),
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
        $permanentGrave->load(['bookings.member', 'creator', 'updater']);

        return Inertia::render('PagesGraveyard/PermanentGraves/Show', [
            'permanentGrave' => $permanentGrave,
        ]);
    }

    /**
     * Show the form for editing the specified permanent grave
     */
    public function edit(PermanentGrave $permanentGrave)
    {
        $sections = PermanentGrave::distinct()->pluck('section')->filter()->sort()->values();
        $statuses = ['available', 'occupied', 'reserved', 'maintenance'];

        return Inertia::render('PagesGraveyard/PermanentGraves/Edit', [
            'permanentGrave' => $permanentGrave,
            'sections' => $sections,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified permanent grave
     */
    public function update(Request $request, PermanentGrave $permanentGrave)
    {
        $request->validate([
            'section' => 'required|string|max:100',
            'row_no' => 'required|integer|min:1',
            'grave_no' => 'required|integer|min:1',
            'oldno' => 'nullable|string|max:50',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'last_burial_date' => 'nullable|date',
            'owner_name' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'plot_size' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Check for duplicate grave in same section (excluding current grave)
        $existingGrave = PermanentGrave::where('section', $request->section)
            ->where('row_no', $request->row_no)
            ->where('grave_no', $request->grave_no)
            ->where('id', '!=', $permanentGrave->id)
            ->first();

        if ($existingGrave) {
            return back()->withErrors(['grave_no' => 'A grave with this number already exists in the specified section and row.']);
        }

        try {
            $permanentGrave->update([
                'section' => $request->section,
                'row_no' => $request->row_no,
                'grave_no' => $request->grave_no,
                'oldno' => $request->oldno,
                'status' => $request->status,
                'last_burial_date' => $request->last_burial_date,
                'owner_name' => $request->owner_name,
                'remarks' => $request->remarks,
                'plot_size' => $request->plot_size,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => Auth::id(),
            ]);

            Log::info('Permanent grave updated', [
                'id' => $permanentGrave->id,
                'section' => $permanentGrave->section,
                'grave_no' => $permanentGrave->grave_no,
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
        try {
            // Check if grave has any bookings
            if ($permanentGrave->bookings()->exists()) {
                return back()->withErrors(['error' => 'Cannot delete grave. It has associated burial records.']);
            }

            $permanentGrave->delete();

            Log::info('Permanent grave deleted', [
                'id' => $permanentGrave->id,
                'section' => $permanentGrave->section,
                'grave_no' => $permanentGrave->grave_no,
                'deleted_by' => Auth::id(),
            ]);

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
        try {
            $permanentGrave = PermanentGrave::onlyTrashed()->findOrFail($id);
            $permanentGrave->restore();

            Log::info('Permanent grave restored', [
                'id' => $permanentGrave->id,
                'section' => $permanentGrave->section,
                'grave_no' => $permanentGrave->grave_no,
                'restored_by' => Auth::id(),
            ]);

            return back()->with('success', 'Permanent grave restored successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to restore permanent grave', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to restore permanent grave. Please try again.']);
        }
    }
}
