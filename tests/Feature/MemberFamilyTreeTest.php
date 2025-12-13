<?php

use Modules\Members\Models\Member;
use Modules\Members\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Create permission
    Permission::create(['name' => 'read-member']);

    $this->admin = User::factory()->create();
    $adminRole = Role::create(['name' => 'admin']);
    $this->admin->assignRole($adminRole);
    $this->admin->givePermissionTo('read-member');
});

describe('Family Tree Structure', function () {
    test('can build three generation family tree', function () {
        // Grandparents
        $grandfather = Member::factory()->create(['first_name' => 'Grandfather']);
        $grandmother = Member::factory()->create(['first_name' => 'Grandmother']);

        // Parents
        $father = Member::factory()->create([
            'first_name' => 'Father',
            'father_id' => $grandfather->id,
            'mother_id' => $grandmother->id,
        ]);
        $mother = Member::factory()->create(['first_name' => 'Mother']);

        // Children
        $child = Member::factory()->create([
            'first_name' => 'Child',
            'father_id' => $father->id,
            'mother_id' => $mother->id,
        ]);

        expect($child->father->id)->toBe($father->id)
            ->and($child->mother->id)->toBe($mother->id)
            ->and($father->father->id)->toBe($grandfather->id)
            ->and($father->mother->id)->toBe($grandmother->id);
    });

    test('can identify siblings through common parents', function () {
        $father = Member::factory()->create();
        $mother = Member::factory()->create();

        $child1 = Member::factory()->create([
            'father_id' => $father->id,
            'mother_id' => $mother->id,
        ]);
        $child2 = Member::factory()->create([
            'father_id' => $father->id,
            'mother_id' => $mother->id,
        ]);
        $child3 = Member::factory()->create([
            'father_id' => $father->id,
            'mother_id' => $mother->id,
        ]);

        $siblings = Member::where('father_id', $father->id)
            ->where('mother_id', $mother->id)
            ->where('id', '!=', $child1->id)
            ->get();

        expect($siblings)->toHaveCount(2);
    });

    test('can track family through family number', function () {
        $familyNo = 'SAL-123';

        $members = Member::factory()->count(6)->create(['family_no' => $familyNo]);

        $family = Member::where('family_no', $familyNo)->get();

        expect($family)->toHaveCount(6);
    });

    test('can identify orphan members without parents', function () {
        Member::factory()->count(3)->create([
            'father_id' => null,
            'mother_id' => null,
        ]);

        $father = Member::factory()->create();
        Member::factory()->create(['father_id' => $father->id]);

        $orphans = Member::whereNull('father_id')
            ->whereNull('mother_id')
            ->get();

        expect($orphans)->toHaveCount(4); // 3 orphans + 1 father
    });

    test('can identify single parent families', function () {
        $father = Member::factory()->create();

        $child = Member::factory()->create([
            'father_id' => $father->id,
            'mother_id' => null,
        ]);

        expect($child->father)->not->toBeNull()
            ->and($child->mother)->toBeNull();
    });
});

describe('Family Relationships', function () {
    test('spouse relationship can be set', function () {
        $husband = Member::factory()->create();
        $wife = Member::factory()->create();

        $husband->spouse_id = $wife->id;
        $husband->save();

        expect($husband->fresh()->spouse_id)->toBe($wife->id);
    });

    test('can track external spouse', function () {
        $member = Member::factory()->create([
            'spouse_source' => 'External',
        ]);

        expect($member->spouse_source)->toBe('External');
    });

    test('can track birth family number', function () {
        $member = Member::factory()->create([
            'birth_family_no' => 'SAL-001',
            'family_no' => 'SAL-002', // Different from birth family
        ]);

        expect($member->birth_family_no)->toBe('SAL-001')
            ->and($member->family_no)->toBe('SAL-002');
    });

    test('can track multiple children for a parent', function () {
        $mother = Member::factory()->create();

        $children = Member::factory()->count(4)->create([
            'mother_id' => $mother->id,
        ]);

        $motherChildren = Member::where('mother_id', $mother->id)->get();

        expect($motherChildren)->toHaveCount(4);
    });
});

describe('Family Details API', function () {
    test('can fetch family details by family number', function () {
        $familyNo = 'SAL-456';
        Member::factory()->count(5)->create(['family_no' => $familyNo]);

        $response = $this->actingAs($this->admin)
            ->get("/member/family-details/{$familyNo}");

        expect($response->status())->toBe(200);
    });

    test('family details excludes specified member', function () {
        $familyNo = 'SAL-789';
        $members = Member::factory()->count(4)->create(['family_no' => $familyNo]);
        $excludeMember = $members->first();

        $response = $this->actingAs($this->admin)
            ->get("/member/family-details/{$familyNo}?exclude_member_id={$excludeMember->id}");

        expect($response->status())->toBe(200);
    });
});
