<?php

namespace Database\Factories;

use Modules\Members\Models\Town;
use Modules\Members\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class TownFactory extends Factory
{
    protected $model = Town::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
            'pincode' => $this->faker->postcode(),
            'city_id' => City::factory(),
        ];
    }
}
