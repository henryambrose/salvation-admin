<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Role Management</h1>
            <Button 
              @click="openCreateModal"
              class="bg-blue-600 hover:bg-blue-700"
              :disabled="!canCreateRole"
            >
              <Plus class="w-4 h-4 mr-2" />
              Create New Role
            </Button>
          </div>

          <!-- Role Cards Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="role in roles" 
              :key="role.id"
              class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
            >
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">{{ role.name }}</h3>
                  <p class="text-sm text-gray-500">{{ role.users_count || 0 }} users</p>
                </div>
                <div class="flex space-x-2">
                  <Button 
                    v-if="canUpdateRole"
                    @click="openEditModal(role)"
                    variant="outline"
                    size="sm"
                  >
                    <Pencil class="w-4 h-4" />
                  </Button>
                  <Button 
                    v-if="canDeleteRole && role.users_count === 0"
                    @click="openDeleteModal(role)"
                    variant="outline"
                    size="sm"
                    class="text-red-600 hover:text-red-700"
                  >
                    <Trash class="w-4 h-4" />
                  </Button>
                </div>
              </div>

              <!-- Permissions Summary -->
              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Permissions</h4>
                <div class="flex flex-wrap gap-1">
                  <span 
                    v-for="permission in role.permissions.slice(0, 5)" 
                    :key="permission.id"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                  >
                    {{ formatPermission(permission.name) }}
                  </span>
                  <span 
                    v-if="role.permissions.length > 5"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                  >
                    +{{ role.permissions.length - 5 }} more
                  </span>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex space-x-2">
                <Button 
                  @click="openPermissionsModal(role)"
                  variant="outline"
                  size="sm"
                  class="flex-1"
                >
                  Manage Permissions
                </Button>
                <Button 
                  @click="openUsersModal(role)"
                  variant="outline"
                  size="sm"
                  class="flex-1"
                >
                  View Users
                </Button>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="roles.length === 0" class="text-center py-12">
            <div class="text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No roles yet</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by creating your first role.</p>
              <div class="mt-6">
                <Button 
                  @click="openCreateModal"
                  class="bg-blue-600 hover:bg-blue-700"
                  :disabled="!canCreateRole"
                >
                  <Plus class="w-4 h-4 mr-2" />
                  Create Role
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Role Modal -->
    <RoleModal 
      v-if="showModal"
      :role="editingRole"
      :permissions="allPermissions"
      @close="closeModal"
      @saved="onRoleSaved"
    />

    <!-- Permissions Modal -->
    <PermissionsModal 
      v-if="showPermissionsModal"
      :role="selectedRole"
      :permissions="allPermissions"
      @close="closePermissionsModal"
      @saved="onPermissionsSaved"
    />

    <!-- Users Modal -->
    <UsersModal 
      v-if="showUsersModal"
      :role="selectedRole"
      @close="closeUsersModal"
    />

    <!-- Delete Confirmation Modal -->
    <DeleteModal 
      v-if="showDeleteModal"
      :item="deletingRole"
      title="Delete Role"
      message="Are you sure you want to delete this role? This action cannot be undone."
      @close="closeDeleteModal"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Trash } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { permissionHelpers } from '@/composables/permissionHelpers';
import RoleModal from './RoleModal.vue';
import PermissionsModal from './PermissionsModal.vue';
import UsersModal from './UsersModal.vue';
import DeleteModal from '@/components/DeleteModal.vue';

defineOptions({
  layout: AppLayout
});

const { can } = permissionHelpers();

// Permission checks
const canCreateRole = can('create-role');
const canUpdateRole = can('update-role');
const canDeleteRole = can('delete-role');

// Props
const props = defineProps({
  roles: {
    type: Array,
    default: () => []
  },
  permissions: {
    type: Array,
    default: () => []
  }
});

// Reactive state
const showModal = ref(false);
const showPermissionsModal = ref(false);
const showUsersModal = ref(false);
const showDeleteModal = ref(false);
const editingRole = ref(null);
const selectedRole = ref(null);
const deletingRole = ref(null);

// Computed
const allPermissions = computed(() => props.permissions || []);
const roles = computed(() => props.roles || []);

// Methods
function openCreateModal() {
  editingRole.value = null;
  showModal.value = true;
}

function openEditModal(role) {
  editingRole.value = role;
  showModal.value = true;
}

function openPermissionsModal(role) {
  selectedRole.value = role;
  showPermissionsModal.value = true;
}

function openUsersModal(role) {
  selectedRole.value = role;
  showUsersModal.value = true;
}

function openDeleteModal(role) {
  deletingRole.value = role;
  showDeleteModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingRole.value = null;
}

function closePermissionsModal() {
  showPermissionsModal.value = false;
  selectedRole.value = null;
}

function closeUsersModal() {
  showUsersModal.value = false;
  selectedRole.value = null;
}

function closeDeleteModal() {
  showDeleteModal.value = false;
  deletingRole.value = null;
}

function onRoleSaved() {
  closeModal();
  // Refresh the page to get updated data
  window.location.reload();
}

function onPermissionsSaved() {
  closePermissionsModal();
  // Refresh the page to get updated data
  window.location.reload();
}

function confirmDelete() {
  if (deletingRole.value) {
    // Delete the role
    router.delete(route('roles.destroy', deletingRole.value.id), {
      onSuccess: () => {
        closeDeleteModal();
        // Refresh the page to get updated data
        window.location.reload();
      }
    });
  }
}

function formatPermission(permissionName) {
  // Convert "create-member" to "Create Member"
  return permissionName
    .split('-')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}
</script>
