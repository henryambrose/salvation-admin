<?php

namespace Modules\Members\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarriageArchiveCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-marriage-archive');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'reg_year' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'reg_no' => 'required|string|max:50',
            'marriage_year' => 'required|integer|min:1800|max:' . (date('Y') + 1),
            'marriage_month' => 'required|integer|min:1|max:12',
            'marriage_day' => 'required|integer|min:1|max:31',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:5000',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Please select a certificate file to upload.',
            'file.mimes' => 'The file must be a PDF, JPG, JPEG, or PNG.',
            'file.max' => 'The file size must not exceed 10MB.',
            'marriage_year.required' => 'Marriage year is required.',
            'marriage_month.min' => 'Month must be between 1 and 12.',
            'marriage_month.max' => 'Month must be between 1 and 12.',
            'marriage_day.min' => 'Day must be between 1 and 31.',
            'marriage_day.max' => 'Day must be between 1 and 31.',
        ];
    }

    /**
     * Validate that the date is valid
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->marriage_year && $this->marriage_month && $this->marriage_day) {
                if (!checkdate($this->marriage_month, $this->marriage_day, $this->marriage_year)) {
                    $validator->errors()->add('marriage_day', 'Invalid date combination.');
                }
            }
        });
    }
}
