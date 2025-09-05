<?php

namespace Modules\Members\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Modules\Members\Http\Requests\StoreContactDetailsRequest;
use Modules\Members\Http\Requests\UpdateContactDetailsRequest;
use Modules\Members\Models\Member;

class ContactDetailsController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreMemberRequest $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateMemberRequest $request, Member $member)
    // {
    //     //
    // }
}
