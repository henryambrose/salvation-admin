<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-[#ffffff] overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Mass Types</h1>
            <button 
              @click="openCreateModal"
              class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              <Plus class="w-[1rem] h-[1rem] mr-2" />
              Add New Mass Type
            </button>
          </div>

          <!-- Show Archived Toggle -->
          <div class="mb-6">
            <label class="flex items-center">
              <input 
                type="checkbox" 
                v-model="showArchived"
                @change="toggleArchived"
                class="switch-checkbox"
              />
              <span class="ml-2 text-sm text-gray-700">Show Archived</span>
            </label>
          </div>

          <!-- Search and Filters -->
          <div class="mb-6">
            <div class="flex gap-4">
              <div class="flex-1">
                <input 
                  v-model="searchQuery"
                  type="text" 
                  placeholder="Search mass types..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                />
              </div>
              <div class="flex gap-2">
                <select 
                  v-model="sortBy"
                  @change="updateSorting"
                  class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                >
                  <option value="name">Sort by Name</option>
                  <option value="default_time">Sort by Time</option>
                  <option value="sort_order">Sort by Order</option>
                  <option value="created_at">Sort by Created</option>
                </select>
                <button 
                  @click="toggleSortOrder"
                  class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                >
                  {{ sortOrder === 'asc' ? '↑' : '↓' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Data Table -->
          <div v-if="massTypes.data && massTypes.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default Time</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delete</th>
                </tr>
              </thead>
              <tbody class="bg-[#ffffff] divide-y divide-gray-200">
                <tr v-for="type in massTypes.data" :key="type.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button 
                      @click="openEditModal(type)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      <Pencil class="w-[1rem] h-[1rem]" />
                    </button>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ type.name }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-500">{{ type.description || 'No description' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ formatTime(type.default_time) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">{{ type.sort_order }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="type.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 text-xs font-medium rounded-full">
                      {{ type.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(type.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button 
                      @click="deleteType(type.id)"
                      class="text-red-600 hover:text-red-900"
                    >
                      <Trash class="w-[1rem] h-[1rem]" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ massTypes.from }} to {{ massTypes.to }} of {{ massTypes.total }} results
              </div>
              <div class="flex space-x-2">
                <Link 
                  v-if="massTypes.prev_page_url"
                  :href="massTypes.prev_page_url"
                  class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-[#ffffff] hover:bg-gray-50"
                >
                  Previous
                </Link>
                <Link 
                  v-if="massTypes.next_page_url"
                  :href="massTypes.next_page_url"
                  class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-[#ffffff] hover:bg-gray-50"
                >
                  Next
                </Link>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="bg-gray-50 p-8 rounded-lg text-center">
            <div class="text-gray-500">
              <FolderOpen class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">No mass types found</h3>
              <p class="mt-1 text-sm text-gray-500">
                {{ searchQuery ? 'Try adjusting your search terms.' : 'Get started by creating the first mass type.' }}
              </p>
              <div class="mt-6">
                <button 
                  @click="openCreateModal"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  <Plus class="w-[1rem] h-[1rem] mr-2" />
                  Add Mass Type
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-[#ffffff]">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ editingType ? 'Edit Mass Type' : 'Create Mass Type' }}
          </h3>
          
          <form @submit.prevent="editingType ? updateType() : createType()">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input 
                  v-model="form.name"
                  type="text" 
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea 
                  v-model="form.description"
                  rows="3"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                ></textarea>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Default Time</label>
                <input 
                  v-model="form.default_time"
                  type="time" 
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                <input 
                  v-model="form.sort_order"
                  type="number" 
                  min="0"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                />
              </div>
              
              <div>
                <label class="flex items-center">
                  <input 
                    v-model="form.is_active"
                    type="checkbox" 
                    class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
                  />
                  <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
              </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
              <button 
                type="button"
                @click="closeModal"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-[#ffffff] hover:bg-gray-50"
              >
                Cancel
              </button>
              <button 
                type="submit"
                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700"
              >
                {{ editingType ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Pencil, Trash, FolderOpen } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({
  layout: AppLayout
});

interface Props {
  massTypes: any;
  filters: {
    search?: string;
    sort_by?: string;
    sort_order?: string;
    per_page?: number;
  };
}

const props = defineProps<Props>();

// Local state
const showArchived = ref(false);
const searchQuery = ref(props.filters.search || '');
const sortBy = ref(props.filters.sort_by || 'sort_order');
const sortOrder = ref(props.filters.sort_order || 'asc');
const showModal = ref(false);
const editingType = ref<any>(null);

// Form state
const form = useForm({
  name: '',
  description: '',
  default_time: '',
  sort_order: 0,
  is_active: true,
});

// Watch for search changes and update the page
watch(searchQuery, (newQuery) => {
  router.get(route('fund.mass-types.index'), { search: newQuery }, {
    preserveState: true,
    replace: true
  });
}, { debounce: 300 });

// Methods
function toggleArchived() {
  router.get(route('fund.mass-types.index'), { show_archived: showArchived.value }, {
    preserveState: true,
    replace: true
  });
}

function updateSorting() {
  router.get(route('fund.mass-types.index'), { sort_by: sortBy.value, sort_order: sortOrder.value }, {
    preserveState: true,
    replace: true
  });
}

function toggleSortOrder() {
  sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  updateSorting();
}

function openCreateModal() {
  editingType.value = null;
  form.reset();
  showModal.value = true;
}

function openEditModal(type: any) {
  editingType.value = type;
  form.name = type.name;
  form.description = type.description || '';
  form.default_time = type.default_time;
  form.sort_order = type.sort_order || 0;
  form.is_active = type.is_active;
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingType.value = null;
  form.reset();
}

function createType() {
  form.post(route('fund.mass-types.store'), {
    onSuccess: () => {
      closeModal();
    }
  });
}

function updateType() {
  if (!editingType.value) return;
  
  form.put(route('fund.mass-types.update', editingType.value.id), {
    onSuccess: () => {
      closeModal();
    }
  });
}

function deleteType(id: number) {
  if (confirm('Are you sure you want to delete this mass type?')) {
    router.delete(route('fund.mass-types.destroy', id));
  }
}

function formatDate(dateString: string) {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-IN');
}

function formatTime(timeString: string) {
  if (!timeString) return 'N/A';
  const [hours, minutes] = timeString.split(':');
  const hour = parseInt(hours);
  const ampm = hour >= 12 ? 'PM' : 'AM';
  const displayHour = hour % 12 || 12;
  return `${displayHour}:${minutes} ${ampm}`;
}
</script>

<style scoped>
.switch-checkbox {
  @apply relative inline-flex h-[1.5rem] w-[2.75rem] items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-[#3b82f6] focus:ring-offset-2;
  background-color: #d1d5db;
}

.switch-checkbox:checked {
  background-color: #2563eb;
}

.switch-checkbox::before {
  content: '';
  @apply inline-block h-[1rem] w-[1rem] transform rounded-full bg-[#ffffff] transition-transform;
  margin-left: 2px;
}

.switch-checkbox:checked::before {
  transform: translateX(20px);
}
</style>
