<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCellsAndAssociationMemberRequest;
use App\Http\Requests\UpdateCellsAndAssociationMemberRequest;
use App\Models\CellsAndAssociationMember;
use App\Models\Member;
use App\Models\CellsAndAssociation;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            $query->whereHas('member', function($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                  ->orWhere('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%");
            })->orWhereHas('cellsAndAssociation', function($q) use ($search) {
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
            $memberName = $item->member ? trim($item->member->first_name . ' ' . $item->member->last_name) : '';
            $communityName = $item->member && $item->member->community ? $item->member->community->name : 'N/A';
            $memberNo = $item->member ? $item->member->member_no : 'N/A';
            $item->member_name = $memberName . ' - ' . $communityName . ' - ' . $memberNo;
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
        CellsAndAssociationMember::create($request->validated());

        return redirect()->route('cells-and-association-members.index')->with('success', 'Cells Association Member created successfully.');
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
        $cellsAndAssociationMember->update($request->validated());

        return redirect()->route('cells-and-association-members.index')->with('success', 'Cells Association Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CellsAndAssociationMember $cellsAndAssociationMember)
    {
        $cellsAndAssociationMember->delete();

        return redirect()->route('cells-and-association-members.index')->with('success', 'Cells Association Member deleted successfully.');
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
            ->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
            ->orWhere('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->limit(10)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => trim($member->first_name . ' ' . $member->last_name) . ' - ' . ($member->community->name ?? 'N/A') . ' - ' . ($member->member_no ?? 'N/A')
                ];
            });

        return response()->json($members);
    }

    public function getMemberById($id)
    {
        $member = Member::with('community:id,name')
            ->select('id', 'first_name', 'last_name', 'member_no', 'community_id')
            ->find($id);

        if (!$member) {
            return response()->json(null, 404);
        }

        return response()->json([
            'id' => $member->id,
            'name' => trim($member->first_name . ' ' . $member->last_name) . ' - ' . ($member->community->name ?? 'N/A') . ' - ' . ($member->member_no ?? 'N/A')
        ]);
    }

    public function export(Request $request)
    {
        try {
            $query = CellsAndAssociationMember::with(['member.community', 'cellsAndAssociation']);

            // Handle archived records
            if ($request->input('isArchived') === 'true') {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->whereHas('member', function($q) use ($search) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                      ->orWhere('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%");
                })->orWhereHas('cellsAndAssociation', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            }

            if ($cellAssociation = $request->input('cellAssociation')) {
                $query->where('cells_and_association_id', $cellAssociation);
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'cells_and_association_name', 'member_name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');
            
            if (in_array($sort, $allowedSortColumns)) {
                if ($sort === 'cells_and_association_name') {
                    $query->join('cells_and_associations', 'cells_and_association_members.cells_and_association_id', '=', 'cells_and_associations.id')
                          ->orderBy('cells_and_associations.name', $direction);
                } elseif ($sort === 'member_name') {
                    $query->join('members', 'cells_and_association_members.member_id', '=', 'members.id')
                          ->orderBy('members.first_name', $direction)
                          ->orderBy('members.last_name', $direction);
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
                $memberName = $item->member ? trim($item->member->first_name . ' ' . $item->member->last_name) : '';
                $communityName = $item->member && $item->member->community ? $item->member->community->name : 'N/A';
                $memberNo = $item->member ? $item->member->member_no : 'N/A';
                $memberDisplayName = $memberName . ' - ' . $communityName . ' - ' . $memberNo;
                
                $exportData[] = [
                    'ID' => $item->id,
                    'Cell Association Name' => $item->cellsAndAssociation->name ?? '',
                    'Member Name' => $memberDisplayName,
                ];
            }

            // Create Excel file
            $spreadsheet = new Spreadsheet();
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
            $writer = new Xlsx($spreadsheet);
            $filename = 'cells_association_members_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Save to temporary file and return as download
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);
            
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend();

        } catch (\Exception $e) {
            \Log::error('Export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }
}
