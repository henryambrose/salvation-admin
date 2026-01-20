<?php

namespace Modules\Members\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCellsAndAssociationLeaderRequest extends FormRequest
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
            'cells_and_association_id' => 'required|integer|exists:cells_and_associations,id|unique:cells_and_association_leaders,cells_and_association_id,NULL,id,deleted_at,NULL',
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
