<?php

namespace Modules\Members\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkImportArchiveCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check permission based on archive type
        $type = $this->route('type'); // 'birth', 'marriage', 'death'
        return $this->user()->can("create-{$type}-archive");
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max for CSV
            'certificate_files' => 'required|array|min:1',
            'certificate_files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'csv_file.required' => 'Please upload a CSV file with certificate metadata.',
            'csv_file.mimes' => 'The metadata file must be a CSV.',
            'certificate_files.required' => 'Please upload at least one certificate file.',
            'certificate_files.*.mimes' => 'Certificate files must be PDF, JPG, JPEG, or PNG.',
            'certificate_files.*.max' => 'Each certificate file must not exceed 10MB.',
        ];
    }
}
