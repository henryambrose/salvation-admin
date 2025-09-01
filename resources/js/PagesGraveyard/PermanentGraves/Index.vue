<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Permanent Graves" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Permanent Graves</h2>
        <Button @click="router.visit('/graveyard/permanent-graves/create')" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
          <Plus class="w-[1rem] h-[1rem]" />
          <span>Add Permanent Grave</span>
        </Button>
      </div>
      <div class="mb-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative">
            <input v-model="filters.search" @keyup.enter="applyFilters()" type="text" class="rounded-full border border-gray-300 px-3 py-1 pr-8 focus:ring-2 focus:ring-blue-200" placeholder="Search..." @keydown.escape="clearSearch" />
            <button v-if="filters.search" @click="clearSearch" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">✕</button>
          </div>
          <select v-model="filters.perPage" @change="applyFilters()" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>
        <div class="flex items-center gap-4">
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <Checkbox v-model="isArchived" class="switch-checkbox" />
            <span class="text-sm font-medium">Show Archived</span>
          </label>
        </div>
      </div>
    </DatatableHeader>
    
    <div class="mt-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
      <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="w-full border-collapse text-left">
          <thead>
            <tr class="bg-blue-50">
              <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
              <th class="border-b p-3 font-semibold text-gray-700 cursor-pointer" @click="toggleSort('section')">
                <div class="flex items-center">
                  Section
                  <ChevronUp v-if="filters.sort === 'section' && filters.direction === 'asc'" class="w-4 h-4 ml-1" />
                  <ChevronDown v-else-if="filters.sort === 'section' && filters.direction === 'desc'" class="w-4 h-4 ml-1" />
                  <ChevronsUpDown v-else class="w-4 h-4 ml-1 text-gray-300" />
                </div>
              </th>
              <th class="border-b p-3 font-semibold text-gray-700 cursor-pointer" @click="toggleSort('row_no')">
                <div class="flex items-center">
                  Row
                  <ChevronUp v-if="filters.sort === 'row_no' && filters.direction === 'asc'" class="w-4 h-4 ml-1" />
                  <ChevronDown v-else-if="filters.sort === 'row_no' && filters.direction === 'desc'" class="w-4 h-4 ml-1" />
                  <ChevronsUpDown v-else class="w-4 h-4 ml-1 text-gray-300" />
                </div>
              </th>
              <th class="border-b p-3 font-semibold text-gray-700 cursor-pointer" @click="toggleSort('grave_no')">
                <div class="flex items-center">
                  Grave No
                  <ChevronUp v-if="filters.sort === 'grave_no' && filters.direction === 'asc'" class="w-4 h-4 ml-1" />
                  <ChevronDown v-else-if="filters.sort === 'grave_no' && filters.direction === 'desc'" class="w-4 h-4 ml-1" />
                  <ChevronsUpDown v-else class="w-4 h-4 ml-1 text-gray-300" />
                </div>
              </th>
              <th class="border-b p-3 font-semibold text-gray-700 cursor-pointer" @click="toggleSort('status')">
                <div class="flex items-center">
                  Status
                  <ChevronUp v-if="filters.sort === 'status' && filters.direction === 'asc'" class="w-4 h-4 ml-1" />
                  <ChevronDown v-else-if="filters.sort === 'status' && filters.direction === 'desc'" class="w-4 h-4 ml-1" />
                  <ChevronsUpDown v-else class="w-4 h-4 ml-1 text-gray-300" />
                </div>
              </th>
              <th class="border-b p-3 font-semibold text-gray-700">Owner Name</th>
              <th class="border-b p-3 font-semibold text-gray-700">Plot Size</th>
              <th class="border-b p-3 font-semibold text-gray-700">Last Burial</th>
              <th v-if="!serverArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="grave in props.data?.data" :key="grave.id" :id="`grave-row-${grave.id}`" :class="['even:bg-gray-50 hover:bg-blue-50 transition', highlightedRowId === grave.id ? 'highlight-row' : '']">
              <td class="p-2">
                <div class="flex items-center gap-2">
                  <template v-if="!serverArchived">
                    <Button @click="router.visit('/graveyard/permanent-graves/' + grave.id + '/edit')" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition p-2">
                      <Pencil class="w-[1rem] h-[1rem]" />
                    </Button>
                  </template>
                  <template v-else>
                    <Button @click="restoreGrave(grave.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition p-2">
                      <RotateCcw class="w-[1rem] h-[1rem]" />
                    </Button>
                  </template>
                </div>
              </td>
              <td class="p-2">{{ grave.section }}</td>
              <td class="p-2">{{ grave.row_no }}</td>
              <td class="p-2">{{ grave.grave_no }}</td>
              <td class="p-2">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(grave.status)">
                  {{ grave.status.charAt(0).toUpperCase() + grave.status.slice(1) }}
                </span>
              </td>
              <td class="p-2">{{ grave.owner_name || '-' }}</td>
              <td class="p-2">{{ grave.plot_size ? `${grave.plot_size} sq ft` : '-' }}</td>
              <td class="p-2">{{ grave.last_burial_date ? formatDate(grave.last_burial_date) : '-' }}</td>
              <td v-if="!serverArchived" class="p-2">
                <Button @click="deleteGrave(grave)" variant="destructive" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition p-2">
                  <X class="w-[1rem] h-[1rem]" />
                </Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Delete Modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="showDeleteModal = false"></div>
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg relative z-10">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Permanent Grave</h3>
            <p>Are you sure you want to delete this permanent grave <span class="font-bold">{{ graveToDelete?.section }}-{{ graveToDelete?.grave_no }}</span>?</p>
            <div class="mt-6 flex justify-end space-x-2">
              <Button variant="secondary" type="button" @click="showDeleteModal = false" class="rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition px-6 py-2">
                Cancel
              </Button>
              <Button variant="destructive" type="button" @click="confirmDelete" class="rounded-full bg-red-600 text-white shadow hover:bg-red-700 transition px-6 py-2 flex items-center gap-2">
                Delete
              </Button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </AppLayout>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { Pencil, Trash, RotateCcw, Plus, ChevronUp, ChevronDown, ChevronsUpDown, AlertTriangle, X } from 'lucide-vue-next';
import { ref, computed, onMounted, watch } from 'vue';

interface Props {
  data: any;
  filters: any;
  filterOptions: any;
  fetchUrl: string;
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Permanent Graves', href: '/graveyard/permanent-graves' }
];

// Reactive state
const filters = ref({ ...props.filters });
const highlightedRowId = ref<number | null>(null);
const showDeleteModal = ref(false);
const graveToDelete = ref<any>(null);
const isArchived = ref(String(props.filters?.isArchived) === 'true');
const serverArchived = computed(() => String(props.filters?.isArchived) === 'true');

// Debounced search
let searchTimeout: number;
const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    applyFilters();
  }, 300);
};

const clearSearch = () => {
  filters.value.search = '';
  applyFilters();
};

const applyFilters = () => {
  router.get(props.fetchUrl, { ...filters.value, isArchived: isArchived.value ? 'true' : 'false' }, {
    preserveState: true,
    preserveScroll: true,
    only: ['data', 'filters'],
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

const deleteGrave = (grave: any) => {
  graveToDelete.value = grave;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (graveToDelete.value) {
    router.delete('/graveyard/permanent-graves/' + graveToDelete.value.id, {
      onSuccess: () => {
        showDeleteModal.value = false;
        graveToDelete.value = null;
        highlightRow(graveToDelete.value?.id);
      },
    });
  }
};

const restoreGrave = (id: number) => {
  router.post('/graveyard/permanent-graves/' + id + '/restore', {}, {
    preserveScroll: true,
    only: ['data', 'filters'],
    onSuccess: () => {
      isArchived.value = false;
      highlightRow(id);
    },
  });
};

const highlightRow = (id: number) => {
  highlightedRowId.value = id;
  setTimeout(() => {
    highlightedRowId.value = null;
  }, 3000);
};

const getStatusClass = (status: string) => {
  const classes = {
    available: 'bg-green-100 text-green-800',
    occupied: 'bg-red-100 text-red-800',
    reserved: 'bg-yellow-100 text-yellow-800',
    maintenance: 'bg-gray-100 text-gray-800',
  };
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-IN');
};

// Watch for changes in props
watch(() => props.filters, (newFilters) => {
  filters.value = { ...newFilters };
  isArchived.value = String(newFilters?.isArchived) === 'true';
}, { deep: true });

// Watch for isArchived changes
watch(isArchived, () => {
  applyFilters();
});

onMounted(() => {
  // Any initialization logic
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.switch-checkbox {
  width: 2.5rem;
  height: 1.25rem;
  border-radius: 9999px;
  background: #ef4444;
  box-shadow: 0 2px 8px 0 rgba(239, 68, 68, 0.25), 0 1.5px 4px 0 rgba(0,0,0,0.10);
  position: relative;
  transition: background 0.2s, box-shadow 0.2s;
}
.switch-checkbox[data-state="checked"] {
  background: #2563eb;
}
.switch-checkbox input[type="checkbox"] {
  opacity: 0;
  width: 100%;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
  margin: 0;
  cursor: pointer;
}
.switch-checkbox [data-slot="checkbox-indicator"] {
  position: absolute;
  left: 0.125rem;
  top: 0.125rem;
  width: 1rem;
  height: 1rem;
  border-radius: 9999px;
  background: #fff;
  transition: left 0.2s;
}
.switch-checkbox[data-state="checked"] [data-slot="checkbox-indicator"] {
  left: 1.375rem;
}
.highlight-row {
  animation: highlight-fade 2s;
  background-color: #fef08a !important;
}
@keyframes highlight-fade {
  0% { background-color: #fde047; }
  100% { background-color: inherit; }
}
</style>
