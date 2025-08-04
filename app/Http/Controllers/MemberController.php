<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\BloodGroup;
use App\Models\Community;
use App\Models\Country;
use App\Models\Designation;
use App\Models\IncomeRange;
use App\Models\Member;
use App\Models\Relationship;
use App\Models\State;
use App\Models\Town;
use App\Models\City;
use App\Models\Gender;
use App\Models\Status;
use App\Models\Parish;
use App\Models\CommunityCluster;
use App\Models\AgeGroup;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use DB;

class MemberController extends Controller
{

    public function index(Request $request) :Response
    {
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
            } else if ($filterColumnValue) {
                $query->where($filterColumnKey, 'like', "%$filterColumnValue%");
            }
        }

        // Sorting
        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
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
        
        // Get total statistics
        $totalMembers = $totalStatsQuery->count();
        $totalFamilies = $totalStatsQuery->distinct()->whereNotNull('family_no')->count('family_no');
        $averageMembersPerFamily = $totalFamilies > 0 ? round($totalMembers / $totalFamilies, 1) : 0;
        
        $familyStats = [
            'totalMembers' => $totalMembers,
            'totalFamilies' => $totalFamilies,
            'averageMembersPerFamily' => $averageMembersPerFamily
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
            'canViewAnyMember' => true,
            'canCreateMember' => true,
            'canEditMember' => true,
            'canDeleteMember' => true,
            'pagination' => [
                'currentPage' => $query->paginate($perPage)->currentPage(),
                'lastPage' => $query->paginate($perPage)->lastPage(),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
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
                    'city_id' => $item->city_id, 
                    'state_id' => $item->city->state_id ?? null, 
                    'country_id' => $item->city->state->country_id ?? null
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
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            
            // Set default values for family numbering
            $data['church_code'] = $data['church_code'] ?? config('app.church_code', 'SAL');
            $data['registration_year'] = $data['registration_year'] ?? date('Y');
            $data['marital_status'] = $data['marital_status'] ?? 'single';
            
            // Check if this is a new family or existing family
            if ($request->has('existing_family_no') && $request->existing_family_no) {
                $familyNo = $request->existing_family_no;
                
                $numberingService = new \App\Services\FamilyNumberingService();
                
                // Validate existing family number
                if (!$numberingService->validateFamilyNumber($familyNo)) {
                    throw new \Exception('Invalid family number format. Expected: SAL-XXX');
                }
                
                // Check if family actually exists in database
                $existingFamily = Member::where('family_no', $familyNo)->first();
                if (!$existingFamily) {
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
            
            $member = Member::create($data);
            
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
            ))->with('success', 'Member created successfully with Family No: ' . $member->family_no);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function show(Member $member): Response
    {
        return Inertia::render('member/Member', [
            'member' => $member
        ]);
    }

    public function edit(Member $member)
    {
        $incomeRanges = IncomeRange::all()->map(function ($item) {
            return ['id' => $item->id, 'name' => $item->name];
        })->toArray();
        return Inertia::render('member/Member', [
            'member' => $member,
            'communities' => Community::all(),
            'parishes' => Parish::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            }),
            'incomeRanges' => $incomeRanges,
            'communityClusters' => CommunityCluster::with('cluster')->get()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->cluster->name ?? 'Unknown Cluster', 'community_id' => $item->community_id];
            })->toArray(),
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
                    'city_id' => $item->city_id, 
                    'state_id' => $item->city->state_id ?? null, 
                    'country_id' => $item->city->state->country_id ?? null
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
        ]);
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        $validated = $request->validated();
        $member->update($validated);
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
        $member->delete();

        return redirect()->route('member.index')->with('success', 'Member deleted successfully.');
    }

    public function restore($id)
    {
        $member = Member::onlyTrashed()->findOrFail($id);
        $member->restore();
        return redirect()->route('member.index')->with('success', 'Member restored successfully.');
    }

    public function showFamilyTree($id)
    {
        $member = Member::with(['relationships.relatedMember', 'relatedMembers'])->findOrFail($id);

        return view('members.family_tree', compact('member'));
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
                'name' => $member->first_name . ' ' . $member->last_name,
            ];
        });

        return response()->json($members);
    }

    public function export(Request $request)
    {
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
            \Log::info('Member Export - Data count: ' . $data->count());
            \Log::info('Member Export - Query SQL: ' . $query->toSql());
            \Log::info('Member Export - Query bindings: ' . json_encode($query->getBindings()));

            // Transform data for export
            $exportData = [];
            foreach ($data as $item) {
                // Calculate age
                $age = '';
                if ($item->date_of_birth) {
                    $birthDate = new \DateTime($item->date_of_birth);
                    $today = new \DateTime();
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
            \Log::info('Member Export - Export data count: ' . count($exportData));
            if (count($exportData) > 0) {
                \Log::info('Member Export - First row sample: ' . json_encode($exportData[0]));
            } else {
                \Log::warning('Member Export - No data to export!');
                return response()->json(['error' => 'No data found to export'], 404);
            }

            // Create Excel file
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            if (count($exportData) > 0) {
                $headers = array_keys($exportData[0]);
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($col . '1', $header);
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                    $col++;
                }

                // Set data
                $row = 2;
                foreach ($exportData as $rowData) {
                    $col = 'A';
                    foreach ($rowData as $value) {
                        $sheet->setCellValue($col . $row, $value);
                        $col++;
                    }
                    $row++;
                }

                // Style header row
                $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
            }

            // Create writer and output
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'members_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Save to temporary file and return as download
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);
            
            \Log::info('Member Export - File created: ' . $tempFile . ', Size: ' . filesize($tempFile));
            
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend();

        } catch (\Exception $e) {
            \Log::error('Member Export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
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
            'reason' => 'nullable|string'
        ]);
        
        $numberingService = new \App\Services\FamilyNumberingService();
        
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
                'updated_at' => now()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Family moved successfully',
                'family_no' => $familyNo,
                'new_community' => \App\Models\Community::find($request->new_community_id)->name
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to move family: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleMarriage(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'spouse_id' => 'required|exists:members,id',
            'marriage_date' => 'required|date'
        ]);
        
        $numberingService = new \App\Services\FamilyNumberingService();
        
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
                'spouse' => $result['spouse']
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to record marriage: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchFamilies(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $numberingService = new \App\Services\FamilyNumberingService();
        $results = $numberingService->searchFamilies($query);
        
        return response()->json($results);
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
                    ->value('family_no')
            ]
        ]);
    }

    /**
     * Search members for spouse selection
     */
    public function searchMembers(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 10);
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        
        $members = Member::with(['community', 'relationship', 'gender'])
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
                  ->orWhere('member_no', 'like', "%{$query}%")
                  ->orWhere('family_no', 'like', "%{$query}%");
            })
            ->where('id', '!=', $request->input('exclude_id')) // Exclude current member
            ->limit($limit)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'text' => "{$member->first_name} {$member->last_name} ({$member->member_no}) - {$member->family_no}",
                    'member_no' => $member->member_no,
                    'family_no' => $member->family_no,
                    'full_name' => "{$member->first_name} {$member->last_name}",
                    'community' => $member->community->name ?? '',
                    'relationship' => $member->relationship->name ?? '',
                    'gender' => $member->gender->name ?? ''
                ];
            });
        
        return response()->json($members);
    }
}

