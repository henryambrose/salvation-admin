<?php

namespace Database\Factories;

use Modules\Members\Models\Member;
use Modules\Members\Models\Community;
use Modules\Members\Models\CommunityCluster;
use Modules\Members\Models\Gender;
use Modules\Members\Models\Status;
use Modules\Members\Models\Relationship;
use Modules\Members\Models\Country;
use Modules\Members\Models\State;
use Modules\Members\Models\City;
use Modules\Members\Models\Parish;
use Modules\Members\Models\Designation;
use Modules\Members\Models\BloodGroup;
use Modules\Members\Models\IncomeRange;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        $community = Community::factory()->create();

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'contact_no_1' => fake()->phoneNumber(),
            'date_of_birth' => fake()->date(),
            'aadhar' => fake()->numerify('##########'),
            'family_no' => 'SAL-' . fake()->unique()->numerify('###'),
            'member_no' => fake()->year() . '-SAL-M' . fake()->unique()->numerify('######'),
            'registration_year' => fake()->year(),
            'gender_id' => Gender::factory(),
            'community_id' => $community->id,
            'community_cluster_id' => CommunityCluster::factory()->create(['community_id' => $community->id])->id,
            'relationship_id' => Relationship::factory(),
        ];
    }

    public function forCommunity(Community $community): static
    {
        return $this->state(function (array $attributes) use ($community) {
            return [
                'community_id' => $community->id,
            ];
        });
    }

    public function withRequiredFields(): static
    {
        return $this->state(function (array $attributes) {
            // Create required master data if it doesn't exist
            $gender = Gender::firstOrCreate(['name' => 'Test Gender']);
            $status = Status::firstOrCreate(['name' => 'Active']);
            $relationship = Relationship::firstOrCreate(['name' => 'Member']);
            $country = Country::firstOrCreate(['name' => 'Test Country']);
            $state = State::firstOrCreate(['name' => 'Test State', 'country_id' => $country->id]);
            $city = City::firstOrCreate(['name' => 'Test City', 'state_id' => $state->id]);
            
            // Fix: Add required deanery field for parish
            $parish = Parish::firstOrCreate([
                'name' => 'Test Parish',
                'deanery' => 'Test Deanery'  // This field was missing!
            ]);
            
            $designation = Designation::firstOrCreate(['name' => 'Test Designation']);
            $bloodGroup = BloodGroup::firstOrCreate(['name' => 'Test Blood Group']);
            $incomeRange = IncomeRange::firstOrCreate(['name' => 'Test Income Range']);

            return [
                'gender_id' => $gender->id,
                'status_id' => $status->id,
                'relationship_id' => $relationship->id,
                'current_city_id' => $city->id,
                'current_state_id' => $state->id,
                'current_country_id' => $country->id,
                'permanent_city_id' => $city->id,
                'permanent_state_id' => $state->id,
                'permanent_country_id' => $country->id,
                'parish_id' => $parish->id,
                'designation_id' => $designation->id,
                'blood_group_id' => $bloodGroup->id,
                'income_range_id' => $incomeRange->id,
            ];
        });
    }
}
