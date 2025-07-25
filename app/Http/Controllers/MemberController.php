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
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
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
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
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

        // Filter by custom column
        if ($filterColumnKey = $request->input('filterColumnKey')) {
            $filterColumnValue = $request->input('filterColumnValue');
            if ($filterColumnKey && $filterColumnValue) {
                if (in_array($filterColumnKey, array_keys($dropdownColumns))) {
                    // $query->where($filterColumnKey, $filterColumnValue);
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

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

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
            'fetchUrl' => route('member.index'),
            'members' => $data,
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'communityId', 'filterColumnKey', 'filterColumnValue', 'isArchived']),
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
}

