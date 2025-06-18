<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1000 members using Faker
        for ($i = 0; $i < 1000; $i++) {
            Member::create([
                'community_id' => null,
                'community_cluster_id' => null,
                'new_olsc_id' => fake()->unique()->numerify('OLSC####'),
                'old_sal_id' => fake()->unique()->numerify('SAL####'),
                'aadhar' => fake()->unique()->numerify('####-####-####'),
                'family_no' => fake()->numerify('FAM####'),
                'last_name' => fake()->lastName(),
                'first_name' => fake()->firstName(),
                'middle_name' => fake()->optional()->firstName(),
                'date_of_birth' => fake()->date(),
                'permanent_add1' => fake()->streetAddress(),
                'permanent_add2' => fake()->optional()->secondaryAddress(),
                'permanent_add3' => null,
                'permanent_town' => fake()->city(),
                'permanent_city' => fake()->city(),
                'permanent_pincode' => fake()->postcode(),
                'permanent_state' => fake()->state(),
                'permanent_country' => fake()->country(),
                'current_add1' => fake()->streetAddress(),
                'current_add2' => fake()->optional()->secondaryAddress(),
                'current_add3' => null,
                'current_town' => fake()->city(),
                'current_city' => fake()->city(),
                'current_pincode' => fake()->postcode(),
                'current_state' => fake()->state(),
                'current_country' => fake()->country(),
                'contact_no' => fake()->phoneNumber(),
                'email' => fake()->unique()->safeEmail(),
                'blood_group' => fake()->optional()->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
                'cells_and_association_id' => null,
                'school_name' => fake()->optional()->company(),
                'college_name' => fake()->optional()->company(),
                'latest_qualifications' => fake()->optional()->word(),
                'company_name' => fake()->optional()->company(),
                'designation' => fake()->optional()->jobTitle(),
                'family_income_range' => fake()->optional()->randomElement(['<1L', '1L-5L', '5L-10L', '>10L']),
                'baptism_date' => fake()->optional()->date(),
                'baptism_reg_no' => fake()->optional()->numerify('BAPT####'),
                'baptism_parish' => fake()->optional()->city(),
                'confirmation_date' => fake()->optional()->date(),
                'confirmation_reg_no' => fake()->optional()->numerify('CONF####'),
                'confirmation_parish' => fake()->optional()->city(),
                'marriage_date' => fake()->optional()->date(),
                'marriage_reg_no' => fake()->optional()->numerify('MARR####'),
                'marriage_parish' => fake()->optional()->city(),
                'death_date' => fake()->optional()->date(),
                'deaths_reg_no' => fake()->optional()->numerify('DEATH####'),
                'death_parish' => fake()->optional()->city(),
            ]);
        }
    }
}
