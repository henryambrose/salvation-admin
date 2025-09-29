<?php

namespace Modules\Members\Services;

use Modules\Members\Models\CertificateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


class CertificateGenerationService
{
  /**
   * Generate a certificate PDF and save it to storage
   */
  public function generateCertificate(CertificateRecord $certificate): string
  {
    // Load relationships
    $certificate->load(['member', 'template', 'issuer']);

    // Load certificate type separately to avoid relationship issues
    $certificateType = \Modules\Members\Models\CertificateType::find($certificate->certificate_type_id);
    if (!$certificateType) {
      throw new \Exception("Certificate type not found for ID: {$certificate->certificate_type_id}");
    }

    // Generate the PDF content
    $pdfContent = $this->generatePdfContent($certificate, $certificateType);

    // Create directory structure
    $year = $certificate->issued_date->year;
    $month = $certificate->issued_date->format('m');
    $directory = "certificates/{$year}/{$month}";

    // Ensure directory exists
    Storage::disk('local')->makeDirectory($directory);

    // Generate filename
    $filename = sprintf(
      '%s_%s_%s.pdf',
      $certificateType->code,
      $certificate->member_id,
      $certificate->issued_date->format('Y-m-d_H-i-s')
    );

    $filePath = "{$directory}/{$filename}";

    // Save PDF to storage
    Storage::disk('local')->put($filePath, $pdfContent);

    return $filePath;
  }

  /**
   * Generate a preview PDF without saving
   */
  public function generatePreview(CertificateRecord $certificate): string
  {
    // Load certificate type separately to avoid relationship issues
    $certificateType = \Modules\Members\Models\CertificateType::find($certificate->certificate_type_id);
    if (!$certificateType) {
      throw new \Exception("Certificate type not found for ID: {$certificate->certificate_type_id}");
    }

    return $this->generatePdfContent($certificate, $certificateType);
  }

  /**
   * Generate PDF content from certificate data
   */
  protected function generatePdfContent(CertificateRecord $certificate, $certificateType): string
  {
    // Prepare data for the template
    $data = $this->prepareCertificateData($certificate, $certificateType);

    // Use custom template if available, otherwise use default
    if ($certificate->template && $certificate->template->template_content) {
      Log::info('Certificate Generation - Using CUSTOM template', [
        'template_id' => $certificate->template->id,
        'template_name' => $certificate->template->name,
        'has_template_content' => !empty($certificate->template->template_content),
        'template_config_in_data' => isset($data['template_config']),
        'logo_in_config' => isset($data['template_config']['logo_url']) ? 'YES' : 'NO',
      ]);
      $html = $this->renderCustomTemplate($certificate->template->template_content, $data);
    } else {
      Log::info('Certificate Generation - Using DEFAULT template', [
        'certificate_type' => $certificateType->code,
        'template_id' => $certificate->template->id ?? 'none',
        'template_config_in_data' => isset($data['template_config']),
        'logo_in_config' => isset($data['template_config']['logo_url']) ? 'YES' : 'NO',
        'logo_url_prefix' => isset($data['template_config']['logo_url']) ? substr($data['template_config']['logo_url'], 0, 50) : 'none',
      ]);
      $html = $this->renderDefaultTemplate($certificateType->code, $data);
    }

    // Log HTML content sample for debugging
    Log::info('Certificate Generation - HTML content check', [
      'html_length' => strlen($html),
      'contains_img_tag' => strpos($html, '<img') !== false ? 'YES' : 'NO',
      'contains_base64' => strpos($html, 'base64,') !== false ? 'YES' : 'NO',
      'html_sample' => substr($html, 0, 500) . '...',
    ]);

    // Generate PDF using DomPDF
    $pdf = Pdf::loadHTML($html);

    // Apply template configuration if available
    if ($certificate->template && $certificate->template->template_config) {
      $this->applyTemplateConfig($pdf, $certificate->template->template_config);
    } else {
      // Default configuration
      $pdf->setPaper('A4', 'portrait');
    }

    return $pdf->output();
  }

  /**
   * Prepare certificate data for template rendering
   */
  protected function prepareCertificateData(CertificateRecord $certificate, $certificateType): array
  {
    $member = $certificate->member;
    $additionalData = $certificate->additional_data ?? [];

    // Base member data
    $data = [
      'certificate' => $certificate,
      'member' => $member,
      'member_full_name' => trim("{$member->first_name} {$member->middle_name} {$member->last_name}"),
      'member_first_name' => $member->first_name,
      'member_middle_name' => $member->middle_name ?? '',
      'member_last_name' => $member->last_name,
      'member_dob' => $member->date_of_birth?->format('d/m/Y'),
      'member_family_no' => $member->family_no,
      'member_member_no' => $member->member_no,
      'certificate_number' => $certificate->certificate_number,
      'issued_date' => $certificate->issued_date->format('d/m/Y'),
      'issued_by' => $certificate->issuer?->name ?? 'System',
      'parish_name' => $member->parish?->name ?? config('app.parish_name', 'Our Lady of Salvation Church'),
      'community_name' => $member->community?->name ?? '',
    ];

    // Add certificate type specific data
    switch ($certificateType->code) {
      case 'baptism':
        $data = array_merge($data, [
          'baptism_date' => $member->baptism_date?->format('d/m/Y'),
          'baptism_reg_no' => $member->baptism_reg_no,
          'baptism_parish' => $member->baptism_parish ?? $data['parish_name'],
          'godfather_name' => $additionalData['godfather_name'] ?? '',
          'godmother_name' => $additionalData['godmother_name'] ?? '',
          'priest_name' => $additionalData['priest_name'] ?? '',
          'witnesses' => $additionalData['witnesses'] ?? '',
        ]);
        break;

      case 'confirmation':
        $data = array_merge($data, [
          'confirmation_date' => $member->confirmation_date?->format('d/m/Y'),
          'confirmation_reg_no' => $member->confirmation_reg_no,
          'confirmation_parish' => $member->confirmation_parish ?? $data['parish_name'],
          'confirmation_name' => $additionalData['confirmation_name'] ?? '',
          'sponsor_name' => $additionalData['sponsor_name'] ?? '',
          'bishop_name' => $additionalData['bishop_name'] ?? '',
          'priest_name' => $additionalData['priest_name'] ?? '',
        ]);
        break;

      case 'marriage':
        $data = array_merge($data, [
          'marriage_date' => $member->marriage_date?->format('d/m/Y'),
          'marriage_reg_no' => $member->marriage_reg_no,
          'marriage_parish' => $member->marriage_parish ?? $data['parish_name'],
          'spouse_name' => $additionalData['spouse_name'] ?? '',
          'witness1_name' => $additionalData['witness1_name'] ?? '',
          'witness2_name' => $additionalData['witness2_name'] ?? '',
          'priest_name' => $additionalData['priest_name'] ?? '',
          'marriage_type' => $additionalData['marriage_type'] ?? 'Catholic Marriage',
        ]);
        break;

      case 'membership':
        $data = array_merge($data, [
          'join_date' => $additionalData['join_date'] ?? $member->created_at->format('d/m/Y'),
          'community_name' => $additionalData['community_name'] ?? $data['community_name'],
          'family_number' => $additionalData['family_number'] ?? $member->family_no,
        ]);
        break;

      case 'death':
        $data = array_merge($data, [
          'death_date' => $member->death_date?->format('d/m/Y'),
          'deaths_reg_no' => $member->deaths_reg_no,
          'death_parish' => $member->death_parish ?? $data['parish_name'],
          'burial_date' => $additionalData['burial_date'] ?? '',
          'burial_place' => $additionalData['burial_place'] ?? '',
          'priest_name' => $additionalData['priest_name'] ?? '',
          'last_rites_given' => $additionalData['last_rites_given'] ?? 'Yes',
        ]);
        break;
    }

    // Add template configuration data
    $templateConfig = $certificate->template->template_config ?? [];

    // Add fixed logo configuration from environment
    $templateConfig['show_logo'] = config('app.certificate_show_logo', true);
    $logoPath = config('app.certificate_logo_path', 'images/logo.png');

    // Convert logo URL to data URL for PDF generation
    if (!empty($logoPath)) {
      $templateConfig['logo_url'] = $this->makeLogoSrc($logoPath);
    }

    $data['template_config'] = $templateConfig;

    Log::info('Template config added to certificate data', [
      'template_id' => $certificate->template->id,
      'template_config' => $templateConfig,
      'logo_from_env' => true
    ]);

    // Add certificate type for template use
    $data['certificate_type'] = $certificateType->code;

    return $data;
  }

  /**
   * Render custom template content
   */
  protected function renderCustomTemplate(string $templateContent, array $data): string
  {
    // Create a temporary view from the template content
    $tempViewName = 'temp_certificate_' . uniqid();
    $tempDir = storage_path('app/temp');

    // Ensure temp directory exists
    if (!file_exists($tempDir)) {
      mkdir($tempDir, 0755, true);
    }

    View::addLocation($tempDir);

    $tempPath = "{$tempDir}/{$tempViewName}.blade.php";

    // Write template content to temporary file
    $result = file_put_contents($tempPath, $templateContent);
    if ($result === false) {
      throw new \Exception("Failed to write template file to: {$tempPath}");
    }

    try {
      $html = view($tempViewName, $data)->render();
    } finally {
      // Clean up temporary file
      if (file_exists($tempPath)) {
        unlink($tempPath);
      }
    }

    return $html;
  }

  /**
   * Render default template for certificate type
   */
  protected function renderDefaultTemplate(string $certificateType, array $data): string
  {
    $viewPath = "certificates.templates.{$certificateType}";

    // Check if custom view exists
    if (View::exists($viewPath)) {
      return view($viewPath, $data)->render();
    }

    // Fall back to generic template
    return view('certificates.templates.default', $data)->render();
  }

  /**
   * Apply template configuration to PDF
   */
  protected function applyTemplateConfig($pdf, array $config): void
  {
    // Paper size and orientation
    $paper = $config['paper'] ?? 'A4';
    $orientation = $config['orientation'] ?? 'portrait';
    $pdf->setPaper($paper, $orientation);

    // Additional PDF options
    if (isset($config['margin'])) {
      $pdf->setOptions([
        'margin_top' => $config['margin']['top'] ?? 20,
        'margin_right' => $config['margin']['right'] ?? 20,
        'margin_bottom' => $config['margin']['bottom'] ?? 20,
        'margin_left' => $config['margin']['left'] ?? 20,
      ]);
    }
  }

  /**
   * Validate certificate data before generation
   */
  public function validateCertificateData(CertificateRecord $certificate): array
  {
    $errors = [];
    $member = $certificate->member;

    // Load certificate type separately to avoid relationship issues
    $certificateType = \Modules\Members\Models\CertificateType::find($certificate->certificate_type_id);
    if (!$certificateType) {
      $errors[] = "Certificate type not found for ID: {$certificate->certificate_type_id}";
      return $errors;
    }

    // Common validations
    if (!$member->first_name || !$member->last_name) {
      $errors[] = 'Member name is required';
    }

    // Certificate type specific validations
    switch ($certificateType->code) {
      case 'baptism':
        if (!$member->baptism_date) {
          $errors[] = 'Baptism date is required';
        }
        break;

      case 'confirmation':
        if (!$member->confirmation_date) {
          $errors[] = 'Confirmation date is required';
        }
        break;

      case 'marriage':
        if (!$member->marriage_date) {
          $errors[] = 'Marriage date is required';
        }
        if (!($certificate->additional_data['spouse_name'] ?? null)) {
          $errors[] = 'Spouse name is required for marriage certificate';
        }
        break;

      case 'death':
        if (!$member->death_date) {
          $errors[] = 'Death date is required';
        }
        break;
    }

    return $errors;
  }

  /**
   * Convert logo path to data URL for PDF generation
   */
  public function makeLogoSrc(?string $src): ?string
  {

    if (!$src) {
      $envPath = config('app.certificate_logo_path'); // e.g. "images/logo.png"
      if ($envPath) {
        $src = '/' . ltrim($envPath, '/');
      }
    }
    if (!$src) return null;

    // Already a data URI?
    if (Str::startsWith($src, ['data:image', 'data:'])) {
      return $src;
    }

    // Parse the path and resolve as local file
    $path = parse_url($src, PHP_URL_PATH) ?: $src;
    $path = ltrim($path, '/\\');
    if (Str::startsWith($path, 'storage/')) {
      $full = public_path($path);
    } else {
      $full = public_path($path);
    }

    if (is_file($full) && is_readable($full)) {
      $mime = File::mimeType($full) ?: 'image/png';
      return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($full));
    }

    // If it's a full http(s) URL, return as-is (requires DomPDF remote enabled)
    if (Str::startsWith($src, ['http://', 'https://'])) {
      $appUrl = rtrim(config('app.url'), '/');
      if ($appUrl && Str::startsWith($src, $appUrl)) {
        $path = parse_url($src, PHP_URL_PATH) ?: '';
        $path = ltrim($path, '/\\');
        $full = public_path($path);
        if (is_file($full) && is_readable($full)) {
          $mime = File::mimeType($full) ?: 'image/png';
          return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($full));
        }
      }

      // Otherwise it’s a remote URL: only works if Dompdf remote is enabled.
      // Prefer to avoid this (base64 is safest), but return as-is if needed.
      return $src;
    }

    // Could not resolve
    return null;
  }
}
