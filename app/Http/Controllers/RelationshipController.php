<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRelationshipRequest;
use App\Http\Requests\UpdateRelationshipRequest;
use App\Models\Relationship;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RelationshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Relationship::query();

        // Handle archived records
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('relationship/Index', [
            'fetchUrl' => route('relationship.index'),
            'relationships' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('relationship/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRelationshipRequest $request)
    {
        Relationship::create($request->validated());

        return redirect()->route('relationship.index')->with('success', 'Relationship created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Relationship $relationship)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Relationship $relationship)
    {
        return Inertia::render('relationship/Edit', [
            'relationship' => $relationship,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRelationshipRequest $request, Relationship $relationship)
    {
        $relationship->update($request->validated());

        return redirect()->route('relationship.index')->with('success', 'Relationship updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Relationship $relationship)
    {
        $relationship->delete();

        return redirect()->route('relationship.index')->with('success', 'Relationship deleted successfully.');
    }

    public function restore($id)
    {
        $relationship = Relationship::onlyTrashed()->findOrFail($id);
        $relationship->restore();

        return redirect()->route('relationship.index')->with('success', 'Relationship restored successfully.');
    }

    public function export(Request $request)
    {
        try {
            $query = Relationship::query();

            if ($request->input('isArchived') === 'true') {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->where('name', 'like', "%$search%");
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                $query->orderBy($sort, $direction);
            } else {
                $query->orderBy('id', 'asc');
            }

            $data = $query->get();

            // Transform data for export
            $exportData = [];
            foreach ($data as $item) {
                $exportData[] = [
                    'ID' => $item->id,
                    'Relationship Name' => $item->name ?? '',
                ];
            }

            // Create Excel file
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $headers = array_keys($exportData[0] ?? []);
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col.'1', $header);
                $sheet->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }

            // Set data
            $row = 2;
            foreach ($exportData as $rowData) {
                $col = 'A';
                foreach ($rowData as $value) {
                    $sheet->setCellValue($col.$row, $value);
                    $col++;
                }
                $row++;
            }

            // Style header row
            $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')->getFont()->setBold(true);

            // Create writer and output
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'relationships_'.date('Y-m-d_H-i-s').'.xlsx';

            // Save to temporary file and return as download
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend();

        } catch (\Exception $e) {
            \Log::error('Relationship Export failed: '.$e->getMessage());

            return response()->json(['error' => 'Export failed: '.$e->getMessage()], 500);
        }
    }
}
