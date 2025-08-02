<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClusterRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:clusters,name',
                function ($attribute, $value, $fail) {
                    if (empty(trim($value))) {
                        $fail('The cluster name cannot be empty.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The cluster name is required.',
            'name.string' => 'The cluster name must be a string.',
            'name.max' => 'The cluster name cannot exceed 255 characters.',
            'name.unique' => 'A cluster with this name already exists.',
        ];
    }
} 