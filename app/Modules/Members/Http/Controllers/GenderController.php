<?php

namespace Modules\Members\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Members\Http\Requests\StoreGenderRequest;
use Modules\Members\Http\Requests\UpdateGenderRequest;
use Modules\Members\Models\Gender;
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
        $this->authorize('list-gender');

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
        $this->authorize('create-gender');

        return Inertia::render('gender/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGenderRequest $request)
    {
        $this->authorize('create-gender');

        Gender::create($request->validated());

        return redirect()->route('gender.index')->with('success', 'Gender created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gender $gender)
    {
        $this->authorize('read-gender');

        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gender $gender)
    {
        $this->authorize('update-gender');

        return Inertia::render('gender/Edit', [
            'gender' => $gender,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGenderRequest $request, Gender $gender)
    {
        $this->authorize('update-gender');

        $gender->update($request->validated());

        return redirect()->route('gender.index')->with('success', 'Gender updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Gender $gender)
    {
        $this->authorize('delete-gender');

        $gender->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('gender.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Gender deleted successfully.');
    }

    public function restore($id)
    {
        $this->authorize('restore-gender');

        $gender = Gender::onlyTrashed()->findOrFail($id);
        $gender->restore();

        return redirect()->route('gender.index')->with('success', 'Gender restored successfully.');
    }
}
