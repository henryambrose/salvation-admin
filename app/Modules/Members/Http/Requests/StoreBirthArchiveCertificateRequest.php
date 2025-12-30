<?php

namespace Modules\Members\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBirthArchiveCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-birth-archive');
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
            'birth_year' => 'required|integer|min:1800|max:' . (date('Y') + 1),
            'birth_month' => 'required|integer|min:1|max:12',
            'birth_day' => 'required|integer|min:1|max:31',
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
            'birth_year.required' => 'Birth year is required.',
            'birth_month.min' => 'Month must be between 1 and 12.',
            'birth_month.max' => 'Month must be between 1 and 12.',
            'birth_day.min' => 'Day must be between 1 and 31.',
            'birth_day.max' => 'Day must be between 1 and 31.',
        ];
    }

    /**
     * Validate that the date is valid
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->birth_year && $this->birth_month && $this->birth_day) {
                if (!checkdate($this->birth_month, $this->birth_day, $this->birth_year)) {
                    $validator->errors()->add('birth_day', 'Invalid date combination.');
                }
            }
        });
    }
}
