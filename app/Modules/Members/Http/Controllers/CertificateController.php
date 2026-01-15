<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Members\Models\Member;
use Modules\Members\Models\CertificateTemplate;
use Modules\Members\Models\CertificateRecord;
use Modules\Members\Models\CertificateType;
use Modules\Members\Services\CertificateGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class CertificateController extends Controller
{
  protected $certificateService;

  public function __construct(CertificateGenerationService $certificateService)
  {
    $this->certificateService = $certificateService;
  }

  /**
   * Display a listing of certificates
   */
  public function index(Request $request)
  {
    $this->authorize('list-certificate');

    $perPage = $request->get('perPage', 25);
    $search = $request->get('search');
    $type = $request->get('type');
    $memberId = $request->get('member_id');

    $query = CertificateRecord::with(['member', 'template', 'issuer', 'certificateType'])
      ->orderBy('issued_date', 'desc');

    if ($search) {
      $query->whereHas('member', function ($q) use ($search) {
        $q->where('first_name', 'like', "%{$search}%")
          ->orWhere('last_name', 'like', "%{$search}%")
          ->orWhere('family_no', 'like', "%{$search}%");
      })->orWhere('certificate_number', 'like', "%{$search}%");
    }

    if ($type) {
      $query->whereHas('certificateType', function ($q) use ($type) {
        $q->where('code', $type);
      });
    }

    if ($memberId) {
      $query->where('member_id', $memberId);
    }

    $certificates = $query->paginate($perPage)->withQueryString();

    // Get certificate types from database
    $certificateTypes = CertificateType::active()
      ->ordered()
      ->get(['id', 'name', 'code'])
      ->map(function ($type) {
        return [
          'value' => $type->id,
          'label' => $type->name,
          'code' => $type->code
        ];
      });

    return Inertia::render('certificates/Index', [
      'certificates' => $certificates,
      'filters' => $request->only(['search', 'type', 'member_id', 'perPage']),
      'certificateTypes' => $certificateTypes,
      'canGenerateCertificates' => Gate::allows('generate-certificate'),
      'canViewCertificateHistory' => Gate::allows('view-certificate-history'),
      'canReprintCertificates' => Gate::allows('reprint-certificate'),
    ]);
  }

  /**
   * Search members for certificate generation (AJAX endpoint)
   */
  public function searchMembers(Request $request)
  {
    $query = $request->get('query');

    if (!$query || strlen($query) < 2) {
      return response()->json([]);
    }

    $members = Member::with(['community'])
      ->where(function ($q) use ($query) {
        $q->where('first_name', 'like', "%{$query}%")
          ->orWhere('last_name', 'like', "%{$query}%")
          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
          ->orWhere('family_no', 'like', "%{$query}%")
          ->orWhere('member_no', 'like', "%{$query}%")
          ->orWhereHas('community', fn($q2) => $q2->where('name', 'like', "%{$query}%"));
      })
      ->orderBy('first_name')
      ->orderBy('last_name')
      ->limit(10)
      ->get();

    return response()->json($members);
  }

  /**
   * Get member details (AJAX endpoint)
   */
  public function getMember(Member $member)
  {
    return response()->json($member->load(['community']));
  }

  /**
   * Get available certificate types for a specific member (AJAX endpoint)
   */
  public function getMemberAvailableTypes(Member $member)
  {
    $availableTypes = $member->getAvailableCertificateTypes();
    return response()->json($availableTypes);
  }

  /**
   * Get existing certificates for a specific member (AJAX endpoint)
   */
  public function getMemberCertificates(Member $member)
  {
    $certificates = $member->certificates()
      ->with(['template', 'certificateType'])
      ->orderBy('issued_date', 'desc')
      ->get(['id', 'certificate_type_id', 'certificate_number', 'issued_date', 'template_id']);

    return response()->json($certificates);
  }

  /**
   * Show member selection page for certificate generation
   */
  public function generateMemberSelection(Request $request)
  {
    $this->authorize('generate-certificate');

    $certificateTypes = CertificateType::active()
      ->ordered()
      ->get(['id', 'name', 'code'])
      ->map(function ($type) {
        return [
          'value' => $type->id,
          'label' => $type->name,
          'code' => $type->code
        ];
      });

    return Inertia::render('certificates/Generate', [
      'certificateTypes' => $certificateTypes,
      'canGenerateCertificates' => Gate::allows('generate-certificate'),
    ]);
  }

  /**
   * Show the form for generating a new certificate
   */
  public function generate(Member $member, Request $request)
  {
    $this->authorize('generate-certificate');

    $type = $request->get('type');
    $availableTypes = $member->getAvailableCertificateTypes();

    if ($type && !in_array($type, $availableTypes)) {
      return redirect()->back()->with('error', 'This certificate type is not available for this member.');
    }

    $existingCertificates = $member->certificates()
      ->when($type, function ($q) use ($type) {
        return $q->whereHas('certificateType', function ($subq) use ($type) {
          $subq->where('code', (string) $type);
        });
      })
      ->with(['template', 'certificateType'])
      ->get();

    $certificateTypes = CertificateType::active()
      ->ordered()
      ->get(['id', 'name', 'code'])
      ->map(function ($type) {
        return [
          'value' => $type->id,
          'label' => $type->name,
          'code' => $type->code
        ];
      });

    return Inertia::render('certificates/Generate', [
      'member' => $member->load(['community']),
      'availableTypes' => $availableTypes,
      'selectedType' => $type,
      'existingCertificates' => $existingCertificates,
      'certificateTypes' => $certificateTypes,
      'requiredAdditionalData' => $type ? CertificateRecord::getRequiredAdditionalData($type) : [],
      'canGenerateCertificates' => Gate::allows('generate-certificate'),
    ]);
  }

  /**
   * Generate and store a new certificate
   */
  public function store(Request $request)
  {
    $this->authorize('create-certificate');
    $this->authorize('generate-certificate');

    $additionalData = $request->input('additional_data');
    if (is_string($additionalData)) {
      $additionalData = json_decode($additionalData, true) ?? [];
    }

    $validated = $request->validate([
      'member_id' => 'required|exists:members,id',
      'certificate_type_id' => 'required|exists:certificate_types,id',
      'notes' => 'nullable|string|max:1000',
    ]);

    $validated['additional_data'] = $additionalData;

    $member = Member::findOrFail($validated['member_id']);
    $certificateType = CertificateType::findOrFail($validated['certificate_type_id']);

    // Automatically select default template for certificate type
    $template = CertificateTemplate::where('certificate_type_id', $validated['certificate_type_id'])
      ->where('is_default', true)
      ->where('is_active', true)
      ->first();

    if (!$template) {
      return back()->withErrors([
        'certificate_type' => 'No template configured for this certificate type. Please contact administrator.'
      ]);
    }

    if ($template->template_content && !str_contains($template->template_content, 'template_config["logo_url"]')) {
      $template->template_content = str_replace(
        '<div class="header">{{ $parish_name }}</div>',
        '@if(isset($template_config["show_logo"]) && $template_config["show_logo"] && !empty($template_config["logo_url"]))
        <div style="margin-bottom: 20px;">
            <img src="{{ $template_config["logo_url"] }}" alt="Church Logo" style="max-width: 100px; max-height: 100px; object-fit: contain;">
        </div>
    @endif
    <div class="header">{{ $parish_name }}</div>',
        $template->template_content
      );
      if ($template->id) {
        $template->save();
      }
    }

    $availableTypes = $member->getAvailableCertificateTypes();
    if (!in_array($certificateType->code, $availableTypes)) {
      return redirect()->back()->with('error', 'This certificate type is not available for this member.');
    }

    $existingCertificate = CertificateRecord::where('member_id', $member->id)
      ->where('certificate_type_id', $certificateType->id)
      ->first();

    if ($existingCertificate && !$request->has('force_duplicate')) {
      return redirect()->back()
        ->withInput()
        ->with('warning', "A {$certificateType->code} certificate already exists for this member (#{$existingCertificate->certificate_number}). Use the 'Force Generate' option if you need to create a duplicate.")
        ->with('existing_certificate', $existingCertificate->toArray());
    }

    $certificateNumber = CertificateRecord::generateCertificateNumber(
      $certificateType->code,
      $member->id
    );

    $certificate = CertificateRecord::create([
      'member_id' => $member->id,
      'certificate_type_id' => $certificateType->id,
      'template_id' => $template->id,
      'issued_date' => now()->toDateString(),
      'issued_by' => Auth::id(),
      'certificate_number' => $certificateNumber,
      'additional_data' => $validated['additional_data'] ?? [],
      'notes' => $validated['notes'] ?? null,
    ]);

    try {
      $filePath = $this->certificateService->generateCertificate($certificate);

      // Use hash() with get() for S3 compatibility (hash_file() only works with local paths)
      $disk = Storage::disk(config('filesystems.private_storage'));
      $certificate->update([
        'file_path' => $filePath,
        'file_hash' => hash('sha256', $disk->get($filePath)),
      ]);

      return redirect()->route('certificates.show', $certificate)
        ->with('success', 'Certificate generated successfully.');
    } catch (\Exception $e) {
      $certificate->delete();

      return redirect()->back()
        ->with('error', 'Failed to generate certificate: ' . $e->getMessage());
    }
  }

  /**
   * Display the specified certificate
   */
  public function show(Request $request, CertificateRecord $certificate)
  {
    $this->authorize('read-certificate');

    $certificate->load(['member', 'template', 'issuer', 'originalCertificate']);

    return Inertia::render('certificates/Show', [
      'certificate' => $certificate,
      'canGenerateCertificates' => Gate::allows('generate-certificate'),
      'canReprintCertificates' => Gate::allows('reprint-certificate'),
    ]);
  }


  /**
   * Download certificate PDF
   */
  public function download(CertificateRecord $certificate)
  {
    $this->authorize('download-certificate');

    $certificate->load(['member', 'certificateType']);

    if (!$certificate->fileExists()) {
      return redirect()->back()->with('error', 'Certificate file not found.');
    }

    $certificate->incrementDownloadCount();

    $certificateTypeCode = is_object($certificate->certificateType) ? $certificate->certificateType->code : $certificate->certificateType;
    $fileName = "certificate_{$certificateTypeCode}_{$certificate->member->first_name}_{$certificate->member->last_name}_{$certificate->issued_date->format('Y-m-d')}.pdf";

    return response()->download(Storage::disk(config('filesystems.private_storage'))->path($certificate->file_path), $fileName);
  }

  /**
   * Preview certificate without saving
   */
  public function preview(Request $request)
  {
    $this->authorize('preview-certificate-template');

    $additionalData = $request->input('additional_data');
    if (is_string($additionalData)) {
      $additionalData = json_decode($additionalData, true) ?? [];
    }

    $validated = $request->validate([
      'member_id' => 'required|exists:members,id',
      'certificate_type_id' => 'required|exists:certificate_types,id',
      'template_id' => 'nullable|exists:certificate_templates,id',
    ]);

    $validated['additional_data'] = $additionalData;

    $member = Member::findOrFail($validated['member_id']);
    $certificateType = CertificateType::findOrFail($validated['certificate_type_id']);

    if (!empty($validated['template_id'])) {
      $template = CertificateTemplate::findOrFail($validated['template_id']);
    } else {
      $template = CertificateTemplate::where('certificate_type_id', $validated['certificate_type_id'])
        ->where('is_default', true)
        ->where('is_active', true)
        ->first();

      if (!$template) {
        $template = CertificateTemplate::where('certificate_type_id', $validated['certificate_type_id'])
          ->where('is_active', true)
          ->first();
      }

      if (!$template) {
        $template = new CertificateTemplate([
          'name' => 'Default ' . ucfirst($certificateType->code),
          'certificate_type_id' => $certificateType->id,
          'template_content' => null,
          'is_active' => true,
        ]);
      }
    }

    try {
      if (!$template->template_config) {
        $template->template_config = [
          'paper' => 'A4',
          'orientation' => 'portrait',
          'margin' => ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20],
          'show_logo' => true,
          'logo_url' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCA5MyA4MiI+CiAgPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDI5LjYuMSwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDIuMS4xIEJ1aWxkIDkpICAtLT4KICAKICA8cGF0aCBmaWxsPSIjMmM0YTk5IiBkPSJNOC4yNSw1Ni44NWMtNS44OSw2LjI2LTIuNjgsMTEuNzEsNi4xNiwxNS4zNywxMi40LDUuMTQsNDcuOTIsMS40MSw0Ny43NC0xNi4yLS4wNC0zLjY1LTMuMzEtNi42Mi05Ljk1LTcuNjhsLS4yNi01LjJjNy4zMywxLjAzLDEyLjcyLDQuNSwxNC4yNSw5LjE4LDQuNDUsMTMuNjUtMTkuNjQsMjAuNjUtMjcuODUsMjIuNTMtMTAuNDksMi40LTMzLjk5LDUuMTQtMzguMDctOS4yOS0xLjMzLTQuNjQuMjgtOS44OS40Ljk5LTEzLjkxTTU1LjY3LDIyLjg1YzEzLjUyLDguODUsMjUuODQsMjQuNCwzNi4wNyw1MS41NC0xNy4wMiw5LjI1LTM3Ljk3LDkuMjMtNjAuMjgsNi4wNCwxMS4xLTIuNzQsMjAuODQtNS45OCwyNy43NC0xMC4yOSw5LjU1LTUuOTUsMTQuNjYtMTUuODEsNS4zNC0yNS4zLTMuMTQtMy4xOS03LjI4LTQuNTMtMTEuOTktNC44MSwyLjE1LTQuNjYsMy40Mi0xMC4yMywzLjE0LTE3LjE4aC0uMDFaMzYuMzMsMjkuMzdjLTguNCwyLjAzLTEzLjQyLTIuMzYtMTQuODYtNy43Mi0zLjk5LTE0Ljc5LDE3LjY0LTIzLjcxLDI0LjcyLTQuODksMy41Niw5LjQ2LS40MSwyMi4yNS03Ljc2LDMwLjUxLTUuNTQsNi4yNS0xNC4yMywxMi43My0yNy43NSwxNy4xMywxNC44MS02LjM4LDI0LjIzLTE2LjA0LDMwLjAzLTI3LjkxLDMuNDgtMTItLjQ1LTE4LjU1LTUuMjMtMjEuMTctMTAuMDYtNS41MS0xNC4zLDYtOS4zMSwxMC40OSwxLjk2LDEuNzUsNS4zLDIuOTUsMTAuMTUsMy41NloiLz4KICAKPC9zdmc+',
        ];
      }
      $config = $template->template_config;
      $config['show_logo'] = config('app.certificate_show_logo', true);
      $config['logo_url'] = $this->certificateService->makeLogoSrc(config('app.certificate_logo_path', 'images/logo.png'));
      $template->template_config = $config;

      $tempCertificate = new CertificateRecord([
        'member_id' => $member->id,
        'certificate_type_id' => $validated['certificate_type_id'],
        'template_id' => $template->id,
        'issued_date' => now(),
        'issued_by' => Auth::id(),
        'certificate_number' => 'PREVIEW-' . time(),
        'additional_data' => $validated['additional_data'] ?? [],
      ]);

      if ($template->template_content && !str_contains($template->template_content, 'template_config["logo_url"]')) {
        $template->template_content = str_replace(
          '<div class="header">{{ $parish_name }}</div>',
          '@if(isset($template_config["show_logo"]) && $template_config["show_logo"] && !empty($template_config["logo_url"]))
        <div style="margin-bottom: 20px;">
            <img src="{{ $template_config["logo_url"] }}" alt="Church Logo" style="max-width: 100px; max-height: 100px; object-fit: contain;">
        </div>
    @endif
    <div class="header">{{ $parish_name }}</div>',
          $template->template_content
        );
      }

      $tempCertificate->setRelation('member', $member);
      $tempCertificate->setRelation('template', $template);

      $pdfContent = $this->certificateService->generatePreview($tempCertificate);
      $pdfBase64 = base64_encode($pdfContent);

      return response()->view('certificates.pdf-viewer', [
        'pdfData' => $pdfBase64,
        'filename' => 'Salvation_Admin_Certificate_Preview.pdf'
      ]);
    } catch (\Exception $e) {
      if ($request->expectsJson() || $request->wantsJson() || $request->header('Accept') === 'application/pdf, application/json') {
        return response()->json([
          'error' => 'Failed to generate preview: ' . $e->getMessage(),
        ], 500);
      }

      return redirect()->back()->with('error', 'Failed to generate preview: ' . $e->getMessage());
    }
  }


  /**
   * Create a reprint of an existing certificate
   */
  public function reprint($certificateId)
  {
    $this->authorize('reprint-certificate');

    try {
      $originalCertificate = CertificateRecord::find($certificateId);

      if (!$originalCertificate) {
        return redirect()->back()->with('error', 'Certificate not found.');
      }

      $originalCertificate->load(['member', 'template']);

      if (!$originalCertificate->certificate_type_id) {
        return redirect()->back()->with('error', 'Cannot reprint certificate: original certificate type is missing.');
      }

      if (!$originalCertificate->member_id) {
        return redirect()->back()->with('error', 'Cannot reprint certificate: original certificate member is missing.');
      }

      // Load certificate type separately to avoid accessor conflict
      $certificateType = \Modules\Members\Models\CertificateType::find($originalCertificate->certificate_type_id);
      if (!$certificateType) {
        return redirect()->back()->with('error', 'Certificate type not found.');
      }

      $certificateNumber = CertificateRecord::generateCertificateNumber(
        $certificateType->code,
        $originalCertificate->member_id
      );

      $reprint = CertificateRecord::create([
        'member_id' => $originalCertificate->member_id,
        'certificate_type_id' => $originalCertificate->certificate_type_id,
        'template_id' => $originalCertificate->template_id,
        'issued_date' => now()->toDateString(),
        'issued_by' => Auth::id(),
        'certificate_number' => $certificateNumber,
        'additional_data' => $originalCertificate->additional_data,
        'notes' => 'Reprint of certificate #' . $originalCertificate->certificate_number,
        'is_reprint' => true,
        'original_certificate_id' => $originalCertificate->id,
      ]);

      $filePath = $this->certificateService->generateCertificate($reprint);

      // Use hash() with get() for S3 compatibility (hash_file() only works with local paths)
      $disk = Storage::disk(config('filesystems.private_storage'));
      $reprint->update([
        'file_path' => $filePath,
        'file_hash' => hash('sha256', $disk->get($filePath)),
      ]);

      return redirect()->route('certificates.show', $reprint)
        ->with('success', 'Certificate reprinted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Failed to reprint certificate: ' . $e->getMessage());
    }
  }

  /**
   * Generate PDF for an existing certificate record that doesn't have a file
   */
  public function generatePdf(CertificateRecord $certificate)
  {
    $this->authorize('update-certificate');

    if ($certificate->file_path) {
      return redirect()->back()
        ->with('info', 'Certificate PDF already exists.');
    }

    try {
      $filePath = $this->certificateService->generateCertificate($certificate);

      // Use hash() with get() for S3 compatibility (hash_file() only works with local paths)
      $disk = Storage::disk(config('filesystems.private_storage'));
      $certificate->update([
        'file_path' => $filePath,
        'file_hash' => hash('sha256', $disk->get($filePath)),
      ]);

      return redirect()->back()
        ->with('success', 'Certificate PDF generated successfully.');
    } catch (\Exception $e) {
      Log::error('Certificate PDF generation failed', [
        'certificate_id' => $certificate->id,
        'error' => $e->getMessage(),
      ]);

      return redirect()->back()
        ->with('error', 'Failed to generate certificate PDF: ' . $e->getMessage());
    }
  }
}
