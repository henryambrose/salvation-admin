<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParishRequest;
use App\Http\Requests\UpdateParishRequest;
use App\Models\Parish;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ParishController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        \DB::enableQueryLog();
        $query = Parish::query();

        // Handle archived records
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($search = $request->input('search')) {
            $query->whereRaw(
                "CONCAT(
          COALESCE(deanery, ''),
          COALESCE(name, ''),
          COALESCE(code, ''),
          COALESCE(address, '')
          ) LIKE ?",
                ["%$search%"]
            );
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('parish/Index', [
            'fetchUrl' => route('parish.index'),
            'parishes' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
            'query' => \DB::getQueryLog(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('parish/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParishRequest $request)
    {
        Parish::create($request->validated());

        return redirect()->route('parish.index')->with('success', 'Parish created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Parish $parish)
    {
        // Optionally implement if needed
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Parish $parish)
    {
        return Inertia::render('parish/Edit', [
            'parish' => $parish,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParishRequest $request, Parish $parish)
    {
        $parish->update($request->validated());

        return redirect()->route('parish.index')->with('success', 'Parish updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parish $parish)
    {
        $parish->delete();

        return redirect()->route('parish.index')->with('success', 'Parish deleted successfully.');
    }

    public function restore($id)
    {
        $parish = Parish::onlyTrashed()->findOrFail($id);
        $parish->restore();

        return redirect()->route('parish.index')->with('success', 'Parish restored successfully.');
    }

    public function export(Request $request)
    {
        try {
            $query = Parish::query();

            if ($request->input('isArchived') === 'true') {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->whereRaw(
                    "CONCAT(
          COALESCE(deanery, ''),
          COALESCE(name, ''),
          COALESCE(code, ''),
          COALESCE(address, '')
          ) LIKE ?",
                    ["%$search%"]
                );
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'deanery', 'name'];
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
                    'Deanery' => $item->deanery ?? '',
                    'Parish Name' => $item->name ?? '',
                    'Code' => $item->code ?? '',
                    'Address' => $item->address ?? '',
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
            $filename = 'parishes_'.date('Y-m-d_H-i-s').'.xlsx';

            // Save to temporary file and return as download
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend();

        } catch (\Exception $e) {
            \Log::error('Parish Export failed: '.$e->getMessage());

            return response()->json(['error' => 'Export failed: '.$e->getMessage()], 500);
        }
    }
}
