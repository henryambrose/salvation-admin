<?php

namespace Modules\Members\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Members\Models\Member;

class FamilyPhotoUploadRequest extends FormRequest
{
    /**
     * Get the validated family number from route parameter
     */
    public function getFamilyNo(): string
    {
        return $this->route('familyNo');
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user has permission to update members (general permission)
        // For family photos, we check if user can update any member in the family
        $familyNo = $this->getFamilyNo();

        if (!$familyNo) {
            return false;
        }

        // Get any member from the family to check authorization
        $member = Member::where('family_no', $familyNo)->first();

        if (!$member) {
            return false;
        }

        // Check if user can update this specific member
        return $this->user()->can('update', $member);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'photo' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,gif,jpg',
                'max:20480', // 20MB in kilobytes
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'photo.required' => 'Please select a photo to upload.',
            'photo.file' => 'The uploaded file is not valid.',
            'photo.image' => 'The uploaded file must be an image.',
            'photo.mimes' => 'The photo must be a file of type: jpeg, png, gif, jpg.',
            'photo.max' => 'The photo may not be greater than 20MB.',
            'photo.dimensions' => 'The photo dimensions are invalid. Minimum 100x100px, maximum 4000x4000px.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Additional security: Validate that the family exists
        $familyNo = $this->getFamilyNo();

        if (!$familyNo) {
            abort(400, 'Family number is required');
        }

        // Check if family exists
        $familyExists = Member::where('family_no', $familyNo)->exists();

        if (!$familyExists) {
            abort(404, 'Family not found');
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}