<?php

use Modules\Members\Models\Member;
use Modules\Members\Models\Community;
use Modules\Members\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Create permission
    Permission::create(['name' => 'list-member']);

    $this->admin = User::factory()->create();
    $adminRole = Role::create(['name' => 'admin']);
    $this->admin->assignRole($adminRole);
    $this->admin->givePermissionTo('list-member');
});

describe('Member Search Functionality', function () {
    test('can search members by first name', function () {
        Member::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        Member::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith']);
        Member::factory()->create(['first_name' => 'Bob', 'last_name' => 'Johnson']);

        $response = $this->actingAs($this->admin)
            ->get(route('member.index', ['search' => 'John']));

        expect($response->status())->toBe(200);
    });

    test('can search members by last name', function () {
        Member::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        Member::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe']);
        Member::factory()->create(['first_name' => 'Bob', 'last_name' => 'Smith']);

        $response = $this->actingAs($this->admin)
            ->get(route('member.index', ['search' => 'Doe']));

        expect($response->status())->toBe(200);
    });

    test('can search members by family number', function () {
        Member::factory()->create(['family_no' => 'SAL-001']);
        Member::factory()->create(['family_no' => 'SAL-001']);
        Member::factory()->create(['family_no' => 'SAL-002']);

        $response = $this->actingAs($this->admin)
            ->get(route('member.index', ['search' => 'SAL-001']));

        expect($response->status())->toBe(200);
    });

    test('can search members by member number', function () {
        Member::factory()->create(['member_no' => '2024-SAL-M000001']);
        Member::factory()->create(['member_no' => '2024-SAL-M000002']);

        $response = $this->actingAs($this->admin)
            ->get(route('member.index', ['search' => '2024-SAL-M000001']));

        expect($response->status())->toBe(200);
    });

    test('can search members by contact number', function () {
        Member::factory()->create(['contact_no_1' => '9876543210']);
        Member::factory()->create(['contact_no_1' => '9876543211']);

        $response = $this->actingAs($this->admin)
            ->get(route('member.index', ['search' => '9876543210']));

        expect($response->status())->toBe(200);
    });

    test('search is case insensitive', function () {
        Member::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);

        $response = $this->actingAs($this->admin)
            ->get(route('member.index', ['search' => 'JOHN']));

        expect($response->status())->toBe(200);
    });

    test('can filter members by community', function () {
        $community1 = Community::factory()->create();
        $community2 = Community::factory()->create();

        Member::factory()->count(3)->create(['community_id' => $community1->id]);
        Member::factory()->count(2)->create(['community_id' => $community2->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('member.index', ['community_id' => $community1->id]));

        expect($response->status())->toBe(200);
    });

    test('can filter members by gender', function () {
        $maleGender = \Modules\Members\Models\Gender::factory()->create(['name' => 'Male']);
        $femaleGender = \Modules\Members\Models\Gender::factory()->create(['name' => 'Female']);

        Member::factory()->count(3)->create(['gender_id' => $maleGender->id]);
        Member::factory()->count(2)->create(['gender_id' => $femaleGender->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('member.index', ['gender_id' => $maleGender->id]));

        expect($response->status())->toBe(200);
    });

    test('can filter members by marital status', function () {
        Member::factory()->count(3)->create(['marital_status' => 'Married']);
        Member::factory()->count(2)->create(['marital_status' => 'Single']);

        $response = $this->actingAs($this->admin)
            ->get(route('member.index', ['marital_status' => 'Married']));

        expect($response->status())->toBe(200);
    });

    test('can combine search and filters', function () {
        $community = Community::factory()->create();

        Member::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'community_id' => $community->id,
            'marital_status' => 'Married',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('member.index', [
                'search' => 'John',
                'community_id' => $community->id,
                'marital_status' => 'Married',
            ]));

        expect($response->status())->toBe(200);
    });
});

describe('Member Pagination', function () {
    test('members are paginated', function () {
        Member::factory()->count(50)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('member.index'));

        expect($response->status())->toBe(200);
    });

    test('can change items per page', function () {
        Member::factory()->count(50)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('member.index', ['per_page' => 25]));

        expect($response->status())->toBe(200);
    });
});
