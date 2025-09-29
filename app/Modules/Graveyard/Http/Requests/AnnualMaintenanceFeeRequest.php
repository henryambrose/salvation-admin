<?php

namespace Modules\Graveyard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Graveyard\Models\AnnualMaintenanceFee;

class AnnualMaintenanceFeeRequest extends FormRequest
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
        $feeId = $this->route('annualMaintenanceFee')?->id;

        return [
            'year' => [
                'required',
                'integer',
                'min:2020',
                'max:' . (now()->year + 10),
                Rule::unique('annual_maintenance_fees', 'year')->ignore($feeId),
            ],
            'permanent_grave_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'niche_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'effective_from' => [
                'required',
                'date',
            ],
            'effective_until' => [
                'nullable',
                'date',
                'after_or_equal:effective_from',
            ],
            'is_active' => [
                'boolean',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'year.unique' => 'A maintenance fee for this year already exists.',
            'year.min' => 'Year must be 2020 or later.',
            'year.max' => 'Year cannot be more than 10 years in the future.',
            'permanent_grave_amount.numeric' => 'Permanent grave amount must be a valid number.',
            'niche_amount.numeric' => 'Niche amount must be a valid number.',
            'effective_until.after_or_equal' => 'Effective until date must be after or equal to effective from date.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation for update requests
            if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                $fee = $this->route('annualMaintenanceFee');

                if ($fee && !$fee->canBeEdited()) {
                    $validator->errors()->add('year', 'This maintenance fee cannot be edited as it has already been applied to members.');
                }
            }

            // Ensure at least one amount is provided
            $permanentAmount = $this->input('permanent_grave_amount');
            $nicheAmount = $this->input('niche_amount');

            if (empty($permanentAmount) && empty($nicheAmount)) {
                $validator->errors()->add('permanent_grave_amount', 'Either permanent grave amount or niche amount must be provided.');
                $validator->errors()->add('niche_amount', 'Either permanent grave amount or niche amount must be provided.');
            }

            // Validate effective dates
            $effectiveFrom = $this->input('effective_from');
            $effectiveUntil = $this->input('effective_until');
            $year = $this->input('year');

            // Validate that effective_from is in the same year as the maintenance fee year
            if ($effectiveFrom && $year) {
                $effectiveFromYear = date('Y', strtotime($effectiveFrom));
                if ($effectiveFromYear != $year) {
                    $validator->errors()->add('effective_from', 'Effective from date should be in the same year as the maintenance fee year.');
                }
            }

            // Only validate date order if both dates are provided
            if ($effectiveFrom && $effectiveUntil) {
                $fromDate = \Carbon\Carbon::parse($effectiveFrom);
                $untilDate = \Carbon\Carbon::parse($effectiveUntil);

                if ($fromDate->isAfter($untilDate)) {
                    $validator->errors()->add('effective_until', 'Effective until date must be after or equal to effective from date.');
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Set default effective_from if not provided
        if (!$this->has('effective_from') && $this->has('year')) {
            $this->merge([
                'effective_from' => $this->input('year') . '-01-01'
            ]);
        }

        // Ensure boolean values are properly set
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }

        // Clean up decimal values
        if ($this->has('permanent_grave_amount')) {
            $this->merge([
                'permanent_grave_amount' => $this->input('permanent_grave_amount') ?: null
            ]);
        }

        if ($this->has('niche_amount')) {
            $this->merge([
                'niche_amount' => $this->input('niche_amount') ?: null
            ]);
        }
    }
}