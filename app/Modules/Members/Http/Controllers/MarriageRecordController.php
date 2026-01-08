<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Members\Models\MarriageRecord;
use Modules\Members\Models\Member;
use Modules\Members\Models\Parish;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\LaravelPdf\Facades\Pdf;

class MarriageRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = MarriageRecord::with(['bridegroom', 'bride', 'marriageParish']);

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
                $q->where('marriage_reg_no', 'like', "%{$search}%")
                    ->orWhere('bridegroom_name', 'like', "%{$search}%")
                    ->orWhere('bride_name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'marriage_date');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $marriageRecords = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('MarriageRecords/Index', [
            'marriageRecords' => $marriageRecords,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'isArchived']),
        ]);
    }

    public function create()
    {
        $parishes = Parish::orderBy('name')->get();

        return Inertia::render('MarriageRecords/Create', [
            'parishes' => $parishes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'marriage_archive_certificate_id' => 'nullable|exists:marriage_archive_certificates,id',
            'marriage_date' => 'nullable|date',
            'marriage_reg_no' => 'nullable|string|max:255',
            'parish_of_marriage' => 'nullable|string',
            'bridegroom_member_id' => 'nullable|exists:members,id',
            'bridegroom_name' => 'nullable|string|max:255',
            'bridegroom_surname' => 'nullable|string|max:255',
            'bridegroom_dob' => 'nullable|date',
            'bridegroom_nationality' => 'nullable|string|max:100',
            'bridegroom_profession' => 'nullable|string|max:255',
            'bridegroom_residence' => 'nullable|string',
            'bridegroom_father_name' => 'nullable|string|max:255',
            'bridegroom_mother_name' => 'nullable|string|max:255',
            'bridegroom_status' => 'nullable|string|max:50',
            'bridegroom_if_widower_whose' => 'nullable|string|max:255',
            'bride_member_id' => 'nullable|exists:members,id',
            'bride_name' => 'nullable|string|max:255',
            'bride_surname' => 'nullable|string|max:255',
            'bride_dob' => 'nullable|date',
            'bride_nationality' => 'nullable|string|max:100',
            'bride_profession' => 'nullable|string|max:255',
            'bride_residence' => 'nullable|string',
            'bride_father_name' => 'nullable|string|max:255',
            'bride_mother_name' => 'nullable|string|max:255',
            'bride_status' => 'nullable|string|max:50',
            'bride_if_widow_whose' => 'nullable|string|max:255',
            'first_witness_name' => 'nullable|string|max:255',
            'first_witness_residence' => 'nullable|string',
            'second_witness_name' => 'nullable|string|max:255',
            'second_witness_residence' => 'nullable|string',
            'minister_name' => 'nullable|string|max:255',
            'marriage_remarks' => 'nullable|string',
        ]);

        $marriageRecord = MarriageRecord::create($validated);

        // Update: Set foreign key for bridegroom
        if ($marriageRecord->bridegroom_member_id) {
            Member::where('id', $marriageRecord->bridegroom_member_id)
                  ->update(['marriagerecord_id' => $marriageRecord->id]);
        }

        // Update: Set foreign key for bride
        if ($marriageRecord->bride_member_id) {
            Member::where('id', $marriageRecord->bride_member_id)
                  ->update(['marriagerecord_id' => $marriageRecord->id]);
        }

        // If created from archive certificate, redirect to the unified view
        if ($validated['marriage_archive_certificate_id'] ?? null) {
            return redirect()->route('archive.marriage.certificates.marriage', $validated['marriage_archive_certificate_id'])
                ->with('success', 'Marriage record created successfully from archive certificate.');
        }

        return redirect()->route('marriage-records.index')
            ->with('success', 'Marriage record created successfully.');
    }

    public function show(MarriageRecord $marriageRecord)
    {
        $marriageRecord->load(['bridegroom', 'bride', 'marriageParish']);

        return Inertia::render('MarriageRecords/Show', [
            'marriageRecord' => $marriageRecord,
        ]);
    }

    public function edit(MarriageRecord $marriageRecord)
    {
        $marriageRecord->load(['bridegroom.community', 'bride.community', 'marriageParish']);
        $parishes = Parish::orderBy('name')->get();

        return Inertia::render('MarriageRecords/Edit', [
            'marriageRecord' => $marriageRecord,
            'parishes' => $parishes,
        ]);
    }

    public function update(Request $request, MarriageRecord $marriageRecord)
    {
        $validated = $request->validate([
            'marriage_date' => 'nullable|date',
            'marriage_reg_no' => 'nullable|string|max:255',
            'parish_of_marriage' => 'nullable|string',
            'bridegroom_member_id' => 'nullable|exists:members,id',
            'bridegroom_name' => 'nullable|string|max:255',
            'bridegroom_surname' => 'nullable|string|max:255',
            'bridegroom_dob' => 'nullable|date',
            'bridegroom_nationality' => 'nullable|string|max:100',
            'bridegroom_profession' => 'nullable|string|max:255',
            'bridegroom_residence' => 'nullable|string',
            'bridegroom_father_name' => 'nullable|string|max:255',
            'bridegroom_mother_name' => 'nullable|string|max:255',
            'bridegroom_status' => 'nullable|string|max:50',
            'bridegroom_if_widower_whose' => 'nullable|string|max:255',
            'bride_member_id' => 'nullable|exists:members,id',
            'bride_name' => 'nullable|string|max:255',
            'bride_surname' => 'nullable|string|max:255',
            'bride_dob' => 'nullable|date',
            'bride_nationality' => 'nullable|string|max:100',
            'bride_profession' => 'nullable|string|max:255',
            'bride_residence' => 'nullable|string',
            'bride_father_name' => 'nullable|string|max:255',
            'bride_mother_name' => 'nullable|string|max:255',
            'bride_status' => 'nullable|string|max:50',
            'bride_if_widow_whose' => 'nullable|string|max:255',
            'first_witness_name' => 'nullable|string|max:255',
            'first_witness_residence' => 'nullable|string',
            'second_witness_name' => 'nullable|string|max:255',
            'second_witness_residence' => 'nullable|string',
            'minister_name' => 'nullable|string|max:255',
            'marriage_remarks' => 'nullable|string',
        ]);

        $marriageRecord->update($validated);

        return redirect()->route('marriage-records.index')
            ->with('success', 'Marriage record updated successfully.');
    }

    public function destroy(MarriageRecord $marriageRecord)
    {
        // Clear foreign keys for both members
        if ($marriageRecord->bridegroom_member_id) {
            Member::where('id', $marriageRecord->bridegroom_member_id)
                  ->update(['marriagerecord_id' => null]);
        }

        if ($marriageRecord->bride_member_id) {
            Member::where('id', $marriageRecord->bride_member_id)
                  ->update(['marriagerecord_id' => null]);
        }

        $marriageRecord->delete();

        return redirect()->route('marriage-records.index')
            ->with('success', 'Marriage record deleted successfully.');
    }

    /**
     * Restore a soft-deleted marriage record
     */
    public function restore($id)
    {
        $marriageRecord = MarriageRecord::onlyTrashed()->findOrFail($id);
        $marriageRecord->restore();

        return redirect()->route('marriage-records.index')
            ->with('success', 'Marriage record restored successfully.');
    }

    /**
     * Get marriage record by member ID
     */
    public function getByMember($memberId)
    {
        $marriageRecord = MarriageRecord::where('bridegroom_member_id', $memberId)
            ->orWhere('bride_member_id', $memberId)
            ->with(['bridegroom', 'bride', 'marriageParish'])
            ->first();

        return response()->json($marriageRecord);
    }

    /**
     * Generate and download marriage certificate PDF
     */
    public function downloadPdf(MarriageRecord $marriageRecord)
    {
        // Helper to format dates (handles both string and Carbon instances)
        $formatDate = function ($date, $format = 'd/m/Y') {
            if (!$date) return null;
            if ($date instanceof \Carbon\Carbon) {
                return $date->format($format);
            }
            return \Carbon\Carbon::parse($date)->format($format);
        };

        // Load relationships
        $marriageRecord->load([
            'bridegroom',
            'bride'

        ]);

        // Get bridegroom name - use member data if available, otherwise use manually entered data
        $bridegroomName = $marriageRecord->bridegroom_member_id && $marriageRecord->bridegroom
            ? $marriageRecord->bridegroom->first_name
            : $marriageRecord->bridegroom_name;
        $bridegroomSurname = $marriageRecord->bridegroom_member_id && $marriageRecord->bridegroom
            ? $marriageRecord->bridegroom->last_name
            : $marriageRecord->bridegroom_surname;

        // Get bride name - use member data if available, otherwise use manually entered data
        $brideName = $marriageRecord->bride_member_id && $marriageRecord->bride
            ? $marriageRecord->bride->first_name
            : $marriageRecord->bride_name;
        $brideSurname = $marriageRecord->bride_member_id && $marriageRecord->bride
            ? $marriageRecord->bride->last_name
            : $marriageRecord->bride_surname;

        // Prepare data for the template
        $data = [
            // Marriage data
            'marriage_date' => $formatDate($marriageRecord->marriage_date),
            'marriage_reg_no' => $marriageRecord->marriage_reg_no,
            'marriage_year' => $marriageRecord->marriage_date ? \Carbon\Carbon::parse($marriageRecord->marriage_date)->year : null,
            'parish_of_marriage' => $marriageRecord->parish_of_marriage?? '',

            // Bridegroom data
            'bridegroom_name' => $bridegroomName,
            'bridegroom_surname' => $bridegroomSurname,
            'bridegroom_dob' => $formatDate($marriageRecord->bridegroom_dob),
            'bridegroom_nationality' => $marriageRecord->bridegroom_nationality,
            'bridegroom_profession' => $marriageRecord->bridegroom_profession,
            'bridegroom_residence' => $marriageRecord->bridegroom_residence,
            'bridegroom_father_name' => $marriageRecord->bridegroom_father_name,
            'bridegroom_mother_name' => $marriageRecord->bridegroom_mother_name,
            'bridegroom_status' => $marriageRecord->bridegroom_status,
            'bridegroom_if_widower_whose' => $marriageRecord->bridegroom_if_widower_whose,

            // Bride data
            'bride_name' => $brideName,
            'bride_surname' => $brideSurname,
            'bride_dob' => $formatDate($marriageRecord->bride_dob),
            'bride_nationality' => $marriageRecord->bride_nationality,
            'bride_profession' => $marriageRecord->bride_profession,
            'bride_residence' => $marriageRecord->bride_residence,
            'bride_father_name' => $marriageRecord->bride_father_name,
            'bride_mother_name' => $marriageRecord->bride_mother_name,
            'bride_status' => $marriageRecord->bride_status,
            'bride_if_widow_whose' => $marriageRecord->bride_if_widow_whose,

            // Witnesses
            'first_witness_name' => $marriageRecord->first_witness_name,
            'first_witness_residence' => $marriageRecord->first_witness_residence,
            'second_witness_name' => $marriageRecord->second_witness_name,
            'second_witness_residence' => $marriageRecord->second_witness_residence,

            // Minister
            'minister_name' => $marriageRecord->minister_name,

            // Remarks
            'marriage_remarks' => $marriageRecord->marriage_remarks,

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
        ];

        // Generate filename
        $filename = sprintf(
            'Marriage_Certificate_%s_%s_%s.pdf',
            str_replace(' ', '_', $marriageRecord->bridegroom_surname),
            str_replace(' ', '_', $marriageRecord->bride_surname),
            now()->format('Y-m-d')
        );

        // Generate PDF with Spatie (Chromium-based - supports modern CSS)
        $pdf = Pdf::view('certificates.templates.parochial_register_marriage', $data)
            ->format('a4')
            ->name($filename);

        // Configure Browsershot to use Puppeteer's Chrome
        $chromePath = env('PUPPETEER_EXECUTABLE_PATH', '/var/www/.cache/puppeteer/chrome/linux-143.0.7499.169/chrome-linux64/chrome');
        if (file_exists($chromePath)) {
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
                        '--mute-audio'
                    ]);
            });
        }

        return $pdf->inline();
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
}
