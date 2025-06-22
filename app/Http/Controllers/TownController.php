<?php

namespace App\Http\Controllers;

use App\Models\Town;
use App\Models\State;
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
        $query = Town::query();

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
            'filters' => $request->only('search', 'sort', 'direction', 'perPage'),
            'fetchUrl' => route('town.index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $states = State::all();

        return Inertia::render('town/Create', [
            'states' => $states,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'state_id' => 'required|exists:states,id',
        ]);

        Town::create($request->only('name', 'pincode', 'state_id'));

        return redirect()->route('town.index')->with('success', 'Town created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Town $town): Response
    {
        $states = State::all();

        return Inertia::render('town/Edit', [
            'town' => $town,
            'states' => $states,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Town $town)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'state_id' => 'required|exists:states,id',
        ]);

        $town->update($request->only('name', 'pincode', 'state_id'));

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
}
