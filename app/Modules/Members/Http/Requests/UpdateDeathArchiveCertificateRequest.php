<?php

namespace Modules\Members\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeathArchiveCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update-death-archive');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Optional on update
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
