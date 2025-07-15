<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunityFundRequest;
use App\Http\Requests\UpdateCommunityFundRequest;
use App\Models\CommunityFund;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommunityFundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = CommunityFund::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $query->with(['member', 'member.community']);

        $perPage = $request->input('perPage', 10);

        return Inertia::render('community_fund/Index', [
            'communityFunds' => $query->paginate($perPage)->appends($request->query()),
            'filters' => request()->only('search', 'sort', 'direction', 'perPage'),
            'fetchUrl' => route('community-fund.index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('community_fund/CommunityFund', [
            'communities' => \App\Models\Community::all(),
            'members' => \App\Models\Member::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommunityFundRequest $request)
    {
        CommunityFund::create($request->validated());

        return redirect()->route('community-fund.index')->with('success', 'Community Fund created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CommunityFund $communityFund)
    {
        return Inertia::render('community_fund/CommunityFund', [
            'communityFund' => $communityFund,
            'communities' => \App\Models\Community::all(),
            'members' => \App\Models\Member::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CommunityFund $communityFund)
    {
        // Ensure all fields are present for the form (avoid undefined/null)
        return Inertia::render('community_fund/CommunityFund', [
            'communityFund' => [
                'id' => $communityFund->id,
                'member_id' => $communityFund->member_id,
                'amount' => $communityFund->amount,
                'fund_date' => $communityFund->fund_date,
                'description' => $communityFund->description,
            ],
            'communities' => \App\Models\Community::all(),
            'members' => \App\Models\Member::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommunityFundRequest $request, CommunityFund $communityFund)
    {
        $communityFund->update($request->validated());

        return redirect()->route('community-fund.index')->with('success', 'Community Fund updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommunityFund $communityFund)
    {
        $communityFund->delete();

        return redirect()->route('community-fund.index')->with('success', 'Community Fund deleted successfully.');
    }
}
