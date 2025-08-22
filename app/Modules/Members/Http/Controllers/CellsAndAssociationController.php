<?php

namespace Modules\Members\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Members\Http\Requests\StoreCellsAndAssociationRequest;
use Modules\Members\Http\Requests\UpdateCellsAndAssociationRequest;
use Modules\Members\Models\CellsAndAssociation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CellsAndAssociationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = CellsAndAssociation::query();

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

        return Inertia::render('cells-and-association/Index', [
            'fetchUrl' => route('cells-and-association.index'),
            'cellsAndAssociations' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('cells-and-association/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCellsAndAssociationRequest $request)
    {
        CellsAndAssociation::create($request->validated());

        return redirect()->route('cells-and-association.index')->with('success', 'Cells and Association created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CellsAndAssociation $cellsAndAssociation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CellsAndAssociation $cellsAndAssociation)
    {
        return Inertia::render('cells-and-association/Edit', [
            'cellsAndAssociation' => $cellsAndAssociation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCellsAndAssociationRequest $request, CellsAndAssociation $cellsAndAssociation)
    {
        $cellsAndAssociation->update($request->validated());

        return redirect()->route('cells-and-association.index')->with('success', 'Cells and Association updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CellsAndAssociation $cellsAndAssociation)
    {
        $cellsAndAssociation->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('cells-and-association.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Cells and Association deleted successfully.');
    }

    public function restore($id)
    {
        $cellsAndAssociation = CellsAndAssociation::onlyTrashed()->findOrFail($id);
        $cellsAndAssociation->restore();

        return redirect()->route('cells-and-association.index')->with('success', 'Cells and Association restored successfully.');
    }
}
