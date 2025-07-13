<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = State::query();

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

        return Inertia::render('state/Index', [
            'states' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only('search', 'sort', 'direction', 'perPage'),
            'fetchUrl' => route('state.index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $countries = Country::all();

        return Inertia::render('state/Create', [
            'countries' => $countries,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abbr' => 'nullable|string|max:10',
            'country_id' => 'required|exists:countries,id',
        ]);

        State::create($request->only('name', 'abbr', 'country_id'));

        return redirect()->route('state.index')->with('success', 'State created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(State $state): Response
    {
        $countries = Country::all();

        return Inertia::render('state/Edit', [
            'state' => $state,
            'countries' => $countries,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abbr' => 'nullable|string|max:10',
            'country_id' => 'required|exists:countries,id',
        ]);

        $state->update($request->only('name', 'abbr', 'country_id'));

        return redirect()->route('state.index')->with('success', 'State updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        $state->delete();

        return redirect()->route('state.index')->with('success', 'State deleted successfully.');
    }

    /**
     * Display a listing of the deleted states.
     */
    public function deleted(Request $request): Response
    {
        $query = State::onlyTrashed();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('state/Deleted', [
            'states' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only('search', 'perPage'),
            'fetchUrl' => route('state.deleted'),
        ]);
    }

    /**
     * Restore a deleted state.
     */
    public function restore($id)
    {
        $state = State::onlyTrashed()->findOrFail($id);
        $state->restore();

        return redirect()->route('state.deleted')->with('success', 'State restored successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Response
    {
        $state = \App\Models\State::withTrashed()->findOrFail($id);

        return Inertia::render('state/Show', [
            'state' => $state,
        ]);
    }
}
