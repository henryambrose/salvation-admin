<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberProfileDetailsRequest;
use App\Http\Requests\Member\UpdateMemberProfileDetailsRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ProfileDetailsController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('member/Details', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberProfileDetailsRequest $request)
    {
        $validated = $request->validated();

        Member::create($validated);

        Log::debug($validated);
        Log::debug($request->all());
        return redirect()->route('member.index')->with('success', 'Member created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        return Inertia::render('member/Details', [
            'member' => $member
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberProfileDetailsRequest $request, Member $member)
    {
        $validated = $request->validated();

        // $member->update($validated);

        Log::debug($validated);
        Log::debug($request->all());
        return redirect()->route('member.index')->with('success', 'Member updated successfully.');
    }

}
