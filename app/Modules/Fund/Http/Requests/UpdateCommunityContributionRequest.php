<?php

namespace Modules\Fund\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommunityContributionRequest extends FormRequest
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
            'contribution_type_id' => ['required', 'exists:community_contribution_types,id'],
            'collection_date' => ['required', 'date', 'before_or_equal:today'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'collected_by_user_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:recorded,verified,archived'],
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
            'contribution_type_id.required' => 'Please select a contribution type.',
            'contribution_type_id.exists' => 'The selected contribution type is invalid.',
            'collection_date.required' => 'The collection date is required.',
            'collection_date.date' => 'Please enter a valid collection date.',
            'collection_date.before_or_equal' => 'The collection date cannot be in the future.',
            'amount.required' => 'The contribution amount is required.',
            'amount.numeric' => 'The amount must be a valid number.',
            'amount.min' => 'The amount must be greater than zero.',
            'amount.max' => 'The amount cannot exceed ₹999,999.99.',
            'collected_by_user_id.exists' => 'The selected collector is invalid.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'description.max' => 'Description cannot exceed 255 characters.',
            'location.max' => 'Location cannot exceed 255 characters.',
            'status.required' => 'Please select a status.',
            'status.in' => 'The status must be Recorded, Verified, or Archived.',
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
            'contribution_type_id' => 'contribution type',
            'collection_date' => 'collection date',
            'amount' => 'contribution amount',
            'description' => 'description',
            'location' => 'collection location',
            'collected_by_user_id' => 'collected by',
            'notes' => 'notes',
            'status' => 'status',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for optional fields
        $this->merge([
            'description' => $this->description ?: null,
            'location' => $this->location ?: null,
            'collected_by_user_id' => $this->collected_by_user_id ?: null,
            'notes' => $this->notes ?: null,
        ]);
    }
}