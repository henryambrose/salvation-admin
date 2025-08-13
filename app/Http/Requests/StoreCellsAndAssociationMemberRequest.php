<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CellsAndAssociationMember;

class StoreCellsAndAssociationMemberRequest extends FormRequest
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
            'member_id' => 'required|exists:members,id',
            'cells_and_association_id' => 'required|array',
            'cells_and_association_id.*' => 'exists:cells_and_associations,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $memberId = $this->input('member_id');
            $cellAssociationIds = $this->input('cells_and_association_id', []);

            if (is_array($cellAssociationIds) && !empty($cellAssociationIds)) {
                // Check for existing associations to prevent duplicates
                $existingAssociations = CellsAndAssociationMember::where('member_id', $memberId)
                    ->whereIn('cells_and_association_id', $cellAssociationIds)
                    ->pluck('cells_and_association_id')
                    ->toArray();

                if (!empty($existingAssociations)) {
                    $validator->errors()->add('cells_and_association_id', 
                        'Member is already associated with some of the selected cell associations: ' . 
                        implode(', ', $existingAssociations)
                    );
                }

                // Check for duplicate values within the same request
                $duplicates = array_diff_assoc($cellAssociationIds, array_unique($cellAssociationIds));
                if (!empty($duplicates)) {
                    $validator->errors()->add('cells_and_association_id', 
                        'Duplicate cell associations are not allowed: ' . 
                        implode(', ', array_unique($duplicates))
                    );
                }
            }
        });
    }
}
