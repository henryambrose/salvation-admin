<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Members\Http\Requests\StorePPCHeadRequest;
use Modules\Members\Http\Requests\UpdatePPCHeadRequest;
use Modules\Members\Models\Community;
use Modules\Members\Models\Member;
use Modules\Members\Models\PPCHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PPCHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = PPCHead::query();
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        $query->select('p_p_c_heads.*');
        $query->join('members', 'p_p_c_heads.member_id', '=', 'members.id');
        $query->join('communities', 'p_p_c_heads.community_id', '=', 'communities.id');
        $query->select(
            'p_p_c_heads.*',
            'members.first_name as member_first_name',
            'members.last_name as member_last_name',
            DB::raw("CONCAT(members.first_name, ' ', members.last_name) as member_full_name"),
            'communities.name as community_name'
        );
        // Apply filters
        if ($communityId = $request->input('community_id')) {
            $query->where('p_p_c_heads.community_id', $communityId);
        }
        if ($memberId = $request->input('member_id')) {
            $query->where('p_p_c_heads.member_id', $memberId);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('members.first_name', 'like', "%$search%")
                    ->orWhere('members.middle_name', 'like', "%$search%")
                    ->orWhere('members.last_name', 'like', "%$search%")
                    ->orWhere('communities.name', 'like', "%$search%");
            });
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('p_p_c_head/Index', [
            'ppcHeads' => $query->paginate($perPage)->appends($request->query()),
            'filters' => request()->only('search', 'sort', 'direction', 'perPage', 'isArchived'),
            'fetchUrl' => route('ppc-head.index'),
            'communities' => Community::all(),
            // 'members' => Member::all(), // REMOVE THIS
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('p_p_c_head/PPCHead', [
            'communities' => Community::all(),
            'members' => Member::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePPCHeadRequest $request)
    {
        $validated = $request->validated();
        PPCHead::create([
            'member_id' => $validated['member_id'],
            'community_id' => $validated['community_id'],
        ]);
        $perPage = $request->input('perPage', 10);
        $total = PPCHead::count();
        $lastPage = (int) ceil($total / $perPage);

        return redirect()->route('ppc-head.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $lastPage,
                'perPage' => $perPage,
            ]
        ))->with('success', 'PPC Head created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PPCHead $pPCHead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PPCHead $ppcHead): Response
    {
        return Inertia::render('p_p_c_head/PPCHead', [
            'PPCHead' => $ppcHead,
            'communities' => Community::all(),
            'members' => Member::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePPCHeadRequest $request, $id)
    {
        $ppcHead = PPCHead::findOrFail($id);
        $validated = $request->validated();
        $ppcHead->update([
            'member_id' => $validated['member_id'],
            'community_id' => $validated['community_id'],
        ]);
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        return redirect()->route('ppc-head.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'PPC Head updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, PPCHead $ppcHead)
    {
        $ppcHead->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('ppc-head.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'PPC Head deleted successfully.');
    }

    public function restore($id)
    {
        $ppcHead = PPCHead::withTrashed()->findOrFail($id);
        $ppcHead->restore();

        return redirect()->route('ppc-head.index')->with('success', 'PPC Head restored successfully.');
    }

    // Add API endpoint for fetching members by community
    public function membersByCommunity($communityId)
    {
        try {
            $members = Member::where('community_id', $communityId)
                ->select('id', 'first_name', 'middle_name', 'last_name')
                ->get()
                ->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'name' => trim("{$m->first_name} {$m->middle_name} {$m->last_name}"),
                    ];
                });

            return response()->json($members);
        } catch (\Exception $e) {
            Log::error('Error fetching members by community: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch members'], 500);
        }
    }

    /**
     * Export PPC heads to CSV.
     */
    public function export(Request $request)
    {
        try {
            $this->authorize('viewAny', PPCHead::class);

            $query = PPCHead::with(['member', 'community']);

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
                        ->orWhereHas('community', function ($communityQuery) use ($search) {
                            $communityQuery->where('name', 'like', "%$search%");
                        });
                });
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'member.first_name', 'community.name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                if ($sort === 'member.first_name') {
                    $query->join('members', 'p_p_c_heads.member_id', '=', 'members.id')
                        ->orderBy('members.first_name', $direction);
                } elseif ($sort === 'community.name') {
                    $query->join('communities', 'p_p_c_heads.community_id', '=', 'communities.id')
                        ->orderBy('communities.name', $direction);
                } else {
                    $query->orderBy($sort, $direction);
                }
            } else {
                $query->orderBy('id', 'asc');
            }

            // Streamed CSV keeps memory flat
            return response()->streamDownload(function () use ($query) {
                // Clear any output buffers to prevent extra whitespace
                while (ob_get_level()) {
                    ob_end_clean();
                }

                $out = fopen('php://output', 'w');

                fputcsv($out, [
                    'ID',
                    'Member Name',
                    'Community',
                    'Contact Number',
                    'Email'
                ]);

                foreach ($query->cursor() as $item) {
                    $memberName = $item->member
                        ? trim($item->member->first_name . ' ' . $item->member->last_name)
                        : '';

                    fputcsv($out, [
                        $item->id,
                        $memberName,
                        $item->community ? $item->community->name : '',
                        $item->member ? $item->member->contact_no_1 : '',
                        $item->member ? $item->member->email : '',
                    ]);
                }

                fclose($out);
            }, 'ppc_heads_' . now()->format('Y-m-d_H-i-s') . '.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache',
            ]);
        } catch (\Exception $e) {
            Log::error('PPC Head Export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }
}
