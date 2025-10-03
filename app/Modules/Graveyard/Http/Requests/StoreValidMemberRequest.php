<?php

namespace Modules\Graveyard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class StoreValidMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Grave selection - exactly one must be provided
            'grave_type' => ['required', 'in:permanent_grave,niche'],

            // Only validate the grave id relevant to the chosen type
            'permanent_grave_id' => [
                'required_if:grave_type,permanent_grave',
                'nullable',      // harmless if provided null when not required
                'exists:permanent_graves,id',
            ],
            'niche_id' => [
                'required_if:grave_type,niche',
                'nullable',      // <-- important to avoid "invalid" when null
                'exists:niches,id',
            ],

            // Members array (max 5)
            'members' => ['required', 'array', 'min:1', 'max:5'],
            'members.*.member_type' => ['required', 'in:member,external'],

            // Parish member: must have member_id; external must NOT have member_id
            'members.*.member_id' => [
                'required_if:members.*.member_type,member',
                'nullable', // allows it to be absent/null when external
                'exists:members,id',
            ],

            // External member: names required; parish members should NOT provide names
            'members.*.first_name' => [
                'required_if:members.*.member_type,external',
                'nullable',
                'string',
                'max:255',

            ],
            'members.*.last_name' => [
                'required_if:members.*.member_type,external',
                'nullable',
                'string',
                'max:255',

            ],

            // Optional fields for either type
            'members.*.contact_no' => ['nullable', 'string', 'max:20'],
            'members.*.aadhar_no'  => ['nullable', 'string', 'max:20'],
            'members.*.relationship_id'  => ['nullable', 'integer'],
            'members.*.notes'  => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {

        return [
            'grave_type.required' => 'Please select a grave type.',
            'grave_type.in' => 'Invalid grave type selected.',
            'permanent_grave_id.required_if' => 'Please select a permanent grave.',
            'niche_id.required_if' => 'Please select a niche.',
            'members.required' => 'At least one member must be added.',
            'members.max' => 'Maximum 5 members can be added per grave.',
            'members.*.member_type.required' => 'Member type is required.',
            'members.*.member_type.in' => 'Member type must be either member or external.',
            'members.*.member_id.required_if' => 'Parish member selection is required.',
            'members.*.first_name.required_if' => 'First name is required for external members.',
            'members.*.last_name.required_if' => 'Last name is required for external members.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        Log::error('Validation failed in StoreValidMemberRequest', [
            'errors' => $validator->errors()->toArray(),
            'input'  => $this->all(),
        ]);

        // Let Laravel/Inertia handle the validation response properly
        parent::failedValidation($validator);
    }
}
