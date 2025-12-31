<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Members\Http\Requests\StoreDeathArchiveCertificateRequest;
use Modules\Members\Http\Requests\UpdateDeathArchiveCertificateRequest;
use Modules\Members\Models\DeathArchiveCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DeathArchiveCertificateController extends Controller
{
    /**
     * Display a listing of death archive certificates
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', DeathArchiveCertificate::class);

        $query = DeathArchiveCertificate::query();

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

        // Pagination
        $perPage = $request->input('perPage', 10);
        $certificates = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('PagesMembers/archive/death/Index', [
            'fetchUrl' => route('archive.death.index'),
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
        $this->authorize('create', DeathArchiveCertificate::class);

        return Inertia::render('PagesMembers/archive/death/Create');
    }

    /**
     * Store a newly created certificate
     */
    public function store(StoreDeathArchiveCertificateRequest $request)
    {
        $validated = $request->validated();

        try {
            // Upload file to S3
            $file = $request->file('file');
            $folderPath = 'archive/death/' . $validated['reg_year'];
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());

            $file->storeAs($folderPath, $fileName, 's3');

            // Create record
            DeathArchiveCertificate::create([
                'folder_path' => $folderPath,
                'file_name' => $fileName,
                'reg_year' => $validated['reg_year'],
                'reg_no' => $validated['reg_no'],
                'death_year' => $validated['death_year'],
                'death_month' => $validated['death_month'],
                'death_day' => $validated['death_day'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('archive.death.index')
                ->with('success', 'Death archive certificate created successfully.');
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
    public function show(DeathArchiveCertificate $deathArchive)
    {
        $this->authorize('view', $deathArchive);

        return Inertia::render('PagesMembers/archive/death/Show', [
            'certificate' => $deathArchive->load(['creator', 'updater']),
        ]);
    }

    /**
     * Show the form for editing the certificate
     */
    public function edit(DeathArchiveCertificate $deathArchive)
    {
        $this->authorize('update', $deathArchive);

        return Inertia::render('PagesMembers/archive/death/Edit', [
            'certificate' => $deathArchive,
        ]);
    }

    /**
     * Update the specified certificate
     */
    public function update(UpdateDeathArchiveCertificateRequest $request, DeathArchiveCertificate $deathArchive)
    {
        $validated = $request->validated();

        try {
            // If new file uploaded, replace old one
            if ($request->hasFile('file')) {
                // Delete old file from S3
                $oldPath = trim($deathArchive->folder_path, '/') . '/' . $deathArchive->file_name;
                if (Storage::disk('s3')->exists($oldPath)) {
                    Storage::disk('s3')->delete($oldPath);
                }

                // Upload new file
                $file = $request->file('file');
                $folderPath = 'archive/death/' . $validated['reg_year'];
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());

                $file->storeAs($folderPath, $fileName, 's3');

                $validated['folder_path'] = $folderPath;
                $validated['file_name'] = $fileName;
            }

            $validated['updated_by'] = auth()->id();
            $deathArchive->update($validated);

            return redirect()
                ->route('archive.death.index')
                ->with('success', 'Death archive certificate updated successfully.');
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
    public function destroy(Request $request, DeathArchiveCertificate $deathArchive)
    {
        $this->authorize('delete', $deathArchive);

        $deathArchive->delete();

        return redirect()
            ->route('archive.death.index', $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']))
            ->with('success', 'Death archive certificate deleted successfully.');
    }

    /**
     * Restore a soft-deleted certificate
     */
    public function restore($id)
    {
        $certificate = DeathArchiveCertificate::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $certificate);

        $certificate->restore();

        return redirect()
            ->route('archive.death.index')
            ->with('success', 'Death archive certificate restored successfully.');
    }

    /**
     * Download the certificate file from S3
     */
    public function download(DeathArchiveCertificate $deathArchive)
    {
        $this->authorize('download', $deathArchive);

        if (!$deathArchive->fileExists()) {
            return redirect()
                ->back()
                ->with('error', 'Certificate file not found in storage.');
        }

        $path = trim($deathArchive->folder_path, '/') . '/' . $deathArchive->file_name;

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
