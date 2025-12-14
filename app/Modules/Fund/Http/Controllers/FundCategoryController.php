<?php

namespace Modules\Fund\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Fund\Models\FundCategory;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class FundCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('list-fund-category');

        $query = FundCategory::query();

        // Handle search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Handle archived filter
        if ($request->get('isArchived') === 'true') {
            $query->onlyTrashed();
        }

        // Handle sorting
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->get('perPage', 10);
        $categories = $query->paginate($perPage);

        // Add filters to pagination links
        $categories->appends($request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']));

        return Inertia::render('Fund/Category/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
            'fetchUrl' => route('fund.categories.index'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create-fund-category');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fund_categories,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        FundCategory::create($validated);

        return redirect()->route('fund.categories.index')
            ->with('success', 'Fund category created successfully.');
    }

    public function show(FundCategory $category): Response
    {
        $this->authorize('read-fund-category');

        return Inertia::render('Fund/Category/Show', [
            'category' => $category->load(['createdBy', 'updatedBy']),
        ]);
    }

    public function update(Request $request, FundCategory $category): RedirectResponse
    {
        $this->authorize('update-fund-category');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fund_categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['updated_by'] = Auth::id();

        $category->update($validated);

        return redirect()->route('fund.categories.index')
            ->with('success', 'Fund category updated successfully.');
    }

    public function destroy(FundCategory $category): RedirectResponse
    {
        $this->authorize('delete-fund-category');

        $category->delete();

        return redirect()->route('fund.categories.index')
            ->with('success', 'Fund category deleted successfully.');
    }

    public function restore($id): RedirectResponse
    {
        $category = FundCategory::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('fund.categories.index')
            ->with('success', 'Fund category restored successfully.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $category = FundCategory::onlyTrashed()->findOrFail($id);
        $category->forceDelete();

        return redirect()->route('fund.categories.index')
            ->with('success', 'Fund category permanently deleted.');
    }
}
