<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Roles, Modules, Permissions } from '@/types';
import { permissionHelpers } from '@/composables/permissionHelpers';

const { can } = permissionHelpers();

const props = defineProps<{
  roles: Roles;
  modules: Modules;
  permissions: Permissions;
  modulesIdWise: Record<number, any>;
  permissionGroups: Array<{
    id: number;
    name: string;
    description: string;
    is_default: boolean;
    permissions: Array<{ id: number; slug: string; }>;
  }>;
}>();

// Permission checks
const canManageRoles = can('manage-roles');
const canViewRoles = can('read-role');

const actions = ['create', 'read', 'update', 'delete', 'list', 'restore'];

const selectedRoleId = ref(props.roles.length > 0 ? props.roles[0].id : 0);
const selectedGroupId = ref('custom');
const permissionState = ref(props.permissions[selectedRoleId.value] ? JSON.parse(JSON.stringify(props.permissions[selectedRoleId.value])) : {});

// Define module order to match sidebar navigation
const moduleOrder = [
  'member', 'community', 'parish', // Core Management
  'zone', 'community-cluster', 'cluster', 'cells-and-association', 'cells-and-association-member', // Organizational Structure
  'scc-head', 'ppc-head', // Leadership
  'relationship', 'designation', 'age-group', 'blood-group', 'gender', 'status', 'income-range', // Member Attributes
  'country', 'state', 'city', 'town', // Geographic Data
  'users', 'role', // System Management
  'dashboard' // Special Pages
];

// Sort modules to match sidebar order
const sortedModules = computed(() => {
  return [...props.modules].sort((a, b) => {
    const aIndex = moduleOrder.indexOf(a.slug);
    const bIndex = moduleOrder.indexOf(b.slug);
    return aIndex - bIndex;
  });
});

watch(selectedRoleId, (newRoleId) => {
  if (newRoleId && props.permissions[newRoleId]) {
    permissionState.value = JSON.parse(JSON.stringify(props.permissions[newRoleId]));
  } else {
    permissionState.value = {};
  }
  // Reset group selection when role changes
  selectedGroupId.value = 'custom';
});

watch(selectedGroupId, (newGroupId) => {
  applyPermissionGroup(newGroupId);
});

function getActionForModule(module: any, actionName: string) {
  return module.actions.find((action: any) => action.action === actionName);
}

function togglePermission(moduleId: number, actionId: number) {
  if (permissionState.value[moduleId] && typeof permissionState.value[moduleId][actionId] !== 'undefined') {
    permissionState.value[moduleId][actionId] = permissionState.value[moduleId][actionId] ? 0 : 1;
  }
}

function applyPermissionGroup(groupId: string) {
  if (groupId === 'custom') {
    return; // Keep current manual selection
  }

  const group = props.permissionGroups.find(g => g.id.toString() === groupId);
  if (!group) return;

  // Reset all permissions to 0
  const newPermissionState: Record<number, Record<number, number>> = {};
  props.modules.forEach(module => {
    newPermissionState[module.id] = {};
    module.actions.forEach(action => {
      newPermissionState[module.id][action.id] = 0;
    });
  });

  // Set permissions based on the selected group
  group.permissions.forEach(permission => {
    // Find the module action by slug and set it to 1
    props.modules.forEach(module => {
      module.actions.forEach(action => {
        if (action.slug === permission.slug) {
          newPermissionState[module.id][action.id] = 1;
        }
      });
    });
  });

  permissionState.value = newPermissionState;
}

function savePermissions() {
  router.post('/roles-permissions/update', {
    role_id: selectedRoleId.value,
    permissions: permissionState.value,
    selected_group_id: selectedGroupId.value,
  });
}
</script>

<template>
  <AppLayout>
    <Head title="Role Permissions" />
    <div v-if="canViewRoles" class="mx-auto max-w-4xl py-8">
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Role Permissions</h2>
        <button 
          @click="router.visit('/roles-permissions/users')"
          class="text-blue-600 hover:text-blue-800 transition px-4 py-2 rounded-lg hover:bg-blue-50"
        >
          Manage User Roles →
        </button>
      </div>
      <div class="mb-4">
        <label class="mb-1 block font-semibold">Select Role</label>
        <select v-model="selectedRoleId" class="w-full rounded-full border border-gray-200 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200">
          <option v-for="role in props.roles" :key="role.id" :value="role.id">{{ (role as { id: number; name: string }).name }}</option>
        </select>
      </div>
      
      <!-- Permission Groups Section -->
      <div class="mb-6 rounded-2xl bg-blue-50 p-6 border border-blue-200">
        <h3 class="mb-4 text-lg font-semibold text-blue-800">Quick Permission Groups</h3>
        <p class="mb-4 text-sm text-blue-600">Select a predefined permission group to quickly apply common permission sets, or choose "Custom" for manual selection.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="group in props.permissionGroups" :key="group.id" 
               class="relative">
            <input 
              type="radio" 
              :id="`group-${group.id}`" 
              :value="group.id.toString()" 
              v-model="selectedGroupId"
              class="sr-only"
            />
            <label 
              :for="`group-${group.id}`" 
              class="block p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
              :class="selectedGroupId === group.id.toString() 
                ? 'border-blue-500 bg-blue-100 shadow-md' 
                : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50'"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <h4 class="font-semibold text-gray-900">{{ group.name }}</h4>
                  <p class="text-sm text-gray-600 mt-1">{{ group.description }}</p>
                  <div class="mt-2 text-xs text-gray-500">
                    {{ group.permissions.length }} permissions
                  </div>
                </div>
                <div v-if="selectedGroupId === group.id.toString()" 
                     class="ml-3 text-blue-500">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </label>
          </div>
        </div>
      </div>
      <div class="mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
        <div class="overflow-x-auto rounded-xl border border-gray-100">
          <table class="w-full border-collapse text-left">
          <thead>
              <tr class="bg-blue-50">
                <th class="border-b p-3 font-semibold text-gray-700">Module</th>
                <th v-for="action in actions" :key="action" class="border-b p-3 font-semibold text-gray-700 capitalize">{{ action }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="module in sortedModules" :key="module.id" class="even:bg-gray-50 hover:bg-blue-50 transition">
              <td class="border-b p-3 font-semibold">{{ module.name }}</td>
              <td v-for="actionName in actions" :key="actionName" class="border-b p-3 text-center">
                <input 
                  v-if="getActionForModule(module, actionName)"
                  type="checkbox" 
                  :checked="permissionState[module.id]?.[getActionForModule(module, actionName)?.id] === 1" 
                  @change="togglePermission(module.id, getActionForModule(module, actionName)?.id)" 
                  class="accent-blue-600 w-5 h-5 rounded-full border-gray-300 focus:ring-2 focus:ring-blue-200" 
                />
                <span v-else class="text-gray-300">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-6 flex justify-end">
          <Button v-if="canManageRoles" class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2 flex items-center gap-2" @click="savePermissions">
            Save Permissions
          </Button>
        </div>
      </div>
    </div>
    <div v-else class="py-10 text-center text-gray-500">You do not have permission to view role permissions.</div>
  </AppLayout>
</template>
