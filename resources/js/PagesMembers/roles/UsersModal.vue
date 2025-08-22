<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-lg font-medium text-gray-900">
              Users with "{{ role?.name }}" Role
            </h3>
            <p class="text-sm text-gray-500 mt-1">
              {{ role?.users_count || 0 }} users assigned to this role
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

        <!-- Users List -->
        <div class="space-y-4 max-h-96 overflow-y-auto">
          <div 
            v-for="user in roleUsers" 
            :key="user.id"
            class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50"
          >
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 font-medium text-sm">
                  {{ user.name.charAt(0).toUpperCase() }}
                </span>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-900">{{ user.name }}</h4>
                <p class="text-xs text-gray-500">{{ user.email }}</p>
                <div class="flex items-center space-x-2 mt-1">
                  <span 
                    v-if="user.is_superadmin"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                  >
                    Super Admin
                  </span>
                  <span 
                    v-for="role in user.roles" 
                    :key="role.id"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                  >
                    {{ role.name }}
                  </span>
                </div>
              </div>
            </div>
            
            <div class="flex items-center space-x-2">
              <Button
                variant="outline"
                size="sm"
                @click="viewUser(user)"
              >
                View
              </Button>
              <Button
                variant="outline"
                size="sm"
                @click="editUserRoles(user)"
              >
                Edit Roles
              </Button>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="roleUsers.length === 0" class="text-center py-8">
            <div class="text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No users assigned</h3>
              <p class="mt-1 text-sm text-gray-500">
                This role doesn't have any users assigned to it yet.
              </p>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t">
          <Button
            variant="outline"
            @click="$emit('close')"
          >
            Close
          </Button>
          <Button
            @click="assignUsersToRole"
            class="bg-blue-600 hover:bg-blue-700"
          >
            Assign Users
          </Button>
        </div>
      </div>
    </div>

    <!-- Edit User Roles Modal -->
    <EditUserRolesModal 
      v-if="showEditUserRolesModal"
      :user="selectedUser"
      :allRoles="allRoles"
      @close="closeEditUserRolesModal"
      @saved="onUserRolesSaved"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import EditUserRolesModal from './EditUserRolesModal.vue';

// Props
const props = defineProps({
  role: {
    type: Object,
    default: null
  }
});

// Emits
const emit = defineEmits(['close']);

// Local state
const showEditUserRolesModal = ref(false);
const selectedUser = ref(null);
const allRoles = ref([]);

// Computed
const roleUsers = computed(() => {
  if (!props.role || !props.role.users) return [];
  return props.role.users;
});

// Methods
function viewUser(user) {
  // Navigate to user show page
  window.location.href = route('users.show', user.id);
}

function editUserRoles(user) {
  selectedUser.value = user;
  showEditUserRolesModal.value = true;
}

function assignUsersToRole() {
  // Navigate to user management with role filter
  window.location.href = route('users.index', { role: props.role.id });
}

function closeEditUserRolesModal() {
  showEditUserRolesModal.value = false;
  selectedUser.value = null;
}

function onUserRolesSaved() {
  closeEditUserRolesModal();
  // Refresh the page to get updated data
  window.location.reload();
}
</script>
