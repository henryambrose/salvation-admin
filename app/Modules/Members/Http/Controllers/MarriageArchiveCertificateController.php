<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Members\Http\Requests\StoreMarriageArchiveCertificateRequest;
use Modules\Members\Http\Requests\UpdateMarriageArchiveCertificateRequest;
use Modules\Members\Models\MarriageArchiveCertificate;
use Modules\Members\Models\Parish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MarriageArchiveCertificateController extends Controller
{
    /**
     * Display a listing of marriage archive certificates
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', MarriageArchiveCertificate::class);

        $query = MarriageArchiveCertificate::query();

        // Handle archived records
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        }

        // Search across all fields
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filter by year
        if ($year = $request->input('year')) {
            $query->byYear((int) $year);
        }

        // Filter by month
        if ($month = $request->input('month')) {
            $query->byMonth((int) $month);
        }

        // Filter by day
        if ($day = $request->input('day')) {
            $query->byDay((int) $day);
        }

        // Sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Eager load marriage record relationship for status indicators
        $query->with('marriageRecord:id,marriage_archive_certificate_id');

        // Pagination
        $perPage = $request->input('perPage', 10);
        $certificates = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('archive/marriage/Index', [
            'fetchUrl' => route('archive.marriage.index'),
            'certificates' => $certificates,
            'filters' => $request->only([
                'search', 'year', 'month', 'day',
                'sort', 'direction', 'perPage', 'isArchived'
            ]),
        ]);
    }

    /**
     * Show the form for creating a new certificate
     */
    public function create()
    {
        $this->authorize('create', MarriageArchiveCertificate::class);

        return Inertia::render('archive/marriage/Create');
    }

    /**
     * Store a newly created certificate
     */
    public function store(StoreMarriageArchiveCertificateRequest $request)
    {
        $validated = $request->validated();

        try {
            // Upload file to S3
            $file = $request->file('file');
            $folderPath = 'archive/marriage/' . $validated['reg_year'];
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());

            $file->storeAs($folderPath, $fileName, 's3');

            // Create record
            MarriageArchiveCertificate::create([
                'folder_path' => $folderPath,
                'file_name' => $fileName,
                'reg_year' => $validated['reg_year'],
                'reg_no' => $validated['reg_no'],
                'marriage_year' => $validated['marriage_year'],
                'marriage_month' => $validated['marriage_month'],
                'marriage_day' => $validated['marriage_day'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('archive.marriage.index')
                ->with('success', 'Marriage archive certificate created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create certificate: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified certificate
     */
    public function show(MarriageArchiveCertificate $marriageArchive)
    {
        $this->authorize('view', $marriageArchive);

        return Inertia::render('archive/marriage/Show', [
            'certificate' => $marriageArchive->load(['creator', 'updater']),
        ]);
    }

    /**
     * View archive certificate with marriage record integration
     * Shows create form if no marriage record exists, otherwise shows marriage details
     */
    public function viewWithMarriage(MarriageArchiveCertificate $marriageArchive)
    {
        $this->authorize('view', $marriageArchive);

        // Load the marriage record relationship
        $marriageArchive->load(['marriageRecord', 'creator', 'updater']);

        // Check if PDF file exists and show warning if not
        if (!$marriageArchive->fileExists()) {
            session()->flash('warning', 'Certificate PDF file has not been uploaded yet.');
        }

        // Determine mode based on whether marriage record exists
        $mode = $marriageArchive->marriageRecord ? 'view' : 'create';

        // Prepare data based on mode
        $data = [
            'certificate' => $marriageArchive,
            'marriageRecord' => $marriageArchive->marriageRecord,
            'mode' => $mode,
        ];

        // If in create mode, load parishes for the dropdown
        if ($mode === 'create') {
            $data['parishes'] = Parish::orderBy('name')
                ->get(['id', 'name']);
        }

        return Inertia::render('archive/marriage/MarriageView', $data);
    }

    /**
     * Show the form for editing the certificate
     */
    public function edit(MarriageArchiveCertificate $marriageArchive)
    {
        $this->authorize('update', $marriageArchive);

        return Inertia::render('archive/marriage/Edit', [
            'certificate' => $marriageArchive,
        ]);
    }

    /**
     * Update the specified certificate
     */
    public function update(UpdateMarriageArchiveCertificateRequest $request, MarriageArchiveCertificate $marriageArchive)
    {
        $validated = $request->validated();

        try {
            // If new file uploaded, replace old one
            if ($request->hasFile('file')) {
                // Delete old file from S3
                $oldPath = trim($marriageArchive->folder_path, '/') . '/' . $marriageArchive->file_name;
                if (Storage::disk('s3')->exists($oldPath)) {
                    Storage::disk('s3')->delete($oldPath);
                }

                // Upload new file
                $file = $request->file('file');
                $folderPath = 'archive/marriage/' . $validated['reg_year'];
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());

                $file->storeAs($folderPath, $fileName, 's3');

                $validated['folder_path'] = $folderPath;
                $validated['file_name'] = $fileName;
            }

            $validated['updated_by'] = auth()->id();
            $marriageArchive->update($validated);

            return redirect()
                ->route('archive.marriage.index')
                ->with('success', 'Marriage archive certificate updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update certificate: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified certificate (soft delete)
     */
    public function destroy(Request $request, MarriageArchiveCertificate $marriageArchive)
    {
        $this->authorize('delete', $marriageArchive);

        $marriageArchive->delete();

        return redirect()
            ->route('archive.marriage.index', $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']))
            ->with('success', 'Marriage archive certificate deleted successfully.');
    }

    /**
     * Restore a soft-deleted certificate
     */
    public function restore($id)
    {
        $certificate = MarriageArchiveCertificate::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $certificate);

        $certificate->restore();

        return redirect()
            ->route('archive.marriage.index')
            ->with('success', 'Marriage archive certificate restored successfully.');
    }

    /**
     * Download the certificate file from S3
     */
    public function download(MarriageArchiveCertificate $marriageArchive)
    {
        $this->authorize('download', $marriageArchive);

        if (!$marriageArchive->fileExists()) {
            return redirect()
                ->back()
                ->with('error', 'Certificate file not found in storage.');
        }

        $path = trim($marriageArchive->folder_path, '/') . '/' . $marriageArchive->file_name;

        try {
            // Generate temporary signed URL (valid for 5 minutes)
            $url = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5));

            return redirect($url);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to generate download link: ' . $e->getMessage());
        }
    }
}
