<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePPCHeadRequest;
use App\Http\Requests\UpdatePPCHeadRequest;
use App\Models\Community;
use App\Models\Member;
use App\Models\PPCHead;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PPCHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = PPCHead::query();
        if ($request->input('isArchived')  === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        $query->select('p_p_c_heads.*');
        $query->join('members', 'p_p_c_heads.member_id', '=', 'members.id');
        $query->join('communities', 'p_p_c_heads.community_id', '=', 'communities.id');
        $query->select('p_p_c_heads.*', 'members.first_name as member_first_name', 'communities.name as community_name');
        // Apply filters
        if ($communityId = $request->input('community_id')) {
            $query->where('p_p_c_heads.community_id', $communityId);
        }
        if ($memberId = $request->input('member_id')) {
            $query->where('p_p_c_heads.member_id', $memberId);
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

        return Inertia::render('p_p_c_head/Index', [
            'ppcHeads' => $query->paginate($perPage)->appends($request->query()),
            'filters' => request()->only('search', 'sort', 'direction', 'perPage', 'isArchived'),
            'fetchUrl' => route('ppc-head.index'),
            'communities' => Community::all(),
            // 'members' => Member::all(), // REMOVE THIS
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('p_p_c_head/PPCHead', [
            'communities' => Community::all(),
            'members' => Member::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePPCHeadRequest $request)
    {
        $validated = $request->validated();
        PPCHead::create([
            'member_id' => $validated['member_id'],
            'community_id' => $validated['community_id'],
        ]);
        return redirect()->route('ppc-head.index')->with('success', 'PPC Head created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PPCHead $pPCHead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PPCHead $ppcHead): Response
    {
        return Inertia::render('p_p_c_head/PPCHead', [
            'PPCHead' => $ppcHead,
            'communities' => Community::all(),
            'members' => Member::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePPCHeadRequest $request, $id)
    {
        $ppcHead = PPCHead::findOrFail($id);
        $validated = $request->validated();
        $ppcHead->update([
            'member_id' => $validated['member_id'],
            'community_id' => $validated['community_id'],
        ]);
        
         return redirect()->route('ppc-head.index')->with('success', 'PPC Head updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ppcHead = PPCHead::findOrFail($id);
        $ppcHead->delete();
        return redirect()->route('ppc-head.index')->with('success', 'PPC Head deleted successfully.');
    }

    public function restore($id)
    {
        $ppcHead = PPCHead::withTrashed()->findOrFail($id);
        $ppcHead->restore();
        return redirect()->route('ppc-head.index')->with('success', 'PPC Head restored successfully.');
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
