<?php

namespace App\Http\Controllers;

use App\Models\ExternalMember;
use App\Models\Relationship;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExternalMemberController extends Controller
{
    /**
     * Display a listing of external members
     */
    public function index(Request $request): Response
    {
        $query = ExternalMember::with(['relationship'])
            ->orderBy('created_at', 'desc');

        // Filter by family number if provided
        if ($familyNo = $request->input('family_no')) {
            $query->where('family_no', 'like', "%{$familyNo}%");
        }

        // Filter by name if provided
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $externalMembers = $query->paginate(15);

        return Inertia::render('external_members/Index', [
            'externalMembers' => $externalMembers,
            'relationships' => Relationship::orderBy('name')->get(),
            'filters' => $request->only(['search', 'family_no'])
        ]);
    }

    /**
     * Show the form for creating a new external member
     */
    public function create(): Response
    {
        return Inertia::render('external_members/Create', [
            'relationships' => Relationship::orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created external member
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'family_no' => 'required|string|max:255',
            'relationship_id' => 'required|exists:relationships,id'
        ]);

        ExternalMember::create($request->all());

        return redirect()->route('external-members.index')
            ->with('success', 'External member created successfully.');
    }

    /**
     * Display the specified external member
     */
    public function show(ExternalMember $externalMember): Response
    {
        $externalMember->load(['relationship', 'familyLinks', 'relatedFamilyLinks']);

        return Inertia::render('external_members/Show', [
            'externalMember' => $externalMember
        ]);
    }

    /**
     * Show the form for editing the specified external member
     */
    public function edit(ExternalMember $externalMember): Response
    {
        return Inertia::render('external_members/Edit', [
            'externalMember' => $externalMember,
            'relationships' => Relationship::orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified external member
     */
    public function update(Request $request, ExternalMember $externalMember)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'family_no' => 'required|string|max:255',
            'relationship_id' => 'required|exists:relationships,id'
        ]);

        $externalMember->update($request->all());

        return redirect()->route('external-members.index')
            ->with('success', 'External member updated successfully.');
    }

    /**
     * Remove the specified external member
     */
    public function destroy(ExternalMember $externalMember)
    {
        $externalMember->delete();

        return redirect()->route('external-members.index')
            ->with('success', 'External member deleted successfully.');
    }

    /**
     * Get external members by family number (for family tree)
     */
    public function getByFamily(Request $request)
    {
        $familyNo = $request->input('family_no');
        
        if (!$familyNo) {
            return response()->json([]);
        }

        $externalMembers = ExternalMember::where('family_no', $familyNo)
            ->with('relationship')
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'full_name' => $member->full_name,
                    'first_name' => $member->first_name,
                    'last_name' => $member->last_name,
                    'address' => $member->address,
                    'family_no' => $member->family_no,
                    'relationship' => $member->relationship,
                    'is_external' => true
                ];
            });

        return response()->json($externalMembers);
    }
}
