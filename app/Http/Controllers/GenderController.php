<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenderRequest;
use App\Http\Requests\UpdateGenderRequest;
use App\Models\Gender;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Gender::query();

        // Handle archived records
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

        return Inertia::render('gender/Index', [
            'fetchUrl' => route('gender.index'),
            'genders' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('gender/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGenderRequest $request)
    {
        Gender::create($request->validated());

        return redirect()->route('gender.index')->with('success', 'Gender created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gender $gender)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gender $gender)
    {
        return Inertia::render('gender/Edit', [
            'gender' => $gender,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGenderRequest $request, Gender $gender)
    {
        $gender->update($request->validated());

        return redirect()->route('gender.index')->with('success', 'Gender updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gender $gender)
    {
        $gender->delete();

        return redirect()->route('gender.index')->with('success', 'Gender deleted successfully.');
    }

    public function restore($id)
    {
        $gender = Gender::onlyTrashed()->findOrFail($id);
        $gender->restore();

        return redirect()->route('gender.index')->with('success', 'Gender restored successfully.');
    }
}
