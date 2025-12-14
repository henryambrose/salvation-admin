<?php

namespace Modules\Members\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Members\Http\Requests\StoreCountryRequest;
use Modules\Members\Http\Requests\UpdateCountryRequest;
use Modules\Members\Models\Country;
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
        $this->authorize('list-country');

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
            'filters' => $request->only('search', 'sort', 'direction', 'perPage', 'isArchived'),
            'fetchUrl' => route('country.index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create-country');

        return Inertia::render('country/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCountryRequest $request)
    {
        $this->authorize('create-country');

        Country::create($request->validated());

        return redirect()->route('country.index')->with('success', 'Country created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country): Response
    {
        $this->authorize('update-country');

        return Inertia::render('country/Edit', [
            'country' => $country,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCountryRequest $request, Country $country)
    {
        $this->authorize('update-country');

        $country->update($request->validated());

        return redirect()->route('country.index')->with('success', 'Country updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Country $country)
    {
        $this->authorize('delete-country');

        $country->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('country.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Country deleted successfully.');
    }

    /**
     * Display a listing of the deleted countries.
     */

    /**
     * Restore a deleted country.
     */
    public function restore($id)
    {
        $this->authorize('restore-country');

        $country = Country::onlyTrashed()->findOrFail($id);
        $country->restore();

        return redirect()->route('country.index')->with('success', 'Country restored successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Response
    {
        $this->authorize('read-country');

        $country = Country::withTrashed()->findOrFail($id);

        return Inertia::render('country/Show', [
            'country' => $country,
        ]);
    }
}
