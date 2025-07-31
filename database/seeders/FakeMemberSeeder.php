<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Community;
use App\Models\CommunityCluster;
use App\Models\Relationship;
use App\Models\Gender;
use App\Models\BloodGroup;
use App\Models\Status;
use App\Models\Designation;
use App\Models\FamilyIncomeRange;
use App\Models\CellsAndAssociation;
use App\Models\Town;
use App\Models\State;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FakeMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get existing data for relationships
        $communities = Community::all();
        $clusters = CommunityCluster::all();
        $relationships = Relationship::all();
        $genders = Gender::all();
        $bloodGroups = BloodGroup::all();
        $statuses = Status::all();
        $designations = Designation::all();
        $familyIncomeRanges = FamilyIncomeRange::all();
        $cellsAndAssociations = CellsAndAssociation::all();
        $towns = Town::all();
        $states = State::all();
        $countries = Country::all();

        // Create 50 fake members with complete data
        for ($i = 1; $i <= 50; $i++) {
            $gender = $genders->random();
            $relationship = $relationships->random();
            
            // Generate appropriate names based on gender
            $firstName = $gender->name === 'Male' ? $faker->firstNameMale() : $faker->firstNameFemale();
            $lastName = $faker->lastName();
            
            // Generate family number (format: FAM-YYYY-XXXX)
            $familyNo = 'FAM-' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            
            // Generate Aadhar number (12 digits)
            $aadhar = $faker->numerify('##########');
            
            // Generate dates
            $dateOfBirth = $faker->dateTimeBetween('-80 years', '-18 years');
            $baptismDate = $faker->dateTimeBetween($dateOfBirth, '+5 years');
            $confirmationDate = $faker->dateTimeBetween($baptismDate, '+10 years');
            $marriageDate = $faker->optional(0.7)->dateTimeBetween('-40 years', '-20 years');
            $deathDate = $faker->optional(0.1)->dateTimeBetween('-10 years', 'now');
            
            // Generate addresses
            $permanentAddress1 = $faker->streetAddress();
            $permanentAddress2 = $faker->optional(0.8)->secondaryAddress();
            $permanentAddress3 = $faker->optional(0.3)->city();
            
            $currentAddress1 = $faker->streetAddress();
            $currentAddress2 = $faker->optional(0.8)->secondaryAddress();
            $currentAddress3 = $faker->optional(0.3)->city();
            
            // Generate contact information
            $contactNo = $faker->numerify('##########'); // 10 digits
            $email = $faker->email();
            
            // Generate registration numbers
            $baptismRegNo = 'BAP-' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $confirmationRegNo = 'CON-' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $marriageRegNo = $marriageDate ? 'MAR-' . $marriageDate->format('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT) : null;
            $deathRegNo = $deathDate ? 'DEA-' . $deathDate->format('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT) : null;
            
            // Generate parish names
            $baptismParish = $faker->company() . ' Parish';
            $confirmationParish = $faker->company() . ' Parish';
            $marriageParish = $marriageDate ? $faker->company() . ' Parish' : null;
            $deathParish = $deathDate ? $faker->company() . ' Parish' : null;
            
            // Generate education and work information
            $schoolName = $faker->company() . ' School';
            $collegeName = $faker->company() . ' College';
            $latestQualifications = $faker->randomElement(['High School', 'Bachelor\'s Degree', 'Master\'s Degree', 'PhD', 'Diploma', 'Certificate']);
            $companyName = $faker->company();
            
            // Generate old and new IDs
            $newOlscId = 'OLSC-' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $oldSalId = 'SAL-' . date('Y', strtotime('-5 years')) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            
            Member::create([
                'community_id' => $communities->random()->id,
                'community_cluster_id' => $clusters->random()->id,
                'new_olsc_id' => $newOlscId,
                'old_sal_id' => $oldSalId,
                'aadhar' => $aadhar,
                'family_no' => $familyNo,
                'status_id' => $statuses->random()->id,
                'relationship_id' => $relationship->id,
                'last_name' => $lastName,
                'first_name' => $firstName,
                'middle_name' => $faker->optional(0.6)->firstName(),
                'gender_id' => $gender->id,
                'date_of_birth' => $dateOfBirth->format('Y-m-d'),
                'permanent_add1' => $permanentAddress1,
                'permanent_add2' => $permanentAddress2,
                'permanent_add3' => $permanentAddress3,
                'permanent_town_id' => $towns->random()->id,
                'permanent_pincode' => $faker->numerify('######'),
                'permanent_state_id' => $states->random()->id,
                'permanent_country_id' => $countries->random()->id,
                'current_add1' => $currentAddress1,
                'current_add2' => $currentAddress2,
                'current_add3' => $currentAddress3,
                'current_town_id' => $towns->random()->id,
                'current_pincode' => $faker->numerify('######'),
                'current_state_id' => $states->random()->id,
                'current_country_id' => $countries->random()->id,
                'contact_no_1' => $contactNo,
                'email' => $email,
                'blood_group_id' => $bloodGroups->random()->id,
                'cells_and_association_id' => $cellsAndAssociations->random()->id,
                'school_name' => $schoolName,
                'college_name' => $collegeName,
                'latest_qualifications' => $latestQualifications,
                'company_name' => $companyName,
                'designation_id' => $designations->random()->id,
                'family_income_range_id' => $familyIncomeRanges->random()->id,
                'baptism_date' => $baptismDate->format('Y-m-d'),
                'baptism_reg_no' => $baptismRegNo,
                'baptism_parish' => $baptismParish,
                'confirmation_date' => $confirmationDate->format('Y-m-d'),
                'confirmation_reg_no' => $confirmationRegNo,
                'confirmation_parish' => $confirmationParish,
                'marriage_date' => $marriageDate ? $marriageDate->format('Y-m-d') : null,
                'marriage_reg_no' => $marriageRegNo,
                'marriage_parish' => $marriageParish,
                'death_date' => $deathDate ? $deathDate->format('Y-m-d') : null,
                'deaths_reg_no' => $deathRegNo,
                'death_parish' => $deathParish,
            ]);
        }
        
        $this->command->info('50 fake members created successfully with complete data!');
    }
} 