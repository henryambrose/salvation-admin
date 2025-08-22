<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-lg font-medium text-gray-900">
              Edit Roles for "{{ user?.name }}"
            </h3>
            <p class="text-sm text-gray-500 mt-1">
              Manage which roles this user should have
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

        <!-- User Info -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <span class="text-blue-600 font-medium text-lg">
                {{ user?.name?.charAt(0)?.toUpperCase() }}
              </span>
            </div>
            <div>
              <h4 class="text-lg font-medium text-gray-900">{{ user?.name }}</h4>
              <p class="text-sm text-gray-500">{{ user?.email }}</p>
              <div class="flex items-center space-x-2 mt-1">
                <span 
                  v-if="user?.is_superadmin"
                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                >
                  Super Admin
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Roles Selection -->
        <form @submit.prevent="submitForm">
          <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Select Roles</h4>
            <div class="space-y-3">
              <label 
                v-for="role in allRoles" 
                :key="role.id"
                class="flex items-center space-x-3 p-3 rounded border border-gray-200 hover:bg-gray-50"
              >
                <input
                  type="checkbox"
                  :value="role.id"
                  v-model="selectedRoles"
                  :disabled="role.name === 'super admin' && user?.is_superadmin"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <div class="flex-1">
                  <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-900">
                      {{ role.name }}
                    </span>
                    <span 
                      v-if="role.name === 'super admin'"
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                    >
                      System Role
                    </span>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ role.permissions?.length || 0 }} permissions
                  </p>
                  <div class="flex flex-wrap gap-1 mt-2">
                    <span 
                      v-for="permission in role.permissions?.slice(0, 3)" 
                      :key="permission.id"
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      {{ formatPermission(permission.name) }}
                    </span>
                    <span 
                      v-if="role.permissions?.length > 3"
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                    >
                      +{{ role.permissions.length - 3 }} more
                    </span>
                  </div>
                </div>
              </label>
            </div>
          </div>

          <!-- Summary -->
          <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-blue-900">
                  Selected Roles: {{ selectedRoles.length }}
                </p>
                <p class="text-xs text-blue-700 mt-1">
                  Total Available: {{ allRoles.length }}
                </p>
              </div>
              <div class="text-right">
                <p class="text-sm font-medium text-blue-900">
                  {{ Math.round((selectedRoles.length / allRoles.length) * 100) }}% Selected
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
              Save Roles
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
  user: {
    type: Object,
    default: null
  },
  allRoles: {
    type: Array,
    default: () => []
  }
});

// Emits
const emit = defineEmits(['close', 'saved']);

// Form
const form = useForm({
  roles: []
});

// Local state
const selectedRoles = ref([]);

// Computed
const allRoles = computed(() => props.allRoles || []);

// Methods
function formatPermission(permissionName) {
  // Convert "create-member" to "Create Member"
  return permissionName
    .split('-')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}

function submitForm() {
  form.roles = selectedRoles.value;
  form.put(route('users.update', props.user.id), {
    onSuccess: () => emit('saved')
  });
}

// Watch for user changes and populate selected roles
watch(() => props.user, (newUser) => {
  if (newUser) {
    selectedRoles.value = newUser.roles?.map(r => r.id) || [];
  } else {
    selectedRoles.value = [];
  }
}, { immediate: true });
</script>
