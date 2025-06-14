<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunityRequest;
use App\Http\Requests\UpdateCommunityRequest;
use App\Models\Community;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $communities = Community::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('community/Index', [
            'communities' => $communities,
            'filters' => request()->only('search', 'sort', 'direction', 'perPage'),
            'fetchUrl' => route('community.index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('community/Community');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommunityRequest $request)
    {
        Community::create($request->validated());

        return redirect()->route('community.index')->with('success', 'Community created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Community $community): Response
    {
        return Inertia::render('community/Community', [
            'community' => $community,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Community $community): Response
    {
        return Inertia::render('community/Community', [
            'community' => $community,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommunityRequest $request, Community $community)
    {
        $community->update($request->validated());

        return redirect()->route('community.index')->with('success', 'Community updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community)
    {
        $community->delete();

        return redirect()->route('community.index')->with('success', 'Community deleted successfully.');
    }
}
