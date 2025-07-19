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
            // 'status' => 'required|in:Resident,Non-Resident,Dead,Redevelopment Unsettled',
            // 'relationship' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'permanent_add1' => 'nullable|string|max:255',
            'permanent_add2' => 'nullable|string|max:255',
            'permanent_add3' => 'nullable|string|max:255',
            'permanent_town_id' => 'nullable|exists:towns,id',
            'permanent_city' => 'nullable|string|max:255',
            'permanent_pincode' => 'nullable|string|max:10',
            'permanent_state_id' => 'nullable|exists:states,id',
            'permanent_country_id' => 'nullable|exists:countries,id',
            'current_add1' => 'nullable|string|max:255',
            'current_add2' => 'nullable|string|max:255',
            'current_add3' => 'nullable|string|max:255',
            'current_town_id' => 'nullable|exists:towns,id',
            'current_city' => 'nullable|string|max:255',
            'current_pincode' => 'nullable|string|max:10',
            'current_state_id' => 'nullable|exists:states,id',
            'current_country_id' => 'nullable|exists:countries,id',
            'contact_no' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'blood_group_id' => 'nullable|exists:blood_groups,id',
            'cells_and_association_id' => 'nullable|exists:cells_and_associations,id',
            'school_name' => 'nullable|string|max:255',
            'college_name' => 'nullable|string|max:255',
            'latest_qualifications' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            // 'designation' => 'nullable|string|max:255',
            'family_income_range_id' => 'nullable|exists:family_income_ranges,id',
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
            'gender_id' => 'nullable|exists:genders,id',
            'status_id' => 'nullable|exists:statuses,id',
            'relationship_id' => 'nullable|exists:relationships,id',
            'parish_id' => 'nullable|exists:parishes,id',
            'designation_id' => 'nullable|exists:designations,id',
        ];
    }
}
