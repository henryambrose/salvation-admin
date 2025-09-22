<?php

namespace Modules\Graveyard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateValidMemberRequest extends FormRequest
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
            'permanent_grave_id' => ['required_if:grave_type,permanent_grave', 'nullable', 'exists:permanent_graves,id'],
            'niche_id' => ['required_if:grave_type,niche', 'nullable', 'exists:niches,id'],
            
            // Single member for update (not multiple like store)
            'member_type' => ['required', 'in:member,external'], // Changed to match store request

            // Parish member validation
            'member_id' => ['required_if:member_type,member', 'nullable', 'exists:members,id'], // Changed to match store request

            // External member validation - should be prohibited for parish members
            'first_name' => [
                'required_if:member_type,external',
                'nullable', 'string', 'max:255',
                'prohibited_if:member_type,member' // Prevent sending names for parish members
            ],
            'last_name' => [
                'required_if:member_type,external',
                'nullable', 'string', 'max:255',
                'prohibited_if:member_type,member' // Prevent sending names for parish members
            ],
            'contact_no' => ['nullable', 'string', 'max:20'],
            'aadhar_no' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'grave_type.required' => 'Please select a grave type.',
            'grave_type.in' => 'Invalid grave type selected.',
            'permanent_grave_id.required_if' => 'Please select a permanent grave.',
            'niche_id.required_if' => 'Please select a niche.',
            'member_type.required' => 'Member type is required.',
            'member_type.in' => 'Member type must be either member or external.',
            'member_id.required_if' => 'Parish member selection is required.',
            'first_name.required_if' => 'First name is required for external members.',
            'last_name.required_if' => 'Last name is required for external members.',
        ];
    }
}
