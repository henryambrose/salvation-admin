<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Http\Requests\StoreNicheValidMemberRequest;
use Modules\Graveyard\Http\Requests\UpdateNicheValidMemberRequest;
use Modules\Graveyard\Models\NicheValidMember;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NicheValidMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = NicheValidMember::query();
        
        if ($request->boolean('isArchived')) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('aadhar_no', 'like', "%$search%")
                  ->orWhere('contact_no', 'like', "%$search%");
            });
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('graveyard/niche_valid_member/Index', [
            'nicheValidMembers' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'perPage', 'isArchived']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('graveyard/niche_valid_member/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNicheValidMemberRequest $request)
    {
        $validatedData = $request->validated();
        
        NicheValidMember::create($validatedData);

        return redirect()->route('niche-valid-member.index')
            ->with('success', 'Niche Valid Member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(NicheValidMember $nicheValidMember): Response
    {
        return Inertia::render('graveyard/niche_valid_member/Show', [
            'nicheValidMember' => $nicheValidMember
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NicheValidMember $nicheValidMember): Response
    {
        return Inertia::render('graveyard/niche_valid_member/Edit', [
            'nicheValidMember' => $nicheValidMember
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNicheValidMemberRequest $request, NicheValidMember $nicheValidMember)
    {
        $validatedData = $request->validated();
        
        $nicheValidMember->update($validatedData);

        return redirect()->route('niche-valid-member.index')
            ->with('success', 'Niche Valid Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, NicheValidMember $nicheValidMember)
    {
        $nicheValidMember->delete();

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        
        return redirect()->route('niche-valid-member.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Niche Valid Member deleted successfully.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $nicheValidMember = NicheValidMember::onlyTrashed()->findOrFail($id);
        $nicheValidMember->restore();

        return redirect()->route('niche-valid-member.index')
            ->with('success', 'Niche Valid Member restored successfully.');
    }
}
