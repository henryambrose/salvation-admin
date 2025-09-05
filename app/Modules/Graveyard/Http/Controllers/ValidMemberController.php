<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Http\Requests\StoreValidMemberRequest;
use Modules\Graveyard\Http\Requests\UpdateValidMemberRequest;
use Modules\Graveyard\Models\ValidMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ValidMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = ValidMember::with(['member', 'permanentGrave', 'niche']);

        if ($request->boolean('isArchived')) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Filter by grave type
        if ($graveType = $request->input('grave_type')) {
            if ($graveType === 'permanent_grave') {
                $query->whereNotNull('permanent_grave_id');
            } elseif ($graveType === 'niche') {
                $query->whereNotNull('niche_id');
            }
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('aadhar_no', 'like', "%$search%")
                    ->orWhere('contact_no', 'like', "%$search%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%");
                    });
            });
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('PagesGraveyard/ValidMember/Index', [
            'data' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'perPage', 'isArchived', 'grave_type']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('PagesGraveyard/ValidMember/Create', [
            'permanentGraves' => \Modules\Graveyard\Models\PermanentGrave::select('id', 'grave_no', 'section', 'row_no')->get(),
            'niches' => \Modules\Graveyard\Models\Niche::select('id', 'niche_no', 'location')->get(),
        ]);
    }

    /**
     * Search for permanent graves or niches based on grave type
     */
    public function searchGraves(Request $request)
    {
        $request->validate([
            'search_term' => 'required|string|min:2',
            'grave_type' => 'required|in:permanent_grave,niche'
        ]);

        $searchTerm = $request->search_term;
        $graveType = $request->grave_type;

        if ($graveType === 'permanent_grave') {
            $query = \Modules\Graveyard\Models\PermanentGrave::with(['member']);

            $query->where(function ($q) use ($searchTerm) {
                $q->where('owner_name', 'like', "%{$searchTerm}%")
                    ->orWhere('contact_no', 'like', "%{$searchTerm}%")
                    ->orWhere('grave_no', 'like', "%{$searchTerm}%")
                    ->orWhere('oldno', 'like', "%{$searchTerm}%")
                    ->orWhereHas('member', function ($memberQuery) use ($searchTerm) {
                        $memberQuery->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
                    });
            });

            $results = $query->get()->map(function ($grave) {
                return [
                    'id' => $grave->id,
                    'type' => 'permanent_grave',
                    'display_name' => "Grave {$grave->grave_no} ({$grave->section}, Row {$grave->row_no})",
                    'details' => [
                        'grave_no' => $grave->grave_no,
                        'section' => $grave->section,
                        'row_no' => $grave->row_no,
                        'owner_name' => $grave->owner_name,
                        'contact_no' => $grave->contact_no,
                        'member' => $grave->member ? [
                            'full_name' => $grave->member->first_name . ' ' . $grave->member->last_name,
                            'family_no' => $grave->member->family_no
                        ] : null
                    ]
                ];
            });
        } else { // niche
            $query = \Modules\Graveyard\Models\Niche::with(['member']);

            $query->where(function ($q) use ($searchTerm) {
                $q->where('niche_no', 'like', "%{$searchTerm}%")
                    ->orWhere('location', 'like', "%{$searchTerm}%")
                    ->orWhere('owner_name', 'like', "%{$searchTerm}%")
                    ->orWhere('contact_no', 'like', "%{$searchTerm}%")
                    ->orWhereHas('member', function ($memberQuery) use ($searchTerm) {
                        $memberQuery->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
                    });
            });

            $results = $query->get()->map(function ($niche) {
                return [
                    'id' => $niche->id,
                    'type' => 'niche',
                    'display_name' => "Niche {$niche->niche_no} ({$niche->location})",
                    'details' => [
                        'niche_no' => $niche->niche_no,
                        'location' => $niche->location,
                        'owner_name' => $niche->owner_name,
                        'contact_no' => $niche->contact_no,
                        'member' => $niche->member ? [
                            'full_name' => $niche->member->first_name . ' ' . $niche->member->last_name,
                            'family_no' => $niche->member->family_no
                        ] : null
                    ]
                ];
            });
        }

        return response()->json([
            'results' => $results,
            'count' => $results->count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreValidMemberRequest $request)
    {
        $validatedData = $request->validated();
        // Check if the grave already has 5 members

        $existingCount = ValidMember::where(function ($query) use ($validatedData) {
            if ($validatedData['grave_type'] === 'permanent_grave') {
                $query->where('permanent_grave_id', $validatedData['permanent_grave_id']);
            } else {
                $query->where('niche_id', $validatedData['niche_id']);
            }
        })->count();

        if ($existingCount + count($validatedData['members']) > 5) {
            return redirect()->back()
                ->withErrors(['members' => 'This grave can only have maximum 5 valid members. Currently has ' . $existingCount . ' members.'])
                ->withInput();
        }

        $duplicates = [];
        $createdMembers = [];

        foreach ($validatedData['members'] as $memberData) {
            // Check for duplicates
            $duplicateCheck = $this->checkForDuplicates($memberData);
            if ($duplicateCheck) {
                $duplicates[] = $duplicateCheck;
                continue;
            }

            // Prepare member data for creation
            $memberToCreate = [
                'permanent_grave_id' => $validatedData['grave_type'] === 'permanent_grave' ? $validatedData['permanent_grave_id'] : null,
                'niche_id' => $validatedData['grave_type'] === 'niche' ? $validatedData['niche_id'] : null,
                'grave_type' => $validatedData['grave_type'],
            ];

            if ($memberData['member_type'] === 'member') {
                $memberToCreate['member_id'] = $memberData['member_id'];
                // Get parish member data for external fields
                $parishMember = \Modules\Members\Models\Member::find($memberData['member_id']);
                $memberToCreate['first_name'] = $parishMember->first_name;
                $memberToCreate['last_name'] = $parishMember->last_name;
                $memberToCreate['contact_no'] = $memberData['contact_no'] ?? $parishMember->contact_no_1;
                $memberToCreate['member_type'] = $memberData['member_type'];
            } else {
                $memberToCreate['member_id'] = null;
                $memberToCreate['first_name'] = $memberData['first_name'];
                $memberToCreate['last_name'] = $memberData['last_name'];
                $memberToCreate['contact_no'] = $memberData['contact_no'];
                $memberToCreate['aadhar_no'] = $memberData['aadhar_no'];
                $memberToCreate['member_type'] = $memberData['member_type'];
            }
            Log::info('MemberData: ', $memberData);
            Log::info('Member to create: ', $memberToCreate);
            $createdMembers[] = ValidMember::create($memberToCreate);
        }

        $message = count($createdMembers) . ' Valid Member(s) created successfully.';
        if (!empty($duplicates)) {
            $message .= ' ' . count($duplicates) . ' duplicate(s) were skipped: ' . implode(', ', $duplicates);
        }

        return redirect()->route('graveyard.valid-members.index')
            ->with('success', $message);
    }

    /**
     * Check for duplicate members
     */
    private function checkForDuplicates($memberData)
    {
        if ($memberData['member_type'] === 'member') {
            $existing = ValidMember::where('member_id', $memberData['member_id'])->first();
            if ($existing) {
                $member = \Modules\Members\Models\Member::find($memberData['member_id']);
                return $member->first_name . ' ' . $member->last_name . ' (Parish Member)';
            }
        } else {
            // Check for external member duplicates by aadhar or name combination
            if (!empty($memberData['aadhar_no'])) {
                $existing = ValidMember::where('aadhar_no', $memberData['aadhar_no'])
                    ->whereNotNull('aadhar_no')
                    ->first();
                if ($existing) {
                    return $memberData['first_name'] . ' ' . $memberData['last_name'] . ' (Aadhar: ' . $memberData['aadhar_no'] . ')';
                }
            }
        }

        return null;
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
        $validMember->load(['member', 'permanentGrave', 'niche']);

        return Inertia::render('PagesGraveyard/ValidMember/Edit', [
            'validMember' => $validMember,
            'permanentGraves' => \Modules\Graveyard\Models\PermanentGrave::select('id', 'grave_no', 'section', 'row_no')->get(),
            'niches' => \Modules\Graveyard\Models\Niche::select('id', 'niche_no', 'location')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValidMemberRequest $request, ValidMember $validMember)
    {
        $validatedData = $request->validated();

        // Check for duplicates (excluding current member)
        if ($validatedData['member_type'] === 'member') {
            $existing = ValidMember::where('member_id', $validatedData['member_id'])
                ->where('id', '!=', $validMember->id)
                ->first();
            if ($existing) {
                $member = \Modules\Members\Models\Member::find($validatedData['member_id']);
                return redirect()->back()
                    ->withErrors(['member_id' => $member->first_name . ' ' . $member->last_name . ' is already a valid member of another grave.'])
                    ->withInput();
            }
        }

        // Prepare update data
        $updateData = [
            'permanent_grave_id' => $validatedData['grave_type'] === 'permanent_grave' ? $validatedData['permanent_grave_id'] : null,
            'niche_id' => $validatedData['grave_type'] === 'niche' ? $validatedData['niche_id'] : null,
            'grave_type' => $validatedData['grave_type'],
        ];

        if ($validatedData['member_type'] === 'member') {
            $parishMember = \Modules\Members\Models\Member::find($validatedData['member_id']);
            $updateData['member_id'] = $validatedData['member_id'];
            $updateData['first_name'] = $parishMember->first_name;
            $updateData['last_name'] = $parishMember->last_name;
            $updateData['contact_no'] = $validatedData['contact_no'] ?? $parishMember->contact_no;
            $updateData['aadhar_no'] = null; // Clear external member data
        } else {
            $updateData['member_id'] = null;
            $updateData['first_name'] = $validatedData['first_name'];
            $updateData['last_name'] = $validatedData['last_name'];
            $updateData['contact_no'] = $validatedData['contact_no'];
            $updateData['aadhar_no'] = $validatedData['aadhar_no'];
        }

        $validMember->update($updateData);

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

    /**
     * Search for parish members for the frontend dropdown.
     */
    public function searchMembers(Request $request)
    {
        $search = $request->input('search');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        // Get members who are not already valid members of any grave
        $existingMemberIds = ValidMember::whereNotNull('member_id')->pluck('member_id');

        $members = \Modules\Members\Models\Member::whereNotIn('id', $existingMemberIds)
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('family_no', 'like', "%$search%");
            })
            ->select('id', 'first_name', 'last_name', 'family_no', 'contact_no_1')
            ->limit(20)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->first_name . ' ' . $member->last_name,
                    'family_number' => $member->family_no,
                    'contact_no' => $member->contact_no_1,
                ];
            });

        return response()->json($members);
    }
}
