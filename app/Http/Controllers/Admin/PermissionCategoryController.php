<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionCategory;
use App\Models\PermissionCategoryRule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PermissionCategoryController extends Controller
{
    public function index(): Response
    {
        $categories = PermissionCategory::with('rules')
            ->ordered()
            ->get();

        return Inertia::render('Admin/PermissionCategories/Index', compact('categories'));
    }

    public function create(): Response
    {
        return Inertia::render('Admin/PermissionCategories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permission_categories',
            'slug' => 'required|string|max:255|unique:permission_categories',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'sort_order' => 'required|integer|min:0',
            'rules' => 'required|array|min:1',
            'rules.*.rule_type' => 'required|in:contains,starts_with,ends_with,regex',
            'rules.*.rule_value' => 'required|string|max:255',
            'rules.*.priority' => 'required|integer|min:0',
        ]);

        $category = PermissionCategory::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'color' => $validated['color'],
            'sort_order' => $validated['sort_order'],
        ]);
        
        foreach ($validated['rules'] as $ruleData) {
            $category->rules()->create($ruleData);
        }

        return redirect()->route('admin.permission-categories.index')
            ->with('success', 'Permission category created successfully');
    }

    public function edit(PermissionCategory $category): Response
    {
        $category->load('rules');
        return Inertia::render('Admin/PermissionCategories/Edit', compact('category'));
    }

    public function update(Request $request, PermissionCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permission_categories,name,' . $category->id,
            'slug' => 'required|string|max:255|unique:permission_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'sort_order' => 'required|integer|min:0',
            'rules' => 'required|array|min:1',
            'rules.*.rule_type' => 'required|in:contains,starts_with,ends_with,regex',
            'rules.*.rule_value' => 'required|string|max:255',
            'rules.*.priority' => 'required|integer|min:0',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'color' => $validated['color'],
            'sort_order' => $validated['sort_order'],
        ]);

        // Delete existing rules and create new ones
        $category->rules()->delete();
        
        foreach ($validated['rules'] as $ruleData) {
            $category->rules()->create($ruleData);
        }

        return redirect()->route('admin.permission-categories.index')
            ->with('success', 'Permission category updated successfully');
    }

    public function destroy(PermissionCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.permission-categories.index')
            ->with('success', 'Permission category deleted successfully');
    }

    public function toggleActive(PermissionCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Permission category {$status} successfully");
    }
}



