<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\Graveyard\Models\ServiceType;

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

        return Inertia::render('PagesGraveyard/ServiceTypes/Index', [
            'data' => $serviceTypes,
            'filters' => $filters,
            'fetchUrl' => route('graveyard.service-types.index'),
        ]);
    }

    /**
     * Store a newly created service type
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:service_types,name',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'type' => 'required|in:normal,concession,free',
            'category' => 'required|in:grave,funeral,additional',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $serviceTypeData = $request->all();
            $serviceTypeData['created_by'] = Auth::id();
            $serviceTypeData['updated_by'] = Auth::id();
            $serviceTypeData['sort_order'] = $serviceTypeData['sort_order'] ?? 0;

            $serviceType = ServiceType::create($serviceTypeData);

            Log::info('Service type created', [
                'service_type_id' => $serviceType->id,
                'name' => $serviceType->name,
                'cost' => $serviceType->cost,
                'type' => $serviceType->type,
                'created_by' => Auth::id(),
            ]);

            return back()->with('success', 'Service type created successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to create service type', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            return back()->withErrors(['error' => 'Failed to create service type. Please try again.']);
        }
    }

    /**
     * Update the specified service type
     */
    public function update(Request $request, $id)
    {
        $serviceType = ServiceType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:service_types,name,' . $id,
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'type' => 'required|in:normal,concession,free',
            'category' => 'required|in:grave,funeral,additional',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $serviceTypeData = $request->all();
            $serviceTypeData['updated_by'] = Auth::id();
            $serviceTypeData['sort_order'] = $serviceTypeData['sort_order'] ?? 0;

            $serviceType->update($serviceTypeData);

            Log::info('Service type updated', [
                'service_type_id' => $serviceType->id,
                'name' => $serviceType->name,
                'cost' => $serviceType->cost,
                'type' => $serviceType->type,
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Service type updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update service type', [
                'service_type_id' => $id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            return back()->withErrors(['error' => 'Failed to update service type. Please try again.']);
        }
    }

    /**
     * Remove the specified service type (soft delete)
     */
    public function destroy($id)
    {
        try {
            $serviceType = ServiceType::findOrFail($id);

            Log::info('Attempting to delete service type', [
                'service_type_id' => $serviceType->id,
                'name' => $serviceType->name,
                'cost' => $serviceType->cost,
                'type' => $serviceType->type,
                'user_id' => Auth::id(),
            ]);

            $serviceType->delete();

            Log::info('Service type deleted successfully', [
                'service_type_id' => $serviceType->id,
                'deleted_at' => $serviceType->deleted_at,
                'user_id' => Auth::id(),
            ]);

            return back()->with('success', 'Service type deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete service type', [
                'service_type_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return back()->with('error', 'Failed to delete service type.');
        }
    }

    /**
     * Restore the specified service type
     */
    public function restore($id)
    {
        try {
            $serviceType = ServiceType::onlyTrashed()->findOrFail($id);
            $serviceType->restore();

            Log::info('Service type restored successfully', [
                'service_type_id' => $serviceType->id,
                'name' => $serviceType->name,
                'user_id' => Auth::id(),
            ]);

            return back()->with('success', 'Service type restored successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to restore service type', [
                'service_type_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return back()->with('error', 'Failed to restore service type.');
        }
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
