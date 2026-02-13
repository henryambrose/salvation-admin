<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Members\Models\BaptismRecord;
use Modules\Members\Models\Member;
use Modules\Members\Models\Parish;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelPdf\Facades\Pdf;

class BaptismRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = BaptismRecord::with(['member.community', 'baptismParish']);

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
                $q->where('baptism_reg_no', 'like', "%{$search}%")
                    ->orWhere('godfather_name', 'like', "%{$search}%")
                    ->orWhere('godmother_name', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by baptism year
        if ($request->filled('baptism_year')) {
            $query->whereYear('baptism_date', $request->baptism_year);
        }

        // Sorting
        $sort = $request->get('sort', 'baptism_date');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $baptismRecords = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('BaptismRecords/Index', [
            'baptismRecords' => $baptismRecords,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'isArchived', 'baptism_year']),
        ]);
    }

    public function create(Request $request)
    {
        $parishes = Parish::orderBy('name')->get();
        $member = null;

        // If member_id is provided, fetch member data
        if ($request->has('member_id')) {
            $member = Member::with(['community', 'father', 'mother', 'spouse', 'baptismRecord'])
                ->find($request->member_id);
        }

        return Inertia::render('BaptismRecords/Create', [
            'parishes' => $parishes,
            'member' => $member,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'birth_archive_certificate_id' => 'nullable|exists:birth_archive_certificates,id',
            'baptized_name' => 'nullable|string|max:255',
            'baptized_surname' => 'nullable|string|max:255',
            'baptism_date' => 'nullable|date',
            'confirmation_date' => 'nullable|date',
            'baptism_reg_no' => 'nullable|string|max:255',
            'place_of_baptism' => 'nullable|string|max:255',
            'baptism_parish_id' => 'nullable|exists:parishes,id',
            'place_of_birth' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'father_name' => 'nullable|string|max:255',
            'father_residence' => 'nullable|string',
            'father_profession' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'godfather_name' => 'nullable|string|max:255',
            'godfather_residence' => 'nullable|string',
            'godmother_name' => 'nullable|string|max:255',
            'godmother_residence' => 'nullable|string',
            'minister_name' => 'nullable|string|max:255',
            'baptism_remarks' => 'nullable|string',
            'birth_date_text' => 'nullable|string|max:255',
            'baptism_reg_year' => 'nullable|string|max:255',
            'confirmation' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
        ]);

        $dateOfBirth = $validated['date_of_birth'] ?? null;
        unset($validated['date_of_birth']);

        if (!isset($validated['member_id'])) {
            // No member linked - store birth_date on the baptism record
            $validated['birth_date'] = $dateOfBirth;
        }

        $baptismRecord = BaptismRecord::create($validated);

        // Update: Set the foreign key in member table and date_of_birth
        if ($baptismRecord->member_id) {
            $memberUpdate = ['baptismrecord_id' => $baptismRecord->id];
            if ($dateOfBirth !== null) {
                $memberUpdate['date_of_birth'] = $dateOfBirth;
            }
            Member::where('id', $baptismRecord->member_id)
                  ->update($memberUpdate);
        }

        // If created from archive certificate, redirect to the unified view
        if ($validated['birth_archive_certificate_id'] ?? null) {
            return redirect()->route('archive.birth.certificates.baptism', $validated['birth_archive_certificate_id'])
                ->with('success', 'Baptism record created successfully from archive certificate.');
        }

        return redirect()->route('baptism-records.index')
            ->with('success', 'Baptism record created successfully.');
    }

    public function show(BaptismRecord $baptismRecord)
    {
        $baptismRecord->load(['member.community', 'baptismParish']);

        return Inertia::render('BaptismRecords/Show', [
            'baptismRecord' => $baptismRecord,
        ]);
    }

    public function edit(BaptismRecord $baptismRecord)
    {
        $baptismRecord->load(['member.community', 'baptismParish', 'birthArchiveCertificate']);
        $parishes = Parish::orderBy('name')->get();
        return Inertia::render('BaptismRecords/Edit', [
            'baptismRecord' => $baptismRecord,
            'parishes' => $parishes,
        ]);
    }

    public function update(Request $request, BaptismRecord $baptismRecord)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'baptized_name' => 'nullable|string|max:255',
            'baptized_surname' => 'nullable|string|max:255',
            'baptism_date' => 'nullable|date',
            'confirmation_date' => 'nullable|date',
            'baptism_reg_no' => 'nullable|string|max:255',
            'place_of_baptism' => 'nullable|string|max:255',
            'baptism_parish_id' => 'nullable|exists:parishes,id',
            'place_of_birth' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'father_name' => 'nullable|string|max:255',
            'father_residence' => 'nullable|string',
            'father_profession' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'godfather_name' => 'nullable|string|max:255',
            'godfather_residence' => 'nullable|string',
            'godmother_name' => 'nullable|string|max:255',
            'godmother_residence' => 'nullable|string',
            'minister_name' => 'nullable|string|max:255',
            'baptism_remarks' => 'nullable|string',
            'birth_date_text' => 'nullable|string|max:255',
            'baptism_reg_year' => 'nullable|string|max:255',
            'confirmation' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
        ]);

        $dateOfBirth = $validated['date_of_birth'] ?? null;
        unset($validated['date_of_birth']);

        if ($baptismRecord->member_id) {
            // Record is linked to a member - update member's date_of_birth
            if ($dateOfBirth !== null) {
                Member::where('id', $baptismRecord->member_id)
                    ->update(['date_of_birth' => $dateOfBirth]);
            }
        } else {
            // Record is not linked to a member - store on baptism record itself
            $validated['birth_date'] = $dateOfBirth;
        }

        $baptismRecord->update($validated);

        return redirect()->route('baptism-records.index')
            ->with('success', 'Baptism record updated successfully.');
    }

    public function destroy(BaptismRecord $baptismRecord)
    {
        // Clear the foreign key in member table first
        if ($baptismRecord->member_id) {
            Member::where('id', $baptismRecord->member_id)
                  ->update(['baptismrecord_id' => null]);
        }

        $baptismRecord->delete();

        return redirect()->route('baptism-records.index')
            ->with('success', 'Baptism record deleted successfully.');
    }

    /**
     * Restore a soft-deleted baptism record
     */
    public function restore($id)
    {
        $baptismRecord = BaptismRecord::onlyTrashed()->findOrFail($id);
        $baptismRecord->restore();

        return redirect()->route('baptism-records.index')
            ->with('success', 'Baptism record restored successfully.');
    }

    /**
     * Get baptism record by member ID
     */
    public function getByMember($memberId)
    {
        $baptismRecord = BaptismRecord::where('member_id', $memberId)
            ->with(['member', 'baptismParish'])
            ->first();

        return response()->json($baptismRecord);
    }

    /**
     * Generate and download baptism certificate PDF
     */
    public function downloadPdf(Request $request, BaptismRecord $baptismRecord)
    {
        try {
        // Get PDF options from query parameters
        $includeRemark = $request->query('include_remark', '1') === '1';
        $signBy = $request->query('sign_by', 'parish_priest');
        $signeeName = $request->query('signee_name', '');
        $printDate = $request->query('print_date', now()->format('Y-m-d'));

        // Format print date
        $formattedPrintDate = $printDate ? \Carbon\Carbon::parse($printDate)->format('d/m/Y') : now()->format('d/m/Y');

        // Helper to format dates (handles both string and Carbon instances)
        $formatDate = function ($date, $format = 'd/m/Y') {
            if (!$date || $date === '' || $date === '0000-00-00') return null;
            try {
                if ($date instanceof \Carbon\Carbon) {
                    return $date->format($format);
                }
                return \Carbon\Carbon::parse($date)->format($format);
            } catch (\Exception $e) {
                \Log::warning("Failed to format date: {$date}", ['error' => $e->getMessage()]);
                return null;
            }
        };

        // Load relationships
        $baptismRecord->load([
            'member.community',
            'member.father',
            'member.mother',
            'member.spouse',
            'member.confirmationParish',
            'member.marriageRecord.bridegroom',
            'member.marriageRecord.bride',
            'baptismParish'
        ]);

        $member = $baptismRecord->member;

        // Debug: Log if member or date_of_birth is missing
        if (!$member) {
            \Log::warning("Baptism record {$baptismRecord->id} has no associated member");
        } elseif (!$member->date_of_birth) {
            \Log::warning("Member {$member->id} has no date_of_birth", [
                'member_name' => "{$member->first_name} {$member->last_name}",
                'baptism_record_id' => $baptismRecord->id
            ]);
        }

        // Get confirmation info if exists (from Member table)
        $confirmationInfo = null;
        if ($member?->confirmation_date) {
            $confirmationInfo = [
                'date' => $formatDate($member->confirmation_date),
                'place' => $member->confirmationParish?->name ?? config('app.parish_name', 'Church of Our Lady of Salvation'),
            ];
        }

        // Get marriage info if exists (from MarriageRecord table via relationship)
        $marriageInfo = null;
        $marriageRecord = $member?->marriageRecord;
        if ($marriageRecord && $marriageRecord->marriage_date) {
            // Determine spouse: if member is bridegroom, spouse is bride and vice versa
            $spouse = null;
            if ($marriageRecord->bridegroom_member_id === $member->id) {
                // Member is bridegroom, get bride as spouse
                $spouse = $marriageRecord->bride;
                if (!$spouse && ($marriageRecord->bride_name || $marriageRecord->bride_surname)) {
                    // Use manually entered bride name
                    $spouseName = trim(($marriageRecord->bride_name ?? '') . ' ' . ($marriageRecord->bride_surname ?? ''));
                }
            } else {
                // Member is bride, get bridegroom as spouse
                $spouse = $marriageRecord->bridegroom;
                if (!$spouse && ($marriageRecord->bridegroom_name || $marriageRecord->bridegroom_surname)) {
                    // Use manually entered bridegroom name
                    $spouseName = trim(($marriageRecord->bridegroom_name ?? '') . ' ' . ($marriageRecord->bridegroom_surname ?? ''));
                }
            }

            // Build spouse name from member record if available
            if ($spouse) {
                $spouseName = trim("{$spouse->first_name} " . ($spouse->middle_name ? "{$spouse->middle_name} " : '') . "{$spouse->last_name}");
            }

            $marriageInfo = [
                'date' => $formatDate($marriageRecord->marriage_date),
                'place' => $marriageRecord->parish_of_marriage ?? config('app.parish_name', 'Church of Our Lady of Salvation'),
                'spouse' => $spouseName ?? '',
            ];
        }

        // Get parent names
        $father = $member?->father;
        $mother = $member?->mother;

        // Prepare data for the template
        $data = [
            // Member data
            'member_full_name' => $member ? trim("{$member->first_name} {$member->middle_name} {$member->last_name}") : trim("{$baptismRecord->baptized_name} {$baptismRecord->baptized_surname}"),
            'member_first_name' => $member?->first_name ?? $baptismRecord->baptized_name ?? '',
            'member_middle_name' => $member?->middle_name ?? '',
            'member_last_name' => $member?->last_name ?? $baptismRecord->baptized_surname ?? '',
            'member_dob' => $formatDate($member?->date_of_birth),

            // Baptism data
            'baptism_date' => $formatDate($baptismRecord->baptism_date),
            'baptism_reg_no' => $baptismRecord->baptism_reg_no,
            'baptism_reg_no_short' => $baptismRecord->baptism_reg_no ? (int) filter_var($baptismRecord->baptism_reg_no, FILTER_SANITIZE_NUMBER_INT) : null,
            'baptism_year' => $baptismRecord->baptism_date ? \Carbon\Carbon::parse($baptismRecord->baptism_date)->year : null,
            'place_of_baptism' => $baptismRecord->place_of_baptism ?: ($baptismRecord->baptismParish?->name ?? ''),
            'baptism_parish' => $baptismRecord->baptismParish?->name ?? '',

            // Parochial register fields
            'place_of_birth' => $baptismRecord->place_of_birth,
            'nationality' => $baptismRecord->nationality,

            // Father and Mother
            'father_name' => $baptismRecord->father_name ?: ($father ? trim("{$father->first_name} {$father->middle_name} {$father->last_name}") : ''),
            'mother_name' => $baptismRecord->mother_name ?: ($mother ? trim("{$mother->first_name} {$mother->middle_name} {$mother->last_name}") : ''),
            'father_residence' => $baptismRecord->father_residence,
            'father_profession' => $baptismRecord->father_profession,

            // Godparents
            'godfather_name' => $baptismRecord->godfather_name,
            'godfather_residence' => $baptismRecord->godfather_residence,
            'godmother_name' => $baptismRecord->godmother_name,
            'godmother_residence' => $baptismRecord->godmother_residence,

            // Minister
            'minister_name' => $baptismRecord->minister_name,

            // Remarks
            'baptism_remarks' => $baptismRecord->baptism_remarks,

            // Cross-references
            'confirmation_info' => $confirmationInfo,
            'marriage_info' => $marriageInfo,

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
            'Baptism_Certificate_%s_%s.pdf',
            str_replace(' ', '_', ($member?->first_name ?? $baptismRecord->baptized_name ?? 'Unknown') . '_' . ($member?->last_name ?? $baptismRecord->baptized_surname ?? '')),
            now()->format('Y-m-d')
        );

        // Generate PDF with Spatie (Chromium-based - supports modern CSS)
        $pdf = Pdf::view('certificates.templates.parochial_register_baptism', $data)
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
        } catch (\Exception $e) {
            \Log::error('Failed to generate baptism certificate PDF', [
                'baptism_record_id' => $baptismRecord->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to generate certificate PDF',
                'message' => config('app.debug') ? $e->getMessage() : 'Please contact administrator'
            ], 500);
        }
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
