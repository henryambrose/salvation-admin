<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { permissionHelpers } from '@/composables/permissionHelpers';

const { can } = permissionHelpers();

const props = defineProps<{
  users: Array<{
    id: number;
    name: string;
    email: string;
    roles: Array<{ id: number; name: string; }>;
  }>;
  roles: Array<{ id: number; name: string; }>;
  modules: Array<{
    id: number;
    name: string;
    slug: string;
    actions: Array<{
      id: number;
      action: string;
      slug: string;
    }>;
  }>;
}>();

const canManageRoles = can('manage-roles');
const canViewRoles = can('read-role');

// State management
const selectedUser = ref<number | null>(null);
const searchTerm = ref('');
const selectedRoleFilter = ref<string>('all');
const showPermissionModal = ref(false);
const editingUser = ref<any>(null);
const userPermissions = ref<{[key: number]: string[]}>({});

// Computed properties
const filteredUsers = computed(() => {
  let filtered = props.users;
  
  if (searchTerm.value) {
    filtered = filtered.filter(user => 
      user.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      user.email.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
  }
  
  if (selectedRoleFilter.value !== 'all') {
    filtered = filtered.filter(user => 
      user.roles.some(role => role.name === selectedRoleFilter.value)
    );
  }
  
  return filtered;
});

const roleOptions = computed(() => [
  { value: 'all', label: 'All Roles' },
  ...props.roles.map(role => ({ value: role.name, label: role.name }))
]);

// Functions
function assignRole(userId: number, roleId: number) {
  router.post('/roles-permissions/assign-role', {
    user_id: userId,
    role_id: roleId,
  });
}

function removeRole(userId: number) {
  router.post('/roles-permissions/remove-role', {
    user_id: userId,
  });
}

function openPermissionModal(user: {
  id: number;
  name: string;
  email: string;
  roles: Array<{ id: number; name: string; }>;
  permissions?: string[];
  effectivePermissions?: string[];
}) {
  editingUser.value = user;
  
  // Get the user's effective permissions (role + custom)
  const currentPermissions = user.effectivePermissions || user.permissions || [];
  
  // Initialize user permissions with their current permissions
  userPermissions.value[user.id] = [...currentPermissions];
  
  showPermissionModal.value = true;
}

function closePermissionModal() {
  showPermissionModal.value = false;
  editingUser.value = null;
}

function togglePermission(permissionSlug: string) {
  if (!editingUser.value) return;
  
  const userId = editingUser.value.id;
  const currentPermissions = userPermissions.value[userId] || [];
  
  if (currentPermissions.includes(permissionSlug)) {
    userPermissions.value[userId] = currentPermissions.filter(p => p !== permissionSlug);
  } else {
    userPermissions.value[userId] = [...currentPermissions, permissionSlug];
  }
}

function saveUserPermissions() {
  if (!editingUser.value) return;
  
  const userId = editingUser.value.id;
  const permissions = userPermissions.value[userId] || [];
  
  router.post('/roles-permissions/update-user-permissions', {
    user_id: userId,
    permissions: permissions,
  });
  
  closePermissionModal();
}

function getRoleBadgeColor(roleName: string) {
  const colors: {[key: string]: string} = {
    'super admin': 'bg-purple-100 text-purple-800',
    'admin': 'bg-blue-100 text-blue-800',
    'viewer': 'bg-green-100 text-green-800',
    'ppc-head': 'bg-orange-100 text-orange-800',
    'scc-head': 'bg-indigo-100 text-indigo-800',
  };
  return colors[roleName] || 'bg-gray-100 text-gray-800';
}

function getPermissionIcon(permission: string) {
  const icons: {[key: string]: string} = {
    'create': '➕',
    'read': '🔍',
    'update': '✏️',
    'delete': '🗑️',
    'list': '📋',
    'restore': '↩️',
    'manage': '⚙️',
  };
  return icons[permission] || '🔑';
}

function onRoleChange(userId: number, event: Event) {
  const target = event.target as HTMLSelectElement;
  const roleId = target.value ? Number(target.value) : 0;
  assignRole(userId, roleId);
}

// Remove unused variables and improve type safety
const isPermissionFromRole = (permissionSlug: string): boolean => {
  return editingUser.value?.permissions?.includes(permissionSlug) || false;
}

const isPermissionCustom = (permissionSlug: string): boolean => {
  return false; // Current backend doesn't distinguish custom vs role permissions
}

const getCustomPermissionsCount = (): number => {
  return 0; // Current backend doesn't distinguish custom vs role permissions
}
</script>

<template>
  <AppLayout>
    <Head title="User Management" />
    
    <div v-if="canViewRoles" class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
              <p class="mt-2 text-sm text-gray-600">
                Manage user roles, permissions, and access levels
              </p>
            </div>
            <button 
              @click="router.visit('/roles-permissions')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              ← Back to Permissions
            </button>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters and Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
          <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">Search Users</label>
              <div class="relative">
                <input
                  v-model="searchTerm"
                  type="text"
                  placeholder="Search by name or email..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
              </div>
            </div>
            
            <!-- Role Filter -->
            <div class="sm:w-48">
              <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Role</label>
              <select
                v-model="selectedRoleFilter"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option v-for="option in roleOptions" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- Users Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="user in filteredUsers" :key="user.id" 
               class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 hover:scale-[1.02]">
            
            <!-- User Header -->
            <div class="p-6 border-b border-gray-100">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-900">{{ user.name }}</h3>
                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                  </div>
                </div>
                
                <!-- Role Badge -->
                <div v-if="user.roles.length > 0" 
                     :class="`px-3 py-1 rounded-full text-xs font-medium ${getRoleBadgeColor(user.roles[0].name)}`">
                  {{ user.roles[0].name }}
                </div>
                <div v-else class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                  No Role
                </div>
              </div>
            </div>

            <!-- User Actions -->
            <div class="p-6 space-y-3">
              <!-- Role Assignment -->
              <div v-if="canManageRoles">
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign Role</label>
                <select 
                  :value="user.roles[0]?.id || ''" 
                  @change="onRoleChange(user.id, $event)"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
                  <option value="">Select Role</option>
                  <option v-for="role in roles" :key="role.id" :value="role.id">
                    {{ role.name }}
                  </option>
                </select>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-2">
                <button
                  @click="openPermissionModal(user)"
                  class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                >
                  🔧 Configure Permissions
                </button>
                
                <button
                  v-if="user.roles.length > 0"
                  @click="removeRole(user.id)"
                  class="px-4 py-2 text-red-600 hover:text-red-700 border border-red-300 hover:border-red-400 rounded-lg text-sm font-medium transition-colors duration-200"
                >
                  Remove Role
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredUsers.length === 0" class="text-center py-12">
          <div class="text-gray-400 mb-4">
            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
          <p class="text-gray-500">Try adjusting your search or filter criteria.</p>
        </div>
      </div>

      <!-- Permission Modal -->
      <div v-if="showPermissionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <!-- Modal Header -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-semibold text-gray-900">
                  Configure Permissions for {{ editingUser?.name }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                  Current role: <span class="font-medium">{{ editingUser?.roles[0]?.name || 'None' }}</span>
                </p>
              </div>
              <button
                @click="closePermissionModal"
                class="text-gray-400 hover:text-gray-600 transition-colors"
              >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Modal Content -->
          <div class="p-6">
            <!-- Permission Summary -->
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                    <span class="text-sm font-medium text-gray-700">
                      User Permissions: {{ editingUser?.permissions?.length || 0 }}
                    </span>
                  </div>
                </div>
                <div class="text-sm text-gray-500">
                  Total: {{ userPermissions[editingUser?.id]?.length || 0 }}
                </div>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="module in modules" :key="module.id" 
                   class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                  <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                  {{ module.name }}
                </h4>
                
                <div class="space-y-3">
                  <label v-for="action in module.actions" :key="action.id" 
                         class="flex items-center p-2 rounded-lg hover:bg-white transition-colors cursor-pointer">
                    <input 
                      type="checkbox"
                      :checked="(userPermissions[editingUser?.id] || []).includes(action.slug)"
                      @change="togglePermission(action.slug)"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <span class="ml-3 text-sm text-gray-700 flex items-center gap-2">
                      <span class="text-lg">{{ getPermissionIcon(action.action) }}</span>
                      {{ action.action }}
                    </span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
            <div class="flex justify-end gap-3">
              <button
                @click="closePermissionModal"
                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cancel
              </button>
              <button
                @click="saveUserPermissions"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
              >
                💾 Save Permissions
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div v-else class="py-20 text-center">
      <div class="text-gray-400 mb-4">
        <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Access Denied</h3>
      <p class="text-gray-500">You don't have permission to view user role assignments.</p>
    </div>
  </AppLayout>
</template>
