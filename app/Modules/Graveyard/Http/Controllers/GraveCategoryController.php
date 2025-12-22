<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\Graveyard\Models\GraveCategories;

class GraveCategoryController extends Controller
{
    /**
     * Display a listing of grave categories
     */
    public function index(Request $request)
    {
        $this->authorize('list-grave-category');

        $query = GraveCategories::query();

        // Archive logic
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('perPage', 10);
        $graveCategories = $query->paginate($perPage);

        return Inertia::render('PagesGraveyard/GraveCategories/Index', [
            'data' => $graveCategories,
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
            'fetchUrl' => route('graveyard.grave-categories.index'),
        ]);
    }

    /**
     * Show the form for creating a new grave category
     */
    public function create()
    {
        $this->authorize('create-grave-category');

        return Inertia::render('PagesGraveyard/GraveCategories/Create');
    }

    /**
     * Store a newly created grave category
     */
    public function store(Request $request)
    {
        $this->authorize('create-grave-category');

        $request->validate([
            'name' => 'required|string|max:255|unique:grave_categories,name',
        ]);

        try {
            $graveCategory = GraveCategories::create([
                'name' => $request->name,
            ]);

            return redirect()->route('graveyard.grave-categories.index')
                ->with('success', 'Grave category created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create grave category', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'Failed to create grave category. Please try again.']);
        }
    }

    /**
     * Display the specified grave category
     */
    public function show($id)
    {
        $this->authorize('read-grave-category');

        $graveCategory = GraveCategories::with(['temporaryGraves'])->findOrFail($id);

        return Inertia::render('PagesGraveyard/GraveCategories/Show', [
            'graveCategory' => $graveCategory,
        ]);
    }

    /**
     * Show the form for editing the specified grave category
     */
    public function edit($id)
    {
        $this->authorize('update-grave-category');

        $graveCategory = GraveCategories::findOrFail($id);

        return Inertia::render('PagesGraveyard/GraveCategories/Edit', [
            'graveCategory' => $graveCategory,
        ]);
    }

    /**
     * Update the specified grave category
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update-grave-category');

        $graveCategory = GraveCategories::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:grave_categories,name,' . $graveCategory->id,
        ]);

        try {
            $graveCategory->update([
                'name' => $request->name,
            ]);

            return redirect()->route('graveyard.grave-categories.index')
                ->with('success', 'Grave category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update grave category', [
                'id' => $graveCategory->id,
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'Failed to update grave category. Please try again.']);
        }
    }

    /**
     * Remove the specified grave category
     */
    public function destroy($id)
    {
        $this->authorize('delete-grave-category');

        try {
            $graveCategory = GraveCategories::findOrFail($id);

            // Check if category is being used by any temporary graves
            if ($graveCategory->temporaryGraves()->exists()) {
                return back()->withErrors(['error' => 'Cannot delete category. It is being used by temporary graves.']);
            }

            $graveCategory->delete();

            return back()->with('success', 'Grave category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete grave category', [
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to delete grave category. Please try again.']);
        }
    }

    /**
     * Restore the specified grave category
     */
    public function restore($id)
    {
        $this->authorize('restore-grave-category');

        try {
            $graveCategory = GraveCategories::onlyTrashed()->findOrFail($id);
            $graveCategory->restore();

            return back()->with('success', 'Grave category restored successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to restore grave category', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to restore grave category. Please try again.']);
        }
    }
}
