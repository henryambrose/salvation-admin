<?php

namespace Database\Factories;

use Modules\Members\Models\State;
use Modules\Members\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class StateFactory extends Factory
{
    protected $model = State::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->state(),
            'abbr' => strtoupper($this->faker->lexify('??')),
            'country_id' => Country::factory(),
        ];
    }
}
