<?php

namespace Modules\Members\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Members\Http\Requests\StoreBloodGroupRequest;
use Modules\Members\Models\BloodGroup;
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
        $this->authorize('list-blood-group');

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
        $this->authorize('create-blood-group');

        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreBloodGroupRequest $request)
    public function store(Request $request)
    {
        $this->authorize('create-blood-group');

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
        $this->authorize('read-blood-group');

        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BloodGroup $bloodGroup)
    {
        $this->authorize('update-blood-group');

        return Inertia::render('blood_group/BloodGroupEdit', [
            'bloodGroup' => $bloodGroup,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BloodGroup $bloodGroup)
    {
        $this->authorize('update-blood-group');

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
    public function destroy(Request $request, BloodGroup $bloodGroup)
    {
        $this->authorize('delete-blood-group');

        $bloodGroup->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('blood-group.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Blood Group deleted successfully.');
    }

    public function restore($id)
    {
        $this->authorize('restore-blood-group');

        $bloodGroup = BloodGroup::onlyTrashed()->findOrFail($id);
        $bloodGroup->restore();

        return redirect()->route('blood-group.index')->with('success', 'Blood Group restored successfully.');
    }
}
