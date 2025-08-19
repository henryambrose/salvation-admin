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
use App\Models\PPCHead;
use App\Models\Relationship;
use App\Models\SCCHead;
use App\Models\State;
use App\Models\Status;
use App\Models\Town;
use App\Models\UnifiedPerson;
use App\Services\FamilyNumberingService;
use App\Services\FamilyTreeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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

        // Community scoping already applied via forUserCommunities scope above

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

        // Get allowed community IDs for PPC/SCC head scoping
        $allowedCommunityIds = $this->allowedCommunityIdsFor(auth()->user());

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
                return ['id' => $item->id, 'name' => $item->name, 'state_id' => $item->state_id];
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
            'parishes' => Parish::select('id', 'name', 'code')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name, 'code' => $item->code];
            }),
            'incomeRanges' => $incomeRanges,
            'communityClusters' => CommunityCluster::select('id', 'community_id', 'cluster_id')->with('cluster')->get()->map(function ($item) {
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
            'cities' => City::select('id', 'name', 'state_id')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name, 'state_id' => $item->state_id];
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

    public function destroy(Request $request, Member $member)
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

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('member.index', array_merge(
            $request->only(['search', 'familySearch', 'sort', 'direction', 'communityId', 'relationship', 'ageGroup', 'bloodGroup', 'gender', 'filterColumnKey', 'filterColumnValue', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Member deleted successfully.');
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
        \Log::info($allFamilyMembers);
        return Inertia::render('member/FamilyTree', [
            'member' => $member,
            'person' => $person,
            'familyTree' => $allFamilyMembers,
            'relationships' => $service->getAvailableRelationships(),
        ]);
    }

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
    
    
    public function export(Request $request)
    {
        $this->authorize('viewAny', Member::class);
    
        // Streamed CSV keeps memory flat
        return response()->streamDownload(function () use ($request) {
            $out = fopen('php://output', 'w');
        
            fputcsv($out, [
                'ID','First Name','Last Name','Old SAL ID','Family No','Member No',
                'Contact No','Email','Date of Birth','Age',
                'Community','Cluster','Relationship','Blood Group','Gender','Status',
            ]);
        
            // Get allowed community IDs for PPC/SCC head scoping
            $allowedCommunityIds = $this->allowedCommunityIdsFor(auth()->user());

            $q = Member::query()
                ->when($request->boolean('isArchived'), fn($qq) => $qq->onlyTrashed(), fn($qq) => $qq->withoutTrashed())
                ->leftJoin('communities as c', 'c.id', '=', 'members.community_id')
                ->leftJoin('community_clusters as cc', 'cc.id', '=', 'members.community_cluster_id')
                ->leftJoin('clusters as cl', 'cl.id', '=', 'cc.cluster_id')
                ->leftJoin('relationships as r', 'r.id', '=', 'members.relationship_id')
                ->leftJoin('blood_groups as bg', 'bg.id', '=', 'members.blood_group_id')
                ->leftJoin('genders as g', 'g.id', '=', 'members.gender_id')
                ->leftJoin('statuses as s', 's.id', '=', 'members.status_id')
                ->select([
                    'members.id','members.first_name','members.last_name','members.old_sal_id',
                    'members.family_no','members.member_no','members.contact_no_1','members.email',
                    'members.date_of_birth',
                    \DB::raw('TIMESTAMPDIFF(YEAR, members.date_of_birth, CURDATE()) as age'),
                    'c.name as community_name','cl.name as cluster_name','r.name as relationship_name',
                    'bg.name as blood_group_name','g.name as gender_name','s.name as status_name',
                ]);

            // Apply PPC/SCC community scoping
            if ($allowedCommunityIds !== null) {
                $q->whereIn('members.community_id', $allowedCommunityIds);
            }
        
            if ($search = $request->input('search')) {
                $q->where(function ($w) use ($search) {
                    $w->where('members.first_name', 'like', "%$search%")
                      ->orWhere('members.last_name', 'like', "%$search%")
                      ->orWhereRaw("CONCAT(members.first_name, ' ', members.last_name) LIKE ?", ["%$search%"])
                      ->orWhere('members.family_no', 'like', "%$search%")
                      ->orWhere('c.name', 'like', "%$search%");
                });
            }
            if ($v = $request->input('familySearch')) { $q->where('members.family_no', 'like', "%$v%"); }
            if ($v = $request->input('communityId'))  { $q->where('members.community_id', $v); }
            if ($v = $request->input('relationship')) { $q->where('members.relationship_id', $v); }
            if ($v = $request->input('bloodGroup'))   { $q->where('members.blood_group_id', $v); }
            if ($v = $request->input('gender'))       { $q->where('members.gender_id', $v); }
            if ($v = $request->input('ageGroup')) {
                if ($ag = \App\Models\AgeGroup::find($v)) {
                    $q->whereRaw('TIMESTAMPDIFF(YEAR, members.date_of_birth, CURDATE()) BETWEEN ? AND ?', [$ag->min_age, $ag->max_age]);
                }
            }
        
            $allowed = ['id','first_name','last_name','family_no','member_no','date_of_birth'];
            $sort = in_array($request->input('sort','id'), $allowed, true) ? $request->input('sort','id') : 'id';
            $direction = $request->input('direction','asc') === 'desc' ? 'desc' : 'asc';
            $q->orderBy("members.$sort", $direction)->orderBy('members.id');
        
            foreach ($q->cursor() as $row) {
                fputcsv($out, [
                    $row->id,
                    $row->first_name ?? '',
                    $row->last_name ?? '',
                    $row->old_sal_id ?? '',
                    $row->family_no ?? '',
                    $row->member_no ?? '',
                    $row->contact_no_1 ?? '',
                    $row->email ?? '',
                    $row->date_of_birth ?? '',
                    $row->age ?? '',
                    $row->community_name ?? '',
                    $row->cluster_name ?? '',
                    $row->relationship_name ?? '',
                    $row->blood_group_name ?? '',
                    $row->gender_name ?? '',
                    $row->status_name ?? '',
                ]);
            }
        
            fclose($out);
        }, 'members_'.now()->format('Y-m-d_H-i-s').'.csv', [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
    

    public function getMembersByCommunity($communityId)
    {
        $members = Member::where('community_id', $communityId)
            ->select('id', 'first_name', 'last_name', 'community_id')
            ->selectRaw('CONCAT(first_name, " ", last_name) as name')
            ->get();

        return response()->json($members);
    }

    public function getMembersByFamily($familyNo, Request $request)
    {
        // $excludeMemberId = $request->query('exclude_member_id');
        
        $query = Member::where('family_no', $familyNo)
            ->select('id', 'first_name', 'last_name', 'date_of_birth', 'gender_id');
        
        // Exclude the current member if exclude_member_id is provided
        // if ($excludeMemberId) {
        //     $query->where('id', '!=', $excludeMemberId);
        // }
        
        $members = $query->get();

        // First, get all UnifiedPerson records for this family to build the complete family tree
        $unifiedPersons = \App\Models\UnifiedPerson::where('family_no', $familyNo)->get();
        
        // Build a map of uid to UnifiedPerson for quick lookup
        $unifiedPersonMap = $unifiedPersons->keyBy('uid');
        
        // Find the root generation (people with no parents)
        // $rootGeneration = $unifiedPersons->filter(function ($person) use ($unifiedPersonMap) {
        //     return !$person->father_uid && !$person->mother_uid;
        // });
        
        // Calculate generation levels for all family members
        $generationMap = [];
        $visited = [];
        
        // Function to calculate generation level recursively
        $calculateGeneration = function ($personUid, $currentGen = 0) use (&$calculateGeneration, &$generationMap, &$visited, $unifiedPersonMap) {
            if (isset($generationMap[$personUid]) || in_array($personUid, $visited)) {
                return $generationMap[$personUid] ?? 0;
            }
            
            $visited[] = $personUid;
            $person = $unifiedPersonMap->get($personUid);
            
            if (!$person) {
                return $currentGen;
            }
            
            // If this person has no parents, they're at generation 0
            if (!$person->father_uid && !$person->mother_uid) {
                $generationMap[$personUid] = 0;
                return 0;
            }
            
            // Find the highest generation of this person's parents
            $parentGen = 0;
            if ($person->father_uid) {
                $parentGen = max($parentGen, $calculateGeneration($person->father_uid, $currentGen + 1));
            }
            if ($person->mother_uid) {
                $parentGen = max($parentGen, $calculateGeneration($person->mother_uid, $currentGen + 1));
            }
            
            // This person's generation is one more than their highest parent
            $generationMap[$personUid] = $parentGen + 1;
            return $generationMap[$personUid];
        };
        
        // Calculate generations for all family members
        foreach ($unifiedPersons as $person) {
            $calculateGeneration($person->uid);
        }
        
        // Enhance with UnifiedPerson information for family relationships
        $enhancedMembers = $members->map(function ($member) use ($unifiedPersonMap, $generationMap) {
            $unifiedPerson = $unifiedPersonMap->get('M-' . $member->id);
            
            $memberData = $member->toArray();
            
            if ($unifiedPerson) {
                // Get father, mother, and spouse information
                $father = null;
                $mother = null;
                $spouse = null;
                
                if ($unifiedPerson->father_uid) {
                    $fatherUnified = $unifiedPersonMap->get($unifiedPerson->father_uid);
                    if ($fatherUnified) {
                        $fatherMember = Member::find(str_replace('M-', '', $fatherUnified->uid));
                        if ($fatherMember) {
                            $father = [
                                'id' => $fatherMember->id,
                                'name' => $fatherMember->first_name . ' ' . $fatherMember->last_name,
                                'member_no' => $fatherMember->member_no
                            ];
                        }
                    }
                }
                
                if ($unifiedPerson->mother_uid) {
                    $motherUnified = $unifiedPersonMap->get($unifiedPerson->mother_uid);
                    if ($motherUnified) {
                        $motherMember = Member::find(str_replace('M-', '', $motherUnified->uid));
                        if ($motherMember) {
                            $mother = [
                                'id' => $motherMember->id,
                                'name' => $motherMember->first_name . ' ' . $motherMember->last_name,
                                'member_no' => $motherMember->member_no
                            ];
                        }
                    }
                }
                
                if ($unifiedPerson->spouse_uid) {
                    $spouseUnified = $unifiedPersonMap->get($unifiedPerson->spouse_uid);
                    if ($spouseUnified) {
                        $spouseMember = Member::find(str_replace('M-', '', $spouseUnified->uid));
                        if ($spouseMember) {
                            $spouse = [
                                'id' => $spouseMember->id,
                                'name' => $spouseMember->first_name . ' ' . $spouseMember->last_name,
                                'member_no' => $spouseMember->member_no
                            ];
                        }
                    }
                }
                
                $memberData['father'] = $father;
                $memberData['mother'] = $mother;
                $memberData['spouse'] = $spouse;
                
                // Get the calculated generation level
                $memberData['generation'] = $generationMap[$unifiedPerson->uid] ?? 0;
            }
            
            return $memberData;
        });
        
        // Sort by generation (ascending - oldest first) and then by date of birth
        $enhancedMembers = $enhancedMembers->sortBy([
            ['generation', 'asc'],
            ['date_of_birth', 'asc']
        ])->values();
        
        // Debug logging to verify generation calculation
        \Log::info('Family members with generations:', $enhancedMembers->map(function ($member) {
            return [
                'name' => $member['first_name'] . ' ' . $member['last_name'],
                'generation' => $member['generation'],
                'date_of_birth' => $member['date_of_birth']
            ];
        })->toArray());

        return response()->json($enhancedMembers);
    }
    // public function handleMarriage(Request $request)
    // {
    //     $request->validate([
    //         'member_id' => 'required|exists:members,id',
    //         'spouse_id' => 'required|exists:members,id',
    //         'marriage_date' => 'required|date',
    //     ]);

    //     $numberingService = new \App\Services\FamilyNumberingService;

    //     DB::beginTransaction();

    //     try {
    //         $result = $numberingService->handleMarriage(
    //             $request->member_id,
    //             $request->spouse_id
    //         );

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Marriage recorded successfully',
    //             'member' => $result['member'],
    //             'spouse' => $result['spouse'],
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to record marriage: '.$e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function getFamilyDetails($familyNo, Request $request)
    {
        try {
            // Get all family members from the UnifiedPerson view
            $familyMembersQuery = UnifiedPerson::where('family_no', $familyNo)
            ->select('original_id', 'uid', 'first_name', 'last_name', 'member_no', 'source',
                    'father_uid', 'mother_uid', 'spouse_uid');

            // Exclude the current member if exclude_member_id is provided
            if ($request->has('exclude_member_id')) {
                $excludeId = $request->input('exclude_member_id');
                
                // Try to find the member in UnifiedPerson view to get the correct uid
                $excludeMember = UnifiedPerson::where('original_id', $excludeId)->first();
                
                if ($excludeMember) {
                    // Exclude by uid (which is unique across both member types)
                    $familyMembersQuery->where('uid', '!=', $excludeMember->uid);
                } else {
                    // Fallback: exclude by original_id
                    $familyMembersQuery->where('original_id', '!=', $excludeId);
                }
            }

            $familyMembers = $familyMembersQuery->get();

            if ($familyMembers->isEmpty()) {
                return response()->json(['error' => 'Family not found'], 404);
            }

            // Get community info from the first member (prefer internal member)
            $firstMember = $familyMembers->where('source', 'Member')->first() ?? $familyMembers->first();
            
            // For internal members, we need to get additional community/cluster info
            $communityId = null;
            $communityClusterId = null;
            $communityName = null;
            $clusterName = null;
            
            if ($firstMember->source === 'Member') {
                // Get the actual Member model for community info
                $member = Member::find($firstMember->original_id);
                if ($member) {
                    $communityId = $member->community_id;
                    $communityClusterId = $member->community_cluster_id;
                    $communityName = $member->community?->name;
                    $clusterName = $member->communityCluster?->cluster?->name;
                }
            }

            // Calculate generation and format data for each member
            $membersWithGeneration = $familyMembers->map(function ($member) {
                // Calculate generation based on family relationships
                $generation = 0;
                
                if ($member->father_uid) {
                        $generation = 1;
                    $father = UnifiedPerson::find($member->father_uid);
                    if ($father && $father->father_uid) {
                            $generation = 2;
                        }
                    }
                    
                if ($member->mother_uid && $generation === 0) {
                        $generation = 1;
                    $mother = UnifiedPerson::find($member->mother_uid);
                    if ($mother && $mother->mother_uid) {
                            $generation = 2;
                        }
                    }

                    return [
                    'id' => $member->original_id,
                    'uid' => $member->uid,
                        'first_name' => $member->first_name,
                        'last_name' => $member->last_name,
                        'member_no' => $member->member_no,
                    'date_of_birth' => null, // Will be populated for internal members if needed
                        'generation' => $generation,
                    'source' => $member->source,
                        'father' => $member->father ? [
                        'id' => $member->father->original_id,
                        'uid' => $member->father->uid,
                            'name' => $member->father->first_name . ' ' . $member->father->last_name
                        ] : null,
                        'mother' => $member->mother ? [
                        'id' => $member->mother->original_id,
                        'uid' => $member->mother->uid,
                            'name' => $member->mother->first_name . ' ' . $member->mother->last_name
                        ] : null,
                        'spouse' => $member->spouse ? [
                        'id' => $member->spouse->original_id,
                        'uid' => $member->spouse->uid,
                            'name' => $member->spouse->first_name . ' ' . $member->spouse->last_name
                        ] : null,
                    ];
            });

            // For internal members, populate date_of_birth
            $membersWithGeneration = $membersWithGeneration->map(function ($member) {
                if ($member['source'] === 'Member') {
                    $memberModel = Member::find($member['id']);
                    if ($memberModel) {
                        $member['date_of_birth'] = $memberModel->date_of_birth;
                    }
                }
                return $member;
            });

            return response()->json([
                'family_no' => $familyNo,
                'community_id' => $communityId,
                'community_cluster_id' => $communityClusterId,
                'community_name' => $communityName,
                'cluster_name' => $clusterName,
                'member_count' => $familyMembers->count(),
                'internal_count' => $familyMembers->where('source', 'Member')->count(),
                'external_count' => $familyMembers->count() - $familyMembers->where('source', 'Member')->count(),
                'members' => $membersWithGeneration,
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
        try {
            $churchCode = config('app.church_code', 'SAL');
            $numberingService = new \App\Services\FamilyNumberingService($churchCode);

            $nextFamilyGroup = $numberingService->generateFamilyGroupNumber();
            $nextFamilyNo = $numberingService->generateMemberNumberInFamily($nextFamilyGroup);
            $nextMemberNo = $numberingService->generateMemberNumber();

            \Log::info('Generated next available numbers', [
                'church_code' => $churchCode,
                'next_family_group' => $nextFamilyGroup,
                'next_family_no' => $nextFamilyNo,
                'next_member_no' => $nextMemberNo
            ]);

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
        } catch (\Exception $e) {
            \Log::error('Error generating next available numbers: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to generate next available numbers',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // public function searchMembers(Request $request)
    // {
    //     $query = $request->input('query', $request->input('q', '')); // Accept both 'query' and 'q'
    //     $limit = $request->input('limit', 10);
    //     $familyNo = $request->input('familyNo'); // Add this parameter

    //     if (empty($query)) {
    //         return response()->json([]);
    //     }

    //     $members = Member::with(['community', 'relationship', 'gender']);

    //     // Check if query is a numeric ID
    //     if (is_numeric($query)) {
    //         // Search by ID
    //         $members = $members->where('id', $query);
    //     } else {
    //         // Search by name, member number, or family number (existing logic)
    //         if (strlen($query) < 2) {
    //             return response()->json([]);
    //         }

    //         $members = $members->where(function ($q) use ($query) {
    //             $q->where('first_name', 'like', "%{$query}%")
    //                 ->orWhere('last_name', 'like', "%{$query}%")
    //                 ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
    //                 ->orWhere('member_no', 'like', "%{$query}%")
    //                 ->orWhere('family_no', 'like', "%{$query}%");
    //         });
    //     }

    //     // Apply family number filter if provided
    //     if ($familyNo) {
    //         $members = $members->where('family_no', $familyNo);
    //     }

    //     $members = $members->where('id', '!=', $request->input('exclude_id')) // Exclude current member
    //         ->limit($limit)
    //         ->get()
    //         ->map(function ($member) {
    //             return [
    //                 'id' => $member->id,
    //                 'text' => "{$member->first_name} {$member->last_name} ({$member->member_no}) - {$member->family_no}",
    //                 'member_no' => $member->member_no,
    //                 'family_no' => $member->family_no,
    //                 'full_name' => "{$member->first_name} {$member->last_name}",
    //                 'first_name' => $member->first_name,
    //                 'last_name' => $member->last_name,
    //                 'community' => $member->community->name ?? '',
    //                 'relationship' => $member->relationship->name ?? '',
    //                 'gender' => $member->gender->name ?? '',
    //             ];
    //         });

    //     return response()->json($members);
    // }

    //for viewmembermodal leadership roles tab
    public function getMemberDetails($id)
    {
        try {
            
            $member = Member::with([
                'community:id,name',
                'relationship:id,name',
                'gender:id,name',
                'bloodGroup:id,name',
                'designation:id,name',
                'incomeRange:id,name',
                'sccHeads.community:id,name',
                'ppcHeads.community:id,name',
                'clusterHeads.cluster:id,name',
                'clusterHeads.community:id,name',
                'cellsAndAssociations:id,name'
            ])->findOrFail($id);


            // Transform the data to match frontend expectations
            $transformedMember = $member->toArray();
            $transformedMember['scc_heads'] = $member->sccHeads;
            $transformedMember['ppc_heads'] = $member->ppcHeads;
            $transformedMember['cluster_heads'] = $member->clusterHeads;
            $transformedMember['cells_and_associations'] = $member->cellsAndAssociations;

            return response()->json($transformedMember);
        } catch (\Exception $e) {
            \Log::error('Error getting member details: '.$e->getMessage());
            return response()->json(['error' => 'Member not found'], 404);
        }
    }

    public function dataVerification()
    {
        // Debug authentication
        if (!auth()->check()) {
            \Log::error('User not authenticated for data verification page');
            abort(401, 'Unauthenticated');
        }
        
        $user = auth()->user();
        
        // Check permission to access data verification
        if (!$user->can('read-data-verification')) {
            \Log::warning('User denied access to data verification page', [
                'user_id' => $user->id, 
                'email' => $user->email,
                'permissions' => $user->getAllPermissionsAttribute()
            ]);
            abort(403, 'Access denied. You do not have permission to view data verification.');
        }
        
        \Log::info('User accessing data verification page', ['user_id' => $user->id, 'email' => $user->email]);
        
        // Load data directly instead of via AJAX
        $members = Member::with([
            'community:id,name',
            'status:id,name',
            'relationship:id,name'
        ])
        ->select([
            'id', 'first_name', 'middle_name', 'last_name', 'date_of_birth',
            'old_sal_id', 'community_id', 'status_id', 'contact_no_1', 'contact_no_2', 'relationship_id'
        ])
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get()
        ->map(function ($member) {
            return [
                'id' => $member->id,
                'first_name' => $member->first_name,
                'middle_name' => $member->middle_name,
                'last_name' => $member->last_name,
                'date_of_birth' => $member->date_of_birth,
                'old_sal_id' => $member->old_sal_id,
                'community_id' => $member->community_id,
                'community_name' => $member->community->name ?? null,
                'status_id' => $member->status_id,
                'status_name' => $member->status ? $member->status->name : null,
                'contact_no_1' => $member->contact_no_1,
                'contact_no_2' => $member->contact_no_2,
                'relationship_id' => $member->relationship_id,
                'relationship_name' => $member->relationship ? $member->relationship->name : null,
            ];
        });
        
        $communities = \App\Models\Community::select('id', 'name')->orderBy('name')->get();
        $statuses = \App\Models\Status::select('id', 'name')->orderBy('name')->get();
        $relationships = \App\Models\Relationship::select('id', 'name')->orderBy('name')->get();
        
        return Inertia::render('member/DataVerification', [
            'members' => $members,
            'communities' => $communities,
            'statuses' => $statuses,
            'relationships' => $relationships
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        // Debug authentication
        if (!auth()->check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        $user = auth()->user();
        
        // Check permission to update data verification
        if (!$user->can('update-data-verification')) {
            \Log::warning('User denied access to bulk update', [
                'user_id' => $user->id, 
                'email' => $user->email,
                'permissions' => $user->getAllPermissionsAttribute()
            ]);
            return response()->json(['error' => 'Access denied. You do not have permission to update data verification.'], 403);
        }
        
        \Log::info('User authenticated for bulk update', ['user_id' => $user->id, 'email' => $user->email]);
        
        $request->validate([
            'changes' => 'required|array',
            'changes.*.id' => 'required|exists:members,id',
            'changes.*.data' => 'required|array'
        ]);

        $updatedCount = 0;
        $errors = [];

        foreach ($request->changes as $change) {
            try {
                $member = Member::find($change['id']);
                if (!$member) {
                    $errors[] = "Member ID {$change['id']} not found";
                    continue;
                }

                // Update only allowed fields
                $allowedFields = [
                    'first_name', 'middle_name', 'last_name', 'date_of_birth',
                    'old_sal_id', 'community_id', 'status_id', 'contact_no_1', 'contact_no_2', 'relationship_id'
                ];

                $updateData = array_intersect_key($change['data'], array_flip($allowedFields));
                
                if (!empty($updateData)) {
                    $member->update($updateData);
                    $updatedCount++;
                }

            } catch (\Exception $e) {
                $errors[] = "Error updating member {$change['id']}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount,
            'total_changes' => count($request->changes),
            'errors' => $errors,
            'message' => "Successfully updated {$updatedCount} out of " . count($request->changes) . " records"
        ]);
    }
}
