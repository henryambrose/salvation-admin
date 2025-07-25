<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunityRequest;
use App\Http\Requests\UpdateCommunityRequest;
use App\Models\Community;
use App\Models\Zone;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Community::query()->with('zone');

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
        $data = $query->paginate($perPage)->appends($request->query());
        return Inertia::render('community/Index', [
            'communities' => $data,
            'filters' => request()->only('search', 'sort', 'direction', 'perPage', 'isArchived'),
            'fetchUrl' => route('community.index'),
            'zones' => Zone::all(),
            'pagination' => [
                'currentPage' => $query->paginate($perPage)->currentPage(),
                'lastPage' => $query->paginate($perPage)->lastPage(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('community/Community', [
            'zones' => Zone::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommunityRequest $request)
    {
        $data = $request->validated();
        $data['zone_id'] = $request->input('zone_id');
        Community::create($data);
        return redirect()->route('community.index')->with('success', 'Community created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Community $community): Response
    {
        return Inertia::render('community/Community', [
            'community' => $community,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Community $community): Response
    {
        return Inertia::render('community/Community', [
            'community' => $community,
            'zones' => Zone::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommunityRequest $request, Community $community)
    {
        $data = $request->validated();
        $data['zone_id'] = $request->input('zone_id');
        $community->update($data);

        return redirect()->route('community.index')->with('success', 'Community updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community)
    {
        
         $community->delete();

        return redirect()->route('community.index')->with('success', 'Community deleted successfully.');
    }

    /**
     * Restore a deleted community.
     */
    public function restore($id)
    {
        $community = Community::onlyTrashed()->findOrFail($id);
        $community->restore();
        return redirect()->route('community.index')->with('success', 'Community restored successfully.');
    }
}
