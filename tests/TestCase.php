<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Member;
use App\Models\Community;
use App\Models\Cluster;
use App\Models\Gender;
use App\Models\BloodGroup;
use App\Models\IncomeRange;
use App\Models\Status;
use App\Models\Relationship;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Zone;
use App\Models\Parish;
use App\Models\Designation;
use App\Models\AgeGroup;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, CreatesApplication;

    protected $adminUser;
    protected $member1;
    protected $member2;
    protected $community;
    protected $cluster;
    protected $gender;
    protected $bloodGroup;
    protected $incomeRange;
    protected $status;
    protected $relationship;
    protected $country;
    protected $state;
    protected $city;
    protected $zone;
    protected $parish;
    protected $designation;
    protected $ageGroup;

    protected function setUp(): void
    {
        parent::setUp();
        
        echo "\n=== Setting up test data ===\n";
        
        // Create test users and roles
        $this->createTestUsers();
        
        // Create master data using your real structure
        $this->createMasterData();
        
        // Create test members using your actual data
        $this->createTestMembers();
        
        echo "=== Test setup complete ===\n\n";
    }

    protected function createTestUsers()
    {
        // Create roles first using Spatie models
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $ppcHeadRole = Role::firstOrCreate(['name' => 'PPCHead']);
        $sccHeadRole = Role::firstOrCreate(['name' => 'SCCHead']);
        $userRole = Role::firstOrCreate(['name' => 'User']);
        
        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        
        $this->adminUser->assignRole($adminRole);
        
        echo "✓ Test users created\n";
    }

    protected function createMasterData()
    {
        // Create master data tables first
        $this->country = Country::firstOrCreate(['name' => 'India']);
        $this->state = State::firstOrCreate(['name' => 'Maharashtra', 'country_id' => $this->country->id]);
        $this->city = City::firstOrCreate(['name' => 'Mumbai', 'state_id' => $this->state->id]);
        $this->zone = Zone::firstOrCreate(['name' => 'Test Zone']);
        $this->parish = Parish::firstOrCreate(['name' => 'Test Parish', 'deanery' => 'mumbai']);
        $this->designation = Designation::firstOrCreate(['name' => 'Test Designation']);
        $this->ageGroup = AgeGroup::firstOrCreate(['name' => 'Test Age Group']);
        $this->gender = Gender::firstOrCreate(['name' => 'Male']);
        $this->bloodGroup = BloodGroup::firstOrCreate(['name' => 'Test Blood Group']);
        $this->incomeRange = IncomeRange::firstOrCreate(['name' => 'Test Income Range']);
        $this->status = Status::firstOrCreate(['name' => 'Active']);
        $this->relationship = Relationship::firstOrCreate(['name' => 'Member']);
        
        echo "✓ Master data created\n";
    }

    protected function createTestMembers()
    {
        // Create cluster and community
        $this->cluster = Cluster::firstOrCreate(['name' => 'Test Cluster']);
        $this->community = Community::firstOrCreate(['name' => 'Test Community']);
        
        // Create test members using your real data structure
        $this->member1 = Member::create([
            'first_name' => 'Xavier',
            'last_name' => 'Mendonca',
            'email' => 'e0000014@olscemail.com',
            'contact_no_1' => '24371272',
            'date_of_birth' => '1930-07-22',
            'blood_group_id' => $this->bloodGroup->id,
            'income_range_id' => $this->incomeRange->id,
            'status_id' => $this->status->id,
            'relationship_id' => $this->relationship->id,
            'community_id' => $this->community->id,
            'current_city_id' => $this->city->id,
            'current_state_id' => $this->state->id,
            'current_country_id' => $this->country->id,
            'permanent_city_id' => $this->city->id,
            'permanent_state_id' => $this->state->id,
            'permanent_country_id' => $this->country->id,
            'gender_id' => $this->gender->id,
            'parish_id' => $this->parish->id,
            'designation_id' => $this->designation->id,
            'aadhar' => '424252520014',
            'family_no' => 'SAL-001',
            'member_no' => '2025-SAL-M000001',
            'registration_year' => '2025',
        ]);

        $this->member2 = Member::create([
            'first_name' => 'Theresa',
            'last_name' => 'Mendonca',
            'email' => 'e0002660@olscemail.com',
            'contact_no_1' => null, // No phone in your data
            'date_of_birth' => '1940-06-05',
            'blood_group_id' => $this->bloodGroup->id,
            'income_range_id' => $this->incomeRange->id,
            'status_id' => $this->status->id,
            'relationship_id' => $this->relationship->id,
            'community_id' => $this->community->id,
            'current_city_id' => $this->city->id,
            'current_state_id' => $this->state->id,
            'current_country_id' => $this->country->id,
            'permanent_city_id' => $this->city->id,
            'permanent_state_id' => $this->state->id,
            'permanent_country_id' => $this->country->id,
            'gender_id' => $this->gender->id,
            'parish_id' => $this->parish->id,
            'designation_id' => $this->designation->id,
            'aadhar' => '424252522660',
            'family_no' => 'SAL-001',
            'member_no' => '2025-SAL-M000002',
            'registration_year' => '2025',
        ]);
        
        echo "✓ Test members created\n";
    }
}
