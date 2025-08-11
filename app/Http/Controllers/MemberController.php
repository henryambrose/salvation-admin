<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\AgeGroup;
use App\Models\AuditLog;
use App\Models\BloodGroup;
use App\Models\City;
use App\Models\Community;
use App\Models\CommunityCluster;
use App\Models\Country;
use App\Models\Designation;
use App\Models\ExternalMember;
use App\Models\Gender;
use App\Models\IncomeRange;
use App\Models\Member;
use App\Models\Parish;
use App\Models\Relationship;
use App\Models\State;
use App\Models\Status;
use App\Models\Town;
use App\Models\UnifiedPerson;
use App\Services\FamilyTreeService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Member::class);

        $dropdownColumns = [
            'community_id' => ['relation' => 'community', 'column' => 'name'],
            'community_cluster_id' => ['relation' => 'communityCluster.cluster', 'column' => 'name'],
            'relationship_id' => ['relation' => 'relationship', 'column' => 'name'],
            'blood_group_id' => ['relation' => 'bloodGroup', 'column' => 'name'],
            'income_range_id' => ['relation' => 'incomeRange', 'column' => 'name'],
            'designation_id' => ['relation' => 'designation', 'column' => 'name'],
            'gender_id' => ['relation' => 'gender', 'column' => 'name'],
            'status_id' => ['relation' => 'status', 'column' => 'name'],
            'permanent_town_id' => ['relation' => 'permanentTown', 'column' => 'name'],
            'permanent_city_id' => ['relation' => 'permanentCity', 'column' => 'name'],
            'permanent_state_id' => ['relation' => 'permanentState', 'column' => 'name'],
            'permanent_country_id' => ['relation' => 'permanentCountry', 'column' => 'name'],
            'current_town_id' => ['relation' => 'currentTown', 'column' => 'name'],
            'current_city_id' => ['relation' => 'currentCity', 'column' => 'name'],
            'current_state_id' => ['relation' => 'currentState', 'column' => 'name'],
            'current_country_id' => ['relation' => 'currentCountry', 'column' => 'name'],
        ];

        $query = Member::query();

        // Handle archived records
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Load relationships
        $query->with([
            'community',
            'communityCluster.cluster',
            'relationship',
            'bloodGroup',
            'designation',
            'incomeRange',
            'status',
            'gender',
            'baptismParish',
            'confirmationParish',
            'marriageParish',
            'deathParish',
            'permanentTown',
            'permanentCity',
            'permanentState',
            'permanentCountry',
            'currentTown',
            'currentCity',
            'currentState',
            'currentCountry',
            'cellsAndAssociations',
            'sccHeads.community',
            'ppcHeads.community',
            'clusterHeads.community',
            'clusterHeads.cluster',
        ]);

        // Restrict by allowed communities for PPC/SCC heads
        $query->forUserCommunities(auth()->user());

        // Enhanced search logic
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    ->orWhere('family_no', 'like', "%$search%")
                    ->orWhereHas('community', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    });

            });
        }

        // Family-specific search logic (server-side)
        if ($familySearch = $request->input('familySearch')) {
            $query->where('family_no', 'like', "%$familySearch%");
        }

        // Community filter
        if ($communityId = $request->input('communityId')) {
            $query->where('community_id', $communityId);
        }

        // Relationship filter
        if ($relationship = $request->input('relationship')) {
            $query->where('relationship_id', $relationship);
        }

        // Age group filter - filter by calculated age based on min and max age from age group
        if ($ageGroup = $request->input('ageGroup')) {
            $ageGroupModel = \App\Models\AgeGroup::find($ageGroup);

            if ($ageGroupModel) {
                $minAge = $ageGroupModel->min_age;
                $maxAge = $ageGroupModel->max_age;

                $query->whereRaw('
                    TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN ? AND ?
                ', [$minAge, $maxAge]);
            }

        }

        // Blood group filter
        if ($bloodGroup = $request->input('bloodGroup')) {
            $query->where('blood_group_id', $bloodGroup);
        }

        // Gender filter
        if ($gender = $request->input('gender')) {
            $query->where('gender_id', $gender);
        }

        // Legacy filter support (keeping for backward compatibility)
        if ($filterColumnKey = $request->input('filterColumnKey')) {
            $filterColumnValue = $request->input('filterColumnValue');
            if ($filterColumnKey && $filterColumnValue) {
                if (in_array($filterColumnKey, array_keys($dropdownColumns))) {
                    $relation = $dropdownColumns[$filterColumnKey]['relation'];
                    $filterColumnKeyName = $dropdownColumns[$filterColumnKey]['column'];
                    $query->whereHas($relation, function ($q) use ($filterColumnKeyName, $filterColumnValue) {
                        $q->where($filterColumnKeyName, 'like', "%$filterColumnValue%");
                    });
                }
            } elseif ($filterColumnValue) {
                $query->where($filterColumnKey, 'like', "%$filterColumnValue%");
            }
        }

        // Sorting
        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        // Apply PPC/SCC community scoping
        $allowedCommunityIds = $this->allowedCommunityIdsFor(auth()->user());
        if ($allowedCommunityIds !== null) {
            $query->whereIn('community_id', $allowedCommunityIds);
        }

        $perPage = $request->input('perPage', 10);

        // Normal pagination
        $totalCount = $query->count();
        $data = $query->paginate($perPage)->appends($request->query());
        $data->getCollection()->transform(function ($item) use ($dropdownColumns) {
            foreach ($dropdownColumns as $key => $relation) {
                $relationPath = explode('.', $relation['relation']);
                $currentRelation = $item;
                $found = true;

                foreach ($relationPath as $path) {
                    if (isset($currentRelation->{$path}) && $currentRelation->{$path}) {
                        $currentRelation = $currentRelation->{$path};
                    } else {
                        $found = false;
                        break;
                    }
                }

                if ($found) {
                    $item->$key = $currentRelation->{$relation['column']} ?? '';
                } else {
                    // For debugging, let's see what's happening with community_cluster_id
                    if ($key === 'community_cluster_id' && $item->community_cluster_id) {
                        \Log::info("Member {$item->id} has community_cluster_id: {$item->community_cluster_id} but no relationship loaded");
                    }
                    $item->$key = '';
                }
            }

            return $item;
        });

        // Calculate total family statistics (for all data, not just current page)
        $totalStatsQuery = Member::query();

        // Apply the same filters as the main query
        if ($request->input('isArchived') === 'true') {
            $totalStatsQuery->onlyTrashed();
        } else {
            $totalStatsQuery->withoutTrashed();
        }

        // Apply PPC/SCC community scoping to stats query
        if ($allowedCommunityIds !== null) {
            $totalStatsQuery->whereIn('community_id', $allowedCommunityIds);
        }

        if ($search = $request->input('search')) {
            $totalStatsQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    ->orWhere('family_no', 'like', "%$search%")
                    ->orWhereHas('community', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    });
            });
        }

        if ($familySearch = $request->input('familySearch')) {
            $totalStatsQuery->where('family_no', 'like', "%$familySearch%");
        }

        if ($communityId = $request->input('communityId')) {
            $totalStatsQuery->where('community_id', $communityId);
        }

        if ($relationship = $request->input('relationship')) {
            $totalStatsQuery->where('relationship_id', $relationship);
        }

        if ($ageGroup = $request->input('ageGroup')) {
            $ageGroupModel = \App\Models\AgeGroup::find($ageGroup);
            if ($ageGroupModel) {
                $minAge = $ageGroupModel->min_age;
                $maxAge = $ageGroupModel->max_age;
                $totalStatsQuery->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN ? AND ?', [$minAge, $maxAge]);
            }
        }

        if ($bloodGroup = $request->input('bloodGroup')) {
            $totalStatsQuery->where('blood_group_id', $bloodGroup);
        }

        if ($gender = $request->input('gender')) {
            $totalStatsQuery->where('gender_id', $gender);
        }

        // Apply community scope to totals as well
        $totalStatsQuery->forUserCommunities(auth()->user());

        // Get total statistics
        $totalMembers = $totalStatsQuery->count();
        $totalFamilies = $totalStatsQuery->distinct()->whereNotNull('family_no')->count('family_no');
        $averageMembersPerFamily = $totalFamilies > 0 ? round($totalMembers / $totalFamilies, 1) : 0;

        $familyStats = [
            'totalMembers' => $totalMembers,
            'totalFamilies' => $totalFamilies,
            'averageMembersPerFamily' => $averageMembersPerFamily,
        ];

        return Inertia::render('member/Index', [
            'communities' => Community::all(),
            'relationships' => Relationship::all(),
            'ageGroups' => AgeGroup::all(),
            'bloodGroups' => BloodGroup::all(),
            'genders' => Gender::all(),
            'parishes' => Parish::all(),
            'communityClusters' => CommunityCluster::with('cluster')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->cluster->name ?? 'Unknown Cluster', 'community_id' => $item->community_id];
            })->toArray(),
            'fetchUrl' => route('member.index'),
            'members' => $data,
            'totalCount' => $totalCount,
            'familyStats' => $familyStats,
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'communityId', 'relationship', 'ageGroup', 'bloodGroup', 'gender', 'filterColumnKey', 'filterColumnValue', 'isArchived']),
            'canViewAnyMember' => auth()->user()->can('list-member'),
            'canCreateMember' => auth()->user()->can('create-member'),
            'canEditMember' => auth()->user()->can('update-member'),
            'canDeleteMember' => auth()->user()->can('delete-member'),
            'canRestoreMember' => auth()->user()->can('restore-member'),
            'pagination' => [
                'currentPage' => $query->paginate($perPage)->currentPage(),
                'lastPage' => $query->paginate($perPage)->lastPage(),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Member::class);

        return Inertia::render('member/Member', [
            'communities' => Community::all(),
            'incomeRanges' => IncomeRange::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            }),
            'parishes' => Parish::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            }),
            'bloodGroups' => BloodGroup::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'relationships' => Relationship::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'countries' => Country::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'states' => State::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name, 'country_id' => $item->country_id];
            })->toArray(),
            'cities' => City::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'towns' => Town::with('city.state.country')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'pincode' => $item->pincode,
                    'city_id' => $item->city_id,
                    'state_id' => $item->city->state_id ?? null,
                    'country_id' => $item->city->state->country_id ?? null,
                ];
            })->toArray(),
            'designations' => Designation::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'genders' => Gender::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'statuses' => Status::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'parishes' => Parish::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'communityClusters' => CommunityCluster::with('cluster')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->cluster->name ?? 'Unknown Cluster', 'community_id' => $item->community_id];
            })->toArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $this->authorize('create', Member::class);

        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Set default values for family numbering
            $data['church_code'] = $data['church_code'] ?? config('app.church_code', 'SAL');
            $data['registration_year'] = $data['registration_year'] ?? date('Y');
            $data['marital_status'] = $data['marital_status'] ?? 'single';

            // Check if this is a new family or existing family
            if ($request->has('existing_family_no') && $request->existing_family_no && ! empty($request->existing_family_no)) {
                $familyNo = $request->existing_family_no;

                $numberingService = new \App\Services\FamilyNumberingService;

                // Validate existing family number
                if (! $numberingService->validateFamilyNumber($familyNo)) {
                    throw new \Exception('Invalid family number format. Expected: SAL-XXX');
                }

                // Check if family actually exists in database
                $existingFamily = Member::where('family_no', $familyNo)->first();
                if (! $existingFamily) {
                    throw new \Exception('Family number "'.$familyNo.'" does not exist. Please search for existing families or create a new family.');
                }

                $familyInfo = $numberingService->parseFamilyNumber($familyNo);
                $data['family_no'] = $familyNo;
                $data['family_sequence'] = $familyInfo['family_group'];
                $data['church_code'] = $familyInfo['church_code'];

                // Generate member number for existing family
                $data['member_no'] = $numberingService->generateMemberNumber();

            } else {
                // New family - numbers will be auto-generated in model
                $data['registration_year'] = date('Y');
            }

            $member = Member::create($data);

            // Create audit log for the creation
            AuditLog::create([
                'table_name' => 'members',
                'action' => 'CREATE',
                'record_id' => $member->id,
                'new_values' => $member->toArray(),
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            $perPage = $request->input('perPage', 10);
            // Build the query as in index
            $query = Member::query();
            if ($search = $request->input('search')) {
                $query->where('first_name', 'like', "%$search%");
                // Add other filters as needed
            }
            if ($sort = $request->input('sort')) {
                $query->orderBy($sort, $request->input('direction', 'asc'));
            } else {
                $query->orderBy('id', 'asc');
            }
            $allIds = $query->pluck('id')->toArray();
            $position = array_search($member->id, $allIds);
            $page = $position !== false ? (int) floor($position / $perPage) + 1 : 1;

            return redirect()->route('member.index', array_merge(
                $request->only(['search', 'sort', 'direction', 'isArchived', 'communityId', 'filterColumnKey', 'filterColumnValue']),
                [
                    'page' => $page,
                    'perPage' => $perPage,
                    'highlightId' => $member->id,
                ]
            ))->with('success', 'Member created successfully with Family No: '.$member->family_no);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Member $member): Response
    {
        $this->authorize('view', $member);

        return Inertia::render('member/Member', [
            'member' => $member,
        ]);
    }

    public function edit(Member $member)
    {
        $this->authorize('update', $member);

        $incomeRanges = IncomeRange::select('id', 'name')->get()->map(function ($item) {
            return ['id' => $item->id, 'name' => $item->name];
        })->toArray();

        return Inertia::render('member/Member', [
            'member' => $member,
            'members' => Member::select('id', 'first_name', 'last_name', 'family_no')->with('spouse')->get(), // Only select needed columns
            'communities' => Community::select('id', 'name')->get(), // Only select needed columns
            'parishes' => Parish::select('id', 'name')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            }),
            'incomeRanges' => $incomeRanges,
            'communityClusters' => CommunityCluster::select('id', 'community_id')->with('cluster')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->cluster->name ?? 'Unknown Cluster', 'community_id' => $item->community_id];
            })->toArray(),
            'bloodGroups' => BloodGroup::select('id', 'name')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'relationships' => Relationship::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'countries' => Country::select('id', 'name')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'states' => State::select('id', 'name', 'country_id')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name, 'country_id' => $item->country_id];
            })->toArray(),
            'cities' => City::select('id', 'name')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'towns' => Town::select('id', 'name', 'pincode', 'city_id')->with('city.state.country')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'pincode' => $item->pincode,
                    'city_id' => $item->city_id,
                    'state_id' => $item->city->state_id ?? null,
                    'country_id' => $item->city->state->country_id ?? null,
                ];
            })->toArray(),
            'designations' => Designation::select('id', 'name')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'genders' => Gender::select('id', 'name')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'statuses' => Status::select('id', 'name')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'parishes' => Parish::select('id', 'name')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            }),
        ]);
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        $this->authorize('update', $member);

        // Capture old values before update
        $oldValues = $member->toArray();

        $validated = $request->validated();
        $member->update($validated);

        // Create audit log for the update
        AuditLog::create([
            'table_name' => 'members',
            'action' => 'UPDATE',
            'record_id' => $member->id,
            'old_values' => $oldValues,
            'new_values' => $member->fresh()->toArray(),
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $perPage = $request->input('perPage', 10);
        // Build the query as in index
        $query = Member::query();
        if ($search = $request->input('search')) {
            $query->where('first_name', 'like', "%$search%");
            // Add other filters as needed
        }
        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }
        $allIds = $query->pluck('id')->toArray();
        $position = array_search($member->id, $allIds);
        $page = $position !== false ? (int) floor($position / $perPage) + 1 : 1;

        return redirect()->route('member.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived', 'communityId', 'filterColumnKey', 'filterColumnValue']),
            [
                'page' => $page,
                'perPage' => $perPage,
                'highlightId' => $member->id,
            ]
        ))->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $this->authorize('delete', $member);

        // Capture member data before deletion
        $memberData = $member->toArray();

        $member->delete();

        // Create audit log for the deletion
        AuditLog::create([
            'table_name' => 'members',
            'action' => 'DELETE',
            'record_id' => $memberData['id'],
            'old_values' => $memberData,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('member.index')->with('success', 'Member deleted successfully.');
    }

    public function restore($id)
    {
        $member = Member::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $member);

        $member->restore();

        return redirect()->route('member.index')->with('success', 'Member restored successfully.');
    }

    public function showFamilyTree($id, $type)
    {
        if ($type == 'internal') {
            // Remove the non-existent 'relationships' relationship
            $member = Member::with(['gender', 'community', 'relationship'])->findOrFail($id);
            $person = UnifiedPerson::where('uid', '=', 'M-'.$id)->first();
        } else {
            $member = ExternalMember::with(['gender', 'relationship'])->findOrFail($id);
            $person = UnifiedPerson::where('uid', '=', 'E-'.$id)->first();
        }
        $service = new FamilyTreeService;
        $allFamilyMembers = UnifiedPerson::where('family_no', $person->family_no)
            ->where('uid', '!=', $person->uid)
            ->get();

        if (! $allFamilyMembers) {
            return redirect()->route('member.index')->with('error', 'Member not found');
        }
        foreach ($allFamilyMembers as $familyMember) {
            $relation = $service->calculateRelationship($person, $familyMember);
            $familyMember->relation = $relation;
        }

        return Inertia::render('member/FamilyTree', [
            'member' => $member,
            'person' => $person,
            'familyTree' => $allFamilyMembers,
            'relationships' => $service->getAvailableRelationships(),
        ]);
    }

    /**
     * Get family tree data via API
     */
    public function getFamilyTreeData($id)
    {
        $member = Member::findOrFail($id);
        $service = new FamilyTreeService;
        $person = UnifiedPerson::where('uid', 'M-'.$member->id)->first();

        return response()->json($person ? $service->getFamilyTree($person) : []);
    }

    /**
     * Search members for family tree
     */
    public function searchFamilyMembers(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 10);

        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $familyTreeService = new FamilyTreeService;
        $members = $familyTreeService->searchMembers($query, $limit);

        return response()->json($members);
    }

    /**
     * Add relationship between members
     */
    public function addFamilyRelationship(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'related_member_id' => 'nullable|exists:members,id',
            'related_external_member_id' => 'nullable|exists:external_members,id',
            'relationship_id' => 'required|exists:relationships,id',
        ]);

        $familyTreeService = new FamilyTreeService;

        // Check if we're adding a relationship with an external member
        if ($request->has('related_external_member_id') && $request->related_external_member_id) {
            $success = $familyTreeService->addExternalRelationship(
                $request->member_id,
                $request->related_external_member_id,
                $request->relationship_id
            );
        } else {
            $success = $familyTreeService->addRelationship(
                $request->member_id,
                $request->related_member_id,
                $request->relationship_id
            );
        }

        if (! $success) {
            return response()->json(['error' => 'Relationship already exists'], 400);
        }

        return response()->json(['message' => 'Relationship added successfully']);
    }

    /**
     * Remove relationship between members
     */
    public function removeFamilyRelationship(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'related_member_id' => 'required|exists:members,id',
        ]);

        $familyTreeService = new FamilyTreeService;
        $success = $familyTreeService->removeRelationship(
            $request->member_id,
            $request->related_member_id
        );

        if (! $success) {
            return response()->json(['error' => 'Relationship not found'], 404);
        }

        return response()->json(['message' => 'Relationship removed successfully']);
    }

    public function searchOptions(Request $request)
    {
        $search = $request->input('search', '');

        $members = Member::query()
            ->select('id', 'first_name', 'last_name')
            ->when($search, function ($query, $search) {
                $query->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%");
            })

            ->orderBy('first_name', 'asc')
            ->limit(10)
            ->get();

        $members = $members->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->first_name.' '.$member->last_name,
            ];
        });

        return response()->json($members);
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Member::class);

        try {
            $query = Member::query();

            // Handle archived records
            if ($request->input('isArchived') === 'true') {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            // Load relationships
            $query->with([
                'community',
                'communityCluster',
                'relationship',
                'bloodGroup',
                'designation',
                'incomeRange',
                'status',
                'gender',
            ]);

            // Enhanced search logic
            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                        ->orWhere('family_no', 'like', "%$search%")
                        ->orWhereHas('community', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%");
                        });
                });
            }

            // Family-specific search logic
            if ($familySearch = $request->input('familySearch')) {
                $query->where('family_no', 'like', "%$familySearch%");
            }

            // Community filter
            if ($communityId = $request->input('communityId')) {
                $query->where('community_id', $communityId);
            }

            // Relationship filter
            if ($relationship = $request->input('relationship')) {
                $query->where('relationship_id', $relationship);
            }

            // Age group filter
            if ($ageGroup = $request->input('ageGroup')) {
                $ageGroupModel = \App\Models\AgeGroup::find($ageGroup);
                if ($ageGroupModel) {
                    $minAge = $ageGroupModel->min_age;
                    $maxAge = $ageGroupModel->max_age;
                    $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN ? AND ?', [$minAge, $maxAge]);
                }
            }

            // Blood group filter
            if ($bloodGroup = $request->input('bloodGroup')) {
                $query->where('blood_group_id', $bloodGroup);
            }

            // Gender filter
            if ($gender = $request->input('gender')) {
                $query->where('gender_id', $gender);
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'first_name', 'last_name', 'family_no', 'member_no', 'date_of_birth'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                $query->orderBy($sort, $direction);
            } else {
                $query->orderBy('id', 'asc');
            }

            $data = $query->get();

            // Debug logging
            \Log::info('Member Export - Data count: '.$data->count());
            \Log::info('Member Export - Query SQL: '.$query->toSql());
            \Log::info('Member Export - Query bindings: '.json_encode($query->getBindings()));

            // Transform data for export
            $exportData = [];
            foreach ($data as $item) {
                // Calculate age
                $age = '';
                if ($item->date_of_birth) {
                    $birthDate = new \DateTime($item->date_of_birth);
                    $today = new \DateTime;
                    $age = $today->diff($birthDate)->y;
                }

                $exportData[] = [
                    'ID' => $item->id,
                    'First Name' => $item->first_name ?? '',
                    'Last Name' => $item->last_name ?? '',
                    'Family No' => $item->family_no ?? '',
                    'Member No' => $item->member_no ?? '',
                    'Contact No' => $item->contact_no_1 ?? '',
                    'Email' => $item->email ?? '',
                    'Date of Birth' => $item->date_of_birth ?? '',
                    'Age' => $age,
                    'Community' => $item->community ? $item->community->name : '',
                    'Cluster' => $item->communityCluster ? $item->communityCluster->name : '',
                    'Relationship' => $item->relationship ? $item->relationship->name : '',
                    'Blood Group' => $item->bloodGroup ? $item->bloodGroup->name : '',
                    'Gender' => $item->gender ? $item->gender->name : '',
                    'Status' => $item->status ? $item->status->name : '',
                ];
            }

            // Debug logging
            \Log::info('Member Export - Export data count: '.count($exportData));
            if (count($exportData) > 0) {
                \Log::info('Member Export - First row sample: '.json_encode($exportData[0]));
            } else {
                \Log::warning('Member Export - No data to export!');

                return response()->json(['error' => 'No data found to export'], 404);
            }

            // Create Excel file
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            if (count($exportData) > 0) {
                $headers = array_keys($exportData[0]);
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($col.'1', $header);
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                    $col++;
                }

                // Set data
                $row = 2;
                foreach ($exportData as $rowData) {
                    $col = 'A';
                    foreach ($rowData as $value) {
                        $sheet->setCellValue($col.$row, $value);
                        $col++;
                    }
                    $row++;
                }

                // Style header row
                $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')->getFont()->setBold(true);
            }

            // Create writer and output
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'members_'.date('Y-m-d_H-i-s').'.xlsx';

            // Save to temporary file and return as download
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);

            \Log::info('Member Export - File created: '.$tempFile.', Size: '.filesize($tempFile));

            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend();

        } catch (\Exception $e) {
            \Log::error('Member Export failed: '.$e->getMessage());

            return response()->json(['error' => 'Export failed: '.$e->getMessage()], 500);
        }
    }

    public function getMembersByCommunity($communityId)
    {
        $members = Member::where('community_id', $communityId)
            ->select('id', 'first_name', 'last_name', 'community_id')
            ->selectRaw('CONCAT(first_name, " ", last_name) as name')
            ->get();

        return response()->json($members);
    }

    public function getMembersByFamily($familyNo)
    {
        $members = Member::where('family_no', $familyNo)
            ->with('relationship')
            ->select('id', 'first_name', 'last_name', 'date_of_birth', 'relationship_id')
            ->get();

        return response()->json($members);
    }

    public function moveFamily(Request $request, $familyNo)
    {
        $request->validate([
            'new_community_id' => 'required|exists:communities,id',
            'move_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        $numberingService = new \App\Services\FamilyNumberingService;

        DB::beginTransaction();

        try {
            // Move family to new community
            $members = $numberingService->handleFamilyMove(
                $familyNo,
                $request->new_community_id
            );

            // Log the move
            DB::table('family_move_logs')->insert([
                'family_no' => $familyNo,
                'old_community_id' => $members->first()->community_id,
                'new_community_id' => $request->new_community_id,
                'move_date' => $request->move_date,
                'reason' => $request->reason,
                'moved_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Family moved successfully',
                'family_no' => $familyNo,
                'new_community' => \App\Models\Community::find($request->new_community_id)->name,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to move family: '.$e->getMessage(),
            ], 500);
        }
    }

    public function handleMarriage(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'spouse_id' => 'required|exists:members,id',
            'marriage_date' => 'required|date',
        ]);

        $numberingService = new \App\Services\FamilyNumberingService;

        DB::beginTransaction();

        try {
            $result = $numberingService->handleMarriage(
                $request->member_id,
                $request->spouse_id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Marriage recorded successfully',
                'member' => $result['member'],
                'spouse' => $result['spouse'],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to record marriage: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getFamilyDetails($familyNo)
    {
        try {
            $familyMembers = Member::where('family_no', $familyNo)
                ->with(['community', 'communityCluster.cluster'])
                ->get();

            if ($familyMembers->isEmpty()) {
                return response()->json(['error' => 'Family not found'], 404);
            }

            // Get the first member's community and cluster info
            $firstMember = $familyMembers->first();
            $communityId = $firstMember->community_id;

            // Find the correct community cluster ID for this community
            // If the stored community_cluster_id doesn't match the community, find the first one for this community
            $correctClusterId = $firstMember->community_cluster_id;

            // Check if the stored cluster belongs to the correct community
            $storedCluster = \App\Models\CommunityCluster::find($firstMember->community_cluster_id);
            if (! $storedCluster || $storedCluster->community_id != $communityId) {
                // Find the first cluster for this community
                $correctCluster = \App\Models\CommunityCluster::where('community_id', $communityId)->first();
                if ($correctCluster) {
                    $correctClusterId = $correctCluster->id;
                }
            }

            return response()->json([
                'family_no' => $familyNo,
                'community_id' => $communityId,
                'community_cluster_id' => $correctClusterId,
                'community_name' => $firstMember->community?->name,
                'cluster_name' => $firstMember->communityCluster?->cluster?->name,
                'member_count' => $familyMembers->count(),
                'members' => $familyMembers->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->first_name.' '.$member->last_name,
                        'member_no' => $member->member_no,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting family details: '.$e->getMessage());

            return response()->json(['error' => 'An error occurred while fetching family details'], 500);
        }
    }

    public function searchFamilies(Request $request)
    {
        try {
            $query = $request->input('q', '');

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $numberingService = new \App\Services\FamilyNumberingService;
            $results = $numberingService->searchFamilies($query);

            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Error in searchFamilies: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'error' => 'An error occurred while searching families',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getChurchStatistics($churchCode = null)
    {
        $churchCode = $churchCode ?? config('app.church_code', 'SAL');
        $numberingService = new \App\Services\FamilyNumberingService($churchCode);
        $statistics = $numberingService->getChurchStatistics($churchCode);

        return response()->json($statistics);
    }

    public function getNextAvailableNumbers()
    {
        $churchCode = config('app.church_code', 'SAL');
        $numberingService = new \App\Services\FamilyNumberingService($churchCode);

        $nextFamilyGroup = $numberingService->generateFamilyGroupNumber();
        $nextFamilyNo = $numberingService->generateMemberNumberInFamily($nextFamilyGroup);
        $nextMemberNo = $numberingService->generateMemberNumber();

        return response()->json([
            'next_family_no' => $nextFamilyNo,
            'next_member_no' => $nextMemberNo,
            'family_group' => $nextFamilyGroup,
            'timestamp' => now()->toISOString(),
            'debug_info' => [
                'highest_member_in_db' => DB::table('members')
                    ->whereNotNull('member_no')
                    ->orderByRaw('CAST(REPLACE(SUBSTRING_INDEX(member_no, "-", -1), "M", "") AS UNSIGNED) DESC')
                    ->value('member_no'),
                'highest_family_in_db' => DB::table('members')
                    ->whereNotNull('family_no')
                    ->orderByRaw('CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(family_no, "-", 2), "-", -1) AS UNSIGNED) DESC')
                    ->value('family_no'),
            ],
        ]);
    }

    /**
     * Search members for spouse selection
     */
    public function searchMembers(Request $request)
    {
        $query = $request->input('query', $request->input('q', '')); // Accept both 'query' and 'q'
        $limit = $request->input('limit', 10);

        if (empty($query)) {
            return response()->json([]);
        }

        $members = Member::with(['community', 'relationship', 'gender']);

        // Check if query is a numeric ID
        if (is_numeric($query)) {
            // Search by ID
            $members = $members->where('id', $query);
        } else {
            // Search by name, member number, or family number (existing logic)
            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $members = $members->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
                    ->orWhere('member_no', 'like', "%{$query}%")
                    ->orWhere('family_no', 'like', "%{$query}%");
            });
        }

        $members = $members->where('id', '!=', $request->input('exclude_id')) // Exclude current member
            ->limit($limit)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'text' => "{$member->first_name} {$member->last_name} ({$member->member_no}) - {$member->family_no}",
                    'member_no' => $member->member_no,
                    'family_no' => $member->family_no,
                    'full_name' => "{$member->first_name} {$member->last_name}",
                    'first_name' => $member->first_name,
                    'last_name' => $member->last_name,
                    'community' => $member->community->name ?? '',
                    'relationship' => $member->relationship->name ?? '',
                    'gender' => $member->gender->name ?? '',
                ];
            });

        return response()->json($members);
    }

    public function getMemberDetails($id)
    {
        $member = Member::with(['gender', 'community', 'relationship'])
            ->findOrFail($id);

        return response()->json([
            'id' => $member->id,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'full_name' => $member->full_name,
            'member_no' => $member->member_no,
            'family_no' => $member->family_no,
            'community' => $member->community,
            'relationship' => $member->relationship,
            'gender' => $member->gender,
        ]);
    }
}
