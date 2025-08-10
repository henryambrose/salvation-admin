<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSCCHeadRequest extends FormRequest
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
        $rules = [
            'member_id' => 'required|integer|exists:members,id',
            'community_id' => [
                'required',
                'integer',
                'exists:communities,id',
            ],
        ];
    
        if ($this->isMethod('post')) { // create
            $rules['community_id'][] = 'unique:p_p_c_heads,community_id';
        }
    
        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'community_id.unique' => 'This community already has an SCC Head assigned.',
        ];
    }
}
