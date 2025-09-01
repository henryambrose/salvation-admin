<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-700">Cemeteries Management</h1>
            <Link 
              :href="'/graveyard/cemeteries/create'"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              <Plus class="w-4 h-4 mr-2" />
              Add Cemetery
            </Link>
          </div>

          <!-- Filters -->
          <div class="bg-blue-50 p-6 rounded-2xl border shadow-xl mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <!-- Search -->
              <div class="relative">
                <input
                  v-model="filters.search"
                  type="text"
                  placeholder="Search cemeteries..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  @input="debouncedSearch"
                />
                <Search class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                <button
                  v-if="filters.search"
                  @click="clearSearch"
                  class="absolute right-3 top-2.5 h-5 w-5 text-gray-400 hover:text-gray-600"
                >
                  <X class="h-5 w-5" />
                </button>
              </div>

              <!-- Status Filter -->
              <div>
                <select
                  v-model="filters.status"
                  class="w-full px-3 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                  @change="applyFilters"
                >
                  <option value="">All Statuses</option>
                  <option v-for="status in filterOptions.statuses" :key="status" :value="status">
                    {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                  </option>
                </select>
              </div>

              <!-- Per Page -->
              <div>
                <select
                  v-model="filters.perPage"
                  class="w-full px-3 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                  @change="applyFilters"
                >
                  <option value="10">10 per page</option>
                  <option value="25">25 per page</option>
                  <option value="50">50 per page</option>
                  <option value="100">100 per page</option>
                </select>
              </div>
            </div>

            <!-- Archive Toggle -->
            <div class="mt-4 flex items-center">
              <label class="flex items-center cursor-pointer">
                <input
                  v-model="filters.isArchived"
                  type="checkbox"
                  class="sr-only"
                  @change="applyFilters"
                />
                <div class="relative">
                  <div class="block w-14 h-8 rounded-full" :class="filters.isArchived ? 'bg-red-500' : 'bg-blue-500'"></div>
                  <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 ease-in-out" :class="filters.isArchived ? 'transform translate-x-6' : ''"></div>
                </div>
                <span class="ml-3 text-sm font-medium text-gray-700">
                  {{ filters.isArchived ? 'Show Archived' : 'Show Active' }}
                </span>
              </label>
            </div>
          </div>

          <!-- Data Table -->
          <div class="bg-white rounded-2xl border shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="toggleSort('name')">
                      <div class="flex items-center">
                        Name
                        <ChevronUp v-if="filters.sort === 'name' && filters.direction === 'asc'" class="w-4 h-4 ml-1" />
                        <ChevronDown v-else-if="filters.sort === 'name' && filters.direction === 'desc'" class="w-4 h-4 ml-1" />
                        <ChevronsUpDown v-else class="w-4 h-4 ml-1 text-gray-300" />
                      </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="toggleSort('location')">
                      <div class="flex items-center">
                        Location
                        <ChevronUp v-if="filters.sort === 'location' && filters.direction === 'asc'" class="w-4 h-4 ml-1" />
                        <ChevronDown v-else-if="filters.sort === 'location' && filters.direction === 'desc'" class="w-4 h-4 ml-1" />
                        <ChevronsUpDown v-else class="w-4 h-4 ml-1 text-gray-300" />
                      </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="toggleSort('total_graves')">
                      <div class="flex items-center">
                        Total Graves
                        <ChevronUp v-if="filters.sort === 'total_graves' && filters.direction === 'asc'" class="w-4 h-4 ml-1" />
                        <ChevronDown v-else-if="filters.sort === 'total_graves' && filters.direction === 'desc'" class="w-4 h-4 ml-1" />
                        <ChevronsUpDown v-else class="w-4 h-4 ml-1 text-gray-300" />
                      </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="toggleSort('status')">
                      <div class="flex items-center">
                        Status
                        <ChevronUp v-if="filters.sort === 'status' && filters.direction === 'asc'" class="w-4 h-4 ml-1" />
                        <ChevronDown v-else-if="filters.sort === 'status' && filters.direction === 'desc'" class="w-4 h-4 ml-1" />
                        <ChevronsUpDown v-else class="w-4 h-4 ml-1 text-gray-300" />
                      </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr 
                    v-for="cemetery in cemeteries.data" 
                    :key="cemetery.id"
                    class="hover:bg-blue-50 transition-colors duration-200"
                    :class="{ 'bg-yellow-50': highlightedRowId === cemetery.id }"
                  >
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ cemetery.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ cemetery.location || '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ cemetery.total_graves || 0 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(cemetery.status)">
                        {{ cemetery.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex space-x-2">
                        <Link
                          :href="'/graveyard/cemeteries/' + cemetery.id"
                          class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-full text-xs font-medium transition-colors duration-200"
                        >
                          View
                        </Link>
                        <Link
                          :href="'/graveyard/cemeteries/' + cemetery.id + '/edit'"
                          class="text-yellow-700 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 px-3 py-1 rounded-full text-xs font-medium transition-colors duration-200"
                        >
                          Edit
                        </Link>
                        <button
                          v-if="!filters.isArchived"
                          @click="deleteCemetery(cemetery)"
                          class="text-red-700 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-full text-xs font-medium transition-colors duration-200"
                        >
                          Delete
                        </button>
                        <button
                          v-else
                          @click="restoreCemetery(cemetery.id)"
                          class="text-green-800 hover:text-green-900 bg-green-100 hover:bg-green-200 px-3 py-1 rounded-full text-xs font-medium transition-colors duration-200"
                        >
                          Restore
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="cemeteries.links && cemeteries.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
              <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                  <Link
                    v-if="cemeteries.prev_page_url"
                    :href="cemeteries.prev_page_url"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                  >
                    Previous
                  </Link>
                  <Link
                    v-if="cemeteries.next_page_url"
                    :href="cemeteries.next_page_url"
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                  >
                    Next
                  </Link>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                  <div>
                    <p class="text-sm text-gray-700">
                      Showing
                      <span class="font-medium">{{ cemeteries.from }}</span>
                      to
                      <span class="font-medium">{{ cemeteries.to }}</span>
                      of
                      <span class="font-medium">{{ cemeteries.total }}</span>
                      results
                    </p>
                  </div>
                  <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                      <Link
                        v-for="(link, index) in cemeteries.links"
                        :key="index"
                        :href="link.url"
                        v-html="link.label"
                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                        :class="[
                          link.url === null
                            ? 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed'
                            : link.active
                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                        ]"
                      />
                    </nav>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <AlertTriangle class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Cemetery</h3>
            </div>
          </div>
          <div class="mt-2">
            <p class="text-sm text-gray-500">
              Are you sure you want to delete this cemetery? This action cannot be undone.
            </p>
          </div>
          <div class="mt-4 flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              Cancel
            </button>
            <button
              @click="confirmDelete"
              class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Restore Confirmation Modal -->
    <div v-if="showRestoreModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
              <RefreshCw class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Restore Cemetery</h3>
            </div>
          </div>
          <div class="mt-2">
            <p class="text-sm text-gray-500">
              Are you sure you want to restore this cemetery?
            </p>
          </div>
          <div class="mt-4 flex justify-end space-x-3">
            <button
              @click="showRestoreModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              Cancel
            </button>
            <button
              @click="confirmRestore"
              class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
              Restore
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { Pencil, Trash, RotateCcw, Plus, ChevronUp, ChevronDown, ChevronsUpDown, AlertTriangle, RefreshCw } from 'lucide-vue-next';
import { computed, ref, watch, nextTick } from 'vue';

interface Props {
  cemeteries: any;
  filters: any;
  fetchUrl: string;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);
const showRestoreModal = ref(false);
const cemeteryToDelete = ref<any>(null);
const cemeteryToRestore = ref<any>(null);
const highlightedRowId = ref<number | null>(null);

const filters = ref({ ...props.filters });
const searchTimeout = ref<number | null>(null);

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`cemetery-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

// Debounced search
const debouncedSearch = () => {
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  searchTimeout.value = window.setTimeout(() => {
    applyFilters();
  }, 300);
};

const clearSearch = () => {
  filters.value.search = '';
  applyFilters();
};

const applyFilters = () => {
  router.get(props.fetchUrl, filters.value, {
    preserveState: true,
    preserveScroll: true,
    only: ['cemeteries', 'filters'],
  });
};

const toggleSort = (column: string) => {
  if (filters.value.sort === column) {
    filters.value.direction = filters.value.direction === 'asc' ? 'desc' : 'asc';
  } else {
    filters.value.sort = column;
    filters.value.direction = 'asc';
  }
  applyFilters();
};

const deleteCemetery = (cemetery: any) => {
  cemeteryToDelete.value = cemetery;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (cemeteryToDelete.value) {
    router.delete('/graveyard/cemeteries/' + cemeteryToDelete.value.id, {
      preserveScroll: true,
      only: ['cemeteries', 'filters'],
      onSuccess: () => {
        showDeleteModal.value = false;
        cemeteryToDelete.value = null;
        highlightedRowId.value = cemeteryToDelete.value?.id + 1;
        nextTick(() => scrollToRow(cemeteryToDelete.value?.id + 1));
      },
    });
  }
};

const restoreCemetery = (id: number) => {
  cemeteryToRestore.value = { id };
  showRestoreModal.value = true;
};

const confirmRestore = () => {
  if (cemeteryToRestore.value) {
    router.post('/graveyard/cemeteries/' + cemeteryToRestore.value.id + '/restore', {}, {
      preserveScroll: true,
      only: ['cemeteries', 'filters'],
      onSuccess: () => {
        showRestoreModal.value = false;
        cemeteryToRestore.value = null;
        highlightedRowId.value = cemeteryToRestore.value?.id;
        nextTick(() => scrollToRow(cemeteryToRestore.value?.id));
      },
    });
  }
};

const getStatusClass = (status: string) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-red-100 text-red-800',
    maintenance: 'bg-gray-100 text-gray-800',
  };
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const enhancedCemeteries = computed(() => {
  const data = props.cemeteries || {};
  return {
    data: data.data || [],
    prev_page_url: data.prev_page_url,
    next_page_url: data.next_page_url,
    current_page: data.current_page,
    last_page: data.last_page,
    total: data.total,
    from: data.from,
    to: data.to,
  };
});

function handlePageChange(event: Event) {
  const target = event.target as HTMLSelectElement;
  if (target) {
    const page = Number(target.value);
    router.get(props.fetchUrl, { ...filters.value, page }, {
      preserveState: true,
      preserveScroll: true,
      only: ['cemeteries', 'filters'],
    });
  }
}

const breadcrumbs = [
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Cemeteries', href: '/graveyard/cemeteries' }
];

// Watch for changes in props
watch(() => props.filters, (newFilters) => {
  filters.value = { ...newFilters };
}, { deep: true });
</script>

<style scoped>
.dot {
  transition: transform 0.3s ease-in-out;
}
</style>
