<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\ObituaryPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ObituaryPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $plans = ObituaryPlan::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->has('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->when($request->isArchived === 'true', function ($query) {
                $query->onlyTrashed();
            })
            ->withCount(['obituaryPages', 'obituaryPayments'])
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('PagesGraveyard/obituary-plans/Index', [
            'plans' => $plans,
            'filters' => $request->only(['search', 'is_active', 'isArchived']),
            'can' => [
                'create' => true, // Temporarily allow create while debugging authorization
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('PagesGraveyard/obituary-plans/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_in_days' => 'nullable|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        ObituaryPlan::create($validated);

        return redirect()->route('graveyard.obituary-plans.index')
            ->with('success', 'Obituary plan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ObituaryPlan $obituaryPlan): Response
    {
        $obituaryPlan->loadCount(['obituaryPages', 'obituaryPayments']);

        return Inertia::render('PagesGraveyard/obituary-plans/Show', [
            'plan' => $obituaryPlan,
            'can' => [
                'update' => Gate::allows('update', $obituaryPlan),
                'delete' => Gate::allows('delete', $obituaryPlan),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ObituaryPlan $obituaryPlan): Response
    {
        return Inertia::render('PagesGraveyard/obituary-plans/Edit', [
            'plan' => $obituaryPlan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ObituaryPlan $obituaryPlan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_in_days' => 'nullable|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $obituaryPlan->update($validated);

        return redirect()->route('graveyard.obituary-plans.index')
            ->with('success', 'Obituary plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ObituaryPlan $obituaryPlan): RedirectResponse
    {
        // Check if plan is being used
        if ($obituaryPlan->obituaryPages()->exists() || $obituaryPlan->obituaryPayments()->exists()) {
            return redirect()->route('graveyard.obituary-plans.index')
                ->with('error', 'Cannot delete obituary plan that is currently in use.');
        }

        $obituaryPlan->delete();

        return redirect()->route('graveyard.obituary-plans.index')
            ->with('success', 'Obituary plan deleted successfully.');
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore($id): RedirectResponse
    {
        $obituaryPlan = ObituaryPlan::withTrashed()->findOrFail($id);
        $obituaryPlan->restore();

        return redirect()->route('graveyard.obituary-plans.index')
            ->with('success', 'Obituary plan restored successfully.');
    }
}
