<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomeRangeRequest;
use App\Models\IncomeRange;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IncomeRangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {

        $query = IncomeRange::query();

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

        return Inertia::render('income_range/Index', [
            'fetchUrl' => route('income-range.index'),
            'incomeRange' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
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
    public function store(StoreIncomeRangeRequest $request)
    {
        $validated = $request->validated();

        IncomeRange::create($validated);

        return redirect()->route('income-range.index')->with('success', 'Income Range created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(IncomeRange $incomeRange)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IncomeRange $incomeRange)
    {
        return Inertia::render('income_range/IncomeRangeEdit', [
            'incomeRange' => $incomeRange,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IncomeRange $incomeRange)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:income_ranges,name,'.$incomeRange->id,
            // Add other fields as needed
        ]);

        $incomeRange->update($validated);

        return redirect()->route('income-range.index')->with('success', 'Income Range updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IncomeRange $incomeRange)
    {
        $incomeRange->delete();

        return redirect()->route('income-range.index')->with('success', 'Income Range deleted successfully.');
    }

    public function restore($id)
    {
        $range = IncomeRange::onlyTrashed()->findOrFail($id);
        $range->restore();

        return redirect()->route('income-range.index')->with('success', 'Income Range restored successfully.');
    }
}
