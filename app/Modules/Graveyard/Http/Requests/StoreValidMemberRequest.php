<?php

namespace Modules\Graveyard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'permanent_grave_id' => ['required_if:grave_type,permanent_grave', 'exists:permanent_graves,id'],
            'niche_id' => ['required_if:grave_type,niche', 'exists:niches,id'],
            
            // Multiple members array (max 5)
            'members' => ['required', 'array', 'min:1', 'max:5'],
            'members.*.member_type' => ['required', 'in:parish,external'],
            
            // Parish member validation
            'members.*.member_id' => ['required_if:members.*.member_type,parish', 'exists:members,id'],
            
            // External member validation
            'members.*.first_name' => ['required_if:members.*.member_type,external', 'string', 'max:255'],
            'members.*.last_name' => ['required_if:members.*.member_type,external', 'string', 'max:255'],
            'members.*.contact_no' => ['nullable', 'string', 'max:20'],
            'members.*.aadhar_no' => ['nullable', 'string', 'max:20'],
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
            'members.*.member_type.in' => 'Member type must be either parish or external.',
            'members.*.member_id.required_if' => 'Parish member selection is required.',
            'members.*.first_name.required_if' => 'First name is required for external members.',
            'members.*.last_name.required_if' => 'Last name is required for external members.',
        ];
    }
}
