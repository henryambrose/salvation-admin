<?php

namespace Modules\Members\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Members\Http\Requests\StoreRelationshipRequest;
use Modules\Members\Http\Requests\UpdateRelationshipRequest;
use Modules\Members\Models\Relationship;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RelationshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Relationship::query();

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

        return Inertia::render('relationship/Index', [
            'fetchUrl' => route('relationship.index'),
            'relationships' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('relationship/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRelationshipRequest $request)
    {
        Relationship::create($request->validated());

        return redirect()->route('relationship.index')->with('success', 'Relationship created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Relationship $relationship)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Relationship $relationship)
    {
        return Inertia::render('relationship/Edit', [
            'relationship' => $relationship,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRelationshipRequest $request, Relationship $relationship)
    {
        $relationship->update($request->validated());

        return redirect()->route('relationship.index')->with('success', 'Relationship updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Relationship $relationship)
    {
        $relationship->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('relationship.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Relationship deleted successfully.');
    }

    public function restore($id)
    {
        $relationship = Relationship::onlyTrashed()->findOrFail($id);
        $relationship->restore();

        return redirect()->route('relationship.index')->with('success', 'Relationship restored successfully.');
    }

    /**
     * Export relationships to CSV.
     */
    public function export(Request $request)
    {
        try {
            $this->authorize('viewAny', Relationship::class);

            $query = Relationship::query();

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
                $out = fopen('php://output', 'w');

                fputcsv($out, [
                    'ID', 'Relationship Name'
                ]);

                foreach ($query->cursor() as $item) {
                    fputcsv($out, [
                        $item->id,
                        $item->name ?? '',
                    ]);
                }

                fclose($out);
            }, 'relationships_'.now()->format('Y-m-d_H-i-s').'.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache',
            ]);

        } catch (\Exception $e) {
            \Log::error('Relationship Export failed: '.$e->getMessage());
            return response()->json(['error' => 'Export failed: '.$e->getMessage()], 500);
        }
    }
}
