<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Members\Http\Requests\StoreDesignationRequest;
use Modules\Members\Http\Requests\UpdateDesignationRequest;
use Modules\Members\Models\Designation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Designation::query();

        // Handle archived records
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('designation/Index', [
            'fetchUrl' => route('designation.index'),
            'designations' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('designation/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDesignationRequest $request)
    {
        Designation::create($request->validated());

        return redirect()->route('designation.index')->with('success', 'Designation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Designation $designation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Designation $designation)
    {
        return Inertia::render('designation/Edit', [
            'designation' => $designation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDesignationRequest $request, Designation $designation)
    {
        $designation->update($request->validated());

        return redirect()->route('designation.index')->with('success', 'Designation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Designation $designation)
    {
        $designation->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('designation.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Designation deleted successfully.');
    }

    public function restore($id)
    {
        $designation = Designation::onlyTrashed()->findOrFail($id);
        $designation->restore();

        return redirect()->route('designation.index')->with('success', 'Designation restored successfully.');
    }

    /**
     * Export designations to CSV.
     */
    public function export(Request $request)
    {
        try {
            $this->authorize('viewAny', Designation::class);

            $query = Designation::query();

            if ($request->boolean('isArchived')) {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->where('name', 'like', "%$search%");
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                $query->orderBy($sort, $direction);
            } else {
                $query->orderBy('id', 'asc');
            }

            // Streamed CSV keeps memory flat
            return response()->streamDownload(function () use ($query) {
                // Clear any output buffers to prevent extra whitespace
                while (ob_get_level()) {
                    ob_end_clean();
                }
                $out = fopen('php://output', 'w');

                fputcsv($out, [
                    'ID',
                    'Designation Name'
                ]);

                foreach ($query->cursor() as $item) {
                    fputcsv($out, [
                        $item->id,
                        $item->name ?? '',
                    ]);
                }

                fclose($out);
            }, 'designations_' . now()->format('Y-m-d_H-i-s') . '.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache',
            ]);
        } catch (\Exception $e) {
            Log::error('Designation Export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }
}
