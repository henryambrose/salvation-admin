<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgeGroupRequest extends FormRequest
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
        $ageGroupId = $this->route('age_group')->id;

        return [
            'name' => 'required|string|unique:age_groups,name,'.$ageGroupId,
            'description' => 'nullable|string',
            'min_age' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($ageGroupId) {
                    $maxAge = request()->input('max_age');
                    if ($maxAge) {
                        $exists = \App\Models\AgeGroup::where('min_age', $value)
                            ->where('max_age', $maxAge)
                            ->where('id', '!=', $ageGroupId)
                            ->exists();

                        if ($exists) {
                            $fail('An age group with this min age and max age combination already exists.');
                        }
                    }
                },
            ],
            'max_age' => [
                'required',
                'integer',
                'gte:min_age',
                function ($attribute, $value, $fail) use ($ageGroupId) {
                    $minAge = request()->input('min_age');
                    if ($minAge) {
                        $exists = \App\Models\AgeGroup::where('min_age', $minAge)
                            ->where('max_age', $value)
                            ->where('id', '!=', $ageGroupId)
                            ->exists();

                        if ($exists) {
                            $fail('An age group with this min age and max age combination already exists.');
                        }
                    }
                },
            ],
        ];
    }
}
