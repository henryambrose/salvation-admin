<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-lg font-medium text-gray-900">
              Manage Permissions for "{{ role?.name }}"
            </h3>
            <p class="text-sm text-gray-500 mt-1">
              Select the permissions this role should have
            </p>
          </div>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Permissions Form -->
        <form @submit.prevent="submitForm">
          <!-- Quick Actions -->
          <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between mb-3">
              <h4 class="text-sm font-medium text-gray-700">Quick Actions</h4>
            </div>
            <div class="flex flex-wrap gap-2">
              <Button
                type="button"
                variant="outline"
                size="sm"
                @click="selectAllPermissions"
              >
                Select All
              </Button>
              <Button
                type="button"
                variant="outline"
                size="sm"
                @click="clearAllPermissions"
              >
                Clear All
              </Button>
              <Button
                type="button"
                variant="outline"
                size="sm"
                @click="selectReadOnlyPermissions"
              >
                Read Only
              </Button>
              <Button
                type="button"
                variant="outline"
                size="sm"
                @click="selectStandardUserPermissions"
              >
                Standard User
              </Button>
            </div>
          </div>

          <!-- Permission Groups -->
          <div class="space-y-6 max-h-96 overflow-y-auto">
            <div 
              v-for="group in permissionGroups" 
              :key="group.name"
              class="border border-gray-200 rounded-lg p-4"
            >
              <div class="flex items-center justify-between mb-3">
                <h5 class="font-medium text-gray-900">{{ group.name }}</h5>
                <div class="flex space-x-2">
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="selectGroupPermissions(group.permissions)"
                  >
                    Select Group
                  </Button>
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="clearGroupPermissions(group.permissions)"
                  >
                    Clear Group
                  </Button>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label 
                  v-for="permission in group.permissions" 
                  :key="permission.id"
                  class="flex items-center space-x-3 p-2 rounded border border-gray-200 hover:bg-gray-50"
                >
                  <input
                    type="checkbox"
                    :value="permission.id"
                    v-model="selectedPermissions"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <div class="flex-1">
                    <span class="text-sm font-medium text-gray-900">
                      {{ formatPermissionName(permission.name) }}
                    </span>
                    <p class="text-xs text-gray-500">
                      {{ getPermissionDescription(permission.name) }}
                    </p>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Summary -->
          <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-blue-900">
                  Selected Permissions: {{ selectedPermissions.length }}
                </p>
                <p class="text-xs text-blue-700 mt-1">
                  Total Available: {{ totalPermissions }}
                </p>
              </div>
              <div class="text-right">
                <p class="text-sm font-medium text-blue-900">
                  {{ Math.round((selectedPermissions.length / totalPermissions) * 100) }}% Selected
                </p>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3 pt-4 border-t">
            <Button
              type="button"
              variant="outline"
              @click="$emit('close')"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              :disabled="form.processing"
              class="bg-blue-600 hover:bg-blue-700"
            >
              Save Permissions
            </Button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

// Props
const props = defineProps({
  role: {
    type: Object,
    default: null
  },
  permissions: {
    type: Array,
    default: () => []
  }
});

// Emits
const emit = defineEmits(['close', 'saved']);

// Form
const form = useForm({
  permissions: []
});

// Local state
const selectedPermissions = ref([]);

// Computed
const totalPermissions = computed(() => props.permissions.length);

// Permission groups
const permissionGroups = computed(() => {
  const groups = {};
  
  props.permissions.forEach(permission => {
    const [action, resource] = permission.name.split('-');
    const groupName = getGroupName(resource);
    
    if (!groups[groupName]) {
      groups[groupName] = {
        name: groupName,
        permissions: []
      };
    }
    
    groups[groupName].permissions.push(permission);
  });
  
  // Sort permissions within each group
  Object.values(groups).forEach(group => {
    group.permissions.sort((a, b) => a.name.localeCompare(b.name));
  });
  
  return Object.values(groups).sort((a, b) => a.name.localeCompare(b.name));
});

// Methods
function getGroupName(resource) {
  const groupMap = {
    'member': 'Members Management',
    'external-member': 'External Members',
    'community': 'Community Management',
    'parish': 'Parish Management',
    'zone': 'Geographic Structure',
    'cluster': 'Organizational Structure',
    'user': 'User Management',
    'role': 'Role Management',
    'dashboard': 'System Access',
    'fund': 'Fund Management',
    'annual-contribution': 'Annual Contributions',
    'mass-intention': 'Mass Intentions'
  };
  
  return groupMap[resource] || 'Other';
}

function formatPermissionName(permissionName) {
  const [action, resource] = permissionName.split('-');
  const actionMap = {
    'create': 'Create',
    'read': 'View',
    'update': 'Edit',
    'delete': 'Delete',
    'list': 'List',
    'restore': 'Restore',
    'manage': 'Manage'
  };
  
  const resourceMap = {
    'member': 'Members',
    'external-member': 'External Members',
    'community': 'Communities',
    'parish': 'Parishes',
    'zone': 'Zones',
    'cluster': 'Clusters',
    'user': 'Users',
    'role': 'Roles',
    'dashboard': 'Dashboard',
    'fund': 'Fund App',
    'annual-contribution': 'Annual Contributions',
    'mass-intention': 'Mass Intentions'
  };
  
  return `${actionMap[action] || action} ${resourceMap[resource] || resource}`;
}

function getPermissionDescription(permissionName) {
  const [action, resource] = permissionName.split('-');
  const descriptions = {
    'create': 'Can create new records',
    'read': 'Can view records',
    'update': 'Can edit existing records',
    'delete': 'Can delete records',
    'list': 'Can view lists of records',
    'restore': 'Can restore deleted records',
    'manage': 'Can manage all aspects'
  };
  
  return descriptions[action] || 'Permission to perform this action';
}

function selectAllPermissions() {
  selectedPermissions.value = props.permissions.map(p => p.id);
}

function clearAllPermissions() {
  selectedPermissions.value = [];
}

function selectReadOnlyPermissions() {
  selectedPermissions.value = props.permissions
    .filter(p => p.name.startsWith('read-') || p.name.startsWith('list-'))
    .map(p => p.id);
}

function selectStandardUserPermissions() {
  selectedPermissions.value = props.permissions
    .filter(p => 
      p.name.startsWith('read-') || 
      p.name.startsWith('list-') || 
      p.name.startsWith('create-') ||
      p.name.startsWith('update-')
    )
    .map(p => p.id);
}

function selectGroupPermissions(permissions) {
  const permissionIds = permissions.map(p => p.id);
  selectedPermissions.value = [...new Set([...selectedPermissions.value, ...permissionIds])];
}

function clearGroupPermissions(permissions) {
  const permissionIds = permissions.map(p => p.id);
  selectedPermissions.value = selectedPermissions.value.filter(id => !permissionIds.includes(id));
}

function submitForm() {
  form.permissions = selectedPermissions.value;
  form.put(route('roles.update', props.role.id), {
    onSuccess: () => emit('saved')
  });
}

// Watch for role changes and populate selected permissions
watch(() => props.role, (newRole) => {
  if (newRole) {
    selectedPermissions.value = newRole.permissions?.map(p => p.id) || [];
  } else {
    selectedPermissions.value = [];
  }
}, { immediate: true });
</script>
