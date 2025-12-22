<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeModulePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-permissions {module : The module name (e.g., Fund, Graveyard, YourModule)} {--resources= : Comma-separated resources (e.g., post,comment,category)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate permission seeder and category rules for a new module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleName = $this->argument('module');
        $resources = $this->option('resources');

        if (!$resources) {
            $resources = $this->ask('Enter comma-separated resources (e.g., post,comment,category)');
        }

        $resourceArray = array_map('trim', explode(',', $resources));

        // Create permission seeder
        $this->createPermissionSeeder($moduleName, $resourceArray);

        // Create permission categories documentation
        $this->createCategoryDocumentation($moduleName, $resourceArray);

        $this->info("\n✅ Module permissions setup complete!");
        $this->line("Next steps:");
        $this->line("1. Run: php artisan db:seed --class={$moduleName}PermissionsSeeder");
        $this->line("2. Add authorization checks to your controllers");
        $this->line("3. Update PermissionCategorySeeder with permission rules");
    }

    /**
     * Create the permission seeder file
     */
    private function createPermissionSeeder(string $moduleName, array $resources): void
    {
        $seederPath = "database/seeders/{$moduleName}PermissionsSeeder.php";

        if (File::exists($seederPath)) {
            $this->warn("Seeder already exists at {$seederPath}");
            return;
        }

        $permissions = $this->generatePermissions($resources);
        $permissionsCode = $this->formatPermissions($permissions);

        $content = <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class {$moduleName}PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all {$moduleName} module permissions
        \${$this->lcfirst($moduleName)}Permissions = [
{$permissionsCode}
        ];

        // Create permissions
        foreach (\${$this->lcfirst($moduleName)}Permissions as \$permissionName) {
            Permission::firstOrCreate(['name' => \$permissionName]);
        }

        // Optionally assign permissions to admin role
        \$adminRole = Role::where('name', 'admin')->first();
        if (\$adminRole) {
            \$adminRole->givePermissionTo(\${$this->lcfirst($moduleName)}Permissions);
        }
    }
}
PHP;

        File::put($seederPath, $content);
        $this->info("✅ Created seeder: {$seederPath}");
    }

    /**
     * Generate permission strings for resources
     */
    private function generatePermissions(array $resources): array
    {
        $permissions = [];
        $actions = ['create', 'read', 'update', 'delete', 'list', 'restore'];

        foreach ($resources as $resource) {
            $resourceName = Str::slug($resource);
            foreach ($actions as $action) {
                $permissions[] = "{$action}-{$resourceName}";
            }
        }

        return $permissions;
    }

    /**
     * Format permissions for code output
     */
    private function formatPermissions(array $permissions): string
    {
        $lines = [];
        foreach ($permissions as $permission) {
            $lines[] = "            '{$permission}',";
        }
        return implode("\n", $lines);
    }

    /**
     * Create documentation for adding permission categories
     */
    private function createCategoryDocumentation(string $moduleName, array $resources): void
    {
        $docPath = "docs/MODULE_PERMISSIONS_{$moduleName}.md";

        if (File::exists($docPath)) {
            return;
        }

        $categoryExamples = $this->generateCategoryExamples($moduleName, $resources);

        $content = <<<MD
# {$moduleName} Module - Permission Setup Guide

## Step 1: Create Permission Seeder ✅
The permission seeder has been created at: `database/seeders/{$moduleName}PermissionsSeeder.php`

Run the seeder:
\`\`\`bash
php artisan db:seed --class={$moduleName}PermissionsSeeder
\`\`\`

## Step 2: Add Permission Categories

Update `database/seeders/PermissionCategorySeeder.php` to add categories for your module's permissions:

{$categoryExamples}

## Step 3: Add Authorization to Controllers

Add authorization checks to your controller methods:

\`\`\`php
// In your controller
public function index(Request \$request)
{
    \$this->authorize('list-resource');
    // ... rest of logic
}

public function store(Request \$request)
{
    \$this->authorize('create-resource');
    // ... rest of logic
}

public function show(Resource \$resource)
{
    \$this->authorize('read-resource');
    // ... rest of logic
}

public function update(Request \$request, Resource \$resource)
{
    \$this->authorize('update-resource');
    // ... rest of logic
}

public function destroy(Resource \$resource)
{
    \$this->authorize('delete-resource');
    // ... rest of logic
}
\`\`\`

## Step 4: (Optional) Create Routes

Add your module routes in `routes/web.php` or a dedicated `routes/{module}.php` file.

## Permission Naming Convention

- **Format**: `{action}-{resource}`
- **Examples**:
  - `list-post` - List all posts
  - `create-post` - Create a new post
  - `read-post` - View a specific post
  - `update-post` - Edit a post
  - `delete-post` - Soft delete a post
  - `restore-post` - Restore a deleted post

## Standard CRUD Actions

| Action | Purpose | Typical Method |
|--------|---------|-----------------|
| list | View all records | index() |
| create | Create new record | store() |
| read | View single record | show() |
| update | Edit a record | update() |
| delete | Soft delete a record | destroy() |
| restore | Restore deleted record | restore() |

## Authorization in Controllers

Always use string-based authorization (not policy-based) for consistency:

\`\`\`php
// ✅ RECOMMENDED
\$this->authorize('permission-name');

// ❌ AVOID (unless complex business logic required)
\$this->authorize('action', \$model);
\`\`\`

## Testing Authorization

Test that permissions work correctly:

\`\`\`php
public function test_user_cannot_access_without_permission()
{
    \$user = User::factory()->create();

    \$this->actingAs(\$user)->get('/resource')
        ->assertForbidden();
}

public function test_user_can_access_with_permission()
{
    \$user = User::factory()->create();
    \$user->givePermissionTo('read-resource');

    \$this->actingAs(\$user)->get('/resource')
        ->assertOk();
}
\`\`\`

## Reference

- [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission/v6/introduction)
- [Authorization Guide](docs/AUTHORIZATION.md)
MD;

        File::put($docPath, $content);
        $this->info("✅ Created documentation: {$docPath}");
    }

    /**
     * Generate category examples
     */
    private function generateCategoryExamples(string $moduleName, array $resources): string
    {
        $color = '#3B82F6'; // Default blue
        $examples = "### Example Category Entries\n\nAdd these to the `\$categories` array in `PermissionCategorySeeder.php`:\n\n\`\`\`php\n";

        $sortOrder = 100;
        foreach ($resources as $resource) {
            $resourceSlug = Str::slug($resource);
            $resourceLabel = Str::title($resource);
            $examples .= <<<PHP
[
    'name' => '{$resourceLabel}',
    'slug' => '{$resourceSlug}',
    'description' => '{$resourceLabel} management permissions',
    'app' => '{$moduleName}',
    'color' => '{$color}',
    'sort_order' => {$sortOrder},
    'rules' => [
        ['rule_type' => 'contains', 'rule_value' => '{$resourceSlug}', 'priority' => 10],
    ]
],

PHP;
            $sortOrder++;
        }

        $examples .= "\`\`\`";

        return $examples;
    }

    /**
     * Convert string to lowercase (lcfirst)
     */
    private function lcfirst(string $str): string
    {
        return lcfirst($str);
    }
}
