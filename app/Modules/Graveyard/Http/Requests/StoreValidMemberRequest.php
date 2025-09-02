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
            'permanent_grave_id' => ['required', 'exists:permanent_graves,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'member_id' => ['required', 'exists:members,id'],
            'contact_no' => ['required', 'string', 'max:20'],
            'aadhar_no' => ['required', 'string', 'max:20', 'unique:valid_members,aadhar_no'],
        ];
    }
}
