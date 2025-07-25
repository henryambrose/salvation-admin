<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSCCHeadRequest;
use App\Http\Requests\UpdateSCCHeadRequest;
use App\Models\Community;
use App\Models\Member;
use App\Models\SCCHead;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;



class SCCHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) :Response
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
        $query->select('s_c_c_heads.*', 'members.first_name as member_first_name', 'communities.name as community_name');
        // Apply filters
        if ($communityId = $request->input('community_id')) {
            $query->where('s_c_c_heads.community_id', $communityId);
        }
        if ($memberId = $request->input('member_id')) {
            $query->where('s_c_c_heads.member_id', $memberId);
        }
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
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

        return redirect()->route('scc-head.index')->with('success', 'SCC Head created successfully.');
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

        return redirect()->route('scc-head.index')->with('success', 'SCC Head updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sCCHead = SCCHead::findOrFail($id);
        $sCCHead->delete();
        return redirect()->route('scc-head.index')->with('success', 'SCC Head deleted successfully.');
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
    public function membersByCommunity($communityId)
    {
        $members = Member::where('community_id', $communityId)
            ->select('id', 'first_name', 'middle_name', 'last_name')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'name' => trim("{$m->first_name} {$m->middle_name} {$m->last_name}"),
                ];
            });
        return response()->json($members);
    }
}
