<?php

namespace Modules\Members\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Members\Models\Cluster;
use Modules\Members\Models\Community;
use Modules\Members\Models\CommunityCluster;
use Modules\Members\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommunityClusterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = CommunityCluster::with(['cluster', 'community', 'member']);
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        $query->select('community_clusters.*');
        $query->join('communities', 'community_clusters.community_id', '=', 'communities.id');
        $query->join('clusters', 'community_clusters.cluster_id', '=', 'clusters.id');
        $query->leftJoin('members', 'community_clusters.member_id', '=', 'members.id');
        $query->select('community_clusters.*', 'communities.name as community_name', 'clusters.name as cluster_name', 'members.first_name', 'members.last_name');
        $query->selectRaw('CONCAT(members.first_name, " ", members.last_name) as member_name');

        if ($communityId = $request->input('community_id')) {
            $query->where('community_clusters.community_id', $communityId);
        }
        if ($clusterId = $request->input('cluster_id')) {
            $query->where('community_clusters.cluster_id', $clusterId);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('clusters.name', 'like', "%$search%")
                    ->orWhere('communities.name', 'like', "%$search%");
            });
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }
        $perPage = $request->input('perPage', 10);

        return Inertia::render('community_clusters/Index', [
            'communityClusters' => $query->paginate($perPage)->appends($request->query()),
            'communities' => Community::all(),
            'clusters' => Cluster::all(),
            'members' => Member::select('id', 'first_name', 'last_name', 'community_id')
                ->get()
                ->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->first_name.' '.$member->last_name,
                        'community_id' => $member->community_id,
                    ];
                }),
            'fetchUrl' => route('community-clusters.index'),
            'filters' => request()->only('search', 'sort', 'direction', 'perPage', 'isArchived'),
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
        $clusters = Cluster::orderBy('name')->get();
        $communities = Community::orderBy('name')->get();

        return Inertia::render('community_clusters/Create', [
            'clusters' => $clusters,
            'communities' => $communities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cluster_id' => 'required|exists:clusters,id',
            'community_id' => 'required|exists:communities,id',
            'member_id' => 'nullable|exists:members,id',
        ]);

        // Check for unique combination
        $existing = CommunityCluster::where('community_id', $validated['community_id'])
            ->where('cluster_id', $validated['cluster_id'])
            ->first();

        if ($existing) {
            return back()->withErrors(['name' => 'A cluster with this name already exists in this community.']);
        }

        CommunityCluster::create($validated);

        return redirect()->route('community-clusters.index')
            ->with('success', 'Community Cluster created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CommunityCluster $communityCluster): Response
    {
        $communityCluster->load(['cluster', 'community', 'communityClusterHeads.cluster', 'clusters']);

        return Inertia::render('community_clusters/Show', [
            'communityCluster' => $communityCluster,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CommunityCluster $communityCluster): Response
    {
        $clusters = Cluster::orderBy('name')->get();
        $communities = Community::orderBy('name')->get();

        return Inertia::render('community_clusters/Edit', [
            'communityCluster' => $communityCluster,
            'clusters' => $clusters,
            'communities' => $communities,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CommunityCluster $communityCluster)
    {
        $validated = $request->validate([
            'cluster_id' => 'required|exists:clusters,id',
            'community_id' => 'required|exists:communities,id',
            'member_id' => 'nullable|exists:members,id',
        ]);

        // Check for unique combination (excluding current record)
        $existing = CommunityCluster::where('community_id', $validated['community_id'])
            ->where('cluster_id', $validated['cluster_id'])
            ->where('id', '!=', $communityCluster->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['name' => 'A cluster with this name already exists in this community.']);
        }

        $communityCluster->update($validated);

        return redirect()->route('community-clusters.index')
            ->with('success', 'Community Cluster updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CommunityCluster $communityCluster)
    {
        $communityCluster->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('community-clusters.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Community Cluster deleted successfully.');
    }

    public function restore($id)
    {
        $communityCluster = CommunityCluster::onlyTrashed()->findOrFail($id);
        $communityCluster->restore();

        return redirect()->route('community-clusters.index')
            ->with('success', 'Community Cluster restored successfully.');
    }

    /**
     * Export community clusters to CSV.
     */
    public function export(Request $request)
    {
        try {
            $this->authorize('viewAny', CommunityCluster::class);

            $query = CommunityCluster::with(['community', 'cluster']);

            if ($request->boolean('isArchived')) {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('community', function ($communityQuery) use ($search) {
                        $communityQuery->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('cluster', function ($clusterQuery) use ($search) {
                        $clusterQuery->where('name', 'like', "%$search%");
                    });
                });
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'community.name', 'cluster.name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                if ($sort === 'community.name') {
                    $query->join('communities', 'community_clusters.community_id', '=', 'communities.id')
                        ->orderBy('communities.name', $direction);
                } elseif ($sort === 'cluster.name') {
                    $query->join('clusters', 'community_clusters.cluster_id', '=', 'clusters.id')
                        ->orderBy('clusters.name', $direction);
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
                    'ID', 'Community', 'Cluster'
                ]);

                foreach ($query->cursor() as $item) {
                    fputcsv($out, [
                        $item->id,
                        $item->community ? $item->community->name : '',
                        $item->cluster ? $item->cluster->name : '',
                    ]);
                }

                fclose($out);
            }, 'community_clusters_'.now()->format('Y-m-d_H-i-s').'.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache',
            ]);

        } catch (\Exception $e) {
            \Log::error('Community Cluster Export failed: '.$e->getMessage());
            return response()->json(['error' => 'Export failed: '.$e->getMessage()], 500);
        }
    }
}
