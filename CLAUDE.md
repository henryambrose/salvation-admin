# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 + Vue 3 + Inertia.js application for church administration called "Salvation Admin". It manages members, fund contributions, and graveyard operations for a Catholic parish. The application uses a modular architecture with three main modules: Members, Fund, and Graveyard.

## Technology Stack

**Backend:**
- Laravel 12 (PHP 8.2+)
- MySQL database
- Spatie Laravel Permission for role-based access control
- Inertia.js for server-driven single-page app
- PHPSpreadsheet for Excel exports
- SimpleSoftwareIO QR Code for generating QR codes

**Frontend:**
- Vue 3 with TypeScript
- Tailwind CSS 4 with PostCSS
- Reka UI (Radix Vue) for accessible components
- Lucide Vue Next for icons
- VueUse for composition utilities
- Axios for HTTP requests
- Ziggy for Laravel route generation in JS

**Development Tools:**
- Vite for asset bundling
- Pest PHP for testing
- Laravel Pint for code formatting (PHP)
- ESLint + Prettier for JavaScript/TypeScript formatting

## Common Commands

### Development
```bash
# Start development server with queue, logs, and vite (recommended)
composer dev

# Alternative: Run services individually
php artisan serve
php artisan queue:listen --tries=1
php artisan pail --timeout=0
npm run dev
```

### Database
```bash
# Run migrations
php artisan migrate

# Run specific module migrations
php artisan migrate --path=database/modules/members/database/migrations
php artisan migrate --path=database/modules/fund/database/migrations
php artisan migrate --path=database/modules/graveyard/database/migrations

# Seed database
php artisan db:seed

# Refresh database (CAUTION: drops all tables)
php artisan migrate:fresh --seed
```

### Testing
```bash
# Run all tests
composer test
# or
php artisan test

# Run specific test file
php artisan test --filter TestName

# Run tests with coverage
php artisan test --coverage
```

### Code Quality
```bash
# Format PHP code
./vendor/bin/pint

# Check PHP syntax
php -l path/to/file.php

# Lint and fix JavaScript/TypeScript
npm run lint

# Format frontend code
npm run format

# Check formatting without changes
npm run format:check
```

### Build
```bash
# Build frontend assets for production
npm run build

# Build with SSR support
npm run build:ssr

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Composer
```bash
# Install PHP dependencies
composer install

# Update autoloader
composer dump-autoload
```

## Modular Architecture

The application follows a custom modular structure where each module is self-contained with its own controllers, models, policies, migrations, and seeders.

### Module Locations

**Module Code:**
- `app/Modules/Members/` - User authentication, member management, roles & permissions
- `app/Modules/Fund/` - Financial contributions, mass intentions, payment tracking
- `app/Modules/Graveyard/` - Cemetery management, grave bookings, niche transfers

**Module Databases:**
- `database/modules/members/database/` - Members module migrations and seeders
- `database/modules/fund/database/` - Fund module migrations and seeders
- `database/modules/graveyard/database/` - Graveyard module migrations and seeders

### Module Autoloading

The composer.json defines PSR-4 autoloading for each module:
```
Modules\Members\ => app/Modules/Members/
Modules\Fund\ => app/Modules/Fund/
Modules\Graveyard\ => app/Modules/Graveyard/
```

### Module Structure

Each module follows this pattern:
```
app/Modules/{ModuleName}/
├── Console/Commands/       # Artisan commands
├── Http/
│   ├── Controllers/       # Request handlers
│   ├── Middleware/        # Module-specific middleware
│   └── Requests/          # Form validation
├── Models/                # Eloquent models
├── Policies/              # Authorization policies
├── Facades/               # Service facades
└── Helpers/               # Utility functions

database/modules/{modulename}/database/
├── migrations/            # Database schema
└── seeders/              # Database seeders
```

## Routing

Routes are split into feature-specific files in the `routes/` directory:

**Core Routes:**
- `routes/web.php` - Main application routes, dashboard, roles
- `routes/auth.php` - Authentication routes
- `routes/api.php` - API endpoints

**Module Routes:**
- `routes/fund.php` - Fund module (prefix: `/fund`)
- `routes/graveyard.php` - Graveyard module (prefix: `/graveyard`)
- Many feature-specific route files (members, community, audit, etc.)

All routes are loaded via `bootstrap/app.php` and most require authentication.

## Frontend Structure

**Entry Point:** `resources/js/app.ts`

**Key Directories:**
- `resources/js/components/` - Reusable Vue components
- `resources/js/components/ui/` - UI component library (Reka UI wrappers)
- `resources/js/layouts/` - Page layout components
- `resources/js/Pages/` - Inertia page components (route destinations)

**Frontend Patterns:**
- Uses Inertia.js for routing (no separate frontend router)
- Pages receive data as props from Laravel controllers
- Route helpers available via Ziggy: `route('name', params)`
- TypeScript for type safety
- Composition API with `<script setup>` in Vue components

## Authentication & Authorization

**Authentication:**
- Multi-guard system: `web` (default) and `external` guards
- Session-based authentication with CSRF protection
- Session refresh middleware prevents timeout during form filling
- Handles 419 CSRF errors gracefully

**Authorization:**
- Spatie Laravel Permission package for roles and permissions
- Gate facade with super admin bypass (see `AppServiceProvider.php`)
- Module-level permissions (e.g., `fund.view`, `graveyard.manage`)
- Middleware aliases: `role`, `permission`, `role_or_permission`, `superadmin`
- Policies for model-level authorization

## Key Features

### Members Module
- Hierarchical structure: Zones → Communities → Members
- Family relationships and family tree visualization
- Parish management with clusters (SCC/PPC heads)
- Certificate generation system
- Audit logging for all database changes
- External member registration (separate guard)
- Catholic calendar integration

### Fund Module
- Annual family contributions tracking
- Mass intentions management
- Community contributions
- Multiple fund categories and payment methods
- Receipt generation
- Export to Excel functionality

### Graveyard Module
- Three grave types: Temporary, Permanent, Niches
- Booking system with valid member validation
- Niche transfer workflow (request → approve → complete)
- Annual maintenance fee tracking
- Payment management with partial payments
- Service types configuration

## Database Patterns

**Common Patterns:**
- Soft deletes on most models (`deleted_at` column)
- Audit trails via `AuditLog` model
- Many models use UUID primary keys
- Relationships use `family_no` for family grouping
- `status` enums (pending, confirmed, paid, cancelled, etc.)

**Views:**
- `unified_people_view` - Consolidated view of members and external users
- Refresh command: `php artisan members:refresh-unified-view`

## Configuration

**Module Configs:**
- `config/permission.php` - Spatie permission settings
- `config/graveyard.php` - Graveyard-specific configuration
- `config/auth.php` - Multi-guard authentication setup

**Environment:**
- Database credentials in `.env`
- Queue connection defaults to `sync` (process immediately)
- Session lifetime and security settings

## Important Middleware

- `HandleInertiaRequests` - Shares data with all Inertia pages
- `HandleAppearance` - Theme/appearance preferences
- `RefreshSession` - Prevents timeout during form usage
- `AuditMiddleware` - Logs user actions
- `PreventBackHistory` - Cache control headers (alias: `nocache`)
- `SuperAdminMiddleware` - Restricts super admin routes

## Migrations Best Practices

When creating migrations for modules:
1. Place in appropriate module directory: `database/modules/{module}/database/migrations/`
2. Use descriptive timestamps in filename (YYYY_MM_DD_HHMMSS format)
3. Always include `down()` method for rollbacks
4. Use foreign key constraints with proper cascading
5. Run module-specific migrations with `--path` flag

## Seeding

Seeders are organized by module in:
- `database/modules/members/database/seeders/`
- `database/modules/fund/database/seeders/`
- `database/modules/graveyard/database/seeders/`

**Important Seeders:**
- `CreateRoleNPermissionSeeder` - Sets up roles and permissions
- `UserSeeder` - Creates admin users
- `ModuleSeeder` - Registers modules for permission system

## Common Development Tasks

### Adding a New Module Feature
1. Create controller in `app/Modules/{Module}/Http/Controllers/`
2. Define routes in `routes/{module}.php`
3. Create Inertia page in `resources/js/Pages/`
4. Add permissions to seeder if needed
5. Update policies for authorization

### Working with Permissions
- Check permission: `$user->hasPermissionTo('permission.name')`
- Assign role: `$user->assignRole('role-name')`
- Super admins bypass all checks (see `AppServiceProvider`)
- Module-level access uses format: `{module}.access`

### Debugging
- Laravel logs: `storage/logs/laravel.log`
- Live logs: `php artisan pail --timeout=0`
- Debug bar available in development
- Vue devtools for frontend debugging
- Check audit logs table for user action history

## Windows Development Notes

This codebase runs on Windows (detected from paths). Common Windows-specific considerations:
- Use `php artisan` instead of `./artisan`
- Path separators handled by Laravel automatically
- Queue workers may need manual restart after code changes
- Symlinks for storage may require admin privileges: `php artisan storage:link`

## Adding New CRUD Features (Pattern Reference)

When adding new CRUD features to the Members module, follow the established pattern from existing features like SCC Head or Cells & Association Leaders.

### Required Files

1. **Migration** - `database/modules/members/database/migrations/YYYY_MM_DD_HHMMSS_create_{feature}_table.php`
   - Use foreign key constraints with `constrained()` and `nullOnDelete()` where appropriate
   - Include `softDeletes()` for archive functionality
   - **Note:** MySQL has a 64-character limit on index names. Use short custom names for compound indexes:
     ```php
     $table->index(['column1', 'column2'], 'short_custom_idx_name');
     ```

2. **Model** - `app/Modules/Members/Models/{FeatureName}.php`
   - Use `HasFactory`, `SoftDeletes` traits
   - Define `$fillable` array and relationships

3. **Form Requests** - `app/Modules/Members/Http/Requests/`
   - `Store{FeatureName}Request.php`
   - `Update{FeatureName}Request.php`
   - Include unique constraints with soft delete awareness:
     ```php
     'field' => 'required|unique:table,field,NULL,id,deleted_at,NULL'
     ```

4. **Controller** - `app/Modules/Members/Http/Controllers/{FeatureName}Controller.php`
   - Methods: `index`, `create`, `store`, `edit`, `update`, `destroy`, `restore`, `export`
   - Use joins for displaying related names in index listing
   - Include API endpoint for member dropdowns if needed

5. **Routes** - `routes/{feature_name}.php`
   - Register in `routes/web.php` with `require __DIR__ . '/{feature_name}.php';`
   - Include redirect, index, export, resource routes, and restore route

6. **Vue Component** - `resources/js/PagesMembers/{feature_name}/Index.vue`
   - Use vue-multiselect for searchable dropdowns
   - Include modal forms for create/edit with proper focus management
   - Add archive toggle, pagination, and CSV export

7. **Sidebar Navigation** - `resources/js/components/AppSidebar.vue`
   - Add entry in appropriate navigation group with permission check

8. **Permissions** - `database/seeders/MembersPermissionsSeeder.php`
   - Add CRUD permissions: `create-`, `read-`, `update-`, `delete-`, `list-`, `restore-`

### Modal Focus Management Pattern

For modal dialogs, use this pattern to focus the first input when opening:

```typescript
const openCreateModal = () => {
  showCreateModal.value = true;
  nextTick(() => {
    const firstInput = document.querySelector('[data-focus-first]') as HTMLElement;
    firstInput?.focus();
  });
};
```

Add `data-focus-first` attribute to the first input element in the modal.

### Tailwind CSS 4 Notes

- Use `bg-black/50` for modal backdrop opacity (not `bg-opacity-50 bg-black`)
- The old opacity utility classes have been replaced with the slash notation

## Leadership Features

The Members module includes leadership management:
- **SCC Head** - Small Christian Community heads (`/scc-head`)
- **PPC Head** - Parish Pastoral Council heads (`/ppc-head`)
- **Cells & Association Leaders** - Cell/Association leadership (`/cells-and-association-leaders`)

Each uses member dropdowns filtered to show only alive members via API endpoints.

## Recent Development Notes

### Graveyard Receipt Template Updates (January 2026)
Updated receipt templates to match fund receipt styling:
- **Graveyard Payment Receipt** (`resources/views/graveyard/receipts/payment.blade.php`)
  - Added church logo (25mm width) aligned left
  - Header layout: logo + church info side by side
  - Includes church name, address, phone, and trust registration number
  - Subtitle shows "Graveyard Services - Payment Receipt"
  - Two copies: Office Copy and Customer Copy
  - Responsive print media queries for proper scaling

### Delete Functionality for Bookings (January 2026)
Implemented permanent delete with reversal logic for grave bookings:

**Temporary Grave Bookings:**
- Added `destroy()` method in `TemporaryGraveBookingController`
- Only pending bookings can be deleted (confirmed/cancelled cannot)
- Permanently deletes record using `forceDelete()` (bypasses soft delete)
- Reverses all changes to temporary grave:
  - Status: `unavailable` → `available`
  - Clears: `buried_name`, `contact_no`, `last_burial_date`, `destination_permanent_grave_id`
- Uses database transactions for data integrity
- Route: `DELETE /graveyard/temporary-grave-bookings/{id}`
- Permission: `delete-temporary-grave-booking`

**Permanent Grave Bookings:**
- Updated `destroy()` method in `PermanentGraveBookingController`
- Only pending bookings can be deleted
- Prevents deletion if payment records exist
- Permanently deletes record using `forceDelete()`
- No grave status reversal needed (pending bookings don't change grave status)
- Uses database transactions
- Route: `DELETE /graveyard/permanent-grave-bookings/{id}`
- Permission: `delete-permanent-grave-booking`

**Frontend (Vue):**
- Delete button with trash icon in Index.vue pages
- Confirmation dialog using `useConfirm()` composable
- Only shows delete button for pending bookings (based on `canDeleteBooking()`)
- Success/error toast notifications

### Cells & Association Leaders (January 2026)
New CRUD feature added with:
- Table: `cells_and_association_leaders` with foreign keys to `cells_and_associations` and `members`
- Member selection dropdowns for Leader and Assistant Leader (fetches alive members via `/api/members/alive`)
- Full archive/restore functionality
- CSV export capability
- Permissions: `create-cells-and-association-leader`, `read-cells-and-association-leader`, etc.
