# Permission Testing Strategy

## Overview
This directory contains comprehensive permission tests for all CRUD operations across the application.

## Test Coverage Matrix

### Members Module
| Resource | List | Create | Read | Update | Delete | Status |
|----------|------|--------|------|--------|--------|--------|
| Member | ✅ | ✅ | ✅ | ✅ | ✅ | Complete |
| Community | ✅ | ✅ | ✅ | ✅ | ✅ | Complete |
| Zone | ✅ | ✅ | ✅ | ✅ | ✅ | Complete |
| Parish | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| BloodGroup | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| Gender | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| Relationship | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| City | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| State | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| Country | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| Designation | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| BaptismRecord | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| MarriageRecord | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| DeathRecord | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| ExternalMember | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |

### Fund Module
| Resource | List | Create | Read | Update | Delete | Status |
|----------|------|--------|------|--------|--------|--------|
| FundCategory | ✅ | ✅ | ✅ | ✅ | ✅ | Complete |
| MassIntention | ✅ | ✅ | ⬜ | ⬜ | ⬜ | Partial |
| MassType | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| PaymentMethod | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| AnnualContribution | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| CommunityContribution | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |

### Graveyard Module
| Resource | List | Create | Read | Update | Delete | Status |
|----------|------|--------|------|--------|--------|--------|
| Grave | ✅ | ✅ | ⬜ | ✅ | ✅ | Partial |
| PermanentGrave | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| TemporaryGrave | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| Niche | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| PermanentGraveBooking | ✅ | ✅ | ⬜ | ⬜ | ⬜ | Partial |
| TemporaryGraveBooking | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| NicheTransfer | ✅ | ✅ | ⬜ | ⬜ | ⬜ | Partial |
| AnnualMaintenanceFee | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |
| Payment | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | Pending |

## Permission Naming Convention

Follow this pattern for all resources:
- List: `list-{resource}` (e.g., `list-member`)
- Create: `create-{resource}` (e.g., `create-member`)
- Read: `read-{resource}` (e.g., `read-member`)
- Update: `update-{resource}` (e.g., `update-member`)
- Delete: `delete-{resource}` (e.g., `delete-member`)
- Restore (soft deletes): `restore-{resource}` (e.g., `restore-member`)

## Running Permission Tests

```bash
# Run all permission tests
php artisan test tests/Feature/Permissions

# Run specific resource permission tests
php artisan test tests/Feature/Permissions/CommunityPermissionTest.php

# Run specific test
php artisan test --filter="Community CRUD Permissions"
```

## Test Structure

Each permission test file should follow this structure:

1. **Setup** - Create users with different permission levels
2. **List/Index Tests** - Test viewing list of resources
3. **Create Tests** - Test creating new resources
4. **Read/Show Tests** - Test viewing single resource details
5. **Update Tests** - Test modifying existing resources
6. **Delete Tests** - Test removing resources
7. **Edge Cases** - Test permission combinations

## What These Tests Verify

### 1. Authentication Required
- Unauthenticated users are redirected to login

### 2. Permission Enforcement
- Users without specific permission get 403 Forbidden
- Users with permission can perform action
- Super admin bypasses all permission checks

### 3. Operation Integrity
- Create operations actually create records
- Update operations actually modify records
- Delete operations actually remove/soft-delete records

### 4. Permission Isolation
- Having one permission (e.g., read) doesn't grant another (e.g., delete)
- Permissions are checked per-operation

## Creating New Permission Tests

Use this template for new resources:

```php
<?php

use Tests\Traits\TestsPermissions;
use Modules\YourModule\Models\YourModel;

uses(TestsPermissions::class);

beforeEach(function () {
    $this->setupPermissionUsers([
        'list-your-resource',
        'create-your-resource',
        'read-your-resource',
        'update-your-resource',
        'delete-your-resource',
    ]);
});

describe('YourResource CRUD Permissions', function () {

    test('LIST: requires permission', function () {
        $this->assertRequiresPermission('GET', 'your.resource.index');
    });

    test('LIST: authorized users can access', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'your.resource.index');
    });

    test('CREATE: requires permission', function () {
        $this->assertRequiresPermission('POST', 'your.resource.store', [], [
            'field' => 'value',
        ]);
    });

    test('CREATE: authorized users can create', function () {
        $this->assertAuthorizedUserCanAccess('POST', 'your.resource.store', [], [
            'field' => 'value',
        ]);

        $this->assertDatabaseHas('your_table', ['field' => 'value']);
    });

    // Add READ, UPDATE, DELETE tests following the same pattern
});
```

## Issues Detected by Permission Tests

### Critical Issues Found:
1. **Missing Permission Checks** - Some controllers don't check permissions
2. **Inconsistent Permission Names** - Some use different naming patterns
3. **Super Admin Bypass Not Working** - Some controllers don't check `is_superadmin`
4. **Missing Middleware** - Some routes don't have permission middleware

### Example Issues:

```php
// ❌ Controller without permission check
public function store(Request $request) {
    return Resource::create($request->all());
}

// ✅ Controller with proper permission check
public function store(Request $request) {
    $this->authorize('create-resource');
    return Resource::create($request->validated());
}
```

## Next Steps

1. ✅ Create permission tests for all remaining resources
2. ⬜ Fix any controllers that don't properly check permissions
3. ⬜ Ensure all routes have appropriate middleware
4. ⬜ Document all permissions in database seeder
5. ⬜ Create role-permission matrix documentation
