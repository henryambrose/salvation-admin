<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSCCHeadRequest;
use App\Http\Requests\UpdateSCCHeadRequest;
use App\Models\Community;
use App\Models\Member;
use App\Models\SCCHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SCCHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = SCCHead::query();
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        $query->select('s_c_c_heads.*');
        $query->join('members', 's_c_c_heads.member_id', '=', 'members.id');
        $query->join('communities', 's_c_c_heads.community_id', '=', 'communities.id');
        $query->select('s_c_c_heads.*', 'members.first_name as member_first_name', 'members.last_name as member_last_name',
            DB::raw("CONCAT(members.first_name, ' ', members.last_name) as member_full_name"),
            'communities.name as community_name'
        );
        // Apply filters
        if ($communityId = $request->input('community_id')) {
            $query->where('s_c_c_heads.community_id', $communityId);
        }
        if ($memberId = $request->input('member_id')) {
            $query->where('s_c_c_heads.member_id', $memberId);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('members.first_name', 'like', "%$search%")
                    ->orWhere('members.middle_name', 'like', "%$search%")
                    ->orWhere('members.last_name', 'like', "%$search%")
                    ->orWhere('communities.name', 'like', "%$search%");
            });
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('s_c_c_head/Index', [
            'fetchUrl' => route('scc-head.index'),
            's_c_c_heads' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
            'communities' => Community::all(),
            // 'members' => Member::all(), // REMOVE THIS
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('s_c_c_head/SCCHead', [
            'communities' => Community::all(),
            'members' => Member::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSCCHeadRequest $request)
    {
        $validated = $request->validated();

        SCCHead::create([
            'member_id' => $validated['member_id'],
            'community_id' => $validated['community_id'],
        ]);
        $perPage = $request->input('perPage', 10);
        $total = SCCHead::count();
        $lastPage = (int) ceil($total / $perPage);

        return redirect()->route('scc-head.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $lastPage,
                'perPage' => $perPage,
            ]
        ))->with('success', 'SCC Head created successfully.');

        // return redirect()->route('scc-head.index')->with('success', 'SCC Head created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SCCHead $sCCHead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SCCHead $sCCHead)
    {
        return Inertia::render('s_c_c_head/Index', [
            'sccHead' => $sCCHead,
            'communities' => Community::all(),
            'members' => Member::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSCCHeadRequest $request, $id)
    {
        $sCCHead = SCCHead::findOrFail($id);
        $validated = $request->validated();
        $sCCHead->update([
            'member_id' => $validated['member_id'],
            'community_id' => $validated['community_id'],
        ]);
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        return redirect()->route('scc-head.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'SCC Head updated successfully.');
        // return redirect()->route('scc-head.index')->with('success', 'SCC Head updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, SCCHead $sccHead)
    {
        $sccHead->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('scc-head.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'SCC Head deleted successfully.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $sccHead = SCCHead::withTrashed()->findOrFail($id);
        $sccHead->restore();

        return redirect()->route('scc-head.index')->with('success', 'SCC Head restored successfully.');
    }

    // Add API endpoint for fetching members by community
    // public function membersByCommunity($communityId)
    // {
    //     $members = Member::where('community_id', $communityId)
    //         ->select('id', 'first_name', 'middle_name', 'last_name')
    //         ->get()
    //         ->map(function ($m) {
    //             return [
    //                 'id' => $m->id,
    //                 'name' => trim("{$m->first_name} {$m->middle_name} {$m->last_name}"),
    //             ];
    //         });

    //     return response()->json($members);
    // }

    /**
     * Export SCC heads to CSV.
     */
    public function export(Request $request)
    {
        try {
            $this->authorize('viewAny', SCCHead::class);

            $query = SCCHead::with(['member', 'community']);

            if ($request->boolean('isArchived')) {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
                    })
                    ->orWhereHas('community', function ($communityQuery) use ($search) {
                        $communityQuery->where('name', 'like', "%$search%");
                    });
                });
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'member.first_name', 'community.name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                if ($sort === 'member.first_name') {
                    $query->join('members', 's_c_c_heads.member_id', '=', 'members.id')
                        ->orderBy('members.first_name', $direction);
                } elseif ($sort === 'community.name') {
                    $query->join('communities', 's_c_c_heads.community_id', '=', 'communities.id')
                        ->orderBy('communities.name', $direction);
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
                    'ID', 'Member Name', 'Community', 'Contact Number', 'Email'
                ]);

                foreach ($query->cursor() as $item) {
                    $memberName = $item->member 
                        ? trim($item->member->first_name.' '.$item->member->last_name)
                        : '';

                    fputcsv($out, [
                        $item->id,
                        $memberName,
                        $item->community ? $item->community->name : '',
                        $item->member ? $item->member->contact_no_1 : '',
                        $item->member ? $item->member->email : '',
                    ]);
                }

                fclose($out);
            }, 'scc_heads_'.now()->format('Y-m-d_H-i-s').'.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache',
            ]);

        } catch (\Exception $e) {
            \Log::error('SCC Head Export failed: '.$e->getMessage());
            return response()->json(['error' => 'Export failed: '.$e->getMessage()], 500);
        }
    }
}
