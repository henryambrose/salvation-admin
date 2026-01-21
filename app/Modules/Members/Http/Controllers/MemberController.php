<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Members\Http\Requests\StoreMemberRequest;
use Modules\Members\Http\Requests\UpdateMemberRequest;
use Modules\Members\Models\AgeGroup;
use Modules\Members\Models\AuditLog;
use Modules\Members\Models\BloodGroup;
use Modules\Members\Models\City;
use Modules\Members\Models\Community;
use Modules\Members\Models\CommunityCluster;
use Modules\Members\Models\Country;
use Modules\Members\Models\Designation;
use Modules\Members\Models\ExternalMember;
use Modules\Members\Models\Gender;
use Modules\Members\Models\IncomeRange;
use Modules\Members\Models\Member;
use Modules\Members\Models\Parish;
use Modules\Members\Models\Relationship;
use Modules\Members\Models\State;
use Modules\Members\Models\Status;
use Modules\Members\Models\Town;
use Modules\Members\Models\UnifiedPerson;
use Modules\Members\Services\FamilyNumberingService;
use Modules\Members\Services\FamilyTreeService;
use Modules\Members\Services\FamilyPhotoService;
use Modules\Members\Models\FamilyPhoto;
use Modules\Members\Http\Requests\FamilyPhotoUploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelPdf\Facades\Pdf;

class MemberController extends Controller
{

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Member::class);
        // Map used for display-enrichment (kept as-is for UI compatibility)
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

        // Base query + eager loads
        $query = Member::query()->with([
            'community',
            'communityCluster.cluster',
            'relationship',
            'bloodGroup',
            'designation',
            'incomeRange',
            'status',
            'gender',
            'baptismRecord',
            'marriageRecord',
            'deathRecord',
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

        // Community scoping
        $query->forUserCommunities(Auth::user());

        // Apply filters & sorting centrally
        $this->applyFilters($query, $request);
        $this->applySorting($query, $request);

        // Count BEFORE paginate (same filtered scope)
        $totalCount = (clone $query)->count();

        // Single paginate call
        $perPage   = (int) $request->input('perPage', 10);
        $paginator = $query->paginate($perPage)->appends($request->query());

        // Enrich rows for dropdown display (kept for UI compatibility)
        $paginator->getCollection()->transform(function ($item) use ($dropdownColumns) {
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

                $item->$key = $found ? ($currentRelation->{$relation['column']} ?? '') : '';
            }

            return $item;
        });

        // -------- Family Statistics (reuse same filters without duplication) --------
        $totalStatsQuery = Member::query();
        $totalStatsQuery->forUserCommunities(Auth::user());
        $this->applyFilters($totalStatsQuery, $request);

        $totalMembers = (clone $totalStatsQuery)->count();
        $totalFamilies = (clone $totalStatsQuery)
            ->whereNotNull('family_no')
            ->distinct()
            ->count('family_no');

        $familyStats = [
            'totalMembers' => $totalMembers,
            'totalFamilies' => $totalFamilies,
            'averageMembersPerFamily' => $totalFamilies > 0 ? round($totalMembers / $totalFamilies, 1) : 0,
        ];

        return Inertia::render('member/Index', [
            'communities' => Community::all(),
            'relationships' => Relationship::all(),
            'ageGroups' => AgeGroup::all(),
            'bloodGroups' => BloodGroup::all(),
            'genders' => Gender::all(),
            'parishes' => Parish::all(),
            'statuses' => Status::all(),
            'communityClusters' => CommunityCluster::with('cluster')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->cluster->name ?? 'Unknown Cluster', 'community_id' => $item->community_id];
            })->toArray(),
            'fetchUrl' => route('member.index'),
            'members' => $paginator, // paginator (not just collection)
            'totalCount' => $totalCount,
            'familyStats' => $familyStats,
            'filters' => $request->only([
                'search',
                'sort',
                'direction',
                'perPage',
                'communityId',
                'relationship',
                'ageGroup',
                'bloodGroup',
                'gender',
                'filterColumnKey',
                'filterColumnValue',
                'isArchived',
                'familySearch',
                'status'
            ]),
            'canViewAnyMember' => Gate::allows('list-member'),
            'canCreateMember' => Gate::allows('create-member'),
            'canEditMember' => Gate::allows('update-member'),
            'canDeleteMember' => Gate::allows('delete-member'),
            'canRestoreMember' => Gate::allows('restore-member'),
            'pagination' => [
                'currentPage' => $paginator->currentPage(),
                'lastPage'    => $paginator->lastPage(),
            ],
        ]);
    }

    /**
     * Centralized filters used by index() + totals.
     */
    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request): void
    {
        // Archived scope
        $request->boolean('isArchived') ? $query->onlyTrashed() : $query->withoutTrashed();

        // Text search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('old_sal_id', 'like', "%$search%")
                    ->orWhere('old_family_no', 'like', "%$search%")
                    ->orWhere('member_no', 'like', "%$search%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    ->orWhere('family_no', 'like', "%$search%")
                    ->orWhereHas('community', fn($q2) => $q2->where('name', 'like', "%$search%"));
            });
        }

        // Individual filters
        if ($v = $request->input('familySearch')) $query->where('family_no', 'like', "%$v%");
        if ($v = $request->input('communityId'))  $query->where('community_id', $v);
        if ($v = $request->input('relationship')) $query->where('relationship_id', $v);
        if ($v = $request->input('status'))       $query->where('status_id', $v);
        if ($v = $request->input('bloodGroup'))   $query->where('blood_group_id', $v);
        if ($v = $request->input('gender'))       $query->where('gender_id', $v);

        // Age group filter
        if ($ageGroup = $request->input('ageGroup')) {
            if ($ag = AgeGroup::find($ageGroup)) {
                $query->whereRaw(
                    'TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN ? AND ?',
                    [$ag->min_age, $ag->max_age]
                );
            }
        }

        // Exclude deceased members filter
        if ($request->boolean('excludeDeceased')) {
            $query->whereNull('deathrecord_id');
        }

        // Legacy filter support (unchanged)
        if (($filterColumnKey = $request->input('filterColumnKey')) !== null) {
            $filterColumnValue = $request->input('filterColumnValue');

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

            if ($filterColumnKey && $filterColumnValue) {
                if (array_key_exists($filterColumnKey, $dropdownColumns)) {
                    $relation = $dropdownColumns[$filterColumnKey]['relation'];
                    $filterColumnKeyName = $dropdownColumns[$filterColumnKey]['column'];
                    $query->whereHas($relation, function ($q) use ($filterColumnKeyName, $filterColumnValue) {
                        $q->where($filterColumnKeyName, 'like', "%$filterColumnValue%");
                    });
                } else {
                    $query->where($filterColumnKey, 'like', "%$filterColumnValue%");
                }
            }
        }
    }

    /**
     * Sorting with sane defaults and whitelist if you want to restrict.
     */
    private function applySorting(\Illuminate\Database\Eloquent\Builder $query, Request $request): void
    {
        $sort = $request->input('sort');
        $direction = $request->input('direction', 'asc');

        if ($sort) {
            $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }
    }


    public function create(Request $request): Response
    {
        $this->authorize('create', Member::class);


        return Inertia::render('member/Member', [
            'communities' => Community::all(),
            'relationships' => Relationship::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'genders' => Gender::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })->toArray(),
            'communityClusters' => CommunityCluster::with('cluster')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->cluster->name ?? 'Unknown Cluster', 'community_id' => $item->community_id];
            })->toArray(),
        ]);
    }

    private function resolvePageFor(Member $member, Request $request, int $perPage): int
    {
        // replicate the same sort (default id asc)
        $sort = $request->input('sort', 'id');
        $dir  = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $base = Member::query();

        // If you have filter reuse:
        $this->applyFilters($base, $request);

        // Compute position by count of rows "before or equal" current row in the sorted order
        // For generality, handle non-id sorts only when you need them; simplest for id sort:
        if ($sort === 'id') {
            $op = $dir === 'asc' ? '<=' : '>=';
            $position = (clone $base)->where('id', $op, $member->id)->count();
            return (int) ceil($position / $perPage);
        }

        // Fallback rough page if custom sort is in play
        $rankQuery = (clone $base)
            ->where(function ($q) use ($sort, $dir, $member) {
                $q->where($sort, $dir === 'asc' ? '<' : '>', $member->{$sort})
                    ->orWhere(function ($qq) use ($sort, $member) {
                        $qq->where($sort, $member->{$sort})->where('id', '<=', $member->id);
                    });
            })
            ->count();

        return (int) ceil(($rankQuery + 1) / $perPage);
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
            $data['marital_status'] = $data['marital_status'] ?? 'Single';

            // Check if this is a new family or existing family
            if ($request->has('existing_family_no') && $request->existing_family_no && ! empty($request->existing_family_no)) {
                $familyNo = $request->existing_family_no;

                $numberingService = new FamilyNumberingService;

                // Validate existing family number
                if (! $numberingService->validateFamilyNumber($familyNo)) {
                    throw new \Exception('Invalid family number format. Expected: SAL-XXX');
                }

                // Check if family actually exists in database
                $existingFamily = Member::where('family_no', $familyNo)->first();
                if (! $existingFamily) {
                    throw new \Exception('Family number "' . $familyNo . '" does not exist. Please search for existing families or create a new family.');
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

            // Handle marriage logic for new members
            if (isset($data['spouse_id']) && $data['spouse_id']) {
                $this->handleMarriageTransitionForNew($data);
            }

            // Store external member creation info before creating member
            $externalMemberInfo = $data['_create_external_member'] ?? null;
            unset($data['_create_external_member']); // Remove from data before creating member

            $member = Member::create($data);

            // Create external member record if needed (for new married females)
            if ($externalMemberInfo && $externalMemberInfo['transition_type'] === 'female_joins_male') {
                $this->createExternalMemberRecord(
                    $member,
                    $externalMemberInfo['birth_family_no'],
                    $externalMemberInfo['spouse']
                );
            }

            // Set bidirectional spouse relationship for new member with spouse
            if (isset($member->spouse_id) && $member->spouse_id) {
                $spouse = Member::find($member->spouse_id);
                if ($spouse && !$spouse->spouse_id) {
                    $spouse->spouse_id = $member->id;
                    $spouse->spouse_source = 'Member';
                    $spouse->save();
                }
            }

            // Create audit log for the creation
            AuditLog::create([
                'table_name' => 'members',
                'action' => 'CREATE',
                'record_id' => $member->id,
                'new_values' => $member->toArray(),
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            $perPage = $request->input('perPage', 10);
            $page = $this->resolvePageFor($member, $request, $perPage);

            return redirect()->route('member.index', array_merge(
                $request->only(['search', 'sort', 'direction', 'isArchived', 'communityId', 'filterColumnKey', 'filterColumnValue']),
                ['page' => $page, 'perPage' => $perPage, 'highlightId' => $member->id]
            ))->with('success', 'Member created successfully with Family No: ' . $member->family_no);

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

        // Eager load sacrament records
        $member->load(['baptismRecord', 'marriageRecord', 'deathRecord']);

        $incomeRanges = IncomeRange::select('id', 'name')->get()->map(function ($item) {
            return ['id' => $item->id, 'name' => $item->name];
        })->toArray();


        return Inertia::render('member/Member', [
            'member' => $member,
            'members' => Member::select('id', 'first_name', 'last_name', 'family_no')->with('spouse')->get(), // Only select needed columns
            'communities' => Community::select('id', 'name')->get(), // Only select needed columns
            'parishes' => Parish::select('id', 'name', 'code', 'town')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name, 'code' => $item->code, 'town' => $item->town];
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
        Log::info('Validated data for update:', $validated);
        Log::info('Current member spouse_id before update:', ['current_spouse_id' => $member->spouse_id, 'new_spouse_id' => $validated['spouse_id'] ?? null]);

        // Check if spouse_id changed and handle family transition
        $shouldCallTransition = isset($validated['spouse_id']) &&
            $validated['spouse_id'] !== null &&
            $validated['spouse_id'] != '' &&
            (string)$validated['spouse_id'] != (string)$member->spouse_id;

        Log::info('Should call transition?', ['shouldCallTransition' => $shouldCallTransition]);

        $member->update($validated);
        Log::info('Member updated. Spouse ID after update:', ['spouse_id' => $member->fresh()->spouse_id]);
        if ($validated['spouse_source'] == 'External' && $validated['spouse_id'] !== null) {
            $externalSpouse = ExternalMember::find($validated['spouse_id']);
            if ($externalSpouse && (!$externalSpouse->spouse_id)) {
                $externalSpouse->spouse_id = $member->id;
                $externalSpouse->spouse_source = 'Member';
                $externalSpouse->marital_status = 'Married';
                $externalSpouse->save();
                $member->marital_status = 'Married';
                $member->save();
            }
        } else {

            if ($shouldCallTransition) {

                $this->handleMarriageTransition($member, $validated);
            } else {
                Log::info('Not calling handleMarriageTransition - condition not met');
            }
        }

        // Create audit log for the update
        AuditLog::create([
            'table_name' => 'members',
            'action' => 'UPDATE',
            'record_id' => $member->id,
            'old_values' => $oldValues,
            'new_values' => $member->fresh()->toArray(),
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $perPage = $request->input('perPage', 10);
        $perPage = (int) $request->input('perPage', 10);
        $page = $this->resolvePageFor($member, $request, $perPage);

        return redirect()->route('member.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived', 'communityId', 'filterColumnKey', 'filterColumnValue']),
            ['page' => $page, 'perPage' => $perPage, 'highlightId' => $member->id]
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
            'user_id' => Auth::id(),
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
            $person = UnifiedPerson::where('uid', '=', 'M-' . $id)->first();
        } else {
            $member = ExternalMember::with(['gender', 'relationship'])->findOrFail($id);
            $person = UnifiedPerson::where('uid', '=', 'E-' . $id)->first();
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
            'print' => true,
            'id' => $id,
            'type' => $type,
        ]);
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Member::class);

        // Streamed CSV keeps memory flat
        return response()->streamDownload(function () use ($request) {
            // Clear any output buffers to prevent extra whitespace
            while (ob_get_level()) {
                ob_end_clean();
            }

            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'ID',
                'First Name',
                'Last Name',
                'Old SAL ID',
                'Family No',
                'Member No',
                'Contact No',
                'Email',
                'Date of Birth',
                'Age',
                'Community',
                'Cluster',
                'Relationship',
                'Blood Group',
                'Gender',
                'Status',
            ]);

            // Get allowed community IDs for PPC/SCC head scoping
            $allowedCommunityIds = $this->allowedCommunityIdsFor(Auth::user());

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
                    'members.id',
                    'members.first_name',
                    'members.last_name',
                    'members.old_sal_id',
                    'members.family_no',
                    'members.member_no',
                    'members.contact_no_1',
                    'members.email',
                    'members.date_of_birth',
                    DB::raw('TIMESTAMPDIFF(YEAR, members.date_of_birth, CURDATE()) as age'),
                    'c.name as community_name',
                    'cl.name as cluster_name',
                    'r.name as relationship_name',
                    'bg.name as blood_group_name',
                    'g.name as gender_name',
                    's.name as status_name',
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
            if ($v = $request->input('familySearch')) {
                $q->where('members.family_no', 'like', "%$v%");
            }
            if ($v = $request->input('communityId')) {
                $q->where('members.community_id', $v);
            }
            if ($v = $request->input('relationship')) {
                $q->where('members.relationship_id', $v);
            }
            if ($v = $request->input('bloodGroup')) {
                $q->where('members.blood_group_id', $v);
            }
            if ($v = $request->input('gender')) {
                $q->where('members.gender_id', $v);
            }
            if ($v = $request->input('ageGroup')) {
                if ($ag = AgeGroup::find($v)) {
                    $q->whereRaw('TIMESTAMPDIFF(YEAR, members.date_of_birth, CURDATE()) BETWEEN ? AND ?', [$ag->min_age, $ag->max_age]);
                }
            }

            $allowed = ['id', 'first_name', 'last_name', 'family_no', 'member_no', 'date_of_birth'];
            $sort = in_array($request->input('sort', 'id'), $allowed, true) ? $request->input('sort', 'id') : 'id';
            $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
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
        }, 'members_' . now()->format('Y-m-d_H-i-s') . '.csv', [
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
        $members = Member::where('family_no', $familyNo)
            ->select('id', 'first_name', 'last_name', 'date_of_birth', 'gender_id', 'member_no')
            ->get();

        $unified = UnifiedPerson::where('family_no', $familyNo)
            ->select('uid', 'original_id', 'father_uid', 'mother_uid', 'spouse_uid', 'source', 'first_name', 'last_name')
            ->get();

        $uByUid = $unified->keyBy('uid');
        $mById  = $members->keyBy('id');

        // generation via DFS using the in-memory map
        $gen = [];
        $vis = [];
        $calc = function ($uid) use (&$calc, &$gen, &$vis, $uByUid) {
            if (isset($gen[$uid])) return $gen[$uid];
            if (isset($vis[$uid])) return 0;
            $vis[$uid] = true;
            $p = $uByUid[$uid] ?? null;
            if (!$p) return $gen[$uid] = 0;
            $best = 0;
            foreach (['father_uid', 'mother_uid'] as $par) {
                if ($pid = $p->{$par}) $best = max($best, $calc($pid) + 1);
            }
            return $gen[$uid] = $best;
        };

        $enhanced = $members->map(function ($m) use ($uByUid, $mById, &$calc) {
            $uid = 'M-' . $m->id;
            $up  = $uByUid->get($uid);

            $father = $mother = $spouse = null;
            if ($up?->father_uid && ($fu = $uByUid->get($up->father_uid))) {
                if (str_starts_with($fu->uid, 'M-') && ($fm = $mById->get((int) substr($fu->uid, 2)))) {
                    $father = ['id' => $fm->id, 'name' => $fm->first_name . ' ' . $fm->last_name, 'member_no' => $fm->member_no];
                }
            }
            if ($up?->mother_uid && ($mu = $uByUid->get($up->mother_uid))) {
                if (str_starts_with($mu->uid, 'M-') && ($mm = $mById->get((int) substr($mu->uid, 2)))) {
                    $mother = ['id' => $mm->id, 'name' => $mm->first_name . ' ' . $mm->last_name, 'member_no' => $mm->member_no];
                }
            }
            if ($up?->spouse_uid && ($su = $uByUid->get($up->spouse_uid))) {
                if (str_starts_with($su->uid, 'M-') && ($sm = $mById->get((int) substr($su->uid, 2)))) {
                    $spouse = ['id' => $sm->id, 'name' => $sm->first_name . ' ' . $sm->last_name, 'member_no' => $sm->member_no];
                }
            }

            return [
                'id' => $m->id,
                'first_name' => $m->first_name,
                'last_name' => $m->last_name,
                'date_of_birth' => $m->date_of_birth,
                'gender_id' => $m->gender_id,
                'member_no' => $m->member_no,
                'father' => $father,
                'mother' => $mother,
                'spouse' => $spouse,
                'generation' => $calc($uid),
            ];
        })->sortBy([['generation', 'asc'], ['date_of_birth', 'asc']])->values();

        return response()->json($enhanced);
    }



    public function getFamilyDetails($familyNo, Request $request)
    {
        try {
            // Load all unified people for this family (single query)
            $family = UnifiedPerson::where('family_no', $familyNo)
                ->select([
                    'uid',
                    'original_id',
                    'first_name',
                    'last_name',
                    'member_no',
                    'source',
                    'father_uid',
                    'mother_uid',
                    'spouse_uid',
                    'gender_id'
                ])
                ->get();

            if ($family->isEmpty()) {
                return response()->json(['error' => 'Family not found'], 404);
            }



            // Build quick lookup maps
            $uByUid = $family->keyBy('uid');                                          // uid => UnifiedPerson
            $internalIds = $family->where('source', 'Member')->pluck('original_id')->map(fn($v) => (int)$v)->unique()->values();

            // Prefetch Members for internal entries (single query)
            $members = Member::whereIn('id', $internalIds)
                ->select([
                    'id',
                    'first_name',
                    'last_name',
                    'member_no',
                    'date_of_birth',
                    'community_id',
                    'community_cluster_id'
                ])
                ->with([
                    'community:id,name',
                    'communityCluster:id,cluster_id,community_id',
                    'communityCluster.cluster:id,name'
                ])
                ->get()
                ->keyBy('id');                                                        // id => Member

            // Compute generation levels (memoized DFS on in-memory graph)
            $gen = [];
            $vis = [];
            $calcGen = function (string $uid) use (&$calcGen, &$gen, &$vis, $uByUid): int {
                if (isset($gen[$uid])) return $gen[$uid];
                if (isset($vis[$uid])) return 0; // cycle guard
                $vis[$uid] = true;

                $p = $uByUid->get($uid);
                if (!$p) return $gen[$uid] = 0;

                $best = 0;
                foreach (['father_uid', 'mother_uid'] as $k) {
                    $pid = $p->{$k};
                    if ($pid && $uByUid->has($pid)) {
                        $best = max($best, $calcGen($pid) + 1);
                    }
                }
                return $gen[$uid] = $best;
            };

            // Community/cluster info (prefer internal member)
            $communityId = null;
            $communityClusterId = null;
            $communityName = null;
            $clusterName = null;
            if ($internalIds->isNotEmpty()) {
                // Choose the first internal for community metadata
                $firstInternal = $members->first();
                if ($firstInternal) {
                    $communityId        = $firstInternal->community_id;
                    $communityClusterId = $firstInternal->community_cluster_id;
                    $communityName      = optional($firstInternal->community)->name;
                    $clusterName        = optional(optional($firstInternal->communityCluster)->cluster)->name;
                }
            }

            // Build response members array (no DB calls inside loop)
            $membersResp = $family->map(function ($p) use ($members, $uByUid, $calcGen) {
                $dateOfBirth = null;
                if ($p->source === 'Member') {
                    $mid = (int) $p->original_id;
                    if ($m = $members->get($mid)) {
                        $dateOfBirth = $m->date_of_birth;
                    }
                }

                $mapRelative = function (?string $relUid) use ($uByUid, $members) {
                    if (!$relUid) return null;
                    $rel = $uByUid->get($relUid);
                    if (!$rel) return null;

                    // Prefer internal member details if available
                    if (str_starts_with($rel->uid, 'M-')) {
                        $rid = (int) substr($rel->uid, 2);
                        if ($m = $members->get($rid)) {
                            return [
                                'id'   => $m->id,
                                'uid'  => $rel->uid,
                                'name' => trim(($m->first_name ?? '') . ' ' . ($m->last_name ?? '')),
                            ];
                        }
                    }

                    // Fallback to unified names (external or missing internal)
                    return [
                        'id'   => (int) $rel->original_id,
                        'uid'  => $rel->uid,
                        'name' => trim(($rel->first_name ?? '') . ' ' . ($rel->last_name ?? '')),
                    ];
                };

                // Map gender_id to gender name
                $genderName = '';
                if ($p->gender_id == 1) {
                    $genderName = 'Male';
                } elseif ($p->gender_id == 2) {
                    $genderName = 'Female';
                } else {
                    $genderName = 'Other';
                }

                return [
                    'id'          => (int) $p->original_id,
                    'uid'         => $p->uid,
                    'first_name'  => $p->first_name,
                    'last_name'   => $p->last_name,
                    'member_no'   => $p->member_no,
                    'date_of_birth' => $dateOfBirth,
                    'generation'  => $calcGen($p->uid),
                    'source'      => $p->source,
                    'gender_name' => $genderName,
                    'father'      => $mapRelative($p->father_uid),
                    'mother'      => $mapRelative($p->mother_uid),
                    'spouse'      => $mapRelative($p->spouse_uid),
                ];
            });

            // Counts
            $memberCount   = $family->count();
            $internalCount = $family->where('source', 'Member')->count();
            $externalCount = $memberCount - $internalCount;

            return response()->json([
                'family_no'            => $familyNo,
                'community_id'         => $communityId,
                'community_cluster_id' => $communityClusterId,
                'community_name'       => $communityName,
                'cluster_name'         => $clusterName,
                'member_count'         => $memberCount,
                'internal_count'       => $internalCount,
                'external_count'       => $externalCount,
                'members'              => $membersResp,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error getting family details: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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

            $numberingService = new FamilyNumberingService;
            $results = $numberingService->searchFamilies($query);

            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Error in searchFamilies: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'An error occurred while searching families',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function searchFamiliesSimple(Request $request)
    {
        try {
            $query = $request->input('search', $request->input('q', ''));

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $numberingService = new FamilyNumberingService;
            $results = $numberingService->searchFamilies($query);

            // Transform results to { id, name } format for SearchDropdown
            $transformed = collect($results)->map(function ($family) {
                $memberCount = $family['member_count'] ?? 0;
                $members = is_array($family['members']) ? implode(', ', array_slice($family['members'], 0, 3)) : '';
                $displayName = "{$family['family_no']} ({$memberCount} members)" . ($members ? " - {$members}" : '');

                return [
                    'id' => $family['family_no'],
                    'name' => $displayName,
                ];
            })->values()->all();

            return response()->json($transformed);
        } catch (\Exception $e) {
            Log::error('Error in searchFamiliesSimple: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'An error occurred while searching families',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getChurchStatistics($churchCode = null)
    {
        $churchCode = $churchCode ?? config('app.church_code', 'SAL');
        $numberingService = new FamilyNumberingService($churchCode);
        $statistics = $numberingService->getChurchStatistics($churchCode);

        return response()->json($statistics);
    }

    public function getNextAvailableNumbers()
    {
        try {
            $churchCode = config('app.church_code', 'SAL');
            $numberingService = new FamilyNumberingService($churchCode);

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
        } catch (\Exception $e) {
            Log::error('Error generating next available numbers: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'Failed to generate next available numbers',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function searchMembers(Request $request)
    {
        $query = $request->input('query', $request->input('q', '')); // Accept both 'query' and 'q'
        $limit = $request->input('limit', 10);
        $familyNo = $request->input('familyNo'); // Add this parameter
        $excludeGender = $request->input('exclude_gender'); // New parameter for gender filtering
        $includeDeceased = $request->input('include_deceased', false); // New parameter to include deceased members

        if (empty($query)) {
            return response()->json([]);
        }

        $members = Member::with(['community', 'relationship', 'gender']);

        // Only apply alive() scope if not including deceased members
        if (!$includeDeceased) {
            $members = $members->alive();
        }

        // Check if query is a numeric ID
        if (is_numeric($query)) {
            // Search by ID
            $members = $members->where('id', $query);
        } else {
            // Search by name, member number, or family number (existing logic)
            if (strlen($query) < 2) {
                return response()->json([]);
            }

            // Split query into words for flexible matching
            // e.g., "Raymond Menezes" matches "Raymond Joseph Menezes"
            $searchWords = preg_split('/\s+/', trim($query));

            if (count($searchWords) > 1) {
                // Multi-word search: each word must match any name field
                $members = $members->where(function ($q) use ($searchWords) {
                    foreach ($searchWords as $word) {
                        $q->where(function ($subQ) use ($word) {
                            $subQ->where('first_name', 'like', "%{$word}%")
                                ->orWhere('middle_name', 'like', "%{$word}%")
                                ->orWhere('last_name', 'like', "%{$word}%");
                        });
                    }
                });
            } else {
                // Single word search: match any field including member_no, family_no
                $members = $members->where(function ($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                        ->orWhere('middle_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?", ["%{$query}%"])
                        ->orWhere('member_no', 'like', "%{$query}%")
                        ->orWhere('family_no', 'like', "%{$query}%");
                });
            }
        }

        // Apply family number filter if provided
        if ($familyNo) {
            $members = $members->where('family_no', $familyNo);
        }

        // Apply gender filter if provided (for spouse search)
        if ($excludeGender) {
            $members = $members->whereHas('gender', function ($q) use ($excludeGender) {
                $q->where('name', '!=', $excludeGender);
            });
        }

        // Only exclude member if exclude_id is provided
        if ($request->has('exclude_id') && $request->input('exclude_id')) {
            $members = $members->where('id', '!=', $request->input('exclude_id'));
        }

        $members = $members->limit($limit)
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
                    'current_add1' => $member->current_add1,
                    'contact_no_1' => $member->contact_no_1,
                    'relationship' => $member->relationship->name ?? '',
                    'gender' => $member->gender->name ?? '',
                ];
            });

        return response()->json($members);
    }

    /**
     * Search members for SearchDropdown component
     * Returns data in format: [{ id, name }]
     */
    public function search(Request $request)
    {
        $query = $request->input('search', '');
        $status = $request->input('status', 'alive'); // Default to 'alive', can be 'deceased'

        if (empty($query)) {
            return response()->json([]);
        }

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $membersQuery = Member::with(['community', 'baptismRecord', 'marriageRecord']);

        // Apply status filter conditionally
        if ($status === 'deceased') {
            $membersQuery->deceased();
        } else {
            $membersQuery->alive();
        }

        // Split query into words for flexible matching
        // e.g., "Raymond Menezes" matches "Raymond Joseph Menezes"
        $searchWords = preg_split('/\s+/', trim($query));

        if (count($searchWords) > 1) {
            // Multi-word search: each word must match any name field
            $membersQuery->where(function ($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->where(function ($subQ) use ($word) {
                        $subQ->where('first_name', 'like', "%{$word}%")
                            ->orWhere('middle_name', 'like', "%{$word}%")
                            ->orWhere('last_name', 'like', "%{$word}%");
                    });
                }
            });
        } else {
            // Single word search: match any field including member_no, family_no
            $membersQuery->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('middle_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?", ["%{$query}%"])
                    ->orWhere('member_no', 'like', "%{$query}%")
                    ->orWhere('family_no', 'like', "%{$query}%");
            });
        }

        $members = $membersQuery
            ->limit(10)
            ->get()
            ->map(function ($member) {
                $communityName = $member->community->name ?? '';
                return [
                    'id' => $member->id,
                    'name' => "{$member->first_name} {$member->last_name} ({$member->member_no}) - {$communityName}",
                    'date_of_birth' => $member->date_of_birth,
                    'baptism_date' => $member->baptismRecord?->baptism_date,
                    'baptism_reg_no' => $member->baptismRecord?->baptism_reg_no,
                    'baptism_parish_id' => $member->baptism_parish_id,
                    'marriage_date' => $member->marriageRecord?->marriage_date,
                    'marriage_reg_no' => $member->marriageRecord?->marriage_reg_no,
                ];
            });

        return response()->json($members);
    }

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
                'permanentTown:id,name',
                'currentTown:id,name',
                'permanentCity:id,name',
                'currentCity:id,name',
                'permanentState:id,name',
                'currentState:id,name',
                'permanentCountry:id,name',
                'currentCountry:id,name',
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
            Log::error('Error getting member details: ' . $e->getMessage());
            return response()->json(['error' => 'Member not found'], 404);
        }
    }

    public function dataVerification()
    {
        // Debug authentication
        if (!Auth::check()) {
            Log::error('User not authenticated for data verification page');
            abort(401, 'Unauthenticated');
        }

        $user = Auth::user();

        // Check permission to access data verification
        if (!Gate::allows('read-data-verification')) {
            Log::warning('User denied access to data verification page', [
                'user_id' => $user->id,
                'email' => $user->email,
                'permissions' => Auth::getAllPermissionsAttribute()
            ]);
            abort(403, 'Access denied. You do not have permission to view data verification.');
        }

        // Load data directly instead of via AJAX
        $members = Member::with([
            'community:id,name',
            'status:id,name',
            'relationship:id,name'
        ])
            ->select([
                'id',
                'first_name',
                'middle_name',
                'last_name',
                'date_of_birth',
                'old_sal_id',
                'community_id',
                'status_id',
                'contact_no_1',
                'contact_no_2',
                'relationship_id'
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

        $communities = Community::select('id', 'name')->orderBy('name')->get();
        $statuses = Status::select('id', 'name')->orderBy('name')->get();
        $relationships = Relationship::select('id', 'name')->orderBy('name')->get();

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
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $user = Auth::user();

        // Check permission to update data verification
        if (!Gate::allows('update-data-verification')) {
            Log::warning('User denied access to bulk update', [
                'user_id' => $user->id,
                'email' => $user->email,
                'permissions' => Auth::getAllPermissionsAttribute()
            ]);
            return response()->json(['error' => 'Access denied. You do not have permission to update data verification.'], 403);
        }

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
                    'first_name',
                    'middle_name',
                    'last_name',
                    'date_of_birth',
                    'old_sal_id',
                    'community_id',
                    'status_id',
                    'contact_no_1',
                    'contact_no_2',
                    'relationship_id'
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

    /**
     * Upload a family photo
     */
    public function uploadFamilyPhoto(FamilyPhotoUploadRequest $request, string $familyNo)
    {
        $familyPhotoService = new FamilyPhotoService(new \App\Services\SecurityService());
        $result = $familyPhotoService->uploadFamilyPhoto($familyNo, $request->file('photo'));

        if ($result['success']) {
            return response()->json($result, 200);
        }

        return response()->json($result, 400);
    }

    /**
     * Delete a family photo
     */
    public function deleteFamilyPhoto(string $familyNo)
    {
        // Get a member from the family to check authorization
        $member = Member::where('family_no', $familyNo)->firstOrFail();
        $this->authorize('update', $member);

        $familyPhotoService = new FamilyPhotoService(new \App\Services\SecurityService());
        $result = $familyPhotoService->deleteFamilyPhoto($familyNo);

        if ($result['success']) {
            return response()->json($result, 200);
        }

        return response()->json($result, 400);
    }

    /**
     * Get family photo
     */
    public function getFamilyPhoto(string $familyNo)
    {
        // Get a member from the family to check authorization
        $member = Member::where('family_no', $familyNo)->firstOrFail();
        $this->authorize('view', $member);

        $familyPhotoService = new FamilyPhotoService(new \App\Services\SecurityService());
        $photo = $familyPhotoService->getFamilyPhoto($familyNo);

        if ($photo) {
            return response()->json([
                'success' => true,
                'data' => [
                    'photo' => $photo,
                    'url' => $photo->photo_url
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No family photo found'
        ], 404);
    }

    /**
     * Get family photo statistics
     */
    public function getFamilyPhotoStatistics()
    {
        $this->authorize('viewAny', Member::class);

        $familyPhotoService = new FamilyPhotoService(new \App\Services\SecurityService());
        $statistics = $familyPhotoService->getPhotoStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Handle family transition for new members with spouse (gender-based)
     */
    private function handleMarriageTransitionForNew(array &$data)
    {
        if (!$data['spouse_id']) {
            return;
        }

        // Get the spouse with their gender
        $spouse = Member::with('gender')->find($data['spouse_id']);
        Log::info('spouse:', $spouse);
        if (!$spouse) {
            return;
        }

        // Get genders - for new members, we need to get gender from the data
        $newMemberGenderId = $data['gender_id'] ?? null;
        $newMemberGender = null;
        if ($newMemberGenderId) {
            $gender = \Modules\Members\Models\Gender::find($newMemberGenderId);
            $newMemberGender = $gender->name ?? null;
        }
        $spouseGender = $spouse->gender->name ?? null;

        // Apply gender-based family transition logic
        if ($newMemberGender === 'Female' && ($spouseGender === 'Male' || $spouseGender === 'Other')) {
            // Female new member joins male/other spouse's family
            $this->processNewMemberFamilyTransition($data, $spouse, 'female_joins_male');
        } elseif ($newMemberGender === 'Male' && $spouseGender === 'Female') {
            // Male new member keeps his family, female spouse joins his family
            $this->updateSpouseToJoinNewMemberFamily($spouse, $data);
            // Male new member keeps his original family assignment
            $data['marital_status'] = 'Married';
        } elseif ($newMemberGender === 'Other' && $spouseGender === 'Female') {
            // Other gender new member keeps family, female spouse joins
            $this->updateSpouseToJoinNewMemberFamily($spouse, $data);
            $data['marital_status'] = 'Married';
        } else {
            // Same gender or other combinations - default to new member joining spouse's family
            $this->processNewMemberFamilyTransition($data, $spouse, 'default');
        }

        // Update spouse's marital status to married
        $spouse->marital_status = 'Married';
        $spouse->save();
    }

    private function processNewMemberFamilyTransition(array &$data, Member $spouse, string $transitionType)
    {
        // Store birth family (will be the family they would have been assigned)
        $birthFamilyNo = $data['family_no'] ?? null;
        $data['birth_family_no'] = $birthFamilyNo;

        // Join spouse's family
        $data['family_no'] = $spouse->family_no;

        // Don't set birth family relationships for new members joining spouse
        $data['father_id'] = null;
        $data['mother_id'] = null;

        // Set marital status to married
        $data['marital_status'] = 'Married';

        // Set appropriate relationship based on gender
        $this->setNewMemberSpouseRelationship($data);

        // Note: External member record will be created after the member is saved
        // Store the birth family info for later use
        $data['_create_external_member'] = [
            'birth_family_no' => $birthFamilyNo,
            'spouse' => $spouse,
            'transition_type' => $transitionType
        ];
    }

    /**
     * Create external member record for family tree genealogy
     */
    private function createExternalMemberRecord(Member $personJoining, string $birthFamilyNo, Member $spouse)
    {
        try {
            // 1. Create external member record for the person joining (existing logic)
            $personJoiningExternal = $this->createPersonJoiningExternalRecord($personJoining, $birthFamilyNo, $spouse);

            // 2. Create external member record for the spouse in the birth family (new bidirectional logic)
            $spouseExternal = $this->createSpouseExternalRecord($spouse, $birthFamilyNo, $personJoining);

            // 3. Update spouse_id references to point to external member IDs for proper family tree relationships
            $this->updateExternalSpouseReferences($personJoiningExternal, $spouseExternal);
        } catch (\Exception $e) {
            Log::error('Failed to create external member records', [
                'member_id' => $personJoining->id,
                'birth_family_no' => $birthFamilyNo,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create external member record for the person joining another family
     */
    private function createPersonJoiningExternalRecord(Member $personJoining, string $birthFamilyNo, Member $spouse): ?ExternalMember
    {
        // Check if external member record already exists
        $existingExternal = ExternalMember::where('family_no', $birthFamilyNo)
            ->where('first_name', $personJoining->first_name)
            ->where('last_name', $personJoining->last_name)
            ->first();

        // Get current address from the member
        $address = collect([
            $personJoining->current_add1,
            $personJoining->current_add2,
            $personJoining->current_add3
        ])->filter()->implode(', ');

        // Create external member record (spouse_id will be updated later to point to external member)
        $externalMember = ExternalMember::create([
            'first_name' => $personJoining->first_name,
            'last_name' => $personJoining->last_name,
            'address' => $address ?: 'Married to ' . $spouse->first_name . ' ' . $spouse->last_name,
            'family_no' => $birthFamilyNo, // The birth family can see her in their tree
            'community_id' => $personJoining->community_id,
            'relationship_id' => $personJoining->relationship_id, // Keep original relationship in birth family
            'gender_id' => $personJoining->gender_id,
            'spouse_id' => null, // Will be updated to external member ID after spouse external record is created
            'spouse_source' => 'External', // Spouse will be external member
            'father_id' => $personJoining->father_id, // Original birth parents
            'mother_id' => $personJoining->mother_id,
            'father_source' => $personJoining->father_source ?? 'Member',
            'mother_source' => $personJoining->mother_source ?? 'Member',
        ]);

        return $externalMember;
    }

    /**
     * Create external member record for the spouse in the birth family (bidirectional relationship)
     */
    private function createSpouseExternalRecord(Member $spouse, string $birthFamilyNo, Member $personJoining): ?ExternalMember
    {
        // Only add spouse as external member if they're from a different family_no
        // This prevents creating external records for couples who were already in the same family from bulk data
        if ($spouse->family_no === $birthFamilyNo) {
            Log::info('Skipping spouse external member creation - same family_no', [
                'spouse_family_no' => $spouse->family_no,
                'birth_family_no' => $birthFamilyNo,
                'spouse_id' => $spouse->id,
                'person_joining_id' => $personJoining->id
            ]);
            return null;
        }
        // Check if external member record already exists for the spouse in the birth family
        $existingSpouseExternal = ExternalMember::where('family_no', $birthFamilyNo)
            ->where('first_name', $spouse->first_name)
            ->where('last_name', $spouse->last_name)
            ->first();

        // Get spouse's current address
        $spouseAddress = collect([
            $spouse->current_add1,
            $spouse->current_add2,
            $spouse->current_add3
        ])->filter()->implode(', ');

        // Determine spouse relationship in birth family context
        $spouseGender = $spouse->gender->name ?? null;
        $spouseRelationshipName = null;

        if ($spouseGender === 'Male') {
            $spouseRelationshipName = 'Son-in-law';
        } elseif ($spouseGender === 'Female') {
            $spouseRelationshipName = 'Daughter-in-law';
        } else {
            $spouseRelationshipName = 'In-law';
        }

        // Find the appropriate relationship
        $spouseRelationship = Relationship::where('name', 'LIKE', "%{$spouseRelationshipName}%")
            ->orWhere('name', 'LIKE', '%in-law%')
            ->first();

        // Create external member record for spouse in the birth family (spouse_id will be updated later)
        $spouseExternalMember = ExternalMember::create([
            'first_name' => $spouse->first_name,
            'last_name' => $spouse->last_name,
            'address' => $spouseAddress ?: 'Married to ' . $personJoining->first_name . ' ' . $personJoining->last_name,
            'family_no' => $birthFamilyNo, // Birth family can see the spouse in their tree
            'community_id' => $spouse->community_id,
            'relationship_id' => $spouseRelationship?->id, // Son-in-law/Daughter-in-law/In-law relationship
            'gender_id' => $spouse->gender_id,
            'spouse_id' => null, // Will be updated to external member ID after person joining external record is created
            'spouse_source' => 'External', // Person joining will be external member
            'father_id' => null, // Spouse's parents are not part of this birth family tree
            'mother_id' => null,
            'father_source' => null,
            'mother_source' => null,
        ]);
        return $spouseExternalMember;
    }

    /**
     * Update spouse_id references in external members to point to their respective external member IDs
     */
    private function updateExternalSpouseReferences(?ExternalMember $personJoiningExternal, ?ExternalMember $spouseExternal): void
    {
        try {
            // Update person joining external member to point to spouse external member ID
            $personJoiningExternal->spouse_id = $spouseExternal->id;
            $personJoiningExternal->save();

            // Update spouse external member to point to person joining external member ID
            $spouseExternal->spouse_id = $personJoiningExternal->id;
            $spouseExternal->save();
        } catch (\Exception $e) {
            Log::error('Failed to update external spouse references', [
                'person_joining_external_id' => $personJoiningExternal->id,
                'spouse_external_id' => $spouseExternal->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function updateSpouseToJoinNewMemberFamily(Member $spouse, array &$data)
    {
        // Store spouse's birth family if not already stored
        $birthFamilyNo = $spouse->family_no;
        if (!$spouse->birth_family_no) {
            $spouse->birth_family_no = $birthFamilyNo;
        }

        // Create external member record for the spouse's birth family
        $spouseGender = $spouse->gender->name ?? null;
        if ($spouseGender === 'Female') {
            // Create a temporary Member object for the new member to pass to createExternalMemberRecord
            $newMember = new Member($data);
            $newMember->id = null; // Will be set after save
            $this->createExternalMemberRecord($spouse, $birthFamilyNo, $newMember);
        }

        // Spouse joins the new member's family
        $spouse->family_no = $data['family_no'];
        $spouse->father_id = null;
        $spouse->mother_id = null;
        $spouse->marital_status = 'Married';

        // Set bidirectional spouse relationship (spouse will be updated after member is created)
        // Note: The new member's spouse_id will be set in the $data array

        // Set spouse's relationship
        if ($spouseGender === 'Female') {
            $relationshipName = 'Wife';
        } else {
            $relationshipName = 'Spouse';
        }

        $spouseRelationship = Relationship::where('name', 'LIKE', "%{$relationshipName}%")
            ->orWhere('name', 'LIKE', '%spouse%')
            ->first();

        if ($spouseRelationship) {
            $spouse->relationship_id = $spouseRelationship->id;
        }

        $spouse->save();
    }

    private function setNewMemberSpouseRelationship(array &$data)
    {
        $newMemberGenderId = $data['gender_id'] ?? null;
        $newMemberGender = null;
        if ($newMemberGenderId) {
            $gender = \Modules\Members\Models\Gender::find($newMemberGenderId);
            $newMemberGender = $gender->name ?? null;
        }

        // Set relationship based on gender
        if ($newMemberGender === 'Female') {
            $relationshipName = 'Wife';
        } elseif ($newMemberGender === 'Male') {
            $relationshipName = 'Husband';
        } else {
            $relationshipName = 'Spouse';
        }

        $spouseRelationship = Relationship::where('name', 'LIKE', "%{$relationshipName}%")
            ->orWhere('name', 'LIKE', '%spouse%')
            ->first();

        if ($spouseRelationship) {
            $data['relationship_id'] = $spouseRelationship->id;
        }
    }

    /**
     * Handle family transition when spouse is assigned
     */
    private function handleMarriageTransition(Member $member, array &$validated)
    {

        // Get the spouse with their gender
        $spouse = Member::with('gender')->find($validated['spouse_id']);

        if (!$spouse) {
            return;
        }

        // If both spouses already have the same family_no, no family transition needed
        // This handles cases where data was bulk uploaded with correct family_no relationships
        if ($member->family_no === $spouse->family_no) {
            Log::info('Skipping family transition - both spouses already have same family_no', [
                'member_id' => $member->id,
                'member_family_no' => $member->family_no,
                'spouse_id' => $spouse->id,
                'spouse_family_no' => $spouse->family_no,
                'reason' => 'Bulk uploaded data with correct family relationships'
            ]);

            // Just set marital status for both members - no family changes or external members needed
            $validated['marital_status'] = 'Married';

            // Also update the current member's marital status immediately in the database
            $member->marital_status = 'Married';
            $member->save();

            // Update spouse's marital status and bidirectional relationship
            $spouse->spouse_id = $member->id;
            $spouse->spouse_source = 'Member';
            $spouse->marital_status = 'Married';
            $spouse->save();

            return;
        }

        // Get current member's gender
        $memberGender = $member->gender->name ?? null;
        $spouseGender = $spouse->gender->name ?? null;

        Log::info('Marriage transition gender check', [
            'member_id' => $member->id,
            'member_gender' => $memberGender,
            'spouse_id' => $spouse->id,
            'spouse_gender' => $spouseGender
        ]);

        // Determine who joins whose family based on gender
        $femaleJoinsMaleFamily = true; // Traditional approach

        if ($memberGender === 'Female' && ($spouseGender === 'Male' || $spouseGender === 'Other')) {
            // Female member joins male/other spouse's family
            Log::info('Path: Female member joins male/other spouse family');
            $this->processFamilyTransition($member, $spouse, $validated, 'female_joins_male');
        } elseif ($memberGender === 'Male' && $spouseGender === 'Female') {
            // Male member stays in his family, female spouse joins his family
            Log::info('Path: Male member stays, female spouse joins his family');
            $this->processSpouseJoinsFamily($spouse, $member, 'female_joins_male');
            // Male member only gets marital status change - no family change
            $validated['marital_status'] = 'married';
        } elseif ($memberGender === 'Other' && $spouseGender === 'Female') {
            // Other gender member stays, female spouse joins
            Log::info('Path: Other gender stays, female spouse joins');
            $this->processSpouseJoinsFamily($spouse, $member, 'female_joins_male');
            $validated['marital_status'] = 'Married';
        } else {
            // Same gender or other combinations - default to member joining spouse's family
            Log::info('Path: Default - member joining spouse family', ['path' => 'default']);
            $this->processFamilyTransition($member, $spouse, $validated, 'default');
        }

        // Note: Spouse marital status and bidirectional relationship is handled in the specific transition methods
    }

    private function processFamilyTransition(Member $personJoining, Member $personStaying, array &$validated, string $transitionType)
    {
        Log::info('processFamilyTransition called', [
            'personJoining_id' => $personJoining->id,
            'personJoining_family' => $personJoining->family_no,
            'personStaying_id' => $personStaying->id,
            'personStaying_family' => $personStaying->family_no,
            'transitionType' => $transitionType
        ]);

        // Store birth family if not already stored (only for the person joining)
        $birthFamilyNo = $personJoining->family_no;
        if (!$personJoining->birth_family_no) {
            $personJoining->birth_family_no = $birthFamilyNo;
            $personJoining->save(); // Save immediately to persist birth family
        }

        // Create external member record in the birth family for genealogy tracking
        // Create for both 'female_joins_male' and 'default' to ensure family tree is complete
        Log::info('Checking if should create external member', [
            'transitionType' => $transitionType,
            'willCreate' => in_array($transitionType, ['female_joins_male', 'default'])
        ]);

        if (in_array($transitionType, ['female_joins_male', 'default'])) {
            Log::info('Creating external member records');
            $this->createExternalMemberRecord($personJoining, $birthFamilyNo, $personStaying);
        } else {
            Log::info('NOT creating external member - transition type mismatch', ['transitionType' => $transitionType]);
        }

        // If this is the current member being updated, modify their validated data
        if ($transitionType === 'female_joins_male' || $transitionType === 'default') {
            $personJoining->family_no = $personStaying->family_no;
            $personJoining->father_id = null; // Clear birth family relationships
            $personJoining->mother_id = null;
            $personJoining->marital_status = 'Married';
            $personJoining->save();

            // Set appropriate relationship
            $this->setSpouseRelationship($validated, $personJoining);
        }

        // Set bidirectional spouse relationship for the person staying
        $personStaying->spouse_id = $personJoining->id;
        $personStaying->spouse_source = 'Member';
        $personStaying->marital_status = 'Married';
        $personStaying->save();
    }

    /**
     * Process spouse joining family (when spouse needs to be updated, not the current member)
     */
    private function processSpouseJoinsFamily(Member $spouse, Member $memberStaying, string $transitionType)
    {
        // Store spouse's birth family if not already stored
        $birthFamilyNo = $spouse->family_no;
        if (!$spouse->birth_family_no) {
            $spouse->birth_family_no = $birthFamilyNo;
        }

        // Create external member record in the spouse's birth family for genealogy tracking
        if ($transitionType === 'female_joins_male') {
            $this->createExternalMemberRecord($spouse, $birthFamilyNo, $memberStaying);
        }

        // Spouse joins the member's family
        $spouse->family_no = $memberStaying->family_no;
        $spouse->father_id = null; // Clear birth family relationships
        $spouse->mother_id = null;
        $spouse->marital_status = 'Married';

        // Set bidirectional spouse relationship
        $spouse->spouse_id = $memberStaying->id;
        $spouse->spouse_source = 'Member';

        // Set appropriate relationship for spouse
        $spouseGender = $spouse->gender->name ?? null;
        if ($spouseGender === 'Female') {
            $relationshipName = 'Wife';
        } elseif ($spouseGender === 'Male') {
            $relationshipName = 'Husband';
        } else {
            $relationshipName = 'Spouse';
        }

        $spouseRelationship = Relationship::where('name', 'LIKE', "%{$relationshipName}%")
            ->orWhere('name', 'LIKE', '%spouse%')
            ->first();

        if ($spouseRelationship) {
            $spouse->relationship_id = $spouseRelationship->id;
        }

        $spouse->save();

        // Update the member staying (male member) - set marital status and bidirectional relationship
        $memberStaying->spouse_id = $spouse->id;
        $memberStaying->spouse_source = 'Member';
        $memberStaying->marital_status = 'Married';
        $memberStaying->save();

        Log::info('Spouse joined family and updated member staying', [
            'spouse_id' => $spouse->id,
            'spouse_gender' => $spouseGender,
            'old_family_no' => $birthFamilyNo,
            'new_family_no' => $spouse->family_no,
            'member_staying_id' => $memberStaying->id,
            'member_staying_marital_status' => $memberStaying->marital_status
        ]);
    }

    private function setSpouseRelationship(array &$validated, Member $member)
    {
        $memberGender = $member->gender->name ?? null;

        // Set relationship based on gender
        if ($memberGender === 'Female') {
            $relationshipName = 'Wife';
        } elseif ($memberGender === 'Male') {
            $relationshipName = 'Husband';
        } else {
            $relationshipName = 'Spouse';
        }

        $spouseRelationship = Relationship::where('name', 'LIKE', "%{$relationshipName}%")
            ->orWhere('name', 'LIKE', '%spouse%')
            ->first();

        if ($spouseRelationship) {
            $validated['relationship_id'] = $spouseRelationship->id;
        }
    }

    /**
     * Handle marriage between two members
     */
    public function handleMarriage(Request $request)
    {
        $request->validate([
            'bride_id' => 'required|integer|exists:members,id',
            'groom_id' => 'required|integer|exists:members,id',
            'bride_joins_groom' => 'boolean'
        ]);

        try {
            $churchCode = config('app.church_code', 'SAL');
            $familyNumberingService = new \Modules\Members\Services\FamilyNumberingService($churchCode);

            $result = $familyNumberingService->handleMarriage(
                $request->bride_id,
                $request->groom_id,
                $request->bride_joins_groom ?? true
            );

            return response()->json([
                'success' => true,
                'message' => 'Marriage recorded successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record marriage: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and download member details PDF
     */
    public function downloadPdf(Member $member)
    {
        // Helper to format dates
        $formatDate = function ($date, $format = 'd/m/Y') {
            if (!$date || $date === '' || $date === '0000-00-00') return null;
            try {
                if ($date instanceof \Carbon\Carbon) {
                    return $date->format($format);
                }
                return \Carbon\Carbon::parse($date)->format($format);
            } catch (\Exception $e) {
                \Log::warning("Failed to format date: {$date}", ['error' => $e->getMessage()]);
                return null;
            }
        };

        // Load all relationships
        $member->load([
            'community',
            'communityCluster.cluster',
            'status',
            'gender',
            'bloodGroup',
            'relationship',
            'parish',
            'designation',
            'incomeRange',
            'father',
            'mother',
            'spouse',
            'baptismRecord',
            'marriageRecord',
            'deathRecord',
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
        ]);

        // Prepare data for the template
        $data = [
            // Basic Information
            'member_no' => $member->member_no,
            'family_no' => $member->family_no,
            'full_name' => trim("{$member->first_name} {$member->middle_name} {$member->last_name}"),
            'first_name' => $member->first_name,
            'middle_name' => $member->middle_name ?? '',
            'last_name' => $member->last_name ?? '',
            'date_of_birth' => $formatDate($member->date_of_birth),
            'gender' => $member->gender?->name ?? '',
            'marital_status' => $member->marital_status ?? '',
            'blood_group' => $member->bloodGroup?->name ?? '',
            'relationship' => $member->relationship?->name ?? '',

            // Contact Information
            'contact_no_1' => $member->contact_no_1 ?? '',
            'contact_no_2' => $member->contact_no_2 ?? '',
            'email' => $member->email ?? '',
            'aadhar' => $member->aadhar ?? '',

            // Permanent Address
            'permanent_add1' => $member->permanent_add1 ?? '',
            'permanent_add2' => $member->permanent_add2 ?? '',
            'permanent_add3' => $member->permanent_add3 ?? '',
            'permanent_town' => $member->permanentTown?->name ?? '',
            'permanent_city' => $member->permanentCity?->name ?? $member->permanent_city ?? '',
            'permanent_state' => $member->permanentState?->name ?? '',
            'permanent_country' => $member->permanentCountry?->name ?? '',
            'permanent_pincode' => $member->permanent_pincode ?? '',

            // Current Address
            'current_add1' => $member->current_add1 ?? '',
            'current_add2' => $member->current_add2 ?? '',
            'current_add3' => $member->current_add3 ?? '',
            'current_town' => $member->currentTown?->name ?? '',
            'current_city' => $member->currentCity?->name ?? $member->current_city ?? '',
            'current_state' => $member->currentState?->name ?? '',
            'current_country' => $member->currentCountry?->name ?? '',
            'current_pincode' => $member->current_pincode ?? '',

            // Community Information
            'community' => $member->community?->name ?? '',
            'community_cluster' => $member->communityCluster?->cluster?->name ?? '',
            'parish' => $member->parish?->name ?? '',
            'status' => $member->status?->name ?? '',

            // Educational and Professional
            'school_name' => $member->school_name ?? '',
            'college_name' => $member->college_name ?? '',
            'latest_qualifications' => $member->latest_qualifications ?? '',
            'company_name' => $member->company_name ?? '',
            'designation' => $member->designation?->name ?? '',
            'income_range' => $member->incomeRange?->name ?? '',

            // Family Relations
            'father_name' => $member->father ? trim("{$member->father->first_name} {$member->father->middle_name} {$member->father->last_name}") : '',
            'mother_name' => $member->mother ? trim("{$member->mother->first_name} {$member->mother->middle_name} {$member->mother->last_name}") : '',
            'spouse_name' => $member->spouse ? trim("{$member->spouse->first_name} {$member->spouse->middle_name} {$member->spouse->last_name}") : '',

            // Sacramental Information
            'baptism_date' => $member->baptismRecord ? $formatDate($member->baptismRecord->baptism_date) : '',
            'baptism_reg_no' => $member->baptismRecord?->baptism_reg_no ?? '',
            'baptism_parish' => $member->baptismParish?->name ?? '',

            'confirmation_date' => $formatDate($member->confirmation_date),
            'confirmation_reg_no' => $member->confirmation_reg_no ?? '',
            'confirmation_parish' => $member->confirmationParish?->name ?? '',

            'marriage_date' => $member->marriageRecord ? $formatDate($member->marriageRecord->marriage_date) : '',
            'marriage_reg_no' => $member->marriageRecord?->marriage_reg_no ?? '',
            'marriage_parish' => $member->marriageParish?->name ?? '',

            'death_date' => $member->deathRecord ? $formatDate($member->deathRecord->death_date) : '',
            'death_reg_no' => $member->deathRecord?->burial_reg_no ?? '',
            'death_parish' => $member->deathParish?->name ?? '',

            // Notes
            'notes' => $member->notes ?? '',

            // Cells and Associations
            'cells_and_associations' => $member->cellsAndAssociations->pluck('name')->toArray(),

            // Parish info
            'parish_name' => config('app.parish_name', 'Church of Our Lady of Salvation'),
            'parish_address' => config('app.parish_address', 'Dadar (W), Mumbai - 400 028'),
            'parish_priest_name' => config('app.parish_priest_name', 'Parish Priest'),
            'issued_date' => now()->format('d/m/Y'),

            // Template config
            'template_config' => [
                'show_logo' => config('app.certificate_show_logo', true),
                'logo_url' => $this->makeLogoDataUrl(config('app.certificate_logo_path', 'images/logo.png')),
            ],
        ];

        // Generate filename
        $filename = sprintf(
            'Member_Details_%s_%s.pdf',
            str_replace(' ', '_', $member->first_name . '_' . $member->last_name),
            now()->format('Y-m-d')
        );

        // Generate PDF with Spatie
        $pdf = Pdf::view('members.member_details_pdf', $data)
            ->format('a4')
            ->name($filename);

        // Configure Browsershot to use Puppeteer's Chrome
        // IMPORTANT: Use config() not env() - env() doesn't work when config is cached
        $chromePath = config('app.chrome_path', '/usr/bin/google-chrome');
        if ($chromePath && file_exists($chromePath)) {
            $pdf->withBrowsershot(function ($browsershot) use ($chromePath) {
                $browsershot->setChromePath($chromePath)
                    ->noSandbox()
                    ->setOption('args', [
                        '--disable-setuid-sandbox',
                        '--disable-dev-shm-usage',
                        '--disable-gpu',
                        '--no-sandbox',
                        '--disable-crash-reporter',
                        '--disable-breakpad',
                        '--disable-extensions',
                        '--disable-sync',
                        '--no-first-run',
                        '--no-zygote',
                        '--single-process',
                        '--disable-background-networking',
                        '--disable-default-apps',
                        '--mute-audio',
                        '--user-data-dir=/tmp/chrome-user-data',
                        '--crash-dumps-dir=/tmp/chrome-user-data'
                    ]);
            });
        }

        return $pdf->download();
    }

    /**
     * Helper function to convert logo to data URL for PDF
     */
    private function makeLogoDataUrl($path)
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            $imageData = base64_encode(file_get_contents($fullPath));
            $mimeType = mime_content_type($fullPath);
            return "data:{$mimeType};base64,{$imageData}";
        }
        return null;
    }
}
