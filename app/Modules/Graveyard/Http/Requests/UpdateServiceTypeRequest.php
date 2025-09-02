<?php

namespace Modules\Graveyard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceTypeRequest extends FormRequest
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
        $serviceType = $this->route('serviceType');
        
        return [
            'name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('service_types', 'name')->ignore($serviceType)
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'cost' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'type' => ['required', 'in:normal,concession,free'],
            'category' => ['required', 'in:grave,funeral,additional'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The service name is required.',
            'name.unique' => 'A service type with this name already exists.',
            'cost.required' => 'The service cost is required.',
            'cost.numeric' => 'The cost must be a valid number.',
            'cost.min' => 'The cost cannot be negative.',
            'cost.max' => 'The cost cannot exceed ₹999,999.99.',
            'type.required' => 'Please select a service type.',
            'type.in' => 'The service type must be Normal, Concession, or Free.',
            'category.required' => 'Please select a service category.',
            'category.in' => 'The service category must be Grave, Funeral, or Additional.',
            'sort_order.required' => 'The sort order is required.',
            'sort_order.integer' => 'The sort order must be a whole number.',
            'sort_order.min' => 'The sort order cannot be negative.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'service name',
            'description' => 'service description',
            'cost' => 'service cost',
            'type' => 'service type',
            'category' => 'service category',
            'is_active' => 'active status',
            'sort_order' => 'sort order',
        ];
    }
}