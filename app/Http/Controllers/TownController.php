<?php

namespace App\Http\Controllers;

use App\Models\Town;
use App\Models\City;
use App\Http\Requests\StoreTownRequest;
use App\Http\Requests\UpdateTownRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TownController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Town::query()->with('city');
        if ($request->input('isArchived')==='true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply filters
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('town/Index', [
            'towns' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only('search', 'sort', 'direction', 'perPage', 'isArchived'),
            'fetchUrl' => route('town.index'),
            'cities' => City::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $cities = City::all();

        return Inertia::render('town/Create', [
            'cities' => $cities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTownRequest $request)
    {
        Town::create($request->validated());

        return redirect()->route('town.index')->with('success', 'Town created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Town $town): Response
    {
        $cities = City::all();

        return Inertia::render('town/Edit', [
            'town' => $town,
            'cities' => $cities,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTownRequest $request, Town $town)
    {
        $town->update($request->validated());

        return redirect()->route('town.index')->with('success', 'Town updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Town $town)
    {
        $town->delete();

        return redirect()->route('town.index')->with('success', 'Town deleted successfully.');
    }

    /**
     * Display a listing of the deleted towns.
     */
    public function deleted(Request $request): Response
    {
        $query = Town::onlyTrashed();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('town/Deleted', [
            'towns' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only('search', 'perPage'),
            'fetchUrl' => route('town.deleted'),
        ]);
    }

    /**
     * Restore a deleted town.
     */
    public function restore($id)
    {
        $town = Town::onlyTrashed()->findOrFail($id);
        $town->restore();
        return redirect()->route('town.index')->with('success', 'Town restored successfully.');
    }
}
