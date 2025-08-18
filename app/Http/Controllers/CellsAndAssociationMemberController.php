<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCellsAndAssociationMemberRequest;
use App\Http\Requests\UpdateCellsAndAssociationMemberRequest;
use App\Models\CellsAndAssociation;
use App\Models\CellsAndAssociationMember;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class CellsAndAssociationMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = CellsAndAssociationMember::with(['member.community', 'cellsAndAssociation']);

        // Handle archived records
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($search = $request->input('search')) {
            $query->whereHas('member', function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%");
            })->orWhereHas('cellsAndAssociation', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        if ($cellAssociation = $request->input('cellAssociation')) {
            $query->where('cells_and_association_id', $cellAssociation);
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        $cellsAndAssociationMembers = $query->paginate($perPage)->appends($request->query());

        // Transform data to include related names
        $cellsAndAssociationMembers->getCollection()->transform(function ($item) {
            $memberName = $item->member ? trim($item->member->first_name.' '.$item->member->last_name) : '';
            $communityName = $item->member && $item->member->community ? $item->member->community->name : 'N/A';
            $memberNo = $item->member ? $item->member->member_no : 'N/A';
            $item->member_name = $memberName.' - '.$communityName.' - '.$memberNo;
            $item->cells_and_association_name = $item->cellsAndAssociation->name ?? '';

            return $item;
        });

        return Inertia::render('cells-and-association-members/Index', [
            'fetchUrl' => route('cells-and-association-members.index'),
            'cellsAndAssociationMembers' => $cellsAndAssociationMembers,
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived', 'cellAssociation']),
            'cellsAndAssociations' => CellsAndAssociation::select('id', 'name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('cells-and-association-members/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCellsAndAssociationMemberRequest $request)
    {
        $validated = $request->validated();
        
        // Handle multiple cell associations
        if (is_array($validated['cells_and_association_id'])) {
            // Remove duplicates from the array
            $uniqueCellAssociationIds = array_unique($validated['cells_and_association_id']);
            
            // Check for existing associations to prevent duplicates
            $existingAssociations = CellsAndAssociationMember::where('member_id', $validated['member_id'])
                ->whereIn('cells_and_association_id', $uniqueCellAssociationIds)
                ->pluck('cells_and_association_id')
                ->toArray();

            if (!empty($existingAssociations)) {
                return response()->json([
                    'message' => 'Member is already associated with some of the selected cell associations: ' . 
                        implode(', ', $existingAssociations),
                    'errors' => [
                        'cells_and_association_id' => ['Member is already associated with some of the selected cell associations: ' . 
                            implode(', ', $existingAssociations)]
                    ]
                ], 422);
            }
            
            // Use bulk insert for better performance
            $records = [];
            foreach ($uniqueCellAssociationIds as $cellAssociationId) {
                $records[] = [
                    'member_id' => $validated['member_id'],
                    'cells_and_association_id' => $cellAssociationId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if (!empty($records)) {
                CellsAndAssociationMember::insert($records);
            }
        } else {
            // Single cell association (backward compatibility)
            CellsAndAssociationMember::create($validated);
        }

        return redirect()->route('cells-and-association-members.index')->with('success', 'Cells Association Member(s) created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CellsAndAssociationMember $cellsAndAssociationMember)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CellsAndAssociationMember $cellsAndAssociationMember)
    {
        return Inertia::render('cells-and-association-members/Edit', [
            'cellsAndAssociationMember' => $cellsAndAssociationMember,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCellsAndAssociationMemberRequest $request, CellsAndAssociationMember $cellsAndAssociationMember)
    {
        $validated = $request->validated();
        
        // Handle multiple cell associations
        if (is_array($validated['cells_and_association_id'])) {
            // Remove duplicates from the array
            $uniqueCellAssociationIds = array_unique($validated['cells_and_association_id']);
            
            // Check for existing associations to prevent duplicates (excluding current record)
            $existingAssociations = CellsAndAssociationMember::where('member_id', $validated['member_id'])
                ->whereIn('cells_and_association_id', $uniqueCellAssociationIds)
                ->where('id', '!=', $cellsAndAssociationMember->id)
                ->pluck('cells_and_association_id')
                ->toArray();

            if (!empty($existingAssociations)) {
                return response()->json([
                    'message' => 'Member is already associated with some of the selected cell associations: ' . 
                        implode(', ', $existingAssociations),
                    'errors' => [
                        'cells_and_association_id' => ['Member is already associated with some of the selected cell associations: ' . 
                            implode(', ', $existingAssociations)]
                    ]
                ], 422);
            }
            
            // Delete existing record
            $cellsAndAssociationMember->delete();
            
            // Use bulk insert for better performance
            $records = [];
            foreach ($uniqueCellAssociationIds as $cellAssociationId) {
                $records[] = [
                    'member_id' => $validated['member_id'],
                    'cells_and_association_id' => $cellAssociationId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if (!empty($records)) {
                CellsAndAssociationMember::insert($records);
            }
        } else {
            // Single cell association (backward compatibility)
            $cellsAndAssociationMember->update($validated);
        }

        return redirect()->route('cells-and-association-members.index')->with('success', 'Cells Association Member(s) updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CellsAndAssociationMember $cellsAndAssociationMember)
    {
        $cellsAndAssociationMember->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('cells-and-association-members.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Cells and Association Member deleted successfully.');
    }

    public function restore($id)
    {
        $cellsAndAssociationMember = CellsAndAssociationMember::onlyTrashed()->findOrFail($id);
        $cellsAndAssociationMember->restore();

        return redirect()->route('cells-and-association-members.index')->with('success', 'Cells Association Member restored successfully.');
    }

    public function searchMembers(Request $request)
    {
        $search = $request->input('search');

        if (strlen($search) < 3) {
            return response()->json([]);
        }

        $members = Member::select('id', 'first_name', 'last_name', 'member_no', 'community_id')
            ->with('community:id,name')
            ->where(function($query) use ($search) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('member_no', 'like', "%{$search}%")
                    ->orWhereHas('community', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->limit(20)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => trim($member->first_name . ' ' . $member->last_name) . ' - ' . ($member->community->name ?? 'N/A') . ' - ' . ($member->member_no ?? 'N/A'),
                    'first_name' => $member->first_name,
                    'last_name' => $member->last_name,
                    'member_no' => $member->member_no,
                    'community_name' => $member->community->name ?? 'N/A'
                ];
            });

        return response()->json($members);
    }

    public function getMemberById($id)
    {
        $member = Member::with('community:id,name')
            ->select('id', 'first_name', 'last_name', 'member_no', 'community_id')
            ->find($id);

        if (! $member) {
            return response()->json(null);
        }

        return response()->json([
            'id' => $member->id,
            'name' => trim($member->first_name . ' ' . $member->last_name) . ' - ' . ($member->community->name ?? 'N/A') . ' - ' . ($member->member_no ?? 'N/A'),
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'member_no' => $member->member_no,
            'community_name' => $member->community->name ?? 'N/A'
        ]);
    }

    /**
     * Get all cell associations for a specific member
     */
    // public function getMemberCellAssociations($memberId)
    // {
    //     $cellAssociations = CellsAndAssociationMember::where('member_id', $memberId)
    //         ->with('cellsAndAssociation:id,name')
    //         ->get()
    //         ->pluck('cellsAndAssociation');

    //     return response()->json($cellAssociations);
    // }

    /**
     * Export cells and association members to CSV.
     */
    public function export(Request $request)
    {
        try {
            $this->authorize('viewAny', CellsAndAssociationMember::class);

            $query = CellsAndAssociationMember::with(['member', 'cellsAndAssociation']);

            if ($request->boolean('isArchived')) {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
                    })
                    ->orWhereHas('cellsAndAssociation', function ($caQuery) use ($search) {
                        $caQuery->where('name', 'like', "%$search%");
                    });
                });
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'member.first_name', 'cellsAndAssociation.name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                if ($sort === 'member.first_name') {
                    $query->join('members', 'cells_and_association_members.member_id', '=', 'members.id')
                        ->orderBy('members.first_name', $direction);
                } elseif ($sort === 'cellsAndAssociation.name') {
                    $query->join('cells_and_associations', 'cells_and_association_members.cells_and_association_id', '=', 'cells_and_associations.id')
                        ->orderBy('cells_and_associations.name', $direction);
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
                    'ID', 'Member Name', 'Cell/Association', 'Contact Number', 'Email'
                ]);

                foreach ($query->cursor() as $item) {
                    $memberName = $item->member 
                        ? trim($item->member->first_name.' '.$item->member->last_name)
                        : '';

                    fputcsv($out, [
                        $item->id,
                        $memberName,
                        $item->cellsAndAssociation ? $item->cellsAndAssociation->name : '',
                        $item->member ? $item->member->contact_no_1 : '',
                        $item->member ? $item->member->email : '',
                    ]);
                }

                fclose($out);
            }, 'cells_and_association_members_'.now()->format('Y-m-d_H-i-s').'.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache',
            ]);

        } catch (\Exception $e) {
            \Log::error('Cells and Association Member Export failed: '.$e->getMessage());
            return response()->json(['error' => 'Export failed: '.$e->getMessage()], 500);
        }
    }
}
