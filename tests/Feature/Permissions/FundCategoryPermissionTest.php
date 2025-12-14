<?php

use Tests\Traits\TestsPermissions;

uses(TestsPermissions::class);

beforeEach(function () {
    $this->setupPermissionUsers([
        'list-fund-category',
        'create-fund-category',
        'read-fund-category',
        'update-fund-category',
        'delete-fund-category',
    ]);
});

describe('Fund Category CRUD Permissions', function () {

    test('LIST: requires authentication', function () {
        $this->assertRequiresAuthentication('GET', 'fund.categories.index');
    });

    test('LIST: requires list-fund-category permission', function () {
        $this->assertRequiresPermission('GET', 'fund.categories.index');
    });

    test('LIST: authorized users can list', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'fund.categories.index');
    });

    test('LIST: super admin can list', function () {
        $this->assertSuperAdminCanAccess('GET', 'fund.categories.index');
    });

    test('CREATE: requires create-fund-category permission', function () {
        $this->assertRequiresPermission('POST', 'fund.categories.store', [], [
            'name' => 'Test Category',
            'description' => 'Test Description',
        ]);
    });

    test('CREATE: authorized users can create', function () {
        $this->assertAuthorizedUserCanAccess('POST', 'fund.categories.store', [], [
            'name' => 'New Category',
            'description' => 'Description',
        ]);

        $this->assertDatabaseHas('fund_categories', [
            'name' => 'New Category',
        ]);
    });

    test('CREATE: super admin can create', function () {
        $this->assertSuperAdminCanAccess('POST', 'fund.categories.store', [], [
            'name' => 'Admin Category',
        ]);
    });
});

describe('Mass Intention Permissions', function () {

    beforeEach(function () {
        $this->setupPermissionUsers([
            'list-mass-intention',
            'create-mass-intention',
            'update-mass-intention',
            'delete-mass-intention',
        ]);
    });

    test('LIST: requires list-mass-intention permission', function () {
        $this->assertRequiresPermission('GET', 'fund.mass-intentions.index');
    });

    test('LIST: authorized users can list', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'fund.mass-intentions.index');
    });

    test('CREATE: requires create-mass-intention permission', function () {
        $massType = \Modules\Fund\Models\MassType::create([
            'name' => 'Sunday Mass',
            'default_time' => '09:00:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $member = \Modules\Members\Models\Member::factory()->create();
        $massIntentionType = \Modules\Fund\Models\MassIntentionType::create([
            'name' => 'Thanksgiving',
            'default_amount' => 100,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $paymentMethod = \Modules\Fund\Models\PaymentMethod::create([
            'name' => 'Cash',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertRequiresPermission('POST', 'fund.mass-intentions.store', [], [
            'member_type' => 'member',
            'member_id' => $member->id,
            'mass_type_id' => $massType->id,
            'mass_intention_type_id' => $massIntentionType->id,
            'mass_date' => now()->addDays(1)->format('Y-m-d'),
            'status' => 'pending',
            'payment_method_id' => $paymentMethod->id,
            'amount' => 100,
        ]);
    });

    test('CREATE: authorized users can create', function () {
        $massType = \Modules\Fund\Models\MassType::create([
            'name' => 'Sunday Mass',
            'default_time' => '09:00:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $member = \Modules\Members\Models\Member::factory()->create();
        $massIntentionType = \Modules\Fund\Models\MassIntentionType::create([
            'name' => 'Thanksgiving',
            'default_amount' => 100,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $paymentMethod = \Modules\Fund\Models\PaymentMethod::create([
            'name' => 'Cash',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertAuthorizedUserCanAccess('POST', 'fund.mass-intentions.store', [], [
            'member_type' => 'member',
            'member_id' => $member->id,
            'mass_type_id' => $massType->id,
            'mass_intention_type_id' => $massIntentionType->id,
            'mass_date' => now()->addDays(1)->format('Y-m-d'),
            'status' => 'pending',
            'payment_method_id' => $paymentMethod->id,
            'amount' => 100,
        ]);

        $this->assertDatabaseHas('mass_intentions', [
            'mass_type_id' => $massType->id,
        ]);
    });
});
