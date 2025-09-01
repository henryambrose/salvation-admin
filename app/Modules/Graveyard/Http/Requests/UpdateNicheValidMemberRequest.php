<?php

namespace Modules\Graveyard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNicheValidMemberRequest extends FormRequest
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
            'niche_grave_id' => ['required', 'exists:niche_graves,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'member_id' => ['required', 'exists:members,id'],
            'contact_no' => ['required', 'string', 'max:20'],
            'aadhar_no' => [
                'required',
                'string',
                'max:20',
                Rule::unique('niche_valid_member', 'aadhar_no')->ignore($this->niche_valid_member),
            ],
        ];
    }
}
