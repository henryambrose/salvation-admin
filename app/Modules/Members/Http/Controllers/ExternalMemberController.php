<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Modules\Members\Models\ExternalMember;
use Modules\Members\Models\Gender;
use Modules\Members\Models\Member;
use Modules\Members\Models\Relationship;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

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

        $allowedCommunityIds = $this->allowedCommunityIdsFor(Auth::user());
        if ($allowedCommunityIds !== null) {
            // Use whereExists to check if any member with this family_no is in allowed communities
            $query->whereExists(function ($subquery) use ($allowedCommunityIds) {
                $subquery->select(DB::raw(1))
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
                $father = Member::find($externalMember->father_id);
                if (! $father) {
                    $father = ExternalMember::find($externalMember->father_id);
                }
                $externalMember->father_data = $father ? [
                    'id' => $father->id,
                    'name' => trim($father->first_name . ' ' . $father->last_name),
                    'type' => $father instanceof Member ? 'Member' : 'External',
                ] : null;
            }

            // Load mother data
            if ($externalMember->mother_id) {
                $mother = Member::find($externalMember->mother_id);
                if (! $mother) {
                    $mother = ExternalMember::find($externalMember->mother_id);
                }
                $externalMember->mother_data = $mother ? [
                    'id' => $mother->id,
                    'name' => trim($mother->first_name . ' ' . $mother->last_name),
                    'type' => $mother instanceof Member ? 'Member' : 'External',
                ] : null;
            }

            // Load spouse data
            if ($externalMember->spouse_id) {
                $spouse = Member::find($externalMember->spouse_id);
                if (! $spouse) {
                    $spouse = ExternalMember::find($externalMember->spouse_id);
                }
                $externalMember->spouse_data = $spouse ? [
                    'id' => $spouse->id,
                    'name' => trim($spouse->first_name . ' ' . $spouse->last_name),
                    'type' => $spouse instanceof Member ? 'Member' : 'External',
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
                'lastPage' => $externalMembers->lastPage()
            ],
            'canViewAnyExternalMember' => Gate::allows('list-external-member') ?? false,
            'canCreateExternalMember' => Gate::allows('create-external-member') ?? false,
            'canEditExternalMember' => Gate::allows('update-external-member') ?? false,
            'canDeleteExternalMember' => Gate::allows('delete-external-member') ?? false,
            'canRestoreExternalMember' => Gate::allows('restore-external-member') ?? false,
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
        $validated['family_no'] = Auth::user()->family_no ?? $validated['family_no'];

        $externalMember = ExternalMember::create($validated);
        if ($externalMember->spouse_source == 'Member' && $externalMember->spouse_id) {
            $spouse = Member::find($externalMember->spouse_id);
            if ($spouse) {
                $spouse->spouse_id = $externalMember->id;
                $spouse->spouse_source = 'External';
                $spouse->marital_status = 'Married'; // Married
                $spouse->save();
            }
        }
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
            $father = Member::find($externalMember->father_id);
            $type = 'Member';
        } else {
            $father = ExternalMember::find($externalMember->father_id);
            $type = 'External';
        }
        $externalMember->father_data = $father ? [
            'id' => $father->id,
            'name' => $father->first_name . ' ' . $father->last_name,
            'type' => $type,
        ] : null;

        if ($externalMember->mother_id && $externalMember->mother_source == 'Member') {
            $mother = Member::find($externalMember->mother_id);
            $type = 'Member';
        } else {
            $mother = ExternalMember::find($externalMember->mother_id);
            $type = 'External';
        }
        $externalMember->mother_data = $mother ? [
            'id' => $mother->id,
            'name' => $mother->first_name . ' ' . $mother->last_name,
            'type' => $type,
        ] : null;

        if ($externalMember->spouse_id && $externalMember->spouse_source == 'Member') {
            $spouse = Member::find($externalMember->spouse_id);
            $type = 'Member';
        } else {
            $spouse = ExternalMember::find($externalMember->spouse_id);
            $type = 'External';
        }
        $externalMember->spouse_data = $spouse ? [
            'id' => $spouse->id,
            'name' => $spouse->first_name . ' ' . $spouse->last_name,
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
        if ($externalMember->family_no !== (Auth::user()->family_no ?? $externalMember->family_no)) {
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

        $externalMember->fill($validated);
        $isChanged = false;
        $isChanged = $externalMember->isDirty('spouse_id');
        $externalMember->save();

        Log::info('External member ID ' . $externalMember->spouse_source . '-' . $isChanged);
        if ($isChanged && $externalMember->spouse_source === 'Member') {
            Log::info('Updating spouse relationship for external member ID ' . $externalMember->id);
            $spouse = Member::find($externalMember->spouse_id);
            if ($spouse) {
                $spouse->spouse_id = $externalMember->id;
                $spouse->spouse_source = 'External';
                $spouse->marital_status = 'Married'; // Married
                $spouse->save();
            }
        }
        // Calculate the page where the updated member will be displayed
        $perPage = $request->input('perPage', 15);
        $query = ExternalMember::query()->with(['relationship', 'gender']);

        // Apply the same filters as the index method
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    ->orWhere('family_no', 'like', "%$search%");
            });
        }

        if ($familySearch = $request->input('familySearch')) {
            $query->where('family_no', 'like', "%$familySearch%");
        }

        if ($relationship = $request->input('relationship')) {
            $query->where('relationship_id', $relationship);
        }

        $sort = $request->input('sort', 'first_name');
        $direction = $request->input('direction', 'asc');
        $query->orderBy($sort, $direction);

        $allIds = $query->pluck('id')->toArray();
        $position = array_search($externalMember->id, $allIds);
        $page = $position !== false ? (int) floor($position / $perPage) + 1 : 1;

        return redirect()->route('external-members.index', array_merge(
            $request->only(['search', 'familySearch', 'relationship', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
                'highlightId' => $externalMember->id,
            ]
        ))->with('success', 'External member updated successfully.');
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
     * Search external members (family-scoped)
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $familyNo = Auth::user()->family_no;

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
            Log::error('External members search error: ' . $e->getMessage());

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
                        ->select('first_name', 'last_name')
                        ->limit(3)
                        ->get();

                    return [
                        'family_no' => $family->family_no,
                        'member_count' => $memberCount,
                        'sample_members' => $sampleMembers->map(function ($member) {
                            return trim($member->first_name . ' ' . $member->last_name);
                        })->join(', '),
                    ];
                });

            return response()->json($familyNumbers);
        } catch (\Exception $e) {
            Log::error('Family numbers search error: ' . $e->getMessage());

            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    /**
     * Get external members by family number
     */
    public function getFamilyDetails($familyNo)
    {
        try {
            $externalMembers = ExternalMember::where('family_no', $familyNo)
                ->with(['relationship', 'gender'])
                ->get()
                ->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'first_name' => $member->first_name,
                        'last_name' => $member->last_name,
                        'full_name' => trim($member->first_name . ' ' . $member->last_name),
                        'family_no' => $member->family_no,
                        'relationship' => $member->relationship?->name,
                        'gender' => $member->gender?->name,
                        'source' => 'External',
                    ];
                });

            return response()->json([
                'family_no' => $familyNo,
                'members' => $externalMembers
            ]);
        } catch (\Exception $e) {
            Log::error('External member family details error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch family details'], 500);
        }
    }

    /**
     * Export external members to CSV.
     */
    public function export(Request $request)
    {
        try {
            // Clear any output buffers to prevent extra whitespace
            while (ob_get_level()) {
                ob_end_clean();
            }

            $this->authorize('viewAny', ExternalMember::class);

            $query = ExternalMember::with(['community', 'relationship']);

            // Apply community-scoped filtering like in index method
            $allowedCommunityIds = $this->allowedCommunityIdsFor(Auth::user());
            if ($allowedCommunityIds !== null) {
                // Use whereExists to check if any member with this family_no is in allowed communities
                $query->whereExists(function ($subquery) use ($allowedCommunityIds) {
                    $subquery->select(DB::raw(1))
                        ->from('members')
                        ->whereColumn('members.family_no', 'external_members.family_no')
                        ->whereIn('members.community_id', $allowedCommunityIds);
                });
            }

            if ($request->boolean('isArchived')) {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                        ->orWhere('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('family_no', 'like', "%$search%")
                        ->orWhereHas('community', function ($communityQuery) use ($search) {
                            $communityQuery->where('name', 'like', "%$search%");
                        });
                });
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'first_name', 'last_name', 'family_no', 'community.name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                if ($sort === 'community.name') {
                    $query->join('communities', 'external_members.community_id', '=', 'communities.id')
                        ->orderBy('communities.name', $direction);
                } else {
                    $query->orderBy($sort, $direction);
                }
            } else {
                $query->orderBy('id', 'asc');
            }

            // Streamed CSV keeps memory flat
            return response()->streamDownload(function () use ($query) {
                $out = fopen('php://output', 'w');

                fputcsv($out, [
                    'ID',
                    'First Name',
                    'Last Name',
                    'Family No',
                    'Community',
                    'Relationship',
                    'Contact Number',
                    'Email',
                    'Date of Birth'
                ]);

                foreach ($query->cursor() as $item) {
                    fputcsv($out, [
                        $item->id,
                        $item->first_name ?? '',
                        $item->last_name ?? '',
                        $item->family_no ?? '',
                        $item->community ? $item->community->name : '',
                        $item->relationship ? $item->relationship->name : '',
                        $item->contact_no_1 ?? '',
                        $item->email ?? '',
                        $item->date_of_birth ?? '',
                    ]);
                }

                fclose($out);
            }, 'external_members_' . now()->format('Y-m-d_H-i-s') . '.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache',
            ]);
        } catch (\Exception $e) {
            Log::error('External Member Export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
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
