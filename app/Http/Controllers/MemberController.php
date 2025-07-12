<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\CellsAndAssociation;
use App\Models\Community;
use App\Models\FamilyIncomeRange;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) :Response
    {
        // $this->authorize('viewAny', Member::class);
        $query = Member::query();
        $query->with([
            'community',
            'communityCluster',
        ]);
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhereHas('community', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('communityCluster', function ($q3) use ($search) {
                      $q3->where('name', 'like', "%$search%");
                  });
            });
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('member/Index', [
            'fetchUrl' => route('member.index'),
            'members' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
            'canViewAnyMember' => $request->user()->can('view-Member'),
            'canCreateMember' => $request->user()->can('create-Member'),
            'canEditMember' => $request->user()->can('edit-Member'),
            'canDeleteMember' => $request->user()->can('delete-Member'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('member/Member', [
            'communities' => Community::with('communityClusters')->get(),
            'cellsAndAssociations' => CellsAndAssociation::all(),
            'familyIncomeRanges' => FamilyIncomeRange::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $validated = $request->validated();

        Member::create($validated);

        Log::debug($validated);
        Log::debug($request->all());
        return redirect()->route('member.index')->with('success', 'Member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member): Response
    {
        return Inertia::render('member/Member', [
            'member' => $member
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        $familyIncomeRanges = FamilyIncomeRange::all()->map(function ($item) {
            return ['id' => $item->name, 'name' => $item->name];
        })->toArray();
        return Inertia::render('member/Member', [
            'member' => $member,
            'communities' => Community::with('communityClusters')->get(),
            'cellsAndAssociations' => CellsAndAssociation::all(),
            'familyIncomeRanges' => $familyIncomeRanges,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $validated = $request->validated();

        $member->update($validated);

        Log::debug($validated);
        Log::debug($request->all());
        return redirect()->route('member.index')->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        //
    }

    public function showFamilyTree($id)
{
    $member = Member::with(['relationships.relatedMember', 'relatedMembers'])->findOrFail($id);

    return view('members.family_tree', compact('member'));
}
}

