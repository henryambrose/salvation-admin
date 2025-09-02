<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Http\Requests\StoreValidMemberRequest;
use Modules\Graveyard\Http\Requests\UpdateValidMemberRequest;
use Modules\Graveyard\Models\ValidMember;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ValidMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = ValidMember::query();
        
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

        return Inertia::render('PagesGraveyard/ValidMember/Index', [
            'validMembers' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'perPage', 'isArchived']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('PagesGraveyard/ValidMember/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreValidMemberRequest $request)
    {
        $validatedData = $request->validated();
        
        ValidMember::create($validatedData);

        return redirect()->route('graveyard.valid-members.index')
            ->with('success', 'Valid Member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ValidMember $validMember): Response
    {
        return Inertia::render('graveyard/valid_member/Show', [
            'validMember' => $validMember
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ValidMember $validMember): Response
    {
        return Inertia::render('PagesGraveyard/ValidMember/Edit', [
            'validMember' => $validMember
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValidMemberRequest $request, ValidMember $validMember)
    {
        $validatedData = $request->validated();
        
        $validMember->update($validatedData);

        return redirect()->route('graveyard.valid-members.index')
            ->with('success', 'Valid Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ValidMember $validMember)
    {
        $validMember->delete();

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        
        return redirect()->route('graveyard.valid-members.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Valid Member deleted successfully.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $validMember = ValidMember::onlyTrashed()->findOrFail($id);
        $validMember->restore();

        return redirect()->route('graveyard.valid-members.index')
            ->with('success', 'Valid Member restored successfully.');
    }
}
