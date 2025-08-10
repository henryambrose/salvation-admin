<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBloodGroupRequest;
use App\Models\BloodGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BloodGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {

        $query = BloodGroup::query();
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

        return Inertia::render('blood_group/BloodGroup', [
            'fetchUrl' => route('blood-group.index'),
            'bloodGroups' => $query->paginate($perPage)->appends($request->query()),
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
    // public function store(StoreBloodGroupRequest $request)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:blood_groups,name',
        ]);

        BloodGroup::create($validated);
        $perPage = $request->input('perPage', 10);
        $total = BloodGroup::count();
        $lastPage = (int) ceil($total / $perPage);

        return redirect()->route('blood-group.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $lastPage,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Blood Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BloodGroup $bloodGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BloodGroup $bloodGroup)
    {
        return Inertia::render('blood_group/BloodGroupEdit', [
            'bloodGroup' => $bloodGroup,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BloodGroup $bloodGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:blood_groups,name,'.$bloodGroup->id,
        ]);

        $bloodGroup->update($validated);
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        return redirect()->route('blood-group.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Blood Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BloodGroup $bloodGroup)
    {
        $bloodGroup->delete();

        return redirect()->route('blood-group.index')->with('success', 'Blood Group deleted successfully.');
    }

    public function restore($id)
    {
        $bloodGroup = BloodGroup::onlyTrashed()->findOrFail($id);
        $bloodGroup->restore();

        return redirect()->route('blood-group.index')->with('success', 'Blood Group restored successfully.');
    }
}
