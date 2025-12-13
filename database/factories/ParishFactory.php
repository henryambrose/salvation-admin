<?php

namespace Database\Factories;

use Modules\Members\Models\Parish;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParishFactory extends Factory
{
    protected $model = Parish::class;

    public function definition(): array
    {
        return [
            'deanery' => $this->faker->word(),
            'name' => $this->faker->company() . ' Parish',
            'code' => strtoupper($this->faker->lexify('???')),
            'address' => $this->faker->address(),
            'town' => $this->faker->city(),
        ];
    }
}
