<?php

namespace Modules\Members\Http\Requests;

use Modules\Members\Rules\NotFutureDate;
use Modules\Members\Rules\IndianPhoneValidation;
use Modules\Members\Rules\EmailValidation;
use Modules\Members\Rules\AadharValidation;
use Modules\Members\Rules\ParishValidation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the member being updated from route parameter
        $member = $this->route('member');

        return [
            'community_id' => 'required|exists:communities,id',
            'community_cluster_id' => 'required|exists:community_clusters,id',
            'aadhar' => 'nullable|string|max:12',
            'family_no' => 'nullable|string|max:255',
            'member_no' => 'nullable|string|max:255',
            'registration_year' => 'nullable|string|max:4',
            'church_code' => 'nullable|string|max:3',
            'family_sequence' => 'nullable|integer',
            'member_sequence' => 'nullable|integer',
            'marital_status' => 'nullable|in:Single,Married,Divorced,Widowed',
            'birth_family_no' => 'nullable|string|max:255',
            'relation_member_id' => 'nullable|exists:members,id',
            'mother_id' => 'nullable|exists:members,id',
            'father_id' => 'nullable|exists:members,id',
            'spouse_id' => 'nullable|exists:members,id',
            'status_id' => 'nullable|exists:statuses,id',
            'relationship' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => ['nullable', 'date', new NotFutureDate],
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
            'contact_no_1' => ['nullable', 'string', 'max:15', new IndianPhoneValidation],
            'contact_no_2' => ['nullable', 'string', 'max:15', new IndianPhoneValidation],
            'email' => ['nullable', 'max:255', new EmailValidation],
            'aadhar' => ['nullable', 'string', 'max:20', new AadharValidation],
            'blood_group_id' => 'nullable|exists:blood_groups,id',
            'school_name' => 'nullable|string|max:255',
            'college_name' => 'nullable|string|max:255',
            'latest_qualifications' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'income_range_id' => 'nullable|exists:income_ranges,id',
            'baptism_date' => ['nullable', 'date', new NotFutureDate],
            'baptism_reg_no' => 'nullable|string|max:255',
            'baptism_parish' => ['nullable', 'string', 'max:255', new ParishValidation('baptism', $member->baptism_parish ?? null)],
            'baptism_parish_id' => 'nullable|exists:parishes,id',
            'confirmation_date' => ['nullable', 'date', new NotFutureDate],
            'confirmation_reg_no' => 'nullable|string|max:255',
            'confirmation_parish' => ['nullable', 'string', 'max:255', new ParishValidation('confirmation', $member->confirmation_parish ?? null)],
            'confirmation_parish_id' => 'nullable|exists:parishes,id',
            'marriage_date' => ['nullable', 'date', new NotFutureDate],
            'marriage_reg_no' => 'nullable|string|max:255',
            'marriage_parish' => ['nullable', 'string', 'max:255', new ParishValidation('marriage', $member->marriage_parish ?? null)],
            'marriage_parish_id' => 'nullable|exists:parishes,id',
            'death_date' => ['nullable', 'date', new NotFutureDate],
            'deaths_reg_no' => 'nullable|string|max:255',
            'death_parish' => ['nullable', 'string', 'max:255', new ParishValidation('death', $member->death_parish ?? null)],
            'death_parish_id' => 'nullable|exists:parishes,id',
            'gender_id' => 'nullable|exists:genders,id',
            'status_id' => 'nullable|exists:statuses,id',
            'relationship_id' => 'required|exists:relationships,id',
            'parish_id' => 'nullable|exists:parishes,id',
            'designation_id' => 'nullable|exists:designations,id',
            'father_source' => 'nullable|string|max:255',
            'mother_source' => 'nullable|string|max:255',
            'spouse_source' => 'nullable|string|max:255',
        ];
    }
}
