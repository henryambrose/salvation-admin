<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClusterRequest;
use App\Http\Requests\UpdateClusterRequest;
use App\Models\Cluster;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClusterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Cluster::query();

        // Archive logic
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Search functionality
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('clusters/Index', [
            'fetchUrl' => route('clusters.index'),
            'clusters' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('clusters/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClusterRequest $request)
    {
        Cluster::create($request->validated());

        return redirect()->route('clusters.index')
            ->with('success', 'Cluster created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cluster $cluster): Response
    {
        $cluster->load(['communityClusters.community', 'communityClusterHeads.member']);

        return Inertia::render('clusters/Show', [
            'cluster' => $cluster,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cluster $cluster): Response
    {
        return Inertia::render('clusters/Edit', [
            'cluster' => $cluster,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClusterRequest $request, Cluster $cluster)
    {
        $cluster->update($request->validated());

        return redirect()->route('clusters.index')
            ->with('success', 'Cluster updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Cluster $cluster)
    {
        $cluster->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('clusters.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Cluster deleted successfully.');
    }

    public function restore($id)
    {
        $cluster = Cluster::onlyTrashed()->findOrFail($id);
        $cluster->restore();

        return redirect()->route('clusters.index')->with('success', 'Cluster restored successfully.');
    }
}
