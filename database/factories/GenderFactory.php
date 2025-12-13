<?php

namespace Database\Factories;

use Modules\Members\Models\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

class GenderFactory extends Factory
{
    protected $model = Gender::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word() . ' Gender',
        ];
    }
}
