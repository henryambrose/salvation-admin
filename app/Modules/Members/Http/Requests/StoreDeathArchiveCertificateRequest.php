<?php

namespace Modules\Members\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeathArchiveCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-death-archive');
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
            'death_year' => 'required|integer|min:1800|max:' . (date('Y') + 1),
            'death_month' => 'required|integer|min:1|max:12',
            'death_day' => 'required|integer|min:1|max:31',
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
            'death_year.required' => 'Death year is required.',
            'death_month.min' => 'Month must be between 1 and 12.',
            'death_month.max' => 'Month must be between 1 and 12.',
            'death_day.min' => 'Day must be between 1 and 31.',
            'death_day.max' => 'Day must be between 1 and 31.',
        ];
    }

    /**
     * Validate that the date is valid
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->death_year && $this->death_month && $this->death_day) {
                if (!checkdate($this->death_month, $this->death_day, $this->death_year)) {
                    $validator->errors()->add('death_day', 'Invalid date combination.');
                }
            }
        });
    }
}
