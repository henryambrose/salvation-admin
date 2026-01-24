<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Members\Http\Requests\StoreBirthArchiveCertificateRequest;
use Modules\Members\Http\Requests\UpdateBirthArchiveCertificateRequest;
use Modules\Members\Models\BirthArchiveCertificate;
use Modules\Members\Models\Parish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BirthArchiveCertificateController extends Controller
{
    /**
     * Display a listing of birth archive certificates
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', BirthArchiveCertificate::class);

        $query = BirthArchiveCertificate::query();

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

        // Eager load baptism record relationship for status indicators
        $query->with('baptismRecord:id,birth_archive_certificate_id');

        // Pagination
        $perPage = $request->input('perPage', 10);
        $certificates = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('archive/birth/Index', [
            'fetchUrl' => route('archive.birth.index'),
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
        $this->authorize('create', BirthArchiveCertificate::class);

        return Inertia::render('archive/birth/Create');
    }

    /**
     * Store a newly created certificate
     */
    public function store(StoreBirthArchiveCertificateRequest $request)
    {
        $validated = $request->validated();

        try {
            // Upload file to configured private storage (S3 in production, local in development)
            $file = $request->file('file');
            $folderPath = 'archive/birth/' . $validated['reg_year'];
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());

            $file->storeAs($folderPath, $fileName, config('filesystems.private_storage'));

            // Create record
            BirthArchiveCertificate::create([
                'folder_path' => $folderPath,
                'file_name' => $fileName,
                'reg_year' => $validated['reg_year'],
                'reg_no' => $validated['reg_no'],
                'birth_year' => $validated['birth_year'],
                'birth_month' => $validated['birth_month'],
                'birth_day' => $validated['birth_day'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('archive.birth.index')
                ->with('success', 'Birth archive certificate created successfully.');
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
    public function show(BirthArchiveCertificate $birthArchive)
    {
        $this->authorize('view', $birthArchive);

        return Inertia::render('archive/birth/Show', [
            'certificate' => $birthArchive->load(['creator', 'updater']),
        ]);
    }

    /**
     * View archive certificate with baptism record integration
     * Shows create form if no baptism record exists, otherwise shows baptism details
     */
    public function viewWithBaptism(BirthArchiveCertificate $birthArchive)
    {
        $this->authorize('view', $birthArchive);

        // Load the baptism record relationship
        $birthArchive->load(['baptismRecord', 'creator', 'updater']);

        // Check if PDF file exists and show warning if not
        if (!$birthArchive->fileExists()) {
            session()->flash('warning', 'Certificate PDF file has not been uploaded yet.');
        }

        // Determine mode based on whether baptism record exists
        $mode = $birthArchive->baptismRecord ? 'view' : 'create';

        // Prepare data based on mode
        $data = [
            'certificate' => $birthArchive,
            'baptismRecord' => $birthArchive->baptismRecord,
            'mode' => $mode,
        ];

        // If in create mode, load parishes for the dropdown
        if ($mode === 'create') {
            $data['parishes'] = Parish::orderBy('name')
                ->get(['id', 'name']);
        }

        return Inertia::render('archive/birth/BaptismView', $data);
    }

    /**
     * Show the form for editing the certificate
     */
    public function edit(BirthArchiveCertificate $birthArchive)
    {
        $this->authorize('update', $birthArchive);

        return Inertia::render('archive/birth/Edit', [
            'certificate' => $birthArchive,
        ]);
    }

    /**
     * Update the specified certificate
     */
    public function update(UpdateBirthArchiveCertificateRequest $request, BirthArchiveCertificate $birthArchive)
    {
        $validated = $request->validated();

        try {
            // If new file uploaded, replace old one
            if ($request->hasFile('file')) {
                // Delete old file from configured storage
                $oldPath = trim($birthArchive->folder_path, '/') . '/' . $birthArchive->file_name;
                if (Storage::disk(config('filesystems.private_storage'))->exists($oldPath)) {
                    Storage::disk(config('filesystems.private_storage'))->delete($oldPath);
                }

                // Upload new file to configured private storage
                $file = $request->file('file');
                $folderPath = 'archive/birth/' . $validated['reg_year'];
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());

                $file->storeAs($folderPath, $fileName, config('filesystems.private_storage'));

                $validated['folder_path'] = $folderPath;
                $validated['file_name'] = $fileName;
            }

            $validated['updated_by'] = auth()->id();
            $birthArchive->update($validated);

            return redirect()
                ->route('archive.birth.index')
                ->with('success', 'Birth archive certificate updated successfully.');
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
    public function destroy(Request $request, BirthArchiveCertificate $birthArchive)
    {
        $this->authorize('delete', $birthArchive);

        $birthArchive->delete();

        return redirect()
            ->route('archive.birth.index', $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']))
            ->with('success', 'Birth archive certificate deleted successfully.');
    }

    /**
     * Restore a soft-deleted certificate
     */
    public function restore($id)
    {
        $certificate = BirthArchiveCertificate::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $certificate);

        $certificate->restore();

        return redirect()
            ->route('archive.birth.index')
            ->with('success', 'Birth archive certificate restored successfully.');
    }

    /**
     * Download the certificate file from S3
     */
    public function download(BirthArchiveCertificate $birthArchive)
    {
        $this->authorize('download', $birthArchive);

        if (!$birthArchive->fileExists()) {
            return redirect()
                ->back()
                ->with('error', 'Certificate file not found in storage.');
        }

        $path = trim($birthArchive->folder_path, '/') . '/' . $birthArchive->file_name;

        try {
            // Generate temporary signed URL (valid for 5 minutes)
            $url = Storage::disk(config('filesystems.private_storage'))->temporaryUrl($path, now()->addMinutes(5));

            return redirect($url);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to generate download link: ' . $e->getMessage());
        }
    }

    /**
     * Get a temporary URL for viewing the certificate (for iframe embedding)
     */
    public function view(BirthArchiveCertificate $birthArchive)
    {
        $this->authorize('view', $birthArchive);

        if (!$birthArchive->fileExists()) {
            abort(404, 'Certificate file not found in storage.');
        }

        $path = trim($birthArchive->folder_path, '/') . '/' . $birthArchive->file_name;
        $disk = Storage::disk(config('filesystems.private_storage'));

        try {
            // Check if this is S3 or local storage
            if (method_exists($disk, 'temporaryUrl')) {
                // S3: Redirect to temporary signed URL
                $url = $disk->temporaryUrl($path, now()->addMinutes(30));
                return redirect($url);
            } else {
                // Local: Serve the file directly with inline disposition for iframe viewing
                $file = $disk->get($path);
                $mimeType = $disk->mimeType($path);
                
                return response($file, 200)
                    ->header('Content-Type', $mimeType)
                    ->header('Content-Disposition', 'inline; filename="' . basename($path) . '"');
            }
        } catch (\Exception $e) {
            abort(500, 'Failed to generate view link: ' . $e->getMessage());
        }
    }
}
