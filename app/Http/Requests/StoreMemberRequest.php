<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'community_id' => 'nullable|exists:communities,id',
            'community_cluster_id' => 'nullable|exists:community_clusters,id',
            'new_olsc_id' => 'nullable|string|max:255',
            'old_sal_id' => 'nullable|string|max:255',
            'aadhar' => 'nullable|string|max:12',
            'family_no' => 'nullable|string|max:255',
            'status' => 'required|in:Resident,Non-Resident,Dead,Redevelopment Unsettled',
            'relationship' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'permanent_add1' => 'nullable|string|max:255',
            'permanent_add2' => 'nullable|string|max:255',
            'permanent_add3' => 'nullable|string|max:255',
            'permanent_town' => 'nullable|string|max:255',
            'permanent_city' => 'nullable|string|max:255',
            'permanent_pincode' => 'nullable|string|max:10',
            'permanent_state' => 'nullable|string|max:255',
            'permanent_country' => 'nullable|string|max:255',
            'current_add1' => 'nullable|string|max:255',
            'current_add2' => 'nullable|string|max:255',
            'current_add3' => 'nullable|string|max:255',
            'current_town' => 'nullable|string|max:255',
            'current_city' => 'nullable|string|max:255',
            'current_pincode' => 'nullable|string|max:10',
            'current_state' => 'nullable|string|max:255',
            'current_country' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'blood_group' => 'nullable|string|max:3',
            'cells_and_association_id' => 'nullable|exists:cells_and_associations,id',
            'school_name' => 'nullable|string|max:255',
            'college_name' => 'nullable|string|max:255',
            'latest_qualifications' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'family_income_range' => 'nullable|string|max:255',
            'baptism_date' => 'nullable|date',
            'baptism_reg_no' => 'nullable|string|max:255',
            'baptism_parish' => 'nullable|string|max:255',
            'confirmation_date' => 'nullable|date',
            'confirmation_reg_no' => 'nullable|string|max:255',
            'confirmation_parish' => 'nullable|string|max:255',
            'marriage_date' => 'nullable|date',
            'marriage_reg_no' => 'nullable|string|max:255',
            'marriage_parish' => 'nullable|string|max:255',
            'death_date' => 'nullable|date',
            'deaths_reg_no' => 'nullable|string|max:255',
            'death_parish' => 'nullable|string|max:255',
        ];
    }
}
