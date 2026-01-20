<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Members\Http\Requests\StoreCellsAndAssociationLeaderRequest;
use Modules\Members\Http\Requests\UpdateCellsAndAssociationLeaderRequest;
use Modules\Members\Models\CellsAndAssociation;
use Modules\Members\Models\CellsAndAssociationLeader;
use Modules\Members\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

class CellsAndAssociationLeaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = CellsAndAssociationLeader::query();

        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        $query->join('cells_and_associations', 'cells_and_association_leaders.cells_and_association_id', '=', 'cells_and_associations.id');
        $query->leftJoin('members as leader', function ($join) {
            $join->on('cells_and_association_leaders.leader_member_id', '=', 'leader.id')
                ->whereNull('leader.deathrecord_id');
        });
        $query->leftJoin('members as assistant', function ($join) {
            $join->on('cells_and_association_leaders.assistant_leader_member_id', '=', 'assistant.id')
                ->whereNull('assistant.deathrecord_id');
        });

        $query->select(
            'cells_and_association_leaders.*',
            'cells_and_associations.name as cells_and_association_name',
            DB::raw("CONCAT(leader.first_name, ' ', leader.last_name) as leader_full_name"),
            DB::raw("CONCAT(assistant.first_name, ' ', assistant.last_name) as assistant_leader_full_name")
        );

        // Apply filters
        if ($cellsAndAssociationId = $request->input('cells_and_association_id')) {
            $query->where('cells_and_association_leaders.cells_and_association_id', $cellsAndAssociationId);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('cells_and_associations.name', 'like', "%$search%")
                    ->orWhere('leader.first_name', 'like', "%$search%")
                    ->orWhere('leader.last_name', 'like', "%$search%")
                    ->orWhere('assistant.first_name', 'like', "%$search%")
                    ->orWhere('assistant.last_name', 'like', "%$search%");
            });
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('cells_and_association_leaders.id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('cells_and_association_leaders/Index', [
            'fetchUrl' => route('cells-and-association-leaders.index'),
            'cells_and_association_leaders' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
            'cellsAndAssociations' => CellsAndAssociation::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('cells_and_association_leaders/Index', [
            'cellsAndAssociations' => CellsAndAssociation::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCellsAndAssociationLeaderRequest $request)
    {
        $validated = $request->validated();

        CellsAndAssociationLeader::create([
            'cells_and_association_id' => $validated['cells_and_association_id'],
            'leader_member_id' => $validated['leader_member_id'],
            'assistant_leader_member_id' => $validated['assistant_leader_member_id'] ?? null,
        ]);

        $perPage = $request->input('perPage', 10);
        $total = CellsAndAssociationLeader::count();
        $lastPage = (int) ceil($total / $perPage);

        return redirect()->route('cells-and-association-leaders.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $lastPage,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Cells & Association Leader created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CellsAndAssociationLeader $cellsAndAssociationLeader)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CellsAndAssociationLeader $cellsAndAssociationLeader)
    {
        return Inertia::render('cells_and_association_leaders/Index', [
            'cellsAndAssociationLeader' => $cellsAndAssociationLeader,
            'cellsAndAssociations' => CellsAndAssociation::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCellsAndAssociationLeaderRequest $request, $id)
    {
        $leader = CellsAndAssociationLeader::findOrFail($id);
        $validated = $request->validated();

        $leader->update([
            'cells_and_association_id' => $validated['cells_and_association_id'],
            'leader_member_id' => $validated['leader_member_id'],
            'assistant_leader_member_id' => $validated['assistant_leader_member_id'] ?? null,
        ]);

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        return redirect()->route('cells-and-association-leaders.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Cells & Association Leader updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CellsAndAssociationLeader $cellsAndAssociationLeader)
    {
        $cellsAndAssociationLeader->delete();

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        return redirect()->route('cells-and-association-leaders.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Cells & Association Leader deleted successfully.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $leader = CellsAndAssociationLeader::withTrashed()->findOrFail($id);
        $leader->restore();

        return redirect()->route('cells-and-association-leaders.index')->with('success', 'Cells & Association Leader restored successfully.');
    }

    /**
     * Get all alive members for dropdowns.
     */
    public function getAllMembers()
    {
        try {
            $members = Member::alive()
                ->select('id', 'first_name', 'middle_name', 'last_name')
                ->orderBy('first_name')
                ->get()
                ->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'name' => trim("{$m->first_name} {$m->middle_name} {$m->last_name}"),
                    ];
                });

            return response()->json($members);
        } catch (\Exception $e) {
            Log::error('Error fetching alive members: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch members'], 500);
        }
    }

    /**
     * Export cells and association leaders to CSV.
     */
    public function export(Request $request)
    {
        try {
            $query = CellsAndAssociationLeader::with(['cellsAndAssociation', 'leader', 'assistantLeader']);

            if ($request->boolean('isArchived')) {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('cellsAndAssociation', function ($caQuery) use ($search) {
                        $caQuery->where('name', 'like', "%$search%");
                    })
                        ->orWhereHas('leader', function ($leaderQuery) use ($search) {
                            $leaderQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
                        })
                        ->orWhereHas('assistantLeader', function ($assistantQuery) use ($search) {
                            $assistantQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
                        });
                });
            }

            $allowedSortColumns = ['id', 'cells_and_association_name', 'leader_full_name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                if ($sort === 'cells_and_association_name') {
                    $query->join('cells_and_associations', 'cells_and_association_leaders.cells_and_association_id', '=', 'cells_and_associations.id')
                        ->orderBy('cells_and_associations.name', $direction);
                } elseif ($sort === 'leader_full_name') {
                    $query->join('members', 'cells_and_association_leaders.leader_member_id', '=', 'members.id')
                        ->orderBy('members.first_name', $direction);
                } else {
                    $query->orderBy($sort, $direction);
                }
            } else {
                $query->orderBy('id', 'asc');
            }

            return response()->streamDownload(function () use ($query) {
                while (ob_get_level()) {
                    ob_end_clean();
                }

                $out = fopen('php://output', 'w');

                fputcsv($out, [
                    'ID',
                    'Cell/Association',
                    'Leader Name',
                    'Leader Contact',
                    'Leader Email',
                    'Assistant Leader Name',
                    'Assistant Leader Contact',
                    'Assistant Leader Email'
                ]);

                foreach ($query->cursor() as $item) {
                    $leaderName = $item->leader
                        ? trim($item->leader->first_name . ' ' . $item->leader->last_name)
                        : '';
                    $assistantName = $item->assistantLeader
                        ? trim($item->assistantLeader->first_name . ' ' . $item->assistantLeader->last_name)
                        : '';

                    fputcsv($out, [
                        $item->id,
                        $item->cellsAndAssociation ? $item->cellsAndAssociation->name : '',
                        $leaderName,
                        $item->leader ? $item->leader->contact_no_1 : '',
                        $item->leader ? $item->leader->email : '',
                        $assistantName,
                        $item->assistantLeader ? $item->assistantLeader->contact_no_1 : '',
                        $item->assistantLeader ? $item->assistantLeader->email : '',
                    ]);
                }

                fclose($out);
            }, 'cells_and_association_leaders_' . now()->format('Y-m-d_H-i-s') . '.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache',
            ]);
        } catch (\Exception $e) {
            Log::error('Cells & Association Leaders Export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }
}
