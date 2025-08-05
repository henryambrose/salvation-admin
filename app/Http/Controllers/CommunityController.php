<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunityRequest;
use App\Http\Requests\UpdateCommunityRequest;
use App\Models\Community;
use App\Models\Zone;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Community::class);
        
        $query = Community::query()->with('zone')->with('ppchead.member')->with('scchead.member')->with('members');

        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhereHas('zone', function($zoneQuery) use ($search) {
                      $zoneQuery->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('ppchead.member', function($memberQuery) use ($search) {
                      $memberQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
                  })
                  ->orWhereHas('scchead.member', function($memberQuery) use ($search) {
                      $memberQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
                  });
            });
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);
        $data = $query->paginate($perPage)->appends($request->query());
        return Inertia::render('community/Index', [
            'communities' => $data,
            'filters' => request()->only('search', 'sort', 'direction', 'perPage', 'isArchived'),
            'fetchUrl' => route('community.index'),
            'zones' => Zone::all(),
            'pagination' => [
                'currentPage' => $query->paginate($perPage)->currentPage(),
                'lastPage' => $query->paginate($perPage)->lastPage(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Community::class);
        
        return Inertia::render('community/Community', [
            'zones' => Zone::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommunityRequest $request)
    {
        $this->authorize('create', Community::class);
        
        Community::create($request->validated());
        $perPage = $request->input('perPage', 10);
        $total = Community::count();
        $lastPage = (int) ceil($total / $perPage);
        return redirect()->route('community.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $lastPage,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Community created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Community $community): Response
    {
        $this->authorize('view', $community);
        
        return Inertia::render('community/Community', [
            'community' => $community,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Community $community): Response
    {
        $this->authorize('update', $community);
        
        return Inertia::render('community/Community', [
            'community' => $community,
            'zones' => Zone::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommunityRequest $request, Community $community)
    {
        $this->authorize('update', $community);
        
        $community->update($request->validated());
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('community.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Community updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community)
    {
        $this->authorize('delete', $community);
        
        $community->delete();

        return redirect()->route('community.index')->with('success', 'Community deleted successfully.');
    }

    /**
     * Restore a deleted community.
     */
    public function restore($id)
    {
        $community = Community::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $community);
        
        $community->restore();
        return redirect()->route('community.index')->with('success', 'Community restored successfully.');
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Community::class);
        
        try {
            $query = Community::query()->with('zone')->with('ppchead.member')->with('scchead.member');
            
            if ($request->input('isArchived') === 'true') {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }
            
            if ($search = $request->input('search')) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhereHas('zone', function($zoneQuery) use ($search) {
                          $zoneQuery->where('name', 'like', "%$search%");
                      })
                      ->orWhereHas('ppchead.member', function($memberQuery) use ($search) {
                          $memberQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
                      })
                      ->orWhereHas('scchead.member', function($memberQuery) use ($search) {
                          $memberQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
                      });
                });
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'name', 'zone.name', 'ppchead.member.first_name', 'scchead.member.first_name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');
            
            if (in_array($sort, $allowedSortColumns)) {
                if ($sort === 'zone.name') {
                    $query->join('zones', 'communities.zone_id', '=', 'zones.id')
                          ->orderBy('zones.name', $direction);
                } elseif ($sort === 'ppchead.member.first_name') {
                    $query->join('p_p_c_heads', 'communities.id', '=', 'p_p_c_heads.community_id')
                          ->join('members', 'p_p_c_heads.member_id', '=', 'members.id')
                          ->orderBy('members.first_name', $direction);
                } elseif ($sort === 'scchead.member.first_name') {
                    $query->join('s_c_c_heads', 'communities.id', '=', 's_c_c_heads.community_id')
                          ->join('members', 's_c_c_heads.member_id', '=', 'members.id')
                          ->orderBy('members.first_name', $direction);
                } else {
                    $query->orderBy($sort, $direction);
                }
            } else {
                $query->orderBy('id', 'asc');
            }

            $data = $query->get();

            // Transform data for export
            $exportData = [];
            foreach ($data as $item) {
                $ppcHeadName = $item->ppchead && $item->ppchead->member 
                    ? trim($item->ppchead->member->first_name . ' ' . $item->ppchead->member->last_name)
                    : '';
                
                $sccHeadName = $item->scchead && $item->scchead->member 
                    ? trim($item->scchead->member->first_name . ' ' . $item->scchead->member->last_name)
                    : '';
                
                $exportData[] = [
                    'ID' => $item->id,
                    'Community Name' => $item->name ?? '',
                    'Zone' => $item->zone ? $item->zone->name : '',
                    'PPC Head' => $ppcHeadName,
                    'SCC Head' => $sccHeadName,
                ];
            }

            // Create Excel file
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $headers = array_keys($exportData[0] ?? []);
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

            // Create writer and output
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'communities_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Save to temporary file and return as download
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);
            
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend();

        } catch (\Exception $e) {
            \Log::error('Community Export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }
}
