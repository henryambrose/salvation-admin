<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Roles, Modules, Permissions } from '@/types';
import { permissionHelpers } from '@/composables/permissionHelpers';

const { can } = permissionHelpers();

interface Props {
  roles: Roles;
  modules: Modules;
  permissions: Permissions;
  modulesIdWise: Record<number, any>;
  permissionGroups: Array<{
    id: number;
    name: string;
    description: string;
    is_default: boolean;
    permissions: Array<{ id: number; slug: string }>;
  }>;
  permissionsByCategory: Record<string, any[]>;
  categories: Array<{
    id: number;
    name: string;
    slug: string;
    color: string;
    sort_order: number;
    description: string;
    app: string;
  }>;
  categoriesByApp: Record<string, any[]>;
}

const props = defineProps<Props>();

// Permission checks
const canManageRoles = can('manage-roles');
const canViewRoles = can('read-role');

const selectedRoleId = ref<number | null>(null);
const selectedGroupId = ref('custom');
const expandedCategories = ref<Set<string>>(new Set([
  'Core Management',
  'Fund Categories',
  'Annual Contributions',
  'Mass Intentions'
]));
const isApplyingGroup = ref(false);
const showSuccessMessage = ref(false);
const successMessage = ref('');

// Store original permissions for reset functionality
const originalPermissions = ref<Permissions>({});

// Local copy of permissions that can be modified
const localPermissions = ref<any>({});

// Initialize original permissions when component mounts
watch(() => props.permissions, (newPermissions) => {
  if (newPermissions && Object.keys(newPermissions).length > 0) {
    // Deep clone the permissions to preserve original state
    originalPermissions.value = JSON.parse(JSON.stringify(newPermissions));
    // Initialize local permissions with the same data
    localPermissions.value = JSON.parse(JSON.stringify(newPermissions));
  }
}, { immediate: true });


// Add Role Modal State
const showAddRoleModal = ref(false);
const newRoleForm = ref({
  name: '',
  description: '',
  is_default: false,
});

// Helper function to get category name safely
function getCategoryName(category: any): string {
  return typeof category === 'object' ? category.name : category;
}

// Helper function to get clean permission display name
function getPermissionDisplayName(permission: any): string {
  // Extract the base permission name from the slug
  // e.g., "create-member" -> "member", "read-community" -> "community"
  const slug = permission.slug || '';
  
  // Remove action prefixes
  const actions = ['create-', 'read-', 'update-', 'delete-', 'list-', 'restore-'];
  let cleanName = slug;
  
  for (const action of actions) {
    if (slug.startsWith(action)) {
      cleanName = slug.replace(action, '');
      break;
    }
  }
  
  // Convert to title case and replace hyphens with spaces
  return cleanName
    .split('-')
    .map((word: string) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}

// Helper function to get base permission name without action prefix
function getPermissionBaseName(permission: any): string {
  const slug = permission.slug || '';
  
  // Remove action prefixes
  const actions = ['create-', 'read-', 'update-', 'delete-', 'list-', 'restore-'];
  let baseName = slug;
  
  for (const action of actions) {
    if (slug.startsWith(action)) {
      baseName = slug.replace(action, '');
      break;
    }
  }
  
  return baseName;
}

// Helper function to get unique models for a category
function getUniqueModelsForCategory(categoryName: string): string[] {
  if (!props.permissionsByCategory || !props.permissionsByCategory[categoryName]) {
    return [];
  }
  
  const models = new Set<string>();
  const permissions = props.permissionsByCategory[categoryName];
  
  permissions.forEach((permission: any) => {
    // Spatie permissions have a 'name' property like "create-member", "read-member"
    const permissionName = permission.name || permission.slug || '';
    const modelName = getPermissionBaseName({ slug: permissionName });
    if (modelName) {
      models.add(modelName);
    }
  });
  
  return Array.from(models).sort();
}

// Helper function to get module for a model
function getModuleForModel(modelName: string): string {
  // Map model names to modules
  const modelToModule: Record<string, string> = {
    // Members module
    'member': 'Members',
    'user': 'Members',
    'family': 'Members',
    'community': 'Members',
    'parish': 'Members',
    'zone': 'Members',
    'cluster': 'Members',
    'age_group': 'Members',
    'blood_group': 'Members',
    'income_range': 'Members',
    'relationship': 'Members',
    'designation': 'Members',
    'gender': 'Members',
    'status': 'Members',
    'city': 'Members',
    'state': 'Members',
    'country': 'Members',
    'town': 'Members',
    'external_member': 'Members',
    'cells_and_association': 'Members',
    'cells_and_association_member': 'Members',
    
    // Fund module
    'fund_category': 'Fund',
    'mass_intention': 'Fund',
    'mass_intention_type': 'Fund',
    'mass_type': 'Fund',
    'payment_method': 'Fund',
    'annual_contribution': 'Fund',
    
    // Graveyard module (for future)
    'grave': 'Graveyard',
    'plot': 'Graveyard',
    'deceased': 'Graveyard',
    'burial': 'Graveyard',
    
    // Default
    'default': 'Core'
  };
  
  return modelToModule[modelName] || 'Core';
}

// Helper function to get module background color
function getModuleBackgroundColor(moduleName: string): string {
  const moduleColors: Record<string, string> = {
    'Members': 'bg-blue-50 border-l-4 border-l-blue-500',
    'Fund': 'bg-green-50 border-l-4 border-l-green-500',
    'Graveyard': 'bg-purple-50 border-l-4 border-l-purple-500',
    'Core': 'bg-gray-50 border-l-4 border-l-gray-500'
  };
  
  return moduleColors[moduleName] || 'bg-gray-50 border-l-4 border-l-gray-500';
}

// Helper function to get module text color
function getModuleTextColor(moduleName: string): string {
  const moduleTextColors: Record<string, string> = {
    'Members': 'text-blue-700',
    'Fund': 'text-green-700',
    'Graveyard': 'text-purple-700',
    'Core': 'text-gray-700'
  };
  
  return moduleTextColors[moduleName] || 'text-gray-700';
}

// Helper function to get module dot color
function getModuleDotColor(moduleName: string): string {
  const moduleDotColors: Record<string, string> = {
    'Members': 'bg-blue-500',
    'Fund': 'bg-green-500',
    'Graveyard': 'bg-purple-500',
    'Core': 'bg-gray-500'
  };
  
  return moduleDotColors[moduleName] || 'bg-gray-500';
}

// Helper function to get unique modules for a category
function getUniqueModulesForCategory(categoryName: string): string[] {
  if (!props.permissionsByCategory || !props.permissionsByCategory[categoryName]) {
    return [];
  }
  
  const modules = new Set<string>();
  const permissions = props.permissionsByCategory[categoryName];
  
  permissions.forEach((permission: any) => {
    const permissionName = permission.name || permission.slug || '';
    const modelName = getPermissionBaseName({ slug: permissionName });
    if (modelName) {
      const moduleName = getModuleForModel(modelName);
      modules.add(moduleName);
    }
  });
  
  return Array.from(modules).sort();
}

// Helper function to get models for a specific module in a category
function getModelsForModuleInCategory(categoryName: string, moduleName: string): string[] {
  if (!props.permissionsByCategory || !props.permissionsByCategory[categoryName]) {
    return [];
  }
  
  const models: string[] = [];
  const permissions = props.permissionsByCategory[categoryName];
  
  permissions.forEach((permission: any) => {
    const permissionName = permission.name || permission.slug || '';
    const modelName = getPermissionBaseName({ slug: permissionName });
    if (modelName && getModuleForModel(modelName) === moduleName) {
      models.push(modelName);
    }
  });
  
  return models.sort();
}

// Helper function to get all Members module models
function getMembersModels(): string[] {
  const allModels = getAllModels();
  return allModels.filter(model => getModuleForModel(model) === 'Members');
}

// Helper function to get all Fund module models
function getFundModels(): string[] {
  const allModels = getAllModels();
  return allModels.filter(model => getModuleForModel(model) === 'Fund');
}

// Helper function to get all Core module models
function getCoreModels(): string[] {
  const allModels = getAllModels();
  return allModels.filter(model => getModuleForModel(model) === 'Core');
}

// Helper function to get all available models from all categories
function getAllModels(): string[] {
  const allModels = new Set<string>();
  
  if (props.permissionsByCategory) {
    Object.values(props.permissionsByCategory).forEach((permissions: any[]) => {
      permissions.forEach((permission: any) => {
        const permissionName = permission.name || permission.slug || '';
        const modelName = getPermissionBaseName({ slug: permissionName });
        if (modelName) {
          allModels.add(modelName);
        }
      });
    });
  }
  
  return Array.from(allModels).sort();
}

// Helper function to format model name for display
function formatModelName(modelName: string): string {
  return modelName
    .split('-')
    .map((word: string) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}

// Get the selected role
const selectedRole = computed(() => {
  if (!selectedRoleId.value) return null;
  return props.roles.find(role => role.id === selectedRoleId.value);
});

// Get category headers for better organization
const getCategoryHeaders = computed(() => {
  if (props.categories && props.categories.length > 0) {
    return props.categories
      .filter(category => {
        return Object.keys(props.permissionsByCategory || {}).some(key => 
          key === category.name
        );
      })
      .sort((a, b) => a.sort_order - b.sort_order);
  }
  
  // Fallback to hardcoded categories
  return [
    'Core Management',
    'Organizational Structure', 
    'Leadership',
    'Member Attributes',
    'Geographic Data',
    'System Management',
    'Fund App Management',
    'Data Management',
    'AI Assistance',
    'Dashboard'
  ];
});

// Get categories grouped by app
const getCategoriesByApp = computed(() => {
  if (props.categoriesByApp) {
    return props.categoriesByApp;
  }
  
  // Fallback grouping
  return {
    'Members': props.categories?.filter(cat => 
      ['Core Management', 'Organizational Structure', 'Leadership', 'Member Attributes', 'Geographic Data', 'System Management', 'Data Management', 'AI Assistance', 'Dashboard'].includes(cat.name)
    ) || [],
    'Fund': props.categories?.filter(cat => 
      ['Fund Categories', 'Annual Contributions', 'Mass Intentions', 'Mass Types', 'Mass Intention Types', 'Payment Methods'].includes(cat.name)
    ) || [],
    'Core': props.categories?.filter(cat => 
      ['System Management', 'Data Management', 'AI Assistance', 'Dashboard'].includes(cat.name)
    ) || [],
  };
});

// Get all unique actions from permissions
const allActions = computed(() => {
  const actions = new Set<string>();
  
  if (props.permissionsByCategory) {
    Object.values(props.permissionsByCategory).forEach(categoryPermissions => {
      categoryPermissions.forEach(permission => {
        if (permission.action) {
          actions.add(permission.action);
        }
      });
    });
  }
  
  // Fallback to common actions
  if (actions.size === 0) {
    ['create', 'read', 'update', 'delete', 'list', 'restore'].forEach(action => actions.add(action));
  }
  
  return Array.from(actions).sort();
});

// Check if a role has a specific permission
function hasPermission(roleId: number, permissionSlug: string): boolean {
  const role = props.roles.find((r) => r.id === roleId);
  if (!role) return false;

  // Check if the role has this permission based on the local permissions state
  for (const module of props.modules) {
    for (const action of module.actions) {
      if (action.slug === permissionSlug) {
        // Check if this role has this permission
        const rolePermissions = localPermissions.value[roleId];
        if (rolePermissions && rolePermissions[module.id]) {
          return rolePermissions[module.id][action.id] === 1;
        }
        return false;
      }
    }
  }

  return false;
}

// Toggle permission for a role
function togglePermission(roleId: number, permissionSlug: string) {
  // Find the module and action for this permission
  for (const module of props.modules) {
    for (const action of module.actions) {
      if (action.slug === permissionSlug) {
        // Toggle the permission state in local permissions
        if (!localPermissions.value[roleId]) {
          localPermissions.value[roleId] = {};
        }
        if (!localPermissions.value[roleId][module.id]) {
          localPermissions.value[roleId][module.id] = {};
        }

        const currentValue = localPermissions.value[roleId][module.id][action.id] || 0;
        localPermissions.value[roleId][module.id][action.id] = currentValue === 1 ? 0 : 1;
        
        return;
      }
    }
  }
}

// Toggle category expansion
function toggleCategory(categoryName: string) {
  if (expandedCategories.value.has(categoryName)) {
    expandedCategories.value.delete(categoryName);
  } else {
    expandedCategories.value.add(categoryName);
  }
}

// Expand all categories
function expandAllCategories() {
  if (props.categories) {
    props.categories.forEach(category => {
      expandedCategories.value.add(category.name);
    });
  }
}

// Collapse all categories
function collapseAllCategories() {
  expandedCategories.value.clear();
}

// Apply permission group to selected role
async function applyPermissionGroup(groupId: string) {
  if (groupId === 'custom' || !selectedRoleId.value) {
    return;
  }

  isApplyingGroup.value = true;
  
  try {
    const response = await router.post('/roles-permissions/apply-group', {
      role_id: selectedRoleId.value,
      group_id: groupId
    }, {
      preserveState: true,
      onSuccess: () => {
        // Reload the page to get updated permissions
        router.reload();
      },
      onError: (errors) => {
        console.error('Error applying permission group:', errors);
        alert('Failed to apply permission group. Please try again.');
      }
    });
  } catch (error) {
    console.error('Error applying permission group:', error);
        alert('Failed to apply permission group. Please try again.');
  } finally {
    isApplyingGroup.value = false;
  }
}

// Save permissions
async function savePermissions() {
  if (!selectedRoleId.value) {
    alert('Please select a role first');
    return;
  }

  // Check if there are any changes to save
  const hasChanges = JSON.stringify(localPermissions.value[selectedRoleId.value]) !== 
                     JSON.stringify(originalPermissions.value[selectedRoleId.value]);
  
  if (!hasChanges) {
    alert('No changes to save');
    return;
  }

  try {
    // Show loading state
    const saveButton = document.querySelector('[data-save-permissions]') as HTMLButtonElement;
    if (saveButton) {
      saveButton.disabled = true;
      saveButton.innerHTML = `
        <svg class="w-[1rem] h-[1rem] mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Saving...
      `;
    }

    // Send the updated permissions to the backend using fetch (not Inertia)
    const response = await fetch('/roles-permissions/update-permissions', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        role_id: selectedRoleId.value,
        permissions: localPermissions.value[selectedRoleId.value]
      })
    });

    if (response.ok) {
      const result = await response.json();
      
      // Update original permissions to match the new state
      if (selectedRoleId.value) {
        originalPermissions.value[selectedRoleId.value] = JSON.parse(JSON.stringify(localPermissions.value[selectedRoleId.value]));
      }
      
      // Show success message
      successMessage.value = 'Permissions saved successfully!';
      showSuccessMessage.value = true;
      
      // Hide success message after 3 seconds
      setTimeout(() => {
        showSuccessMessage.value = false;
      }, 3000);
      
      // Reload the page to get the latest data
      router.reload();
    } else {
      const errorData = await response.json();
      console.error('Error saving permissions:', errorData);
      alert('Failed to save permissions. Please try again.');
    }
  } catch (error) {
    console.error('Error saving permissions:', error);
    alert('Failed to save permissions. Please try again.');
  } finally {
    // Reset button state
    const saveButton = document.querySelector('[data-save-permissions]') as HTMLButtonElement;
    if (saveButton) {
      saveButton.disabled = false;
      saveButton.innerHTML = `
        <svg class="w-[1rem] h-[1rem] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        Save Permissions
      `;
    }
  }
}

// Reset permissions to original state
function resetPermissions() {
  if (confirm('Are you sure you want to reset all changes? This will restore the original permission state.')) {
    // Restore original permissions to local permissions
    localPermissions.value = JSON.parse(JSON.stringify(originalPermissions.value));
  }
}

// Add Role Functions
function openAddRoleModal() {
  showAddRoleModal.value = true;
  newRoleForm.value = {
    name: '',
    description: '',
    is_default: false,
  };
}

function closeAddRoleModal() {
  showAddRoleModal.value = false;
  newRoleForm.value = {
    name: '',
    description: '',
    is_default: false,
  };
}

function createRole() {
  if (!newRoleForm.value.name.trim()) {
    alert('Role name is required');
    return;
  }

  router.post(
    '/roles-permissions/create-role',
    {
      name: newRoleForm.value.name,
    },
    {
      onSuccess: () => {
        closeAddRoleModal();
        router.reload();
      },
      onError: (errors) => {
        if (errors.name) {
          alert(errors.name);
        } else {
          alert('Failed to create role. Please try again.');
        }
      },
    },
  );
}
</script>

<template>
  <AppLayout>
    <Head title="Role Permissions" />
    <div v-if="canViewRoles" class="mx-auto max-w-7xl py-6 px-4">
      <!-- Header Section -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Role Permissions</h1>
          <p class="mt-2 text-gray-600">Manage permissions for different user roles</p>
        </div>
        <div class="flex items-center gap-3">
          <Button
            v-if="canManageRoles"
            @click="openAddRoleModal"
            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700"
          >
            <svg class="h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Role
          </Button>
        </div>
      </div>

      <!-- Success Message -->
      <div v-if="showSuccessMessage" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center justify-between">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <span class="font-medium">{{ successMessage }}</span>
        </div>
        <button @click="showSuccessMessage = false" class="text-green-500 hover:text-green-700">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Role Selection and Quick Actions -->
      <div class="mb-6 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border border-blue-100">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Role Selection -->
          <div class="lg:col-span-1">
            <label class="block text-sm font-medium text-blue-900 mb-2">Select Role to Manage</label>
            <select 
              v-model="selectedRoleId" 
              class="w-full rounded-lg border border-blue-200 px-4 py-3 bg-[#ffffff] text-blue-900 focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
            >
              <option :value="null">Choose a role...</option>
              <option v-for="role in props.roles" :key="role.id" :value="role.id">
                {{ (role as { id: number; name: string }).name }}
              </option>
            </select>
          </div>
          
          <!-- Quick Permission Groups -->
          <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-blue-900 mb-2">Quick Permission Groups</label>
            <div class="flex items-center gap-3">
              <select 
                v-model="selectedGroupId" 
                class="flex-1 rounded-lg border border-blue-200 px-4 py-3 bg-[#ffffff] text-blue-900 focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
              >
                <option value="custom">Custom Selection</option>
                <option v-for="group in props.permissionGroups" :key="group.id" :value="group.id.toString()">
                  {{ group.name }}
                </option>
              </select>
              
              <Button
                @click="applyPermissionGroup(selectedGroupId)"
                :disabled="selectedGroupId === 'custom' || !selectedRoleId"
                class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 px-6 py-3"
              >
                <svg v-if="!isApplyingGroup" class="w-[1rem] h-[1rem] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <svg v-else class="w-[1rem] h-[1rem] mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                {{ isApplyingGroup ? 'Applying...' : 'Apply Group' }}
              </Button>
            </div>
          </div>
        </div>
        
        <!-- Info Text -->
        <div class="mt-4 p-3 bg-blue-100 rounded-lg">
          <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-blue-800">
              <strong>How it works:</strong> Select a role first, then choose a permission group (like "Read Only" or "Administrator") and click "Apply Group" to automatically assign all permissions for that group to the selected role. This will override any existing permissions for that role.
            </div>
          </div>
        </div>
      </div>

      <!-- Role Title and Permissions Matrix -->
      <div v-if="selectedRole" class="space-y-6">
        <!-- Role Title -->
        <div class="bg-[#ffffff] rounded-xl border border-gray-200 p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold text-gray-900">Role: {{ selectedRole.name }}</h2>
              <p class="text-gray-600 mt-1">Manage permissions for this role</p>
            </div>
            <div class="flex gap-3">
              <Button variant="outline" @click="resetPermissions">
                Reset Changes
              </Button>
              <Button
                v-if="canManageRoles"
                @click="savePermissions"
                data-save-permissions
                class="bg-blue-600 hover:bg-blue-700"
              >
                <svg class="w-[1rem] h-[1rem] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Permissions
              </Button>
            </div>
          </div>
        </div>

        <!-- Permission Matrix -->
        <div class="bg-[#ffffff] rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <!-- App Legend and Controls -->
          <div class="bg-gray-50 border-b border-gray-200 p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-6 text-sm">
                <span class="font-medium text-gray-700">Application Groups:</span>
                <div class="flex items-center gap-2">
                  <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                  <span class="text-blue-700">Members</span>
                </div>
                <div class="flex items-center gap-2">
                  <div class="w-3 h-3 rounded-full bg-green-500"></div>
                  <span class="text-green-700">Fund</span>
                </div>
                <div class="flex items-center gap-2">
                  <div class="w-3 h-3 rounded-full bg-gray-500"></div>
                  <span class="text-gray-700">Core</span>
                </div>
              </div>
              <div class="flex gap-2">
                <Button variant="outline" size="sm" @click="expandAllCategories">
                  Expand All
                </Button>
                <Button variant="outline" size="sm" @click="collapseAllCategories">
                  Collapse All
                </Button>
              </div>
            </div>
          </div>

          <!-- Table Header -->
          <div class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
            <div class="grid gap-4 p-4" style="grid-template-columns: 300px repeat(6, 1fr);">
              <div class="font-semibold text-gray-900">Permission</div>
              <div class="text-center font-semibold text-gray-900">Create</div>
              <div class="text-center font-semibold text-gray-900">Update</div>
              <div class="text-center font-semibold text-gray-900">Delete</div>
              <div class="text-center font-semibold text-gray-900">List</div>
              <div class="text-center font-semibold text-gray-900">Read</div>
              <div class="text-center font-semibold text-gray-900">Restore</div>
            </div>
          </div>

          <!-- Permission Categories -->
          <div class="divide-y divide-gray-100">
            <div 
              v-for="(categoryGroup, appName) in getCategoriesByApp" 
              :key="appName"
              class="bg-[#ffffff]"
            >
              <!-- App Header -->
              <div class="p-4 border-b border-gray-200" :class="{
                'bg-blue-50': appName === 'Members',
                'bg-green-50': appName === 'Fund',
                'bg-gray-50': appName === 'Core'
              }">
                <h3 class="text-lg font-semibold" :class="{
                  'text-blue-900': appName === 'Members',
                  'text-green-900': appName === 'Fund',
                  'text-gray-900': appName === 'Core'
                }">{{ appName }}</h3>
              </div>

              <!-- Categories within this app -->
              <div 
                v-for="category in categoryGroup" 
                :key="category.id"
                class="border-b border-gray-100 last:border-b-0"
              >
                <!-- Category Header -->
                <div 
                  @click="toggleCategory(category.name)"
                  class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 transition-colors"
                >
                  <div class="flex items-center gap-3">
                    <div 
                      class="w-3 h-3 rounded-full"
                      :style="{ backgroundColor: category.color }"
                    ></div>
                    <h4 class="text-md font-semibold text-gray-900">
                      {{ category.name }}
                    </h4>
                    <div class="text-xs text-gray-500">
                      {{ category.description }}
                    </div>
                    <span class="text-sm text-gray-500">
                      ({{ (props.permissionsByCategory && props.permissionsByCategory[category.name]) ? props.permissionsByCategory[category.name].length : 0 }} permissions)
                    </span>
                  </div>
                  <svg 
                    class="w-5 h-5 text-gray-400 transition-transform"
                    :class="{ 'rotate-180': expandedCategories.has(category.name) }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>

                <!-- Category Permissions (Collapsible) -->
                <div 
                  v-if="expandedCategories.has(category.name)"
                  class="border-t border-gray-100 bg-gray-50"
                >
                  <div 
                    v-if="getUniqueModelsForCategory(category.name).length === 0"
                    class="p-4 text-center text-gray-500"
                  >
                    No permissions found for this category. This might be due to:
                    <ul class="mt-2 text-sm text-left list-disc list-inside">
                      <li>Permissions not yet generated for this category</li>
                      <li>Category rules not matching existing permissions</li>
                      <li>Database permissions not synced with categories</li>
                    </ul>
                  </div>
                  <div 
                    v-else
                    v-for="modelName in getUniqueModelsForCategory(category.name)" 
                    :key="modelName"
                    class="grid gap-4 p-3 hover:bg-[#ffffff] transition-colors border-b border-gray-100 last:border-b-0"
                    style="grid-template-columns: 300px repeat(6, 1fr);"
                  >
                    <!-- Model Name -->
                    <div class="flex items-center gap-3">
                      <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                      <div>
                        <div class="font-medium text-gray-900 text-sm capitalize">
                          {{ formatModelName(modelName) }}
                        </div>
                        <div class="text-xs text-gray-500 font-mono">
                          {{ modelName }}
                        </div>
                      </div>
                    </div>
                    
                    <!-- Action Checkboxes -->
                    <div class="flex justify-center">
                      <input
                        type="checkbox"
                        :checked="selectedRoleId ? hasPermission(selectedRoleId, `create-${modelName}`) : false"
                        @change="selectedRoleId && togglePermission(selectedRoleId, `create-${modelName}`)"
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6] cursor-pointer"
                      />
                    </div>
                    <div class="flex justify-center">
                      <input
                        type="checkbox"
                        :checked="selectedRoleId ? hasPermission(selectedRoleId, `update-${modelName}`) : false"
                        @change="selectedRoleId && togglePermission(selectedRoleId, `update-${modelName}`)"
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6] cursor-pointer"
                      />
                    </div>
                    <div class="flex justify-center">
                      <input
                        type="checkbox"
                        :checked="selectedRoleId ? hasPermission(selectedRoleId, `delete-${modelName}`) : false"
                        @change="selectedRoleId && togglePermission(selectedRoleId, `delete-${modelName}`)"
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6] cursor-pointer"
                      />
                    </div>
                    <div class="flex justify-center">
                      <input
                        type="checkbox"
                        :checked="selectedRoleId ? hasPermission(selectedRoleId, `list-${modelName}`) : false"
                        @change="selectedRoleId && togglePermission(selectedRoleId, `list-${modelName}`)"
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6] cursor-pointer"
                      />
                    </div>
                    <div class="flex justify-center">
                      <input
                        type="checkbox"
                        :checked="selectedRoleId ? hasPermission(selectedRoleId, `read-${modelName}`) : false"
                        @change="selectedRoleId && togglePermission(selectedRoleId, `read-${modelName}`)"
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6] cursor-pointer"
                      />
                    </div>
                    <div class="flex justify-center">
                      <input
                        type="checkbox"
                        :checked="selectedRoleId ? hasPermission(selectedRoleId, `restore-${modelName}`) : false"
                        @change="selectedRoleId && togglePermission(selectedRoleId, `restore-${modelName}`)"
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6] cursor-pointer"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- No Role Selected Message -->
      <div v-else class="text-center py-12">
        <div class="max-w-md mx-auto">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No role selected</h3>
          <p class="mt-1 text-sm text-gray-500">Select a role from the dropdown above to manage its permissions.</p>
        </div>
      </div>
    </div>
    
    <div v-else class="py-10 text-center text-gray-500">
      You do not have permission to view role permissions.
    </div>

    <!-- Add Role Modal -->
    <div v-if="showAddRoleModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-[#ffffff] rounded-xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Add New Role</h3>
          <button @click="closeAddRoleModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-[1.5rem] h-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
            <input
              v-model="newRoleForm.name"
              type="text"
              required
              placeholder="Enter role name"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
            />
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <Button @click="closeAddRoleModal" variant="outline">Cancel</Button>
          <Button @click="createRole" class="bg-green-600 hover:bg-green-700">Create Role</Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>


