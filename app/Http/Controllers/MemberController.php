<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\BloodGroup;
use App\Models\CellsAndAssociation;
use App\Models\Community;
use App\Models\Country;
use App\Models\Designation;
use App\Models\FamilyIncomeRange;
use App\Models\Member;
use App\Models\Relationship;
use App\Models\State;
use App\Models\Town;
use App\Models\Gender;
use App\Models\Status;
use App\Models\Parish;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{

    public function index(Request $request) :Response
    {
        $dropdownColumns = [
            'community_id' => ['relation' => 'community', 'column' => 'name'],
            'community_cluster_id' => ['relation' => 'communityCluster', 'column' => 'name'],
            'blood_group_id' => ['relation' => 'bloodGroup', 'column' => 'name'],
            'cells_and_association_id' => ['relation' => 'cellsAndAssociation', 'column' => 'name'],
            'family_income_range_id' => ['relation' => 'familyIncomeRange', 'column' => 'name'],
            'designation_id' => ['relation' => 'designation', 'column' => 'name'],
            'gender_id' => ['relation' => 'gender', 'column' => 'name'],
            'status_id' => ['relation' => 'status', 'column' => 'name'],
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
            'communityCluster',
            'cellsAndAssociation',
            'relationship',
            'bloodGroup',
            'designation',
            'familyIncomeRange',
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
                  })
                  ->orWhereHas('communityCluster', function ($q3) use ($search) {
                      $q3->where('name', 'like', "%$search%");
                  });
            });
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

        // Get total count before pagination
        $totalCount = $query->count();

        $data = $query->paginate($perPage)->appends($request->query());
        $data->getCollection()->transform(function ($item) use ($dropdownColumns) {
            foreach ($dropdownColumns as $key => $relation) {
                if (isset($item->{$relation['relation']})) {
                    $item->$key = $item->{$relation['relation']}->{$relation['column']} ?? '';
                } else {
                    $item->$key = '';
                }
            }
            return $item;
        });

        return Inertia::render('member/Index', [
            'communities' => Community::all(),
            'relationships' => Relationship::all(),
            'ageGroups' => \App\Models\AgeGroup::all(),
            'bloodGroups' => BloodGroup::all(),
            'genders' => Gender::all(),
            'fetchUrl' => route('member.index'),
            'members' => $data,
            'totalCount' => $totalCount,
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
            'communities' => Community::with('communityClusters')->get(),
            'cellsAndAssociations' => CellsAndAssociation::all(),
            'familyIncomeRanges' => FamilyIncomeRange::all()->map(function ($item) {
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
            'towns' => Town::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name, 'state_id' => $item->state_id, 'country_id' => $item->country_id];
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $validated = $request->validated();
        $member = Member::create($validated);
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
        ))->with('success', 'Member created successfully.');
    }


    public function show(Member $member): Response
    {
        return Inertia::render('member/Member', [
            'member' => $member
        ]);
    }

    public function edit(Member $member)
    {
        $familyIncomeRanges = FamilyIncomeRange::all()->map(function ($item) {
            return ['id' => $item->id, 'name' => $item->name];
        })->toArray();
        return Inertia::render('member/Member', [
            'member' => $member,
            'communities' => Community::with('communityClusters')->get(),
            'cellsAndAssociations' => CellsAndAssociation::all(),
            'familyIncomeRanges' => $familyIncomeRanges,
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
            'towns' => Town::all()->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name, 'state_id' => $item->state_id, 'country_id' => $item->country_id];
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

    public function exportxls(Request $request)
    {
        $format = $request->input('format', 'csv');
        
        // Build the same query as in index method
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
            'cellsAndAssociation',
            'relationship',
            'bloodGroup',
            'designation',
            'familyIncomeRange',
            'status',
            'gender',
        ]);

        // Apply all filters
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                  ->orWhere('family_no', 'like', "%$search%")
                  ->orWhereHas('community', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('communityCluster', function ($q3) use ($search) {
                      $q3->where('name', 'like', "%$search%");
                  });
            });
        }

        if ($communityId = $request->input('communityId')) {
            $query->where('community_id', $communityId);
        }

        if ($relationship = $request->input('relationship')) {
            $query->where('relationship_id', $relationship);
        }

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

        if ($bloodGroup = $request->input('bloodGroup')) {
            $query->where('blood_group_id', $bloodGroup);
        }

        if ($gender = $request->input('gender')) {
            $query->where('gender_id', $gender);
        }

        $members = $query->get();
         // Prepare data for export
        $exportData = [];
        $headers = [
            'ID', 'First Name', 'Last Name', 'Family No', 'Contact No', 
            'Email', 'Date of Birth', 'Age', 'Community', 'Cluster', 'Relationship',
            'Blood Group', 'Gender', 'Status', 'Created At', 'Updated At'
        ];

        foreach ($members as $member) {
            // Calculate age
            $age = '';
            if ($member->date_of_birth) {
                $birthDate = new \DateTime($member->date_of_birth);
                $today = new \DateTime();
                $age = $today->diff($birthDate)->y;
            }
  
            $exportData[] = [
                $member->id,
                $member->first_name ?? '',
                $member->last_name ?? '',
                $member->family_no ?? '',
                $member->contact_no ?? '',
                $member->email ?? '',
                $member->date_of_birth ?? '',
                $age,
                $member->community?->name ?? '',
                $member->communityCluster?->name ?? '',
                $member->relationship?->name ?? '',
                $member->bloodGroup?->name ?? '',
                $member->gender?->name ?? '',
                $member->status?->name ?? '',
                $member->created_at ?? '',
                $member->updated_at ?? '',
            ];
        }
        if ($format === 'csv') {
            return $this->exportToCsv($headers, $exportData, 'members.csv');
        } else {
            return $this->exportToXls($headers, $exportData, 'members.xlsx');
        }
    }

    // private function exportToCsv($headers, $data, $filename)
    // {
    //     $handle = fopen('php://temp', 'r+');
        
    //     // Add headers
    //     fputcsv($handle, $headers);
        
    //     // Add data
    //     foreach ($data as $row) {
    //         fputcsv($handle, $row);
    //     }
        
    //     rewind($handle);
    //     $csv = stream_get_contents($handle);
    //     fclose($handle);
        
    //     return response($csv)
    //         ->header('Content-Type', 'text/csv')
    //         ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    // }

    private function exportToXls($headers, $data, $filename)
    {
        // For XLS export, we'll use a CSV format that Excel can open
        // Excel can open CSV files directly, so we'll use .csv extension
        $filename = str_replace('.xlsx', '.csv', $filename);
        
        $handle = fopen('php://temp', 'r+');
        
        // Add headers
        fputcsv($handle, $headers);
        
        // Add data
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
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
}

