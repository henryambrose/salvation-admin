<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Members\Http\Requests\BulkImportArchiveCertificateRequest;
use Modules\Members\Models\BirthArchiveCertificate;
use Modules\Members\Models\MarriageArchiveCertificate;
use Modules\Members\Models\DeathArchiveCertificate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ArchiveCertificateBulkImportController extends Controller
{
    /**
     * Show bulk import form
     */
    public function create(string $type)
    {
        $this->authorize("create-{$type}-archive");

        return Inertia::render('archive/BulkImport', [
            'type' => $type,
        ]);
    }

    /**
     * Process bulk import
     */
    public function store(BulkImportArchiveCertificateRequest $request, string $type)
    {
        $csvFile = $request->file('csv_file');
        $certificateFiles = $request->file('certificate_files');

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            // Parse CSV
            $csvData = array_map('str_getcsv', file($csvFile->getRealPath()));
            $headers = array_shift($csvData); // Remove header row

            // Expected CSV format:
            // file_name, reg_year, reg_no, year, month, day, first_name, middle_name, last_name, notes

            foreach ($csvData as $rowIndex => $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $rowData = array_combine($headers, $row);

                    // Find matching file
                    $fileName = $rowData['file_name'];
                    $matchingFile = collect($certificateFiles)->first(function ($file) use ($fileName) {
                        return $file->getClientOriginalName() === $fileName;
                    });

                    if (!$matchingFile) {
                        $results['errors'][] = "Row " . ($rowIndex + 2) . ": File '{$fileName}' not found in uploads.";
                        $results['failed']++;
                        continue;
                    }

                    // Validate date
                    if (!checkdate($rowData['month'], $rowData['day'], $rowData['year'])) {
                        $results['errors'][] = "Row " . ($rowIndex + 2) . ": Invalid date combination.";
                        $results['failed']++;
                        continue;
                    }

                    // Upload to S3
                    $folderPath = "archive/{$type}/" . $rowData['reg_year'];
                    $uploadedFileName = time() . '_' . $rowIndex . '_' . str_replace(' ', '_', $matchingFile->getClientOriginalName());
                    $matchingFile->storeAs($folderPath, $uploadedFileName, 's3');

                    // Create record based on type
                    $modelClass = $this->getModelClass($type);
                    $dateField = $this->getDateField($type);

                    $modelClass::create([
                        'folder_path' => $folderPath,
                        'file_name' => $uploadedFileName,
                        'reg_year' => $rowData['reg_year'],
                        'reg_no' => $rowData['reg_no'],
                        "{$dateField}_year" => $rowData['year'],
                        "{$dateField}_month" => $rowData['month'],
                        "{$dateField}_day" => $rowData['day'],
                        'first_name' => $rowData['first_name'],
                        'middle_name' => $rowData['middle_name'] ?? null,
                        'last_name' => $rowData['last_name'],
                        'notes' => $rowData['notes'] ?? null,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                    $results['failed']++;
                }
            }

            DB::commit();

            $message = "Bulk import completed. Success: {$results['success']}, Failed: {$results['failed']}";

            if ($results['failed'] > 0) {
                return redirect()
                    ->route("archive.{$type}.index")
                    ->with('warning', $message)
                    ->with('import_errors', $results['errors']);
            }

            return redirect()
                ->route("archive.{$type}.index")
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Bulk import failed: ' . $e->getMessage());
        }
    }

    /**
     * Get model class for given type
     */
    private function getModelClass(string $type)
    {
        return match ($type) {
            'birth' => BirthArchiveCertificate::class,
            'marriage' => MarriageArchiveCertificate::class,
            'death' => DeathArchiveCertificate::class,
            default => throw new \InvalidArgumentException("Invalid archive type: {$type}"),
        };
    }

    /**
     * Get date field prefix for given type
     */
    private function getDateField(string $type): string
    {
        return match ($type) {
            'birth' => 'birth',
            'marriage' => 'marriage',
            'death' => 'death',
            default => throw new \InvalidArgumentException("Invalid archive type: {$type}"),
        };
    }
}
