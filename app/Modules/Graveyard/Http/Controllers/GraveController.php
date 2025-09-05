<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Graveyard\Models\PermanentGrave;
use Modules\Graveyard\Models\TemporaryGrave;

class GraveController extends Controller
{
    /**
     * Display a listing of graves (both permanent and temporary)
     */
    public function index(Request $request)
    {
        $graveType = $request->get('type', 'permanent'); // permanent or temporary

        if ($graveType === 'permanent') {
            $query = PermanentGrave::query();
        } else {
            $query = TemporaryGrave::query();
        }

        // Archive logic
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            if ($graveType === 'permanent') {
                $query->search($search);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('section', 'like', "%{$search}%")
                        ->orWhere('grave_no', 'like', "%{$search}%")
                        ->orWhere('oldno', 'like', "%{$search}%");
                });
            }
        }

        // Apply filters
        if ($request->filled('section')) {
            $query->bySection($request->section);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'section');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection)
            ->orderBy('row_no', 'asc')
            ->orderBy('grave_no', 'asc');

        // Pagination
        $perPage = $request->get('perPage', 10);
        $graves = $query->paginate($perPage);

        $filters = [
            'search' => $request->search,
            'type' => $graveType,
            'section' => $request->section,
            'status' => $request->status,
            'sort' => $sortBy,
            'direction' => $sortDirection,
            'perPage' => $perPage,
            'isArchived' => $request->input('isArchived', 'false'),
        ];

        return Inertia::render('PagesGraveyard/Graves/Index', [
            'data' => $graves,
            'filters' => $filters,
            'graveType' => $graveType,
            'fetchUrl' => route('graveyard.graves.index'),
        ]);
    }

    public function store(Request $request)
    {
        return back()->with('success', 'Grave created successfully.');
    }

    public function show($grave)
    {
        return Inertia::render('PagesGraveyard/Graves/Show', []);
    }

    public function update(Request $request, $grave)
    {
        return back()->with('success', 'Grave updated successfully.');
    }

    public function destroy($grave)
    {
        return back()->with('success', 'Grave deleted successfully.');
    }

    public function restore($id)
    {
        return back()->with('success', 'Grave restored successfully.');
    }

    public function forceDelete($id)
    {
        return back()->with('success', 'Grave permanently deleted.');
    }
}
