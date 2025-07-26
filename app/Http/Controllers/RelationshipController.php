<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRelationshipRequest;
use App\Http\Requests\UpdateRelationshipRequest;
use App\Models\Relationship;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;

class RelationshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Relationship::query();

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
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
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
    public function destroy(Relationship $relationship)
    {
        $relationship->delete();

        return redirect()->route('relationship.index')->with('success', 'Relationship deleted successfully.');
    }
}
