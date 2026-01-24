<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Members\Models\DeathRecord;
use Modules\Members\Models\Member;
use Modules\Members\Models\Parish;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelPdf\Facades\Pdf;

class DeathRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = DeathRecord::with(['member', 'burialParish']);

        // Archive filter
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('burial_reg_no', 'like', "%{$search}%")
                    ->orWhere('deceased_name', 'like', "%{$search}%")
                    ->orWhere('deceased_surname', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sort = $request->get('sort', 'death_date');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $deathRecords = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('DeathRecords/Index', [
            'deathRecords' => $deathRecords,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'isArchived']),
        ]);
    }

    public function create(Request $request)
    {
        $parishes = Parish::orderBy('name')->get();
        $member = null;

        // If member_id is provided, fetch member data
        if ($request->has('member_id')) {
            $member = Member::with(['community', 'father', 'mother', 'spouse'])
                ->find($request->member_id);
        }

        return Inertia::render('DeathRecords/Create', [
            'parishes' => $parishes,
            'member' => $member,
        ]);
    }

    public function store(Request $request)
    {
        Log::info($request);
        $validated = $request->validate([
            'death_archive_certificate_id' => 'nullable|exists:death_archive_certificates,id',
            'member_id' => 'nullable|exists:members,id',
            'death_date' => 'nullable|date',
            'burial_date' => 'nullable|date',
            'burial_reg_no' => 'nullable|string|max:255',
            // 'burial_parish_id' => 'nullable|exists:parishes,id',
            'deceased_name' => 'nullable|string|max:255',
            'deceased_surname' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:100',
            'residence' => 'nullable|string',
            'age' => 'nullable|integer|min:0|max:150',
            'nationality' => 'nullable|string|max:100',
            'cause_of_death' => 'nullable|string|max:255',
            'place_of_burial' => 'nullable|string|max:255',
            'minister_name' => 'nullable|string|max:255',
            'death_remarks' => 'nullable|string',
        ]);
Log::info($validated);
        $deathRecord = DeathRecord::create($validated);

        // Update: Set the foreign key in member table
        if ($deathRecord->member_id) {
            Member::where('id', $deathRecord->member_id)
                  ->update(['deathrecord_id' => $deathRecord->id]);
        }

        // If created from archive certificate, redirect back to archive view
        if ($validated['death_archive_certificate_id'] ?? null) {
            return redirect()->route('archive.death.certificates.death', $validated['death_archive_certificate_id'])
                ->with('success', 'Death record created successfully from archive certificate.');
        }

        return redirect()->route('death-records.index')
            ->with('success', 'Death record created successfully.');
    }

    public function show(DeathRecord $deathRecord)
    {
        $deathRecord->load(['member', 'burialParish']);

        return Inertia::render('DeathRecords/Show', [
            'deathRecord' => $deathRecord,
        ]);
    }

    public function edit(DeathRecord $deathRecord)
    {
        $deathRecord->load(['member', 'burialParish']);
        $parishes = Parish::orderBy('name')->get();

        return Inertia::render('DeathRecords/Edit', [
            'deathRecord' => $deathRecord,
            'parishes' => $parishes,
        ]);
    }

    public function update(Request $request, DeathRecord $deathRecord)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'death_date' => 'nullable|date',
            'burial_date' => 'nullable|date',
            'burial_reg_no' => 'nullable|string|max:255',
            'burial_parish_id' => 'nullable|exists:parishes,id',
            'deceased_name' => 'nullable|string|max:255',
            'deceased_surname' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:100',
            'residence' => 'nullable|string',
            'age' => 'nullable|integer|min:0|max:150',
            'nationality' => 'nullable|string|max:100',
            'cause_of_death' => 'nullable|string|max:255',
            'place_of_burial' => 'nullable|string|max:255',
            'minister_name' => 'nullable|string|max:255',
            'death_remarks' => 'nullable|string',
        ]);

        $deathRecord->update($validated);

        return redirect()->route('death-records.index')
            ->with('success', 'Death record updated successfully.');
    }

    public function destroy(DeathRecord $deathRecord)
    {
        // Clear the foreign key in member table first
        if ($deathRecord->member_id) {
            Member::where('id', $deathRecord->member_id)
                  ->update(['deathrecord_id' => null]);
        }

        $deathRecord->delete();

        return redirect()->route('death-records.index')
            ->with('success', 'Death record deleted successfully.');
    }

    /**
     * Restore a soft-deleted death record
     */
    public function restore($id)
    {
        $deathRecord = DeathRecord::onlyTrashed()->findOrFail($id);
        $deathRecord->restore();

        return redirect()->route('death-records.index')
            ->with('success', 'Death record restored successfully.');
    }

    /**
     * Generate and download burial certificate PDF
     */
    public function downloadPdf(Request $request, DeathRecord $deathRecord)
    {
        // Get PDF options from query parameters
        $includeRemark = $request->query('include_remark', '1') === '1';
        $signBy = $request->query('sign_by', 'parish_priest');
        $signeeName = $request->query('signee_name', '');
        $printDate = $request->query('print_date', now()->format('Y-m-d'));

        // Format print date
        $formattedPrintDate = $printDate ? \Carbon\Carbon::parse($printDate)->format('d/m/Y') : now()->format('d/m/Y');

        // Helper to format dates (handles both string and Carbon instances)
        $formatDate = function ($date, $format = 'd/m/Y') {
            if (!$date) return null;
            if ($date instanceof \Carbon\Carbon) {
                return $date->format($format);
            }
            return \Carbon\Carbon::parse($date)->format($format);
        };

        // Load relationships
        $deathRecord->load(['member', 'burialParish']);

        // Get deceased name - use member data if available, otherwise use manually entered data
        $deceasedName = $deathRecord->member_id && $deathRecord->member
            ? $deathRecord->member->first_name
            : $deathRecord->deceased_name;
        $deceasedSurname = $deathRecord->member_id && $deathRecord->member
            ? $deathRecord->member->last_name
            : $deathRecord->deceased_surname;

        // Prepare data for the template
        $data = [
            // Death/Burial data
            'death_date' => $formatDate($deathRecord->death_date),
            'burial_date' => $formatDate($deathRecord->burial_date),
            'burial_reg_no' => $deathRecord->burial_reg_no,
            'burial_year' => $deathRecord->burial_date ? \Carbon\Carbon::parse($deathRecord->burial_date)->year : null,

            // Deceased data
            'deceased_name' => $deceasedName,
            'deceased_surname' => $deceasedSurname,
            'relationship' => $deathRecord->relationship,
            'residence' => $deathRecord->residence,
            'age' => $deathRecord->age,
            'nationality' => $deathRecord->nationality,
            'cause_of_death' => $deathRecord->cause_of_death,
            'place_of_burial' => $deathRecord->place_of_burial,

            // Minister
            'minister_name' => $deathRecord->minister_name,

            // Remarks
            'death_remarks' => $deathRecord->death_remarks,

            // Parish info
            'parish_name' => config('app.parish_name', 'Church of Our Lady of Salvation'),
            'parish_address' => config('app.parish_address', 'Dadar (W), Mumbai - 400 028'),
            'parish_priest_name' => config('app.parish_priest_name', 'Parish Priest'),
            'issued_date' => now()->format('d/m/Y'),

            // Template config
            'template_config' => [
                'show_logo' => config('app.certificate_show_logo', true),
                'logo_url' => $this->makeLogoDataUrl(config('app.certificate_logo_path', 'images/logo.png')),
            ],

            // PDF print options (passed from modal, not saved)
            'pdf_options' => [
                'include_remark' => $includeRemark,
                'sign_by' => $signBy,
                'signee_name' => $signeeName,
                'print_date' => $formattedPrintDate,
                'sign_by_label' => $signBy === 'parish_priest' ? 'Parish Priest' : 'For Parish Priest',
            ],
        ];

        // Generate filename
        $filename = sprintf(
            'Burial_Certificate_%s_%s_%s.pdf',
            str_replace(' ', '_', $deathRecord->deceased_name),
            str_replace(' ', '_', $deathRecord->deceased_surname),
            now()->format('Y-m-d')
        );

        // Generate PDF with Spatie (Chromium-based - supports modern CSS)
        $pdf = Pdf::view('certificates.templates.parochial_register_burial', $data)
            ->format('a4')
            ->name($filename);

        // Configure Browsershot to use Puppeteer's Chrome
        // IMPORTANT: Use config() not env() - env() doesn't work when config is cached
        $chromePath = config('app.chrome_path', '/usr/bin/google-chrome');
        if ($chromePath && file_exists($chromePath)) {
            $pdf->withBrowsershot(function ($browsershot) use ($chromePath) {
                $browsershot->setChromePath($chromePath)
                    ->noSandbox()
                    ->setOption('args', [
                        '--disable-setuid-sandbox',
                        '--disable-dev-shm-usage',
                        '--disable-gpu',
                        '--no-sandbox',
                        '--disable-crash-reporter',
                        '--disable-breakpad',
                        '--disable-extensions',
                        '--disable-sync',
                        '--no-first-run',
                        '--no-zygote',
                        '--single-process',
                        '--disable-background-networking',
                        '--disable-default-apps',
                        '--mute-audio',
                        '--user-data-dir=/tmp/chrome-user-data',
                        '--crash-dumps-dir=/tmp/chrome-user-data'
                    ]);
            });
        }

        return $pdf->download();
    }

    /**
     * Convert logo path to data URL for PDF generation
     */
    protected function makeLogoDataUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $fullPath = public_path($path);

        if (is_file($fullPath) && is_readable($fullPath)) {
            $mime = mime_content_type($fullPath) ?: 'image/png';
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
        }

        return null;
    }

    /**
     * Get death record by member ID
     */
    public function getByMember($memberId)
    {
        $deathRecord = DeathRecord::where('member_id', $memberId)
            ->with(['member', 'burialParish'])
            ->first();

        return response()->json($deathRecord);
    }
}
