<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = City::query()->with('state');
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply filters
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhereHas('state', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    });
            });
        }

        // State filter
        if ($stateId = $request->input('stateId')) {
            $query->where('state_id', $stateId);
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('city/Index', [
            'cities' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only('search', 'sort', 'direction', 'perPage', 'stateId', 'isArchived'),
            'fetchUrl' => route('city.index'),
            'states' => State::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $states = State::all();

        return Inertia::render('city/Create', [
            'states' => $states,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        City::create($request->validated());

        return redirect()->route('city.index')->with('success', 'City created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city): Response
    {
        return Inertia::render('city/Show', [
            'city' => $city->load('state'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city): Response
    {
        $states = State::all();

        return Inertia::render('city/Edit', [
            'city' => $city->load('state'),
            'states' => $states,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, City $city)
    {
        $city->update($request->validated());

        return redirect()->route('city.index')->with('success', 'City updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, City $city)
    {
        $city->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('city.index', array_merge(
            $request->only(['search', 'stateId', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'City deleted successfully.');
    }

    /**
     * Display archived cities.
     */
    // public function deleted(Request $request): Response
    // {
    //     $query = City::query()->with('state')->onlyTrashed();

    //     // Apply filters
    //     if ($search = $request->input('search')) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('name', 'like', "%$search%")
    //                 ->orWhereHas('state', function ($q2) use ($search) {
    //                     $q2->where('name', 'like', "%$search%");
    //                 });
    //         });
    //     }

    //     if ($sort = $request->input('sort')) {
    //         $query->orderBy($sort, $request->input('direction', 'asc'));
    //     } else {
    //         $query->orderBy('id', 'asc');
    //     }

    //     $perPage = $request->input('perPage', 10);

    //     return Inertia::render('city/Deleted', [
    //         'cities' => $query->paginate($perPage)->appends($request->query()),
    //         'filters' => $request->only('search', 'sort', 'direction', 'perPage'),
    //         'fetchUrl' => route('city.deleted'),
    //     ]);
    // }

    /**
     * Restore the specified resource.
     */
    public function restore($id)
    {
        $city = City::onlyTrashed()->findOrFail($id);
        $city->restore();

        return redirect()->route('city.index')->with('success', 'City restored successfully.');
    }

    /**
     * Export cities to CSV.
     */
    public function export(Request $request)
    {
        try {
            $this->authorize('viewAny', City::class);

            $query = City::with(['state']);

            if ($request->boolean('isArchived')) {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhereHas('state', function ($stateQuery) use ($search) {
                            $stateQuery->where('name', 'like', "%$search%");
                        });
                });
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'name', 'state.name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                if ($sort === 'state.name') {
                    $query->join('states', 'cities.state_id', '=', 'states.id')
                        ->orderBy('states.name', $direction);
                } else {
                    $query->orderBy($sort, $direction);
                }
            } else {
                $query->orderBy('id', 'asc');
            }

            // Streamed CSV keeps memory flat
            return response()->streamDownload(function () use ($query) {
                $out = fopen('php://output', 'w');

                fputcsv($out, [
                    'ID', 'City Name', 'State'
                ]);

                foreach ($query->cursor() as $item) {
                    fputcsv($out, [
                        $item->id,
                        $item->name ?? '',
                        $item->state ? $item->state->name : '',
                    ]);
                }

                fclose($out);
            }, 'cities_'.now()->format('Y-m-d_H-i-s').'.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache',
            ]);

        } catch (\Exception $e) {
            \Log::error('City Export failed: '.$e->getMessage());
            return response()->json(['error' => 'Export failed: '.$e->getMessage()], 500);
        }
    }
}
