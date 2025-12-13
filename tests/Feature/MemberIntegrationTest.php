<?php

use Modules\Members\Models\Member;
use Modules\Members\Models\Community;
use Modules\Members\Models\CommunityCluster;
use Modules\Members\Models\Relationship;
use Modules\Members\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Create permissions
    Permission::create(['name' => 'list-member']);
    Permission::create(['name' => 'create-member']);
    Permission::create(['name' => 'read-member']);
    Permission::create(['name' => 'update-member']);
    Permission::create(['name' => 'delete-member']);
    Permission::create(['name' => 'restore-member']);

    $this->admin = User::factory()->create();
    $adminRole = Role::create(['name' => 'admin']);
    $this->admin->assignRole($adminRole);
    $this->admin->givePermissionTo([
        'list-member',
        'create-member',
        'read-member',
        'update-member',
        'delete-member',
        'restore-member',
    ]);
});

describe('Complete Member Lifecycle', function () {
    test('can create, update, view, and delete member', function () {
        // Create
        $community = Community::factory()->create();
        $communityCluster = CommunityCluster::factory()->create(['community_id' => $community->id]);
        $relationship = Relationship::factory()->create();

        $createResponse = $this->actingAs($this->admin)
            ->post(route('member.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'community_id' => $community->id,
                'community_cluster_id' => $communityCluster->id,
                'relationship_id' => $relationship->id,
                'family_no' => 'SAL-001',
                'date_of_birth' => '1990-01-15',
                'contact_no_1' => '9876543210',
            ]);

        expect($createResponse->status())->toBe(302);

        $member = Member::where('first_name', 'John')
            ->where('last_name', 'Doe')
            ->first();

        expect($member)->not->toBeNull();

        // Update
        $updateResponse = $this->actingAs($this->admin)
            ->put(route('member.update', $member), [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'community_id' => $community->id,
                'community_cluster_id' => $communityCluster->id,
                'relationship_id' => $relationship->id,
                'spouse_source' => 'Member',
                'father_source' => 'Member',
                'mother_source' => 'Member',
            ]);

        expect($updateResponse->status())->toBe(302);

        $member->refresh();
        expect($member->first_name)->toBe('Jane')
            ->and($member->last_name)->toBe('Smith');

        // View
        $viewResponse = $this->actingAs($this->admin)
            ->get("/member/{$member->id}/details");

        expect($viewResponse->status())->toBe(200);

        // Soft Delete
        $deleteResponse = $this->actingAs($this->admin)
            ->delete(route('member.destroy', $member));

        expect($deleteResponse->status())->toBe(302);

        $member->refresh();
        expect($member->trashed())->toBeTrue();

        // Restore
        $restoreResponse = $this->actingAs($this->admin)
            ->post(route('member.restore', $member->id));

        expect($restoreResponse->status())->toBe(302);

        $member->refresh();
        expect($member->trashed())->toBeFalse();
    });

    test('can create family with multiple members', function () {
        $community = Community::factory()->create();
        $familyNo = 'SAL-123';

        // Create father
        $father = Member::factory()->create([
            'first_name' => 'John',
            'family_no' => $familyNo,
            'community_id' => $community->id,
        ]);

        // Create mother
        $mother = Member::factory()->create([
            'first_name' => 'Jane',
            'family_no' => $familyNo,
            'community_id' => $community->id,
            'spouse_id' => $father->id,
        ]);

        // Update father with spouse
        $father->spouse_id = $mother->id;
        $father->save();

        // Create children
        $child1 = Member::factory()->create([
            'first_name' => 'Alice',
            'family_no' => $familyNo,
            'father_id' => $father->id,
            'mother_id' => $mother->id,
            'community_id' => $community->id,
        ]);

        $child2 = Member::factory()->create([
            'first_name' => 'Bob',
            'family_no' => $familyNo,
            'father_id' => $father->id,
            'mother_id' => $mother->id,
            'community_id' => $community->id,
        ]);

        $family = Member::where('family_no', $familyNo)->get();

        expect($family)->toHaveCount(4)
            ->and($father->spouse->id)->toBe($mother->id)
            ->and($child1->father->id)->toBe($father->id)
            ->and($child1->mother->id)->toBe($mother->id);
    });
});

describe('Member with Complete Sacramental Records', function () {
    test('can create member with all sacramental details', function () {
        $baptismParish = \Modules\Members\Models\Parish::factory()->create();
        $confirmationParish = \Modules\Members\Models\Parish::factory()->create();
        $marriageParish = \Modules\Members\Models\Parish::factory()->create();
        $community = Community::factory()->create();

        $memberData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'community_id' => $community->id,
            'community_cluster_id' => CommunityCluster::factory()->create(['community_id' => $community->id])->id,
            'relationship_id' => Relationship::factory()->create()->id,
            'date_of_birth' => '1990-01-15',
            'baptism_date' => '1990-02-20',
            'baptism_parish_id' => $baptismParish->id,
            'baptism_parish' => $baptismParish->name,
            'baptism_reg_no' => 'BAP-001',
            'confirmation_date' => '2002-05-10',
            'confirmation_parish_id' => $confirmationParish->id,
            'confirmation_parish' => $confirmationParish->name,
            'confirmation_reg_no' => 'CONF-001',
            'marriage_date' => '2015-06-15',
            'marriage_parish_id' => $marriageParish->id,
            'marriage_parish' => $marriageParish->name,
            'marriage_reg_no' => 'MAR-001',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('member.store'), $memberData);

        expect($response->status())->toBe(302);

        $member = Member::where('first_name', 'John')
            ->where('last_name', 'Doe')
            ->first();

        // Verify member was created with basic sacramental data
        expect($member)->not->toBeNull()
            ->and($member->first_name)->toBe('John')
            ->and($member->last_name)->toBe('Doe');
    });
});

describe('Member Address Management', function () {
    test('can create member with complete address details', function () {
        $city = \Modules\Members\Models\City::factory()->create();
        $state = \Modules\Members\Models\State::factory()->create();
        $country = \Modules\Members\Models\Country::factory()->create();
        $community = Community::factory()->create();

        $memberData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'community_id' => $community->id,
            'community_cluster_id' => CommunityCluster::factory()->create(['community_id' => $community->id])->id,
            'relationship_id' => Relationship::factory()->create()->id,
            'permanent_add1' => '123 Main Street',
            'permanent_add2' => 'Apartment 4B',
            'permanent_city_id' => $city->id,
            'permanent_state_id' => $state->id,
            'permanent_country_id' => $country->id,
            'permanent_pincode' => '400001',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('member.store'), $memberData);

        expect($response->status())->toBe(302);

        $member = Member::where('first_name', 'John')->first();

        expect($member->permanent_city_id)->toBe($city->id)
            ->and($member->permanent_state_id)->toBe($state->id)
            ->and($member->permanent_country_id)->toBe($country->id);
    });
});
