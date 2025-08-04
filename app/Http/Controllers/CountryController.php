<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Country::query();
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply filters
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        $sort = $request->input('sort');
        $direction = $request->input('direction', 'asc');
        if ($sort) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('country/Index', [
            'countries' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only('search', 'sort', 'direction', 'perPage'),
            'fetchUrl' => route('country.index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('country/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCountryRequest $request)
    {
        Country::create($request->validated());

        return redirect()->route('country.index')->with('success', 'Country created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country): Response
    {
        return Inertia::render('country/Edit', [
            'country' => $country,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCountryRequest $request, Country $country)
    {
        $country->update($request->validated());

        return redirect()->route('country.index')->with('success', 'Country updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('country.index')->with('success', 'Country deleted successfully.');
    }

    /**
     * Display a listing of the deleted countries.
     */
    public function deleted(Request $request): Response
    {
        $query = Country::onlyTrashed();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('country/Deleted', [
            'countries' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only('search', 'perPage'),
            'fetchUrl' => route('country.deleted'),
        ]);
    }

    /**
     * Restore a deleted country.
     */
    public function restore($id)
    {
        $country = Country::onlyTrashed()->findOrFail($id);
        $country->restore();
        return redirect()->route('country.index')->with('success', 'Country restored successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Response
    {
        $country = \App\Models\Country::withTrashed()->findOrFail($id);

        return Inertia::render('country/Show', [
            'country' => $country,
        ]);
    }
}
