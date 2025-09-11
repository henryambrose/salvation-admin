<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Modules } from '@/types';
import { permissionHelpers } from '@/composables/permissionHelpers';

const { can } = permissionHelpers();

const props = defineProps<{
  modules: Modules;
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
  }>;
  selectedGroupId: number;
}>();

// Permission checks
const canViewRoles = can('read-role');

const expandedCategories = ref<Set<string>>(new Set(['Core Management']));

// Get the selected group
const selectedGroup = computed(() => {
  return props.permissionGroups.find(group => group.id === props.selectedGroupId);
});

// Calculate permission statistics
const permissionStats = computed(() => {
  if (!selectedGroup.value) return null;
  
  let totalPermissions = 0;
  let permissionsInGroup = 0;
  
  Object.values(props.permissionsByCategory || {}).forEach(categoryPermissions => {
    categoryPermissions.forEach(permission => {
      totalPermissions++;
      if (isInGroup(permission.slug || permission.name || '')) {
        permissionsInGroup++;
      }
    });
  });
  
  return {
    total: totalPermissions,
    inGroup: permissionsInGroup,
    coverage: totalPermissions > 0 ? Math.round((permissionsInGroup / totalPermissions) * 100) : 0
  };
});

// Helper function to get category name safely
function getCategoryName(category: any): string {

  return typeof category === 'object' ? category.name : category;
}

// Helper function to get unique models for a category
function getUniqueModelsForCategory(categoryName: string): string[] {
  
  if (!props.permissionsByCategory || !props.permissionsByCategory[categoryName]) {
    return [];
  }
  
  const models = new Set<string>();
  const permissions = props.permissionsByCategory[categoryName];
  
  permissions.forEach((permission: any) => {
    // Get the permission name
    const permissionName = permission.name || permission.slug || '';
    
    // Extract the model name from permission names like "Create Communities", "Delete Members", etc.
    // Remove the action prefix and get just the model part
    const actionPrefixes = ['Create ', 'Read ', 'Update ', 'Delete ', 'List ', 'Restore '];
    
    let modelName = permissionName;
    for (const prefix of actionPrefixes) {
      if (permissionName.startsWith(prefix)) {
        modelName = permissionName.substring(prefix.length);
        break;
      }
    }
    
    // Clean up the model name (remove extra spaces, handle plural forms)
    modelName = modelName.trim();
    
    if (modelName) {
      models.add(modelName);
    }
  });
  
  const result = Array.from(models).sort();
  return result;
}

// Helper function to format model name for display
function formatModelName(modelName: string): string {
  // Convert "External Members" to "External Member" (singular)
  if (modelName.endsWith('s')) {
    modelName = modelName.slice(0, -1);
  }
  
  return modelName;
}

// Check if a permission is in the selected group
function isInGroup(permissionSlug: string): boolean {
  if (!selectedGroup.value) return false;
  
  // Convert the permission slug to a readable format for comparison
  // e.g., "create-communities" -> "Create Communities"
  const readablePermission = permissionSlug
    .split('-')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
  
  // Check if this readable permission exists in the group
  return selectedGroup.value.permissions.some((p: any) => {
    const groupPermissionName = p.name || p.slug || '';
    return groupPermissionName === readablePermission;
  });
}

// Toggle category expansion
function toggleCategory(categoryName: string) {
  if (expandedCategories.value.has(categoryName)) {
    expandedCategories.value.delete(categoryName);
  } else {
    expandedCategories.value.add(categoryName);
  }
}

// Go back to main page
function goBack() {
  router.visit('/roles-permissions');
}

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

// Get category permission count
function getCategoryPermissionCount(categoryName: string): number {
  if (!props.permissionsByCategory || !props.permissionsByCategory[categoryName]) {
    return 0;
  }
  
  return props.permissionsByCategory[categoryName].length;
}

// Get category coverage percentage
function getCategoryCoverage(categoryName: string): number {
  if (!props.permissionsByCategory || !props.permissionsByCategory[categoryName]) {
    return 0;
  }
  
  const permissions = props.permissionsByCategory[categoryName];
  let inGroupCount = 0;
  
  permissions.forEach(permission => {
    if (isInGroup(permission.slug || permission.name || '')) {
      inGroupCount++;
    }
  });
  
  return permissions.length > 0 ? Math.round((inGroupCount / permissions.length) * 100) : 0;
}
</script>

<template>
  <AppLayout>
    <Head title="Permission Group Preview" />
    <div v-if="canViewRoles" class="mx-auto max-w-7xl py-6 px-4">
      <!-- Header Section -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Permission Group Preview</h1>
          <p class="mt-2 text-gray-600">
            Previewing permissions for <span class="font-semibold text-blue-600">{{ selectedGroup?.name }}</span> 
            permission group
          </p>
        </div>
        <div class="flex items-center gap-3">
          <Button
            @click="goBack"
            variant="outline"
            class="inline-flex items-center gap-2"
          >
            <svg class="h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Roles
          </Button>
        </div>
      </div>

      <!-- Permission Matrix -->
      <div class="bg-[#ffffff] rounded-xl border border-gray-200 shadow-sm overflow-hidden">
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
            v-for="category in getCategoryHeaders" 
            :key="typeof category === 'object' ? category.id : category" 
            class="bg-[#ffffff]"
          >
            <!-- Category Header -->
            <div 
              @click="toggleCategory(getCategoryName(category))"
              class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center gap-3">
                <div 
                  class="w-3 h-3 rounded-full"
                  :style="{ backgroundColor: typeof category === 'object' ? category.color : '#3B82F6' }"
                ></div>
                <h3 class="text-lg font-semibold text-gray-900">
                  {{ getCategoryName(category) }}
                </h3>
                <span class="text-sm text-gray-500">
                  ({{ getCategoryPermissionCount(getCategoryName(category)) }} permissions)
                </span>
              </div>
              <svg 
                class="w-5 h-5 text-gray-400 transition-transform"
                :class="{ 'rotate-180': expandedCategories.has(getCategoryName(category)) }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>

            <!-- Category Permissions (Collapsible) -->
            <div 
              v-if="expandedCategories.has(getCategoryName(category))"
              class="border-t border-gray-100 bg-gray-50"
            >
              <div 
                v-for="modelName in getUniqueModelsForCategory(getCategoryName(category))" 
                :key="modelName"
                class="grid gap-4 p-3 hover:bg-[#ffffff] transition-colors border-b border-gray-100 last:border-b-0"
                style="grid-template-columns: 300px repeat(6, 1fr);"
              >
                <!-- Model Name - Just show the clean model name -->
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
                
                <!-- Action Status Indicators -->
                <div class="flex justify-center">
                  <div 
                    :class="{
                      'w-5 h-5 rounded-full flex items-center justify-center': true,
                      'bg-green-500': isInGroup(`create-${modelName}`),
                      'bg-gray-300': !isInGroup(`create-${modelName}`)
                    }"
                    :title="`create-${modelName} - ${isInGroup(`create-${modelName}`) ? 'Included' : 'Not included'} in group`"
                  >
                    <svg v-if="isInGroup(`create-${modelName}`)" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="flex justify-center">
                  <div 
                    :class="{
                      'w-5 h-5 rounded-full flex items-center justify-center': true,
                      'bg-green-500': isInGroup(`update-${modelName}`),
                      'bg-gray-300': !isInGroup(`update-${modelName}`)
                    }"
                    :title="`update-${modelName} - ${isInGroup(`update-${modelName}`) ? 'Included' : 'Not included'} in group`"
                  >
                    <svg v-if="isInGroup(`update-${modelName}`)" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="flex justify-center">
                  <div 
                    :class="{
                      'w-5 h-5 rounded-full flex items-center justify-center': true,
                      'bg-green-500': isInGroup(`delete-${modelName}`),
                      'bg-gray-300': !isInGroup(`delete-${modelName}`)
                    }"
                    :title="`delete-${modelName} - ${isInGroup(`delete-${modelName}`) ? 'Included' : 'Not included'} in group`"
                  >
                    <svg v-if="isInGroup(`delete-${modelName}`)" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="flex justify-center">
                  <div 
                    :class="{
                      'w-5 h-5 rounded-full flex items-center justify-center': true,
                      'bg-green-500': isInGroup(`list-${modelName}`),
                      'bg-gray-300': !isInGroup(`list-${modelName}`)
                    }"
                    :title="`list-${modelName} - ${isInGroup(`list-${modelName}`) ? 'Included' : 'Not included'} in group`"
                  >
                    <svg v-if="isInGroup(`list-${modelName}`)" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="flex justify-center">
                  <div 
                    :class="{
                      'w-5 h-5 rounded-full flex items-center justify-center': true,
                      'bg-green-500': isInGroup(`read-${modelName}`),
                      'bg-gray-300': !isInGroup(`read-${modelName}`)
                    }"
                    :title="`read-${modelName} - ${isInGroup(`read-${modelName}`) ? 'Included' : 'Not included'} in group`"
                  >
                    <svg v-if="isInGroup(`read-${modelName}`)" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="flex justify-center">
                  <div 
                    :class="{
                      'w-5 h-5 rounded-full flex items-center justify-center': true,
                      'bg-green-500': isInGroup(`restore-${modelName}`),
                      'bg-gray-300': !isInGroup(`restore-${modelName}`)
                    }"
                    :title="`restore-${modelName} - ${isInGroup(`restore-${modelName}`) ? 'Included' : 'Not included'} in group`"
                  >
                    <svg v-if="isInGroup(`restore-${modelName}`)" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="mt-8 flex justify-center gap-4">
        <Button
          @click="goBack"
          variant="outline"
          size="lg"
          class="px-8 py-3"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to Roles
        </Button>
      </div>
    </div>
    
    <div v-else class="py-10 text-center text-gray-500">
      You do not have permission to view role permissions.
    </div>
  </AppLayout>
</template>
