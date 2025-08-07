<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExternalMemberRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'gender_id' => 'nullable|exists:genders,id',
            'family_no' => 'required|string|max:255',
            'father_id' => 'nullable|integer',
            'mother_id' => 'nullable|integer',
            'spouse_id' => 'nullable|integer',
            'address' => 'nullable|string',
            'relationship_id' => 'required|exists:relationships,id', // Changed from nullable to required
        ];
    }
}
