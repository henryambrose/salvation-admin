<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Http\Requests\StoreServiceTypeRequest;
use Modules\Graveyard\Http\Requests\UpdateServiceTypeRequest;
use Modules\Graveyard\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ServiceTypeController extends Controller
{
    /**
     * Display a listing of service types
     */
    public function index(Request $request)
    {
        $query = ServiceType::query();

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
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply filters
        if ($request->filled('type') && $request->type !== 'all') {
            $query->byType($request->type);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('perPage', 10);
        $serviceTypes = $query->paginate($perPage);

        $filters = [
            'search' => $request->search,
            'type' => $request->type,
            'category' => $request->category,
            'sort' => $sortBy,
            'direction' => $sortDirection,
            'perPage' => $perPage,
            'isArchived' => $request->input('isArchived', 'false'),
        ];

        return Inertia::render('PagesGraveyard/ServiceType/Index', [
            'serviceTypes' => $serviceTypes,
            'filters' => $filters,
            'filterOptions' => [
                'categories' => [
                    ['value' => 'grave', 'label' => 'Grave'],
                    ['value' => 'funeral', 'label' => 'Funeral'],
                    ['value' => 'additional', 'label' => 'Additional'],
                ],
                'types' => [
                    ['value' => 'normal', 'label' => 'Normal'],
                    ['value' => 'concession', 'label' => 'Concession'],
                    ['value' => 'free', 'label' => 'Free'],
                ],
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('PagesGraveyard/ServiceType/Create');
    }

    /**
     * Store a newly created service type
     */
    public function store(StoreServiceTypeRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['created_by'] = Auth::id();
        $validatedData['updated_by'] = aUTH::id();
        $validatedData['sort_order'] = $validatedData['sort_order'] ?? ServiceType::max('sort_order') + 1;

        ServiceType::create($validatedData);

        return redirect()->route('graveyard.service-types.index')
            ->with('success', 'Service type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceType $serviceType): Response
    {
        $serviceType->load(['creator', 'updater']);

        return Inertia::render('PagesGraveyard/ServiceType/Show', [
            'serviceType' => $serviceType
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceType $serviceType): Response
    {
        return Inertia::render('PagesGraveyard/ServiceType/Edit', [
            'serviceType' => $serviceType
        ]);
    }

    /**
     * Update the specified service type
     */
    public function update(UpdateServiceTypeRequest $request, ServiceType $serviceType)
    {
        $validatedData = $request->validated();
        $validatedData['updated_by'] = Auth::id();

        $serviceType->update($validatedData);

        return redirect()->route('graveyard.service-types.index')
            ->with('success', 'Service type updated successfully.');
    }

    /**
     * Remove the specified service type (soft delete)
     */
    public function destroy(Request $request, ServiceType $serviceType)
    {
        $serviceType->delete();

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        return redirect()->route('graveyard.service-types.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived', 'category', 'type', 'is_active']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Service type deleted successfully.');
    }

    /**
     * Restore the specified service type
     */
    public function restore($id)
    {
        $serviceType = ServiceType::onlyTrashed()->findOrFail($id);
        $serviceType->restore();

        return redirect()->route('graveyard.service-types.index')
            ->with('success', 'Service type restored successfully.');
    }

    /**
     * Toggle the active status of the service type.
     */
    public function toggleActive(Request $request, ServiceType $serviceType)
    {
        $serviceType->update([
            'is_active' => !$serviceType->is_active,
            'updated_by' => Auth::id(),
        ]);

        $status = $serviceType->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Service type {$status} successfully.");
    }

    /**
     * Permanently delete the specified service type
     */
    public function forceDelete($id)
    {
        try {
            $serviceType = ServiceType::onlyTrashed()->findOrFail($id);

            Log::info('Attempting to permanently delete service type', [
                'service_type_id' => $serviceType->id,
                'name' => $serviceType->name,
                'user_id' => Auth::id(),
            ]);

            $serviceType->forceDelete();

            Log::info('Service type permanently deleted', [
                'service_type_id' => $id,
                'user_id' => Auth::id(),
            ]);

            return back()->with('success', 'Service type permanently deleted.');
        } catch (\Exception $e) {
            Log::error('Failed to permanently delete service type', [
                'service_type_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return back()->with('error', 'Failed to permanently delete service type.');
        }
    }

    /**
     * Get service types for API endpoints
     */
    public function getServiceTypes(Request $request)
    {
        $query = ServiceType::active()->ordered();

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        $serviceTypes = $query->get();

        return response()->json($serviceTypes);
    }
}
