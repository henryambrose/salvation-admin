<?php

namespace Modules\Members\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Members\Http\Requests\StoreCommunityRequest;
use Modules\Members\Http\Requests\UpdateCommunityRequest;
use Modules\Members\Models\Community;
use Modules\Members\Models\Zone;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Community::class);

        $query = Community::query()
            ->select([
                'communities.id',
                'communities.name',
                'communities.zone_id',
                'communities.created_at',
                'communities.updated_at',
                'communities.deleted_at'
            ])
            ->with([
                'zone:id,name',
                'ppchead:id,community_id,member_id',
                'ppchead.member:id,first_name,last_name',
                'scchead:id,community_id,member_id',
                'scchead.member:id,first_name,last_name'
            ]);

        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Optimize search with proper indexing
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('communities.name', 'like', "%$search%")
                    ->orWhereHas('zone', function ($zoneQuery) use ($search) {
                        $zoneQuery->select('id', 'name')
                            ->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('ppchead.member', function ($memberQuery) use ($search) {
                        $memberQuery->select('id', 'first_name', 'last_name')
                            ->whereRaw("CONCAT(first_name, ' ', COALESCE(last_name, '')) LIKE ?", ["%$search%"]);
                    })
                    ->orWhereHas('scchead.member', function ($memberQuery) use ($search) {
                        $memberQuery->select('id', 'first_name', 'last_name')
                            ->whereRaw("CONCAT(first_name, ' ', COALESCE(last_name, '')) LIKE ?", ["%$search%"]);
                    });
            });
        }

        if ($sort = $request->input('sort')) {
            // Ensure sort field is valid to prevent SQL injection
            $allowedSorts = ['id', 'name', 'created_at', 'updated_at'];
            if (in_array($sort, $allowedSorts)) {
                $query->orderBy($sort, $request->input('direction', 'asc'));
            } else {
                $query->orderBy('id', 'asc');
            }
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = min($request->input('perPage', 10), 100); // Limit max perPage
        $data = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('community/Index', [
            'communities' => $data,
            'filters' => request()->only('search', 'sort', 'direction', 'perPage', 'isArchived'),
            'fetchUrl' => route('community.index'),
            'zones' => Zone::select('id', 'name')->get(), // Only select needed fields
            'pagination' => [
                'currentPage' => $data->currentPage(),
                'lastPage' => $data->lastPage(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Community::class);

        return Inertia::render('community/Community', [
            'zones' => Zone::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommunityRequest $request)
    {
        $this->authorize('create', Community::class);

        Community::create($request->validated());
        $perPage = $request->input('perPage', 10);
        $total = Community::count();
        $lastPage = (int) ceil($total / $perPage);

        return redirect()->route('community.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $lastPage,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Community created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Community $community): Response
    {
        $this->authorize('view', $community);

        return Inertia::render('community/Community', [
            'community' => $community,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Community $community): Response
    {
        $this->authorize('update', $community);

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
        $this->authorize('update', $community);

        $community->update($request->validated());
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        return redirect()->route('community.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Community updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Community $community)
    {
        $this->authorize('delete', $community);

        $community->delete();
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('community.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Community deleted successfully.');

        // return redirect()->route('community.index')->with('success', 'Community deleted successfully.');
    }

    /**
     * Restore a deleted community.
     */
    public function restore($id)
    {
        $community = Community::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $community);

        $community->restore();

        return redirect()->route('community.index')->with('success', 'Community restored successfully.');
    }

    public function export(Request $request)
    {
        try {
            $this->authorize('viewAny', Community::class);

            $query = Community::with(['zone', 'ppchead.member', 'scchead.member']);

            if ($request->boolean('isArchived')) {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhereHas('zone', function ($zoneQuery) use ($search) {
                            $zoneQuery->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('ppchead.member', function ($memberQuery) use ($search) {
                            $memberQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
                        })
                        ->orWhereHas('scchead.member', function ($memberQuery) use ($search) {
                            $memberQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
                        });
                });
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'name', 'zone.name', 'ppchead.member.first_name', 'scchead.member.first_name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                if ($sort === 'zone.name') {
                    $query->join('zones', 'communities.zone_id', '=', 'zones.id')
                        ->orderBy('zones.name', $direction);
                } elseif ($sort === 'ppchead.member.first_name') {
                    $query->join('p_p_c_heads', 'communities.id', '=', 'p_p_c_heads.community_id')
                        ->join('members', 'p_p_c_heads.member_id', '=', 'members.id')
                        ->orderBy('members.first_name', $direction);
                } elseif ($sort === 'scchead.member.first_name') {
                    $query->join('s_c_c_heads', 'communities.id', '=', 's_c_c_heads.community_id')
                        ->join('members', 's_c_c_heads.member_id', '=', 'members.id')
                        ->orderBy('members.first_name', $direction);
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
                    'ID', 'Community Name', 'Zone', 'PPC Head', 'SCC Head'
                ]);

                foreach ($query->cursor() as $item) {
                    $ppcHeadName = $item->ppchead && $item->ppchead->member
                        ? trim($item->ppchead->member->first_name.' '.$item->ppchead->member->last_name)
                        : '';

                    $sccHeadName = $item->scchead && $item->scchead->member
                        ? trim($item->scchead->member->first_name.' '.$item->scchead->member->last_name)
                        : '';

                    fputcsv($out, [
                        $item->id,
                        $item->name ?? '',
                        $item->zone ? $item->zone->name : '',
                        $ppcHeadName,
                        $sccHeadName,
                    ]);
                }

                fclose($out);
            }, 'communities_'.now()->format('Y-m-d_H-i-s').'.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache',
            ]);

        } catch (\Exception $e) {
            \Log::error('Community Export failed: '.$e->getMessage());
            return response()->json(['error' => 'Export failed: '.$e->getMessage()], 500);
        }
    }
}
