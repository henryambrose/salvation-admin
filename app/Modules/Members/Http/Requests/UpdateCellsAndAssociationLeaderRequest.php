<?php

namespace Modules\Members\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCellsAndAssociationLeaderRequest extends FormRequest
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
        $leaderId = $this->route('cells_and_association_leader');

        return [
            'cells_and_association_id' => [
                'required',
                'integer',
                'exists:cells_and_associations,id',
                Rule::unique('cells_and_association_leaders')
                    ->ignore($leaderId)
                    ->whereNull('deleted_at'),
            ],
            'leader_member_id' => 'required|integer|exists:members,id',
            'assistant_leader_member_id' => 'nullable|integer|exists:members,id|different:leader_member_id',
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
            'cells_and_association_id.unique' => 'This Cell/Association already has leaders assigned.',
            'assistant_leader_member_id.different' => 'The Assistant Leader must be different from the Leader.',
        ];
    }
}
