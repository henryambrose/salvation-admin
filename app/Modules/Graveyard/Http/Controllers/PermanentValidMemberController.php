<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Http\Requests\StorePermanentValidMemberRequest;
use Modules\Graveyard\Http\Requests\UpdatePermanentValidMemberRequest;
use Modules\Graveyard\Models\PermanentValidMember;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PermanentValidMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = PermanentValidMember::query();
        
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

        return Inertia::render('graveyard/permanent_valid_member/Index', [
            'permanentValidMembers' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'perPage', 'isArchived']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('graveyard/permanent_valid_member/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermanentValidMemberRequest $request)
    {
        $validatedData = $request->validated();
        
        PermanentValidMember::create($validatedData);

        return redirect()->route('graveyard.permanent-valid-members.index')
            ->with('success', 'Permanent Valid Member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PermanentValidMember $permanentValidMember): Response
    {
        return Inertia::render('graveyard/permanent_valid_member/Show', [
            'permanentValidMember' => $permanentValidMember
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PermanentValidMember $permanentValidMember): Response
    {
        return Inertia::render('graveyard/permanent_valid_member/Edit', [
            'permanentValidMember' => $permanentValidMember
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermanentValidMemberRequest $request, PermanentValidMember $permanentValidMember)
    {
        $validatedData = $request->validated();
        
        $permanentValidMember->update($validatedData);

        return redirect()->route('graveyard.permanent-valid-members.index')
            ->with('success', 'Permanent Valid Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, PermanentValidMember $permanentValidMember)
    {
        $permanentValidMember->delete();

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        
        return redirect()->route('graveyard.permanent-valid-members.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Permanent Valid Member deleted successfully.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $permanentValidMember = PermanentValidMember::onlyTrashed()->findOrFail($id);
        $permanentValidMember->restore();

        return redirect()->route('graveyard.permanent-valid-members.index')
            ->with('success', 'Permanent Valid Member restored successfully.');
    }
}
