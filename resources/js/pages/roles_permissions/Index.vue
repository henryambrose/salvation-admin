<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, computed, nextTick } from 'vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Roles, Modules, Permissions } from '@/types';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';
import { useSessionKeepAlive } from '@/composables/useSessionKeepAlive';
import { useFormPersistence } from '@/composables/useFormPersistence';
import SessionWarningDialog from '@/components/SessionWarningDialog.vue';

const { can } = permissionHelpers();
const { success, error, info } = useToast();

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
const expandedCategories = ref<Set<string>>(new Set(['Core Management', 'Fund Categories', 'Annual Contributions', 'Mass Intentions']));
const isApplyingGroup = ref(false);
const showSuccessMessage = ref(false);
const successMessage = ref('');
const searchQuery = ref('');

// Store original permissions for reset functionality
const originalPermissions = ref<Permissions>({});

// Local copy of permissions that can be modified
const localPermissions = ref<any>({});

// Initialize original permissions when component mounts
watch(
  () => props.permissions,
  (newPermissions) => {
    if (newPermissions && Object.keys(newPermissions).length > 0) {
      // Deep clone the permissions to preserve original state
      originalPermissions.value = JSON.parse(JSON.stringify(newPermissions));
      // Initialize local permissions with the same data
      localPermissions.value = JSON.parse(JSON.stringify(newPermissions));
    }
  },
  { immediate: true },
);

// Add Role Modal State
const showAddRoleModal = ref(false);
const newRoleForm = ref({
  name: '',
  description: '',
  is_default: false,
});

// Session management
const showSessionWarning = ref(false);
const sessionMinutesRemaining = ref(5);

// Session keep-alive to prevent timeout while user is working
const { refresh: refreshSession } = useSessionKeepAlive({
  enabled: true,
  intervalMinutes: 2, // Ping every 2 minutes
  warningMinutes: 5,  // Warn 5 minutes before expiry
  onWarning: () => {
    showSessionWarning.value = true;
    sessionMinutesRemaining.value = 5;
  },
  onExpired: () => {
    error('Your session has expired. The page will reload.');
    setTimeout(() => window.location.reload(), 2000);
  },
});

// Form persistence to localStorage (auto-saves permission changes)
const { clear: clearSavedForm, hasRestoredData } = useFormPersistence(
  localPermissions,
  'role-permissions-form',
  {
    enabled: true,
    debounceMs: 2000, // Save 2 seconds after last change
    onRestore: (data) => {
      // Only show restore message if data was actually restored (not on initial load after save)
      if (!sessionStorage.getItem('__permissions_just_saved')) {
        info('Restored your unsaved permission changes');
      }
      sessionStorage.removeItem('__permissions_just_saved');
    },
  }
);

// Handle session warning actions
function handleContinueWorking() {
  showSessionWarning.value = false;
  refreshSession();
  success('Session refreshed! You can continue working.');
}

function handleCloseWarning() {
  showSessionWarning.value = false;
}

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
  const slug = permission.slug || permission.name || '';
  return splitActionModel(slug).model; // normalized, hyphens only
}

// Helper function to get unique models for a category
function getUniqueModelsForCategory(categoryName: string): string[] {
  const list = props.permissionsByCategory?.[categoryName] || [];
  const models = new Set<string>();
  list.forEach((permission: any) => {
    const base = getPermissionBaseName(permission);
    if (base) models.add(base);
  });
  return Array.from(models).sort();
}

// Helper function to get module for a model
function getModuleForModel(modelName: string): string {
  const key = norm(modelName); // hyphens only
  const modelToModule: Record<string, string> = {
    // Members
    member: 'Members',
    'external-member': 'Members', // add this (hyphen form)
    user: 'Members',
    family: 'Members',
    community: 'Members',
    parish: 'Members',
    zone: 'Members',
    cluster: 'Members',
    'age-group': 'Members',
    'blood-group': 'Members',
    'income-range': 'Members',
    relationship: 'Members',
    designation: 'Members',
    gender: 'Members',
    status: 'Members',
    city: 'Members',
    state: 'Members',
    country: 'Members',
    town: 'Members',
    'cells-and-association': 'Members',
    'cells-and-association-member': 'Members',

    // Certificate Management
    certificate: 'Members',
    'certificate-type': 'Members',
    'certificate-template': 'Members',

    // Fund
    'fund-category': 'Fund',
    'mass-intention': 'Fund',
    'mass-intention-type': 'Fund',
    'mass-type': 'Fund',
    'payment-method': 'Fund',
    'annual-contribution': 'Fund',
    'community-contribution': 'Fund',
    'community-contribution-type': 'Fund',

    // Graveyard
    grave: 'Graveyard',
    plot: 'Graveyard',
    deceased: 'Graveyard',
    burial: 'Graveyard',
    niche: 'Graveyard',
    'permanent-grave': 'Graveyard',
    'temporary-grave': 'Graveyard',
  };
  return modelToModule[key] || 'Core';
}

// Helper function to get module background color
function getModuleBackgroundColor(moduleName: string): string {
  const moduleColors: Record<string, string> = {
    Members: 'bg-blue-50 border-l-4 border-l-blue-500',
    Fund: 'bg-green-50 border-l-4 border-l-green-500',
    Graveyard: 'bg-purple-50 border-l-4 border-l-purple-500',
    Core: 'bg-gray-50 border-l-4 border-l-gray-500',
  };

  return moduleColors[moduleName] || 'bg-gray-50 border-l-4 border-l-gray-500';
}

// Helper function to get module text color
function getModuleTextColor(moduleName: string): string {
  const moduleTextColors: Record<string, string> = {
    Members: 'text-blue-700',
    Fund: 'text-green-700',
    Graveyard: 'text-purple-700',
    Core: 'text-gray-700',
  };

  return moduleTextColors[moduleName] || 'text-gray-700';
}

// Helper function to get module dot color
function getModuleDotColor(moduleName: string): string {
  const moduleDotColors: Record<string, string> = {
    Members: 'bg-blue-500',
    Fund: 'bg-green-500',
    Graveyard: 'bg-purple-500',
    Core: 'bg-gray-500',
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
  return allModels.filter((model) => getModuleForModel(model) === 'Members');
}

// Helper function to get all Fund module models
function getFundModels(): string[] {
  const allModels = getAllModels();
  return allModels.filter((model) => getModuleForModel(model) === 'Fund');
}

// Helper function to get all Core module models
function getCoreModels(): string[] {
  const allModels = getAllModels();
  return allModels.filter((model) => getModuleForModel(model) === 'Core');
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
  return props.roles.find((role) => role.id === selectedRoleId.value);
});

// Get category headers for better organization
const getCategoryHeaders = computed(() => {
  if (props.categories && props.categories.length > 0) {
    return props.categories
      .filter((category) => {
        return Object.keys(props.permissionsByCategory || {}).some((key) => key === category.name);
      })
      .sort((a, b) => a.sort_order - b.sort_order);
  }

  // Fallback to hardcoded categories
  return [
    'Core Management',
    'Organizational Structure',
    'Certificate Management',
    'Leadership',
    'Member Attributes',
    'Geographic Data',
    'System Management',
    'Fund App Management',
    'Data Management',
    'AI Assistance',
    'Dashboard',
  ];
});

// Get categories grouped by app
const getCategoriesByApp = computed(() => {
  if (props.categoriesByApp) {
    return props.categoriesByApp;
  }

  // Fallback grouping
  return {
    Members:
      props.categories?.filter((cat) =>
        [
          'Core Management',
          'Organizational Structure',
          'Certificate Management',
          'Leadership',
          'Member Attributes',
          'Geographic Data',
          'System Management',
          'Data Management',
          'AI Assistance',
          'Dashboard',
        ].includes(cat.name),
      ) || [],
    Fund:
      props.categories?.filter((cat) =>
        ['Fund Management', 'Fund Categories', 'Annual Contributions', 'Community Contributions', 'Community Contributions Type', 'Mass Intentions', 'Mass Types', 'Mass Intention Types', 'Payment Methods', 'Mass Schedules'].includes(cat.name),
      ) || [],
    Graveyard: props.categories?.filter((cat) => ['Graveyard Management'].includes(cat.name)) || [],
    Core: props.categories?.filter((cat) => ['System Management', 'Data Management', 'AI Assistance', 'Dashboard'].includes(cat.name)) || [],
  };
});

// Get all unique actions from permissions
const allActions = computed(() => {
  const actions = new Set<string>();

  if (props.permissionsByCategory) {
    Object.values(props.permissionsByCategory).forEach((categoryPermissions) => {
      categoryPermissions.forEach((permission) => {
        if (permission.action) {
          actions.add(permission.action);
        }
      });
    });
  }

  // Fallback to common actions
  if (actions.size === 0) {
    ['create', 'read', 'update', 'delete', 'list', 'restore'].forEach((action) => actions.add(action));
  }

  return Array.from(actions).sort();
});

// Filter permissions by search query
const filteredPermissionsByCategory = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.permissionsByCategory || {};
  }

  const query = searchQuery.value.toLowerCase().trim();
  const filtered: Record<string, any[]> = {};

  if (!props.permissionsByCategory) return filtered;

  Object.entries(props.permissionsByCategory).forEach(([category, permissions]) => {
    const matchedPermissions = (permissions as any[]).filter((permission) => {
      const name = (permission.name || permission.slug || '').toLowerCase();
      const desc = (permission.description || '').toLowerCase();
      return name.includes(query) || desc.includes(query);
    });

    if (matchedPermissions.length > 0) {
      filtered[category] = matchedPermissions;
    }
  });

  return filtered;
});

// Check if a role has a specific permission
function hasPermission(roleId: number, permissionSlug: string): boolean {
  const role = props.roles.find((r) => r.id === roleId);
  if (!role) return false;

  // First, check in modules/actions (old system)
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

  // If not found in modules, check Spatie permissions
  if (props.permissionsByCategory) {
    for (const categoryPermissions of Object.values(props.permissionsByCategory)) {
      for (const permission of categoryPermissions as any[]) {
        if (permission.name === permissionSlug || permission.slug === permissionSlug) {
          const rolePermissions = localPermissions.value[roleId];
          if (rolePermissions && rolePermissions['spatie'] && rolePermissions['spatie'][permission.id]) {
            return rolePermissions['spatie'][permission.id] === 1;
          }
          return false;
        }
      }
    }
  }

  return false;
}

// Toggle permission for a role
function togglePermission(roleId: number, permissionSlug: string) {
  // First, try to find in modules/actions (old system)
  for (const module of props.modules) {
    for (const action of module.actions) {
      if (action.slug === permissionSlug) {
        // Ensure the role exists in local permissions
        if (!localPermissions.value[roleId]) {
          localPermissions.value[roleId] = {};
        }
        if (!localPermissions.value[roleId][module.id]) {
          localPermissions.value[roleId][module.id] = {};
        }

        const currentValue = localPermissions.value[roleId][module.id][action.id] || 0;
        const newValue = currentValue === 1 ? 0 : 1;

        // Update the permission value
        localPermissions.value[roleId][module.id][action.id] = newValue;

        // Force reactivity by creating a new object reference
        localPermissions.value = { ...localPermissions.value };

        return;
      }
    }
  }

  // If not found in modules, check if it's a Spatie permission from permissionsByCategory
  if (props.permissionsByCategory) {
    for (const categoryPermissions of Object.values(props.permissionsByCategory)) {
      for (const permission of categoryPermissions as any[]) {
        if (permission.name === permissionSlug || permission.slug === permissionSlug) {
          // Store Spatie permissions separately
          if (!localPermissions.value[roleId]) {
            localPermissions.value[roleId] = {};
          }

          // Use a special key for Spatie-only permissions (key 'spatie')
          if (!localPermissions.value[roleId]['spatie']) {
            localPermissions.value[roleId]['spatie'] = {};
          }

          const currentValue = localPermissions.value[roleId]['spatie'][permission.id] || 0;
          const newValue = currentValue === 1 ? 0 : 1;

          localPermissions.value[roleId]['spatie'][permission.id] = newValue;

          // Force reactivity
          localPermissions.value = { ...localPermissions.value };

          return;
        }
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
    props.categories.forEach((category) => {
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
    const response = await router.post(
      '/roles-permissions/apply-group',
      {
        role_id: selectedRoleId.value,
        group_id: groupId,
      },
      {
        preserveState: true,
        onSuccess: () => {
          success('Permission group applied successfully!');
          // Reload the page to get updated permissions
          router.reload();
        },
        onError: (errors) => {
          console.error('Error applying permission group:', errors);
          error('Failed to apply permission group. Please try again.');
        },
      },
    );
  } catch (err) {
    console.error('Error applying permission group:', err);
    error('Failed to apply permission group. Please try again.');
  } finally {
    isApplyingGroup.value = false;
  }
}

// Helper function to get fresh CSRF token
async function getCsrfToken(): Promise<string> {
  try {
    // Refresh the CSRF token
    await fetch('/csrf-cookie', {
      method: 'GET',
      credentials: 'same-origin',
    });

    // Get the token from the meta tag
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    return token;
  } catch {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  }
}

// Save permissions
async function savePermissions() {
  if (!selectedRoleId.value) {
    error('Please select a role first');
    return;
  }

  // Check if there are any changes to save
  const localPerms = localPermissions.value[selectedRoleId.value];
  const originalPerms = originalPermissions.value[selectedRoleId.value];

  // Convert Proxy objects to plain objects for proper comparison
  const localPermsPlain = JSON.parse(JSON.stringify(localPerms || {}));
  const originalPermsPlain = JSON.parse(JSON.stringify(originalPerms || {}));

  const hasChanges = JSON.stringify(localPermsPlain) !== JSON.stringify(originalPermsPlain);

  if (!hasChanges) {
    error('No changes to save');
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

    // Get fresh CSRF token before making the request
    const csrfToken = await getCsrfToken();

    // Send the updated permissions to the backend using fetch (not Inertia)
    const response = await fetch('/roles-permissions/update-permissions', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        Accept: 'application/json',
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        role_id: selectedRoleId.value,
        permissions: localPermissions.value[selectedRoleId.value],
      }),
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

      success('Permissions saved successfully!');

      // Clear saved form data from localStorage since we successfully saved
      clearSavedForm();

      // Set flag to prevent restore dialog on reload
      sessionStorage.setItem('__permissions_just_saved', 'true');

      // Reload the page to get the latest data
      router.reload();
    } else if (response.status === 419) {
      // CSRF token mismatch - session expired
      error('Your session has expired. Please refresh the page and try again.');

      // Optionally reload the page after a short delay
      setTimeout(() => {
        window.location.reload();
      }, 2000);
    } else {
      const errorData = await response.json().catch(() => ({ message: 'Unknown error occurred' }));
      error(errorData.message || 'Failed to save permissions. Please try again.');
    }
  } catch {
    error('Failed to save permissions. Network error occurred.');
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
  const { confirm: showConfirm } = useConfirm();
  showConfirm({
    title: 'Reset Permissions',
    message: 'Are you sure you want to reset all changes? This will restore the original permission state.',
    confirmText: 'Reset',
    cancelText: 'Cancel',
    type: 'warning',
    onConfirm: () => {
      // Restore original permissions to local permissions
      localPermissions.value = JSON.parse(JSON.stringify(originalPermissions.value));
      info('Permissions reset to original state');
    },
  });
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
    error('Role name is required');
    return;
  }

  router.post(
    '/roles-permissions/create-role',
    {
      name: newRoleForm.value.name,
    },
    {
      onSuccess: () => {
        success('Role created successfully!');
        closeAddRoleModal();
        router.reload();
      },
      onError: (errors) => {
        if (errors.name) {
          error(errors.name);
        } else {
          error('Failed to create role. Please try again.');
        }
      },
    },
  );
}

// Focus first input when add role modal opens
watch(showAddRoleModal, (isOpen) => {
  if (isOpen) {
    nextTick(() => {
      const input = document.querySelector('[data-create-input]') as HTMLElement;
      if (input) input.focus();
    });
  }
});

const standardActions = ['create', 'read', 'update', 'delete', 'list', 'restore'];
const specialActions = ['publish', 'unpublish', 'approve', 'reject', 'generate', 'download', 'manage', 'preview', 'reprint', 'view']; // special non-CRUD actions
const knownActions = [...standardActions, ...specialActions];
const norm = (s: string) => s.toLowerCase().trim().replace(/_/g, '-');
function splitActionModel(rawSlug: string): { action: string | null; model: string } {
  const slug = norm(rawSlug);
  const m = slug.match(/^([a-z_]+)-(.*)$/i);
  if (!m) return { action: null, model: slug };

  const first = m[1]; // candidate action
  const rest = m[2]; // candidate model

  // Only accept it as an action if it’s known; otherwise the *whole* thing is a model
  if (knownActions.includes(first)) {
    return { action: first, model: norm(rest) };
  }
  return { action: null, model: slug }; // e.g. "external-member" stays as a model
}
// Helper function to get special permissions for a model
function getSpecialPermissionsForModel(categoryName: string, modelName: string): string[] {
  const list = props.permissionsByCategory?.[categoryName] || [];
  const target = norm(modelName);
  const specials: string[] = [];

  list.forEach((permission: any) => {
    const slug = permission.name || permission.slug || '';
    const { action, model } = splitActionModel(slug);
    if (!action) return; // ignore fake-actions (like "external")
    if (norm(model) !== target) return; // exact model match
    if (!standardActions.includes(action) && specialActions.includes(action)) {
      specials.push(norm(slug));
    }
  });

  return specials.sort();
}

// Helper function to format special permission display name
function formatSpecialPermissionName(permissionSlug: string): string {
  const actionMatch = permissionSlug.match(/^([^-]+)-/);
  if (actionMatch) {
    const action = actionMatch[1];
    return action.charAt(0).toUpperCase() + action.slice(1);
  }
  return permissionSlug;
}
</script>

<template>
  <AppLayout>
    <Head title="Role Permissions" />

    <!-- Session Warning Dialog -->
    <SessionWarningDialog
      :open="showSessionWarning"
      :minutes-remaining="sessionMinutesRemaining"
      @continue="handleContinueWorking"
      @close="handleCloseWarning"
    />

    <div v-if="canViewRoles" class="mx-auto max-w-7xl px-4 py-6">
      <!-- Header Section -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Role Permissions</h1>
          <p class="mt-2 text-gray-600">Manage permissions for different user roles</p>
        </div>
        <div class="flex items-center gap-3">
          <Button v-if="canManageRoles" @click="openAddRoleModal" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700">
            <svg class="h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Role
          </Button>
        </div>
      </div>

      <!-- Success Message -->
      <div
        v-if="showSuccessMessage"
        class="mb-6 flex items-center justify-between rounded-lg border border-green-400 bg-green-100 p-4 text-green-700"
      >
        <div class="flex items-center">
          <svg class="mr-2 h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <span class="font-medium">{{ successMessage }}</span>
        </div>
        <button @click="showSuccessMessage = false" class="text-green-500 hover:text-green-700">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Role Selection and Quick Actions -->
      <div class="mb-6 rounded-xl border border-blue-100 bg-gradient-to-r from-blue-50 to-indigo-50 p-6">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <!-- Role Selection -->
          <div class="lg:col-span-1">
            <label class="mb-2 block text-sm font-medium text-blue-900">Select Role to Manage</label>
            <select
              v-model="selectedRoleId"
              class="w-full rounded-lg border border-blue-200 bg-[#ffffff] px-4 py-3 text-blue-900 focus:border-blue-500 focus:ring-2 focus:ring-[#3b82f6]"
            >
              <option :value="null">Choose a role...</option>
              <option v-for="role in props.roles" :key="role.id" :value="role.id">
                {{ (role as { id: number; name: string }).name }}
              </option>
            </select>
          </div>

          <!-- Quick Permission Groups -->
          <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-medium text-blue-900">Quick Permission Groups</label>
            <div class="flex items-center gap-3">
              <select
                v-model="selectedGroupId"
                class="flex-1 rounded-lg border border-blue-200 bg-[#ffffff] px-4 py-3 text-blue-900 focus:border-blue-500 focus:ring-2 focus:ring-[#3b82f6]"
              >
                <option value="custom">Custom Selection</option>
                <option v-for="group in props.permissionGroups" :key="group.id" :value="group.id.toString()">
                  {{ group.name }}
                </option>
              </select>

              <Button
                @click="applyPermissionGroup(selectedGroupId)"
                :disabled="selectedGroupId === 'custom' || !selectedRoleId"
                class="bg-blue-600 px-6 py-3 hover:bg-blue-700 disabled:opacity-50"
              >
                <svg v-if="!isApplyingGroup" class="mr-2 h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <svg v-else class="mr-2 h-[1rem] w-[1rem] animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  />
                </svg>
                {{ isApplyingGroup ? 'Applying...' : 'Apply Group' }}
              </Button>
            </div>
          </div>
        </div>

        <!-- Info Text -->
        <div class="mt-4 rounded-lg bg-blue-100 p-3">
          <div class="flex items-start gap-2">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-blue-800">
              <strong>How it works:</strong> Select a role first, then choose a permission group (like "Read Only" or "Administrator") and click
              "Apply Group" to automatically assign all permissions for that group to the selected role. This will override any existing permissions
              for that role.
            </div>
          </div>
        </div>
      </div>

      <!-- Role Title and Permissions Matrix -->
      <div v-if="selectedRole" class="space-y-6">
        <!-- Role Title -->
        <div class="rounded-xl border border-gray-200 bg-[#ffffff] p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold text-gray-900">Role: {{ selectedRole.name }}</h2>
              <p class="mt-1 text-gray-600">Manage permissions for this role</p>
            </div>
            <div class="flex gap-3">
              <Button variant="outline" @click="resetPermissions"> Reset Changes </Button>
              <Button v-if="canManageRoles" @click="savePermissions" data-save-permissions class="bg-blue-600 hover:bg-blue-700">
                <svg class="mr-2 h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Permissions
              </Button>
            </div>
          </div>
        </div>

        <!-- Permission Matrix -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-[#ffffff] shadow-sm">
          <!-- Search and Controls -->
          <div class="border-b border-gray-200 bg-gray-50 p-4 space-y-4">
            <!-- Search Box -->
            <div class="flex items-center gap-3">
              <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search permissions by name or description (e.g., 'member', 'niche', 'payment')..."
                class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
              />
              <button
                v-if="searchQuery"
                @click="searchQuery = ''"
                class="text-gray-400 hover:text-gray-600"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- App Legend and Controls -->
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-6 text-sm">
                <span class="font-medium text-gray-700">Application Groups:</span>
                <div class="flex items-center gap-2">
                  <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                  <span class="text-blue-700">Members</span>
                </div>
                <div class="flex items-center gap-2">
                  <div class="h-3 w-3 rounded-full bg-green-500"></div>
                  <span class="text-green-700">Fund</span>
                </div>
                <div class="flex items-center gap-2">
                  <div class="h-3 w-3 rounded-full bg-purple-500"></div>
                  <span class="text-purple-700">Graveyard</span>
                </div>
                <div class="flex items-center gap-2">
                  <div class="h-3 w-3 rounded-full bg-gray-500"></div>
                  <span class="text-gray-700">Core</span>
                </div>
              </div>
              <div class="flex gap-2">
                <Button variant="outline" size="sm" @click="expandAllCategories"> Expand All </Button>
                <Button variant="outline" size="sm" @click="collapseAllCategories"> Collapse All </Button>
              </div>
            </div>
          </div>

          <!-- Table Header -->
          <div class="sticky top-0 z-10 border-b border-gray-200 bg-gray-50">
            <div class="grid gap-4 p-4" style="grid-template-columns: 300px repeat(6, 1fr)">
              <div class="font-semibold text-gray-900">Permission</div>
              <div class="text-center font-semibold text-gray-900">Create</div>
              <div class="text-center font-semibold text-gray-900">Update</div>
              <div class="text-center font-semibold text-gray-900">Delete</div>
              <div class="text-center font-semibold text-gray-900">List</div>
              <div class="text-center font-semibold text-gray-900">Read</div>
              <div class="text-center font-semibold text-gray-900">Restore</div>
            </div>
          </div>

          <!-- No Results Message -->
          <div v-if="searchQuery && Object.keys(filteredPermissionsByCategory).length === 0" class="p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No permissions found</h3>
            <p class="mt-1 text-sm text-gray-500">Try a different search term</p>
          </div>

          <!-- Permission Categories -->
          <div v-else class="divide-y divide-gray-100">
            <div v-for="(categoryGroup, appName) in getCategoriesByApp" :key="appName" class="bg-[#ffffff]">
              <!-- App Header -->
              <div
                class="border-b border-gray-200 p-4"
                :class="{
                  'bg-blue-50': appName === 'Members',
                  'bg-green-50': appName === 'Fund',
                  'bg-purple-50': appName === 'Graveyard',
                  'bg-gray-50': appName === 'Core',
                }"
              >
                <h3
                  class="text-lg font-semibold"
                  :class="{
                    'text-blue-900': appName === 'Members',
                    'text-green-900': appName === 'Fund',
                    'text-purple-900': appName === 'Graveyard',
                    'text-gray-900': appName === 'Core',
                  }"
                >
                  {{ appName }}
                </h3>
              </div>

              <!-- Categories within this app -->
              <div v-for="category in categoryGroup" :key="category.id" class="border-b border-gray-100 last:border-b-0">
                <!-- Category Header -->
                <div
                  @click="toggleCategory(category.name)"
                  class="flex cursor-pointer items-center justify-between p-4 transition-colors hover:bg-gray-50"
                >
                  <div class="flex items-center gap-3">
                    <div class="h-3 w-3 rounded-full" :style="{ backgroundColor: category.color }"></div>
                    <h4 class="text-md font-semibold text-gray-900">
                      {{ category.name }}
                    </h4>
                    <div class="text-xs text-gray-500">
                      {{ category.description }}
                    </div>
                    <span class="text-sm text-gray-500">
                      ({{
                        filteredPermissionsByCategory && filteredPermissionsByCategory[category.name]
                          ? filteredPermissionsByCategory[category.name].length
                          : 0
                      }}
                      permissions)
                    </span>
                  </div>
                  <svg
                    class="h-5 w-5 text-gray-400 transition-transform"
                    :class="{ 'rotate-180': expandedCategories.has(category.name) }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>

                <!-- Category Permissions (Collapsible) -->
                <div v-if="expandedCategories.has(category.name)" class="border-t border-gray-100 bg-gray-50">
                  <div v-if="getUniqueModelsForCategory(category.name).length === 0" class="p-4 text-center text-gray-500">
                    No permissions found for this category. This might be due to:
                    <ul class="mt-2 list-inside list-disc text-left text-sm">
                      <li>Permissions not yet generated for this category</li>
                      <li>Category rules not matching existing permissions</li>
                      <li>Database permissions not synced with categories</li>
                    </ul>
                  </div>
                  <!-- Model Container with Special Permissions -->
                  <div
                    v-else
                    v-for="modelName in getUniqueModelsForCategory(category.name)"
                    :key="modelName"
                    class="border-b border-gray-100 last:border-b-0"
                  >
                    <!-- Main CRUD Row -->
                    <div class="grid gap-4 p-3 transition-colors hover:bg-[#ffffff]" style="grid-template-columns: 300px repeat(6, 1fr)">
                      <!-- Model Name -->
                      <div class="flex items-center gap-3">
                        <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                        <div>
                          <div class="text-sm font-medium text-gray-900 capitalize">
                            {{ formatModelName(modelName) }}
                          </div>
                          <div class="font-mono text-xs text-gray-500">
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
                          class="h-5 w-5 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
                        />
                      </div>
                      <div class="flex justify-center">
                        <input
                          type="checkbox"
                          :checked="selectedRoleId ? hasPermission(selectedRoleId, `update-${modelName}`) : false"
                          @change="selectedRoleId && togglePermission(selectedRoleId, `update-${modelName}`)"
                          class="h-5 w-5 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
                        />
                      </div>
                      <div class="flex justify-center">
                        <input
                          type="checkbox"
                          :checked="selectedRoleId ? hasPermission(selectedRoleId, `delete-${modelName}`) : false"
                          @change="selectedRoleId && togglePermission(selectedRoleId, `delete-${modelName}`)"
                          class="h-5 w-5 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
                        />
                      </div>
                      <div class="flex justify-center">
                        <input
                          type="checkbox"
                          :checked="selectedRoleId ? hasPermission(selectedRoleId, `list-${modelName}`) : false"
                          @change="selectedRoleId && togglePermission(selectedRoleId, `list-${modelName}`)"
                          class="h-5 w-5 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
                        />
                      </div>
                      <div class="flex justify-center">
                        <input
                          type="checkbox"
                          :checked="selectedRoleId ? hasPermission(selectedRoleId, `read-${modelName}`) : false"
                          @change="selectedRoleId && togglePermission(selectedRoleId, `read-${modelName}`)"
                          class="h-5 w-5 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
                        />
                      </div>
                      <div class="flex justify-center">
                        <input
                          type="checkbox"
                          :checked="selectedRoleId ? hasPermission(selectedRoleId, `restore-${modelName}`) : false"
                          @change="selectedRoleId && togglePermission(selectedRoleId, `restore-${modelName}`)"
                          class="h-5 w-5 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
                        />
                      </div>
                    </div>

                    <!-- Special Permissions Sub-rows -->
                    <div
                      v-for="specialPermission in getSpecialPermissionsForModel(category.name, modelName)"
                      :key="specialPermission"
                      class="bg-gray-25 grid gap-4 p-2 pl-8 transition-colors hover:bg-gray-50"
                      style="grid-template-columns: 300px repeat(6, 1fr)"
                    >
                      <!-- Special Permission Name -->
                      <div class="flex items-center gap-3">
                        <div class="h-1 w-1 rounded-full bg-blue-400"></div>
                        <div>
                          <div class="text-xs font-medium text-blue-700">↳ {{ formatSpecialPermissionName(specialPermission) }}</div>
                          <div class="font-mono text-xs text-gray-400">
                            {{ specialPermission }}
                          </div>
                        </div>
                      </div>

                      <!-- Special Permission Checkbox (spans all columns) -->
                      <div class="col-span-6 flex justify-start pl-4">
                        <input
                          type="checkbox"
                          :checked="selectedRoleId ? hasPermission(selectedRoleId, specialPermission) : false"
                          @change="selectedRoleId && togglePermission(selectedRoleId, specialPermission)"
                          class="h-4 w-4 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
                        />
                        <span class="ml-2 text-xs text-gray-600"
                          >Enable {{ formatSpecialPermissionName(specialPermission).toLowerCase() }} permission</span
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- No Role Selected Message -->
      <div v-else class="py-12 text-center">
        <div class="mx-auto max-w-[448px]">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No role selected</h3>
          <p class="mt-1 text-sm text-gray-500">Select a role from the dropdown above to manage its permissions.</p>
        </div>
      </div>
    </div>

    <div v-else class="py-10 text-center text-gray-500">You do not have permission to view role permissions.</div>

    <!-- Add Role Modal -->
    <transition name="fade">
      <div v-if="showAddRoleModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="absolute inset-0 bg-black/50" @click="closeAddRoleModal"></div>
        <div class="from-grey-900 via-grey-800 to-grey-600 relative z-10 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">Add New Role</h3>
              <button @click="closeAddRoleModal" class="text-gray-400 hover:text-gray-600">
                <svg class="h-[1.5rem] w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="space-y-4">
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Role Name</label>
                <input
                  v-model="newRoleForm.name"
                  type="text"
                  required
                  placeholder="Enter role name"
                  data-create-input
                  class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-[#3b82f6]"
                />
              </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
              <Button @click="closeAddRoleModal" variant="outline">Cancel</Button>
              <Button @click="createRole" class="bg-green-600 hover:bg-green-700">Create Role</Button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </AppLayout>
</template>
