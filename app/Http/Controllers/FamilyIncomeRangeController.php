<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFamilyIncomeRangeRequest;
use App\Http\Requests\UpdateFamilyIncomeRangeRequest;
use App\Models\FamilyIncomeRange;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class FamilyIncomeRangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) :Response
    {

        $query = FamilyIncomeRange::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('family_income_range/FamilyIncomeRange', [
            'fetchUrl' => route('family-income-range.index'),
            'familyIncomeRange' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
        ]);
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
    public function store(StoreFamilyIncomeRangeRequest $request)
    {
        $validated = $request->validated();

        FamilyIncomeRange::create($validated);

        return redirect()->route('family-income-range.index')->with('success', 'Family Income Range created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FamilyIncomeRange $familyIncomeRange)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FamilyIncomeRange $familyIncomeRange)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFamilyIncomeRangeRequest $request, FamilyIncomeRange $familyIncomeRange)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FamilyIncomeRange $familyIncomeRange)
    {
        //
    }
}
