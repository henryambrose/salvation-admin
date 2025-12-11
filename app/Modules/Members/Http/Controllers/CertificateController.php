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

    // Auth middleware is already applied at route level - no need to duplicate here

    // Apply permissions middleware
    $this->middleware(['permission:generate-certificate'])->only(['generate', 'store', 'generateMemberSelection']);
    $this->middleware(['permission:view-certificate-history|list-certificate'])->only(['index', 'show']);
    $this->middleware(['permission:reprint-certificate'])->only(['reprint']);
    $this->middleware(['permission:download-certificate'])->only(['download']);
    $this->middleware(['permission:manage-certificate-templates'])->only(['templateIndex', 'templateStore', 'templateShow', 'templateUpdate', 'templateDestroy', 'setTemplateAsDefault', 'templatePreview']);
    $this->middleware(['permission:create-certificate'])->only(['store']);
    $this->middleware(['permission:read-certificate'])->only(['show']);
    $this->middleware(['permission:update-certificate'])->only(['generatePdf']);
    $this->middleware(['permission:preview-certificate-template'])->only(['preview']);
  }

  /**
   * Display a listing of certificates
   */
  public function index(Request $request)
  {

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
      ->limit(10) // Limit to 10 results for performance
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
   * Get templates by type (AJAX endpoint)
   */
  public function getTemplatesByType(Request $request)
  {
    $type = $request->get('type');

    $query = CertificateTemplate::active()->with('certificateType');

    if ($type) {
      $query->byCertificateType($type);
    }

    $templates = $query->orderBy('is_default', 'desc')
      ->orderBy('name')
      ->get();

    return response()->json($templates);
  }

  /**
   * Show member selection page for certificate generation
   */
  public function generateMemberSelection(Request $request)
  {
    // Get available templates with certificate type relationship
    $templates = CertificateTemplate::active()->with('certificateType')->get();


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

    return Inertia::render('certificates/Generate', [
      'templates' => $templates,
      'certificateTypes' => $certificateTypes,
      'canGenerateCertificates' => Gate::allows('generate-certificate'),
    ]);
  }

  /**
   * Show the form for generating a new certificate
   */
  public function generate(Member $member, Request $request)
  {
    $type = $request->get('type');
    $availableTypes = $member->getAvailableCertificateTypes();

    if ($type && !in_array($type, $availableTypes)) {
      return redirect()->back()->with('error', 'This certificate type is not available for this member.');
    }

    $templates = CertificateTemplate::active()
      ->with('certificateType')
      ->when($type, fn($q) => $q->byCertificateType($type))
      ->get();


    $existingCertificates = $member->certificates()
      ->when($type, function ($q) use ($type) {
        return $q->whereHas('certificateType', function ($subq) use ($type) {
          $subq->where('code', (string) $type);
        });
      })
      ->with(['template', 'certificateType'])
      ->get();

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

    return Inertia::render('certificates/Generate', [
      'member' => $member->load(['community']),
      'availableTypes' => $availableTypes,
      'selectedType' => $type,
      'templates' => $templates,
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
    // Parse additional_data if it's a JSON string
    $additionalData = $request->input('additional_data');
    if (is_string($additionalData)) {
      $additionalData = json_decode($additionalData, true) ?? [];
    }

    $validated = $request->validate([
      'member_id' => 'required|exists:members,id',
      'certificate_type_id' => 'required|exists:certificate_types,id',
      'template_id' => 'nullable|exists:certificate_templates,id',
      'notes' => 'nullable|string|max:1000',
    ]);

    $validated['additional_data'] = $additionalData;

    $member = Member::findOrFail($validated['member_id']);

    // Get the certificate type for reference
    $certificateType = CertificateType::findOrFail($validated['certificate_type_id']);

    // Use specified template or find default template for the certificate type
    if (!empty($validated['template_id'])) {
      $template = CertificateTemplate::findOrFail($validated['template_id']);
    } else {
      $template = CertificateTemplate::where('certificate_type_id', $validated['certificate_type_id'])
        ->where('is_default', true)
        ->where('is_active', true)
        ->first();

      // If no default template found, use any active template of the same type
      if (!$template) {
        $template = CertificateTemplate::where('certificate_type_id', $validated['certificate_type_id'])
          ->where('is_active', true)
          ->first();
      }

      // If still no template found, create a simple one
      if (!$template) {
        $template = new CertificateTemplate([
          'name' => 'Default ' . ucfirst($certificateType->code),
          'certificate_type_id' => $certificateType->id,
          'template_content' => null, // Will use default blade template
          'is_active' => true,
        ]);
      }
    }

    // Ensure template has logo display logic in content
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
      // Update the template in database if it's a saved template
      if ($template->id) {
        $template->save();
      }
    }

    // Validate that member can have this certificate type
    $availableTypes = $member->getAvailableCertificateTypes();
    if (!in_array($certificateType->code, $availableTypes)) {
      return redirect()->back()->with('error', 'This certificate type is not available for this member.');
    }

    // Check for existing certificate of the same type (optional - can be bypassed with force parameter)
    $existingCertificate = CertificateRecord::where('member_id', $member->id)
      ->where('certificate_type_id', $certificateType->id)
      ->first();

    if ($existingCertificate && !$request->has('force_duplicate')) {
      return redirect()->back()
        ->withInput()
        ->with('warning', "A {$certificateType->code} certificate already exists for this member (#{$existingCertificate->certificate_number}). Use the 'Force Generate' option if you need to create a duplicate.")
        ->with('existing_certificate', $existingCertificate->toArray());
    }

    // Generate certificate number
    $certificateNumber = CertificateRecord::generateCertificateNumber(
      $certificateType->code,
      $member->id
    );

    // Create certificate record
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
      // Generate PDF
      $filePath = $this->certificateService->generateCertificate($certificate);

      // Update certificate with file path and hash
      $certificate->update([
        'file_path' => $filePath,
        'file_hash' => hash_file('sha256', Storage::disk('local')->path($filePath)),
      ]);

      return redirect()->route('certificates.show', $certificate)
        ->with('success', 'Certificate generated successfully.');
    } catch (\Exception $e) {
      // Clean up certificate record if PDF generation fails
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
    $certificate->load(['member', 'template', 'issuer', 'originalCertificate']);

    return Inertia::render('certificates/Show', [
      'certificate' => $certificate,
      'canGenerateCertificates' => Gate::allows('generate-certificate'),
      'canReprintCertificates' => Gate::allows('reprint-certificate'),
    ]);
  }

  /**
   * Show certificate templates management page
   */
  public function templateIndex(Request $request)
  {
    // Log::info('Template index method called', ['user_id' => Auth::id()]);

    $templates = CertificateTemplate::with(['creator', 'updater', 'certificateType'])
      ->join('certificate_types', 'certificate_templates.certificate_type_id', '=', 'certificate_types.id')
      ->orderBy('certificate_types.sort_order')
      ->orderBy('certificate_templates.is_default', 'desc')
      ->select('certificate_templates.*')
      ->get();

    // Log::info('Templates loaded', ['count' => $templates->count()]);

    $certificateTypes = CertificateType::active()
      ->ordered()
      ->get(['id', 'name', 'code'])
      ->map(function ($type) {
        return [
          'value' => $type->code,
          'label' => $type->name,
          'id' => $type->id
        ];
      });

    // Log::info('Rendering templates page');

    return Inertia::render('certificates/Templates', [
      'templates' => $templates,
      'certificateTypes' => $certificateTypes,
      'defaultLogoUrl' => asset('church-logo.png'),
      'canManageCertificateTemplates' => Gate::allows('manage-certificate-templates'),
    ]);
  }

  /**
   * Store a new certificate template
   */
  public function templateStore(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'certificate_type_id' => 'required|exists:certificate_types,id',
      'description' => 'nullable|string',
      'template_content' => 'nullable|string',
      'template_config' => 'nullable|array',
      'is_active' => 'boolean',
    ]);

    $template = CertificateTemplate::create([
      'name' => $request->name,
      'type' => $request->type,
      'description' => $request->description,
      'template_content' => $request->template_content,
      'template_config' => $request->template_config,
      'is_active' => $request->boolean('is_active', true),
      'created_by' => Auth::id(),
    ]);

    return redirect()->route('certificates.templates.index')
      ->with('success', 'Template created successfully.');
  }

  /**
   * Show a specific certificate template
   */
  public function templateShow(CertificateTemplate $template)
  {
    $template->load(['creator', 'updater']);

    return Inertia::render('certificates/TemplateShow', [
      'template' => $template,
    ]);
  }

  /**
   * Update a certificate template
   */
  public function templateUpdate(Request $request, CertificateTemplate $template)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'certificate_type_id' => 'required|exists:certificate_types,id',
      'description' => 'nullable|string',
      'template_content' => 'nullable|string',
      'template_config' => 'nullable|array',
      'is_active' => 'boolean',
    ]);

    $template->update([
      'name' => $request->name,
      'type' => $request->type,
      'description' => $request->description,
      'template_content' => $request->template_content,
      'template_config' => $request->template_config,
      'is_active' => $request->boolean('is_active', true),
      'updated_by' => Auth::id(),
    ]);

    return redirect()->route('certificates.templates.index')
      ->with('success', 'Template updated successfully.');
  }

  /**
   * Delete a certificate template
   */
  public function templateDestroy(CertificateTemplate $template)
  {
    if (!$template->canBeDeleted()) {
      return redirect()->route('certificates.templates.index')
        ->with('error', 'Cannot delete template that has been used for certificates.');
    }

    $template->delete();

    return redirect()->route('certificates.templates.index')
      ->with('success', 'Template deleted successfully.');
  }

  /**
   * Set template as default for its type
   */
  public function setTemplateAsDefault(CertificateTemplate $template)
  {
    // Remove default flag from other templates of the same type
    CertificateTemplate::where('certificate_type_id', $template->certificate_type_id)
      ->where('id', '!=', $template->id)
      ->update(['is_default' => false]);

    // Set this template as default
    $template->update(['is_default' => true]);

    return redirect()->route('certificates.templates.index')
      ->with('success', 'Template set as default successfully.');
  }

  /**
   * Download certificate PDF
   */
  public function download(CertificateRecord $certificate)
  {
    // Load necessary relationships
    $certificate->load(['member', 'certificateType']);

    if (!$certificate->fileExists()) {
      return redirect()->back()->with('error', 'Certificate file not found.');
    }

    // Increment download count
    $certificate->incrementDownloadCount();

    $certificateTypeCode = is_object($certificate->certificateType) ? $certificate->certificateType->code : $certificate->certificateType;
    $fileName = "certificate_{$certificateTypeCode}_{$certificate->member->first_name}_{$certificate->member->last_name}_{$certificate->issued_date->format('Y-m-d')}.pdf";

    return response()->download(Storage::disk('local')->path($certificate->file_path), $fileName);
  }

  // At the top of the controller:




  /**
   * Preview certificate without saving
   */
  public function preview(Request $request)
  {
    // Parse additional_data if it's a JSON string
    // Log::info('Preview method called', ['request_data' => $request->all()]);
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

    // Get the certificate type for reference
    $certificateType = CertificateType::findOrFail($validated['certificate_type_id']);

    // Use specified template or find default template for the certificate type
    if (!empty($validated['template_id'])) {
      $template = CertificateTemplate::findOrFail($validated['template_id']);
    } else {
      $template = CertificateTemplate::where('certificate_type_id', $validated['certificate_type_id'])
        ->where('is_default', true)
        ->where('is_active', true)
        ->first();

      // If no default template found, use any active template of the same type
      if (!$template) {
        $template = CertificateTemplate::where('certificate_type_id', $validated['certificate_type_id'])
          ->where('is_active', true)
          ->first();
      }

      // If still no template found, create a simple one
      if (!$template) {
        $template = new CertificateTemplate([
          'name' => 'Default ' . ucfirst($certificateType->code),
          'certificate_type_id' => $certificateType->id,
          'template_content' => null, // Will use default blade template
          'is_active' => true,
        ]);
      }
    }

    try {
      // Ensure template has config for logo display
      if (!$template->template_config) {
        $template->template_config = [
          'paper' => 'A4',
          'orientation' => 'portrait',
          'margin' => ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20],
          'show_logo' => true,
          'logo_url' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCA5MyA4MiI+CiAgPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDI5LjYuMSwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDIuMS4xIEJ1aWxkIDkpICAtLT4KICA8ZGVmcz4KICAgIDxzdHlsZT4KICAgICAgLnN0MCB7CiAgICAgICAgZmlsbDogIzJjNGE5OTsKICAgICAgICBmaWxsLXJ1bGU6IGV2ZW5vZGQ7CiAgICAgIH0KICAgIDwvc3R5bGU+CiAgPC9kZWZzPgogIDxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik04LjI1LDU2Ljg1Yy01Ljg5LDYuMjYtMi42OCwxMS43MSw2LjE2LDE1LjM3LDEyLjQsNS4xNCw0Ny45MiwxLjQxLDQ3Ljc0LTE2LjItLjA0LTMuNjUtMy4zMS02LjYyLTkuOTUtNy42OGwtLjI2LTUuMmM3LjMzLDEuMDMsMTIuNzIsNC41LDE0LjI1LDkuMTgsNC40NSwxMy42NS0xOS42NCwyMC42NS0yNy44NSwyMi41My0xMC40OSwyLjQtMzMuOTksNS4xNC0zOC4wNy05LjI5LTEuMzMtNC42NC4yOC05Ljg5LDQuOTktMTMuOTFNNTUuNjcsMjIuODVjMTMuNTIsOC44NSwyNS44NCwyNC40LDM2LjA3LDUxLjU0LTE3LjAyLDkuMjUtMzcuOTcsOS4yMy02MC4yOCw2LjA0LDExLjEtMi43NCwyMC44NC01Ljk4LDI3Ljc0LTEwLjI5LDkuNTUtNS45NSwxNC42Ni0xNS44MSw1LjM0LTI1LjMtMy4xNC0zLjE5LTcuMjgtNC41My0xMS45OS00LjgxLDIuMTUtNC42NiwzLjQyLTEwLjIzLDMuMTQtMTcuMThoLS4wMVpNNDIuMzMsMjkuMzdjLTguNCwyLjAzLTEzLjQyLTIuMzYtMTQuODYtNy43Mi0zLjk5LTE0Ljc5LDE3LjY0LTIzLjcxLDI0LjcyLTQuODksMy41Niw5LjQ2LS40MSwyMi4yNS03Ljc2LDMwLjUxLTUuNTQsNi4yNS0xNC4yMywxMi43My0yNy43NSwxNy4xMywxNC44MS02LjM4LDI0LjIzLTE2LjA0LDMwLjAzLTI3LjkxLDMuNDgtMTItLjQ1LTE4LjU1LTUuMjMtMjEuMTctMTAuMDYtNS41MS0xNC4zLDYtOS4zMSwxMC40OSwxLjk2LDEuNzUsNS4zLDIuOTUsMTAuMTUsMy41NmgwWk01LjYxLDI5Ljg0YzAsLjA4LjEuMDUuMjksMC0uMTktLjA1LS4yOS0uMDktLjI5LDBaTTE4Ljc5LDI5Ljg0YzAtLjA5LS4xLS4wNS0uMjksMCwuMTguMDUuMjkuMDguMjksMFpNMTMuNTIsMjkuODRjLS40NCwwLS44OC0uMDEtMS4zNC0uMDFzLS44OSwwLTEuMzMuMDFoMi42NlpNMTIuMiwyOC41M2MxLjk5LDAsMy44MS4wOCw1LjEyLjIsMS42My4xNCwyLjYzLjUzLDMNi42MywxLjFzLTEsLjk2LTIuNjMsMS4xYy0xLjMxLjEyLTMuMTMuMTktNS4xMi4xOXMtMy43OS0uMDgtNS4xMi0uMTljLTEuNjItLjE0LTIuNjMtLjUzLTIuNjMtMS4xczEuMDEtLjk2LDIuNjMtMS4xYzEuMzEtLjEyLDMuMTMtLjIsNS4xMi0uMlpNNDMuNjMsMS42N2MtMS4yNS0uMzEtMy0uNTEtNC45NS0uNTFzLTMuNy4xOS00Ljk0LjUxYy0xLjAxLjI1LTEuNjQuNDgtMS42NC42M3MuNjMuMzgsMS42NC42NGMxLjI1LjMxLDMsLjQ5LDQuOTQuNDlzMy43LS4xOSw0Ljk1LS40OWMxLjAxLS4yNSwxLjY0LS40OSwxLjY0LS42NHMtLjYzLS4zOC0xLjY0LS42M1pNMzguNjktLjExYzIuMDIsMCwzLjg1LjIsNS4yLjUzLDEuNTcuNCwyLjU1LDEuMDYsMi41NSwxLjg4cy0uOTcsMS41LTIuNTUsMS44OGMtMS4zNS4zMy0zLjE5LjU1LTUuMi41NXMtMy44NS0uMjEtNS4xOS0uNTVjLTEuNTgtLjM4LTIuNTctMS4wNC0yLjU3LTEuODhzLjk3LTEuNSwyLjU3LTEuODhjMS4zNC0uMzMsMy4xNy0uNTMsNS4xOS0uNTNaTTI1Ljc2LDQwLjYzYy41OCwxLjQuODgsMi45NC44OCw0LjQ5LTEuNTgtNS4yMy00Ljg4LTctNy43NS02LjY1LTYuMDguNzYtNi45Niw5LjAxLS45MiwxMi41MSwzLjYyLDIuMSw4Ljg5LDMuNTEsMTQuNTcsMi43NC03LjQ0LDMuMi0yMS43OCwzLjY4LTI0LTYuOTYtLjM5LTEuODgtLjYxLTQuMjMuNTQtNi42MiwyLjc2LTUuNzYsMTMuMzktNy4zNSwxNi42OC40OWguMDFaIi8+CiAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTI4LjE3LDI2LjU2Yy0uNS45Ni0xLjQ2LDIuNDktMy4xNywzLjgzLTIuNDUsMS45NC01LjAyLDIuMzgtNi4xNywyLjUuNjkuMDcsNS41LjY0LDguNSw1LDIuNjksMy45MSwxLjk4LDguMDUsMS44Myw4LjgzLDMuMjgtLjE3LDYuNTYtLjMzLDkuODMtLjUsMS40Ny0xLjc3LDMuMTctNC4xNSw0LjY3LTcuMTcsMS4xOS0yLjM5LDEuOTctNC42MSwyLjUtNi41LTIuMjQuMi02LjQ2LjI3LTExLjE3LTEuNjctMy4xMy0xLjI5LTUuMzktMy4wMy02LjgzLTQuMzNaIi8+Cjwvc3ZnPg==',
        ];
      }
      $config = $template->template_config;
      // Use environment configuration for logo
      $config['show_logo'] = config('app.certificate_show_logo', true);
      $config['logo_url'] = $this->certificateService->makeLogoSrc(config('app.certificate_logo_path', 'images/logo.png'));
      $template->template_config = $config; // assign back so the service sees it
      // Create temporary certificate record for preview
      $tempCertificate = new CertificateRecord([
        'member_id' => $member->id,
        'certificate_type_id' => $validated['certificate_type_id'],
        'template_id' => $template->id,
        'issued_date' => now(),  // Keep as Carbon instance, not string
        'issued_by' => Auth::id(),
        'certificate_number' => 'PREVIEW-' . time(),
        'additional_data' => $validated['additional_data'] ?? [],
      ]);

      // Ensure template has logo display logic in content
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

      // Set the member relationship manually since it's not saved
      $tempCertificate->setRelation('member', $member);
      $tempCertificate->setRelation('template', $template);

      $pdfContent = $this->certificateService->generatePreview($tempCertificate);

      // Create a data URL for the PDF
      $pdfBase64 = base64_encode($pdfContent);
      Log::info('Certificate Preview - PDF render data', [
        'show_logo' => $config['show_logo'] ?? null,
        'logo_url_head' => isset($config['logo_url'])
          ? substr($config['logo_url'], 0, 40) : null,
        'logo_is_data' => isset($config['logo_url'])
          ? str_starts_with($config['logo_url'], 'data:') : null,
        'logo_path_from_config' => config('app.certificate_logo_path'),
        'logo_show_from_config' => config('app.certificate_show_logo'),
      ]);

      return response()->view('certificates.pdf-viewer', [
        'pdfData' => $pdfBase64,
        'filename' => 'Salvation_Admin_Certificate_Preview.pdf'
      ]);
    } catch (\Exception $e) {
      // Log the full exception for debugging
      // Log::error('Certificate preview generation failed', ['message' => $e->getMessage()]);

      // For AJAX requests, return JSON error
      if ($request->expectsJson() || $request->wantsJson() || $request->header('Accept') === 'application/pdf, application/json') {
        return response()->json([
          'error' => 'Failed to generate preview: ' . $e->getMessage(),
          'details' => config('app.debug') ? [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
          ] : null
        ], 500);
      }

      return redirect()->back()->with('error', 'Failed to generate preview: ' . $e->getMessage());
    }
  }

  /**
   * Preview a certificate template
   */
  public function templatePreview(CertificateTemplate $template)
  {
    try {
      // Mock member for preview
      $mockMember = new Member([
        'first_name'  => 'John',
        'middle_name' => 'Sample',
        'last_name'   => 'Doe',
        'family_no'   => 'FAM001',
      ]);

      // ---- Ensure + normalize template_config (important for the logo) ----
      $defaults = [
        'paper'       => 'A4',
        'orientation' => 'portrait',
        'margin'      => ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20],
        'show_logo'   => true,
        'logo_url'    => null,
      ];

      $stored = $template->template_config;
      if (is_object($stored)) $stored = (array) $stored;
      if (!is_array($stored)) $stored = [];
      $config = array_merge($defaults, $stored);

      // Use environment configuration for logo
      $config['show_logo'] = config('app.certificate_show_logo', true);
      $logoPath = config('app.certificate_logo_path', 'images/logo.png');
      $config['logo_url'] = $this->certificateService->makeLogoSrc($logoPath);

      Log::info('Template Preview - Logo Configuration Check', [
        'template_id' => $template->id,
        'template_name' => $template->name,
        'original_config' => $stored,
        'logo_path_from_env' => $logoPath,
        'logo_show_from_env' => config('app.certificate_show_logo', true),
        'processed_logo_url_prefix' => $config['logo_url'] ? substr($config['logo_url'], 0, 50) : 'null',
        'logo_url_is_data_uri' => $config['logo_url'] ? str_starts_with($config['logo_url'], 'data:') : false,
        'logo_url_length' => $config['logo_url'] ? strlen($config['logo_url']) : 0,
      ]);

      if (!$config['logo_url']) {
        $config['logo_url'] = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB...'; // your existing placeholder
        Log::warning('Template Preview - Using fallback logo', [
          'template_id' => $template->id,
          'reason' => 'makeLogoSrc returned null or empty',
          'original_logo_path' => $logoPath
        ]);
      }
      // Put the normalized config back on the model so the service sees it
      $template->template_config = $config;
      // -------------------------------------------------------------------

      // Mock certificate (unsaved)
      $tempCertificate = new CertificateRecord([
        'certificate_type_id' => $template->certificate_type_id,
        'template_id'         => $template->id,
        'issued_date'         => now()->toDateString(),
        'certificate_number'  => 'PREVIEW-' . time(),
        'additional_data'     => [],
      ]);

      // Wire relationships so the service can read member/template data
      $tempCertificate->setRelation('member', $mockMember);
      $tempCertificate->setRelation('template', $template);
      $tempCertificate->setRelation('certificateType', $template->certificateType);

      // Generate PDF bytes
      $pdfContent = $this->certificateService->generatePreview($tempCertificate);

      // Check if PDF content includes logo data
      $pdfHasLogoData = false;
      $logoDataInPdf = false;
      if ($config['logo_url'] && str_starts_with($config['logo_url'], 'data:image')) {
        // Extract base64 part from data URL
        $logoBase64 = substr($config['logo_url'], strpos($config['logo_url'], ',') + 1);
        $logoDataInPdf = strpos($pdfContent, $logoBase64) !== false;
        $pdfHasLogoData = true;
      }

      Log::info('Template Preview - PDF Generation Check', [
        'template_id' => $template->id,
        'pdf_size_bytes' => strlen($pdfContent),
        'pdf_has_logo_config' => $config['show_logo'] ?? false,
        'logo_url_type' => $config['logo_url'] ? (str_starts_with($config['logo_url'], 'data:') ? 'data_uri' : 'url') : 'none',
        'logo_data_found_in_pdf' => $logoDataInPdf,
        'pdf_content_sample' => substr($pdfContent, 0, 200) . '...',
      ]);
      // Render the viewer with the base64 PDF
      return response()->view('certificates.pdf-viewer', [
        'pdfData'  => base64_encode($pdfContent),
        'filename' => 'Salvation_Admin_Template_Preview.pdf',
      ]);
    } catch (\Throwable $e) {
      return redirect()->back()->with('error', 'Failed to generate template preview: ' . $e->getMessage());
    }
  }


  /**
   * Create a reprint of an existing certificate
   */
  public function reprint($certificateId)
  {
    try {
      // Find the certificate manually
      // Log::info('Reprint method called', ['certificate_id_param' => $certificateId]);

      $originalCertificate = CertificateRecord::find($certificateId);

      // Check if the certificate actually exists
      if (!$originalCertificate) {
        // Log::error('Reprint failed: certificate not found', ['certificate_id' => $certificateId]);
        return redirect()->back()->with('error', 'Certificate not found.');
      }

      // Load the original certificate to ensure we have all data
      $originalCertificate->load(['member', 'template', 'certificateType']);

      // Validate original certificate has required data
      if (!$originalCertificate->certificate_type_id || !$originalCertificate->certificateType) {
        // Log::error('Reprint failed: certificate_type_id is null', ['certificate_id' => $originalCertificate->id]);
        return redirect()->back()->with('error', 'Cannot reprint certificate: original certificate type is missing.');
      }

      if (!$originalCertificate->member_id) {
        return redirect()->back()->with('error', 'Cannot reprint certificate: original certificate member is missing.');
      }

      // Generate new certificate number for reprint
      $certificateNumber = CertificateRecord::generateCertificateNumber(
        $originalCertificate->certificateType->code,
        $originalCertificate->member_id
      );

      // Create reprint certificate record
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

      // Generate PDF
      $filePath = $this->certificateService->generateCertificate($reprint);

      // Update certificate with file path and hash
      $reprint->update([
        'file_path' => $filePath,
        'file_hash' => hash_file('sha256', Storage::disk('local')->path($filePath)),
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
    // Check if certificate already has a PDF file
    if ($certificate->file_path) {
      return redirect()->back()
        ->with('info', 'Certificate PDF already exists.');
    }

    try {
      // Generate PDF using the certificate service
      $filePath = $this->certificateService->generateCertificate($certificate);

      // Update certificate with file path and hash
      $certificate->update([
        'file_path' => $filePath,
        'file_hash' => hash_file('sha256', Storage::disk('local')->path($filePath)),
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
