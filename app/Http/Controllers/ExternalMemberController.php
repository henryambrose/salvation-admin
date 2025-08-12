<?php

namespace App\Http\Controllers;

use App\Models\ExternalMember;
use App\Models\Gender;
use App\Models\Member;
use App\Models\Relationship;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExternalMemberController extends Controller
{
    /**
     * Display a listing of external members (family-scoped)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ExternalMember::class);
        $query = ExternalMember::query()->with(['relationship', 'gender']);

        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        $allowedCommunityIds = $this->allowedCommunityIdsFor(auth()->user());
        if ($allowedCommunityIds !== null) {
            // Use whereExists to check if any member with this family_no is in allowed communities
            $query->whereExists(function ($subquery) use ($allowedCommunityIds) {
                $subquery->select(\DB::raw(1))
                    ->from('members')
                    ->whereColumn('members.family_no', 'external_members.family_no')
                    ->whereIn('members.community_id', $allowedCommunityIds);
            });
        }

        // Handle search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    ->orWhere('family_no', 'like', "%$search%");
            });
        }

        // Handle family search
        if ($familySearch = $request->input('familySearch')) {
            $query->where('family_no', 'like', "%$familySearch%");
        }

        // Handle relationship filter
        if ($relationship = $request->input('relationship')) {
            $query->where('relationship_id', $relationship);
        }

        // Handle sorting
        $sort = $request->input('sort', 'first_name');
        $direction = $request->input('direction', 'asc');
        $query->orderBy($sort, $direction);

        // Handle pagination
        $perPage = $request->input('perPage', 15);
        $externalMembers = $query->paginate($perPage);

        // Manually load relationship data for each external member
        $externalMembers->getCollection()->transform(function ($externalMember) {
            // Load father data
            if ($externalMember->father_id) {
                $father = \App\Models\Member::find($externalMember->father_id);
                if (! $father) {
                    $father = ExternalMember::find($externalMember->father_id);
                }
                $externalMember->father_data = $father ? [
                    'id' => $father->id,
                    'name' => trim($father->first_name.' '.$father->last_name),
                    'type' => $father instanceof \App\Models\Member ? 'Member' : 'External',
                ] : null;
            }

            // Load mother data
            if ($externalMember->mother_id) {
                $mother = \App\Models\Member::find($externalMember->mother_id);
                if (! $mother) {
                    $mother = ExternalMember::find($externalMember->mother_id);
                }
                $externalMember->mother_data = $mother ? [
                    'id' => $mother->id,
                    'name' => trim($mother->first_name.' '.$mother->last_name),
                    'type' => $mother instanceof \App\Models\Member ? 'Member' : 'External',
                ] : null;
            }

            // Load spouse data
            if ($externalMember->spouse_id) {
                $spouse = \App\Models\Member::find($externalMember->spouse_id);
                if (! $spouse) {
                    $spouse = ExternalMember::find($externalMember->spouse_id);
                }
                $externalMember->spouse_data = $spouse ? [
                    'id' => $spouse->id,
                    'name' => trim($spouse->first_name.' '.$spouse->last_name),
                    'type' => $spouse instanceof \App\Models\Member ? 'Member' : 'External',
                ] : null;
            }

            return $externalMember;
        });

        // Get relationships for filter dropdown
        $relationships = Relationship::all();

        return Inertia::render('ExternalMembers/Index', [
            'externalMembers' => $externalMembers,
            'relationships' => $relationships,
            'totalCount' => $externalMembers->total(),
            'fetchUrl' => route('external-members.index'),
            'filters' => $request->only(['search', 'familySearch', 'sort', 'direction', 'perPage', 'relationship', 'isArchived']),
            'pagination' => [
                'currentPage' => $externalMembers->currentPage(),
                'lastPage' => $externalMembers->lastPage(),
            ],
            'canViewAnyExternalMember' => auth()->user()->can('list-external-member'),
            'canCreateExternalMember' => auth()->user()->can('create-external-member'),
            'canEditExternalMember' => auth()->user()->can('update-external-member'),
            'canDeleteExternalMember' => auth()->user()->can('delete-external-member'),
            'canRestoreExternalMember' => auth()->user()->can('restore-external-member'),
        ]);
    }

    /**
     * Show the form for creating a new external member
     */
    public function create()
    {
        $this->authorize('create', ExternalMember::class);

        $genders = Gender::all();
        $relationships = Relationship::all();

        return Inertia::render('ExternalMembers/Create', [
            'genders' => $genders,
            'relationships' => $relationships,
        ]);
    }

    /**
     * Store a newly created external member
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'gender_id' => 'required|exists:genders,id',
            'family_no' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'father_id' => 'nullable|integer',
            'mother_id' => 'nullable|integer',
            'spouse_id' => 'nullable|integer',
            'father_source' => 'nullable|string|max:255',
            'mother_source' => 'nullable|string|max:255',
            'spouse_source' => 'nullable|string|max:255',
            'relationship_id' => 'required|exists:relationships,id',
        ]);

        // Use the provided family_no if user doesn't have one set
        $validated['family_no'] = auth()->user()->family_no ?? $validated['family_no'];

        $externalMember = ExternalMember::create($validated);

        return redirect()->route('external-members.index')
            ->with('success', 'External member created successfully.');
    }

    /**
     * Display the specified external member
     */
    public function show(ExternalMember $externalMember)
    {
        $this->authorize('view', $externalMember);

        return Inertia::render('ExternalMembers/Show', [
            'externalMember' => $externalMember->load(['relationship', 'father', 'mother', 'spouse']),
        ]);
    }

    /**
     * Show the form for editing the specified external member
     */
    public function edit(ExternalMember $externalMember)
    {
        $this->authorize('update', $externalMember);


        // Load relationship data for the external member
        $externalMember->load(['relationship', 'gender']);
        $type = 'External';
        // Manually load father, mother, and spouse data with type information
        if ($externalMember->father_id && $externalMember->father_source == 'Member') {
            $father = \App\Models\Member::find($externalMember->father_id);
            $type = 'Member';
        } else {
            $father = ExternalMember::find($externalMember->father_id);
            $type = 'External';
        }
        $externalMember->father_data = $father ? [
            'id' => $father->id,
            'name' => $father->first_name.' '.$father->last_name,
            'type' => $type,
        ] : null;

        if ($externalMember->mother_id && $externalMember->mother_source == 'Member') {
            $mother = \App\Models\Member::find($externalMember->mother_id);
            $type = 'Member';
        } else {
            $mother = ExternalMember::find($externalMember->mother_id);
            $type = 'External';
        }
        $externalMember->mother_data = $mother ? [
            'id' => $mother->id,
            'name' => $mother->first_name.' '.$mother->last_name,
            'type' => $type,
        ] : null;

        if ($externalMember->spouse_id && $externalMember->spouse_source == 'Member') {
            $spouse = \App\Models\Member::find($externalMember->spouse_id);
            $type = 'Member';
        } else {
            $spouse = ExternalMember::find($externalMember->spouse_id);
            $type = 'External';
        }
        $externalMember->spouse_data = $spouse ? [
            'id' => $spouse->id,
            'name' => $spouse->first_name.' '.$spouse->last_name,
            'type' => $type,
        ] : null;

        $genders = Gender::all();
        $relationships = Relationship::all();

        return Inertia::render('ExternalMembers/Edit', [
            'externalMember' => $externalMember,
            'genders' => $genders,
            'relationships' => $relationships,
        ]);
    }

    /**
     * Update the specified external member
     */
    public function update(Request $request, ExternalMember $externalMember)
    {
        // Ensure family-scoped access
        if ($externalMember->family_no !== (auth()->user()->family_no ?? $externalMember->family_no)) {
            abort(403, 'Unauthorized access to external member.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'gender_id' => 'required|exists:genders,id',
            'family_no' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'father_id' => 'nullable|integer',
            'mother_id' => 'nullable|integer',
            'spouse_id' => 'nullable|integer',
            'father_source' => 'nullable|string|max:255',
            'mother_source' => 'nullable|string|max:255',
            'spouse_source' => 'nullable|string|max:255',
            'relationship_id' => 'required|exists:relationships,id',
        ]);

        $externalMember->update($validated);

        return redirect()->route('external-members.index')
            ->with('success', 'External member updated successfully.');
    }

    /**
     * Remove the specified external member from storage.
     */
    public function destroy(Request $request, ExternalMember $externalMember)
    {
        $this->authorize('delete', $externalMember);

        try {
            $externalMember->delete(); // This will now be a soft delete

            // Preserve current state after deletion
            $page = $request->input('page', 1);
            $perPage = $request->input('perPage', 15);
            return redirect()->route('external-members.index', array_merge(
                $request->only(['search', 'familySearch', 'relationship', 'sort', 'direction', 'isArchived']),
                [
                    'page' => $page,
                    'perPage' => $perPage,
                ]
            ))->with('success', 'External member deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('external-members.index')
                ->with('error', 'Failed to delete external member.');
        }
    }

    /**
     * Restore a soft-deleted external member.
     */
    public function restore($id)
    {
        $externalMember = ExternalMember::withTrashed()->findOrFail($id);
        $this->authorize('restore', $externalMember);

        try {
            $externalMember->restore();

            return redirect()->route('external-members.index')
                ->with('success', 'External member restored successfully.');
        } catch (\Exception $e) {
            return redirect()->route('external-members.index')
                ->with('error', 'Failed to restore external member.');
        }
    }

    /**
     * Permanently delete a soft-deleted external member.
     */
    public function forceDelete($id)
    {
        $externalMember = ExternalMember::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $externalMember);

        try {
            $externalMember->forceDelete();

            return redirect()->route('external-members.index')
                ->with('success', 'External member permanently deleted.');
        } catch (\Exception $e) {
            return redirect()->route('external-members.index')
                ->with('error', 'Failed to permanently delete external member.');
        }
    }

    /**
     * Search external members (family-scoped)
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $familyNo = auth()->user()->family_no;

        $externalMembers = ExternalMember::where('family_no', $familyNo)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('family_no', 'like', "%{$query}%")
                    ->orWhere('member_no', 'like', "%{$query}%");
            })
            ->with(['relationship'])
            ->limit(10)
            ->get();

        return response()->json($externalMembers->map(function ($member) {
            return [
                'id' => $member->id,
                'full_name' => $member->full_name,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'family_no' => $member->family_no,
                'address' => $member->address,
                'relationship' => $member->relationship?->name,
                'type' => 'External',
            ];
        }));
    }

    /**
     * Search all external members (for relationship selection)
     */
    public function searchAll(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $familyNo = $request->get('familyNo'); // Add this parameter

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $externalMembers = ExternalMember::where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('family_no', 'like', "%{$query}%");
            });

            // Apply family number filter if provided
            if ($familyNo) {
                $externalMembers = $externalMembers->where('family_no', $familyNo);
            }

            $externalMembers = $externalMembers->with(['relationship'])
                ->limit(10)
                ->get();

            return response()->json($externalMembers->map(function ($member) {
                return [
                    'id' => $member->id,
                    'full_name' => $member->full_name,
                    'first_name' => $member->first_name,
                    'last_name' => $member->last_name,
                    'family_no' => $member->family_no,
                    'address' => $member->address,
                    'relationship' => $member->relationship?->name,
                    'type' => 'External',
                ];
            }));

        } catch (\Exception $e) {
            \Log::error('External members search error: '.$e->getMessage());

            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    public function searchFamilyNumbers(Request $request)
    {
        try {
            $query = $request->get('query', '');

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            // Simple search for family numbers
            $familyNumbers = Member::select('family_no')
                ->where('family_no', 'like', "%{$query}%")
                ->groupBy('family_no')
                ->limit(10)
                ->get()
                ->map(function ($family) {
                    // Count members in this family
                    $memberCount = Member::where('family_no', $family->family_no)->count();

                    // Get sample member names for display
                    $sampleMembers = Member::where('family_no', $family->family_no)
                        ->limit(3)
                        ->get()
                        ->map(fn ($member) => $member->first_name.' '.$member->last_name)
                        ->join(', ');

                    return [
                        'family_no' => $family->family_no,
                        'member_count' => $memberCount,
                        'sample_members' => $sampleMembers,
                    ];
                });

            return response()->json($familyNumbers);

        } catch (\Exception $e) {
            \Log::error('Family numbers search error: '.$e->getMessage());

            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    /**
     * Get external member details for API
     */
    public function getDetails($id)
    {
        $externalMember = ExternalMember::findOrFail($id);

        return response()->json([
            'id' => $externalMember->id,
            'first_name' => $externalMember->first_name,
            'last_name' => $externalMember->last_name,
            'family_no' => $externalMember->family_no,
            'address' => $externalMember->address,
            'type' => 'External',
        ]);
    }
}
