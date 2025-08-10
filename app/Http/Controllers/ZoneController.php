<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreZoneRequest;
use App\Models\Zone;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {

        $query = Zone::query();
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
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('zones/Index', [
            'fetchUrl' => route('zone.index'),
            'zones' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
            'canRestoreZone' => auth()->user()->can('restore-zone'),
        ]);
    }

    public function restore($id)
    {
        $zone = Zone::onlyTrashed()->findOrFail($id);
        $zone->restore();

        return redirect()->route('zone.index')->with('success', 'Zone restored successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreZoneRequest $request)
    // {
    //     //
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:zones,name',
            'description' => 'nullable|string',
        ]);

        Zone::create($validated);

        // return to_route('zone.index')->with('success', 'Zone created successfully.');
        return redirect()->route('zone.index')->with('success', 'Zone created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Zone $zone)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Zone $zone)
    {
        return Inertia::render('zones/ZoneEdit', [
            'zone' => $zone,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:zones,name,'.$zone->id,
            'description' => 'nullable|string',
        ]);

        $zone->update($validated);

        return redirect()->route('zone.index')->with('success', 'Zone updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Zone $zone)
    {
        $zone->delete();

        return redirect()->route('zone.index')->with('success', 'Zone deleted successfully.');
    }
}
