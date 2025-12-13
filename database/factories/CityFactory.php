<?php

namespace Database\Factories;

use Modules\Members\Models\City;
use Modules\Members\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
            'state_id' => State::factory(),
        ];
    }
}
