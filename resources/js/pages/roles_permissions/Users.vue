<script setup lang="ts">
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { can } = permissionHelpers();

// Debounce utility function
function debounce<T extends (...args: any[]) => any>(func: T, wait: number): T {
  let timeout: ReturnType<typeof setTimeout>;
  return ((...args: any[]) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func(...args), wait);
  }) as T;
}

const props = defineProps<{
  users: Array<{
    id: number;
    name: string;
    email: string;
    roles: Array<{ id: number; name: string }>;
  }>;
  roles: Array<{ id: number; name: string }>;
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
const searchTerm = ref('');
const selectedRoleFilter = ref<string>('all');
const showPermissionModal = ref(false);
const editingUser = ref<any>(null);
const userPermissions = ref<{ [key: number]: string[] }>({});
const isProcessingRole = ref<{ [key: number]: boolean }>({});
const failedRequests = ref<Set<string>>(new Set());
const errorMessages = ref<{ [key: number]: string }>({});

// Prevent retrying failed requests for 5 minutes
const addFailedRequest = (key: string) => {
  failedRequests.value.add(key);
  setTimeout(
    () => {
      failedRequests.value.delete(key);
    },
    5 * 60 * 1000,
  ); // 5 minutes
};

const isRequestFailed = (key: string) => failedRequests.value.has(key);

const clearError = (userId: number) => {
  delete errorMessages.value[userId];
};

const retryRoleAssignment = (userId: number, roleId: number | string) => {
  clearError(userId);
  assignRole(userId, Number(roleId));
};

// Computed properties
const filteredUsers = computed(() => {
  let filtered = props.users;

  if (searchTerm.value) {
    filtered = filtered.filter(
      (user) => user.name.toLowerCase().includes(searchTerm.value.toLowerCase()) || user.email.toLowerCase().includes(searchTerm.value.toLowerCase()),
    );
  }

  if (selectedRoleFilter.value !== 'all') {
    filtered = filtered.filter((user) => user.roles.some((role) => role.name === selectedRoleFilter.value));
  }

  return filtered;
});

const roleOptions = computed(() => [{ value: 'all', label: 'All Roles' }, ...props.roles.map((role) => ({ value: role.name, label: role.name }))]);

// Functions
async function assignRole(userId: number, roleId: number) {
  // Prevent multiple rapid requests
  if (isProcessingRole.value[userId]) {
    return;
  }

  // Prevent retrying failed requests
  const requestKey = `assign-role-${userId}-${roleId}`;
  if (isRequestFailed(requestKey)) {
    // Skip request to prevent continuous errors
    return;
  }

  isProcessingRole.value[userId] = true;

  try {
    await router.post('/roles-permissions/assign-role', {
      user_id: userId,
      role_id: roleId,
    });
    // Clear any previous error on success
    clearError(userId);
  } catch (error) {
    console.error('Failed to assign role:', error);
    // Mark this request as failed to prevent continuous retries
    addFailedRequest(requestKey);
    // Set user-friendly error message
    errorMessages.value[userId] = 'Failed to assign role. Please try again.';
    // Don't retry on error - let user try again manually
  } finally {
    isProcessingRole.value[userId] = false;
  }
}

async function removeRole(userId: number) {
  // Prevent multiple rapid requests
  if (isProcessingRole.value[userId]) {
    return;
  }

  isProcessingRole.value[userId] = true;

  try {
    await router.post('/roles-permissions/remove-role', {
      user_id: userId,
    });
  } catch (error) {
    console.error('Failed to remove role:', error);
    // Don't retry on error - let user try again manually
  } finally {
    isProcessingRole.value[userId] = false;
  }
}

function openPermissionModal(user: {
  id: number;
  name: string;
  email: string;
  roles: Array<{ id: number; name: string }>;
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
    userPermissions.value[userId] = currentPermissions.filter((p) => p !== permissionSlug);
  } else {
    userPermissions.value[userId] = [...currentPermissions, permissionSlug];
  }
}

async function saveUserPermissions() {
  if (!editingUser.value) return;

  const userId = editingUser.value.id;
  const permissions = userPermissions.value[userId] || [];

  try {
    await router.post('/roles-permissions/update-user-permissions', {
      user_id: userId,
      permissions: permissions,
    });

    closePermissionModal();
  } catch (error) {
    console.error('Failed to update user permissions:', error);
    // Don't retry on error - let user try again manually
  }
}

function getRoleBadgeColor(roleName: string) {
  const colors: { [key: string]: string } = {
    'super admin': 'bg-purple-100 text-purple-800',
    admin: 'bg-blue-100 text-blue-800',
    viewer: 'bg-green-100 text-green-800',
    'ppc-head': 'bg-orange-100 text-orange-800',
    'scc-head': 'bg-indigo-100 text-indigo-800',
  };
  return colors[roleName] || 'bg-gray-100 text-gray-800';
}

function getPermissionIcon(permission: string) {
  const icons: { [key: string]: string } = {
    create: '➕',
    read: '🔍',
    update: '✏️',
    delete: '🗑️',
    list: '📋',
    restore: '↩️',
    manage: '⚙️',
  };
  return icons[permission] || '🔑';
}

function onRoleChange(userId: number, event: Event) {
  const target = event.target as HTMLSelectElement;
  const roleId = target.value ? Number(target.value) : 0;

  // Debounce the role assignment to prevent rapid requests
  const debouncedAssignRole = debounce(assignRole, 500);
  debouncedAssignRole(userId, roleId);
}
</script>

<template>
  <AppLayout>
    <Head title="User Management" />

    <div v-if="canViewRoles" class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="border-b border-gray-200 bg-[#ffffff]">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
              <p class="mt-2 text-sm text-gray-600">Manage user roles, permissions, and access levels</p>
            </div>
            <button
              @click="router.visit('/roles-permissions')"
              class="inline-flex items-center rounded-md border border-gray-300 bg-[#ffffff] px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-[#3b82f6] focus:ring-offset-2 focus:outline-none"
            >
              ← Back to Permissions
            </button>
          </div>
        </div>
      </div>

      <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <!-- Filters and Search -->
        <div class="mb-6 rounded-xl border border-gray-200 bg-[#ffffff] p-6 shadow-sm">
          <div class="flex flex-col gap-4 sm:flex-row">
            <!-- Search -->
            <div class="flex-1">
              <label class="mb-2 block text-sm font-medium text-gray-700">Search Users</label>
              <div class="relative">
                <input
                  v-model="searchTerm"
                  type="text"
                  placeholder="Search by name or email..."
                  class="w-full rounded-lg border border-gray-300 py-2 pr-4 pl-10 focus:border-blue-500 focus:ring-2 focus:ring-[#3b82f6]"
                />
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Role Filter -->
            <div class="sm:w-48">
              <label class="mb-2 block text-sm font-medium text-gray-700">Filter by Role</label>
              <select
                v-model="selectedRoleFilter"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-[#3b82f6]"
              >
                <option v-for="option in roleOptions" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- Users Grid -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="user in filteredUsers"
            :key="user.id"
            class="rounded-xl border border-gray-200 bg-[#ffffff] shadow-sm transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
          >
            <!-- User Header -->
            <div class="border-b border-gray-100 p-6">
              <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-lg font-semibold text-white"
                  >
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-900">{{ user.name }}</h3>
                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                  </div>
                </div>

                <!-- Role Badge -->
                <div v-if="user.roles.length > 0" :class="`rounded-full px-3 py-1 text-xs font-medium ${getRoleBadgeColor(user.roles[0].name)}`">
                  {{ user.roles[0].name }}
                </div>
                <div v-else class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">No Role</div>
              </div>
            </div>

            <!-- User Actions -->
            <div class="space-y-3 p-6">
              <!-- Role Assignment -->
              <div v-if="canManageRoles">
                <label class="mb-2 block text-sm font-medium text-gray-700">Assign Role</label>
                <select
                  :value="user.roles[0]?.id || ''"
                  @change="onRoleChange(user.id, $event)"
                  :disabled="isProcessingRole[user.id]"
                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-[#3b82f6] disabled:cursor-not-allowed disabled:opacity-50"
                >
                  <option value="">Select Role</option>
                  <option v-for="role in roles" :key="role.id" :value="role.id">
                    {{ role.name }}
                  </option>
                </select>
                <div v-if="isProcessingRole[user.id]" class="mt-1 text-xs text-blue-600">Updating role...</div>
                <div v-if="errorMessages[user.id]" class="mt-1 flex items-center gap-2 text-xs text-red-600">
                  {{ errorMessages[user.id] }}
                  <button
                    @click="() => retryRoleAssignment(user.id, user.roles[0]?.id || '')"
                    class="text-xs text-blue-600 underline hover:text-blue-800"
                  >
                    Retry
                  </button>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-2">
                <button
                  @click="openPermissionModal(user)"
                  class="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-blue-700"
                >
                  🔧 Configure Permissions
                </button>

                <button
                  v-if="user.roles.length > 0"
                  @click="removeRole(user.id)"
                  class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 transition-colors duration-200 hover:border-red-400 hover:text-red-700"
                >
                  Remove Role
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredUsers.length === 0" class="py-12 text-center">
          <div class="mb-4 text-gray-400">
            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
              />
            </svg>
          </div>
          <h3 class="mb-2 text-lg font-medium text-gray-900">No users found</h3>
          <p class="text-gray-500">Try adjusting your search or filter criteria.</p>
        </div>
      </div>

      <!-- Permission Modal -->
      <div v-if="showPermissionModal" class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black p-4">
        <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-2xl bg-[#ffffff] shadow-xl">
          <!-- Modal Header -->
          <div class="border-b border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-semibold text-gray-900">Configure Permissions for {{ editingUser?.name }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                  Current role: <span class="font-medium">{{ editingUser?.roles[0]?.name || 'None' }}</span>
                </p>
              </div>
              <button @click="closePermissionModal" class="text-gray-400 transition-colors hover:text-gray-600">
                <svg class="h-[1.5rem] w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Modal Content -->
          <div class="p-6">
            <!-- Permission Summary -->
            <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                    <span class="text-sm font-medium text-gray-700"> User Permissions: {{ editingUser?.permissions?.length || 0 }} </span>
                  </div>
                </div>
                <div class="text-sm text-gray-500">Total: {{ userPermissions[editingUser?.id]?.length || 0 }}</div>
              </div>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
              <div v-for="module in modules" :key="module.id" class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <h4 class="mb-4 flex items-center gap-2 font-semibold text-gray-800">
                  <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                  {{ module.name }}
                </h4>

                <div class="space-y-3">
                  <label
                    v-for="action in module.actions"
                    :key="action.id"
                    class="flex cursor-pointer items-center rounded-lg p-2 transition-colors hover:bg-[#ffffff]"
                  >
                    <input
                      type="checkbox"
                      :checked="(userPermissions[editingUser?.id] || []).includes(action.slug)"
                      @change="togglePermission(action.slug)"
                      class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
                    />
                    <span class="ml-3 flex items-center gap-2 text-sm text-gray-700">
                      <span class="text-lg">{{ getPermissionIcon(action.action) }}</span>
                      {{ action.action }}
                    </span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="rounded-b-2xl border-t border-gray-200 bg-gray-50 p-6">
            <div class="flex justify-end gap-3">
              <button
                @click="closePermissionModal"
                class="rounded-lg border border-gray-300 bg-[#ffffff] px-4 py-2 text-gray-700 transition-colors hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                @click="saveUserPermissions"
                class="rounded-lg bg-blue-600 px-6 py-2 font-medium text-white transition-colors hover:bg-blue-700"
              >
                💾 Save Permissions
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="py-20 text-center">
      <div class="mb-4 text-gray-400">
        <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
          />
        </svg>
      </div>
      <h3 class="mb-2 text-lg font-medium text-gray-900">Access Denied</h3>
      <p class="text-gray-500">You don't have permission to view user role assignments.</p>
    </div>
  </AppLayout>
</template>
