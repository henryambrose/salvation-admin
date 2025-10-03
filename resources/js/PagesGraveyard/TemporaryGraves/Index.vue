<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Temporary Graves" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Temporary Graves</h2>
        <Button
          v-if="canCreateGrave"
          @click="router.visit('/graveyard/temporary-graves/create')"
          class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
        >
          <Plus class="h-[1rem] w-[1rem]" />
          <span>Add Temporary Grave</span>
        </Button>
      </div>
      <div class="mb-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative">
            <input
              v-model="filters.search"
              @keyup.enter="applyFilters()"
              type="text"
              class="rounded-full border border-gray-300 px-3 py-1 pr-8 focus:ring-2 focus:ring-blue-200"
              placeholder="Search..."
              @keydown.escape="clearSearch"
            />
            <button
              v-if="filters.search"
              @click="clearSearch"
              class="absolute top-1/2 right-2 -translate-y-1/2 transform text-gray-400 hover:text-gray-600"
            >
              ✕
            </button>
          </div>
          <select
            v-model="filters.grave_category_id"
            @change="applyFilters()"
            class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200"
          >
            <option value="">All Categories</option>
            <option v-for="category in filterOptions?.graveCategories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          <select
            v-model="filters.perPage"
            @change="applyFilters()"
            class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200"
          >
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>
        <div class="flex items-center gap-4">
          <label class="flex cursor-pointer items-center gap-2 select-none">
            <Checkbox v-model="isArchived" class="switch-checkbox" />
            <span class="text-sm font-medium">Show Archived</span>
          </label>
        </div>
      </div>
    </DatatableHeader>

    <!-- Compact pagination with inline stats above the table -->
    <div class="mb-2 flex items-center justify-between gap-3 rounded border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs">
      <!-- Left side: Total records info -->
      <div class="text-gray-600">
        Showing <span class="font-semibold">{{ data?.total || 0 }}</span> total temporary graves
        <span v-if="filters.search" class="text-blue-600">for "{{ filters.search }}"</span>
      </div>

      <!-- Center: Pagination controls -->
      <div class="flex items-center gap-2">
        <button
          v-if="data?.prev_page_url"
          @click="changePage(data.current_page - 1)"
          class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
        >
          ← Prev
        </button>

        <div class="flex items-center gap-1 text-gray-600">
          <span>Page</span>
          <select
            v-if="data?.last_page && data.last_page > 1"
            :value="data?.current_page"
            @change="handlePageChange"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50 focus:border-blue-500 focus:ring-1 focus:ring-[#3b82f6]"
          >
            <option v-for="page in data.last_page" :key="page" :value="page">
              {{ page }}
            </option>
          </select>
          <span>of {{ data?.last_page }}</span>
        </div>

        <button
          v-if="data?.next_page_url"
          @click="changePage(data.current_page + 1)"
          class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
        >
          Next →
        </button>
      </div>

      <!-- Right side: Additional info -->
      <div class="text-gray-500">
        <span class="rounded-full bg-teal-100 px-2 py-1 text-xs font-medium text-teal-800"> Temporary Graves </span>
      </div>
    </div>

    <div v-if="canReadAnyGrave">
      <div class="mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
        <div class="overflow-x-auto rounded-xl border border-gray-100">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr class="bg-blue-50">
                <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
                <th class="cursor-pointer border-b p-3 font-semibold text-gray-700" @click="toggleSort('section')">
                  <div class="flex items-center">
                    Grave Position
                    <ChevronUp v-if="filters.sort === 'section' && filters.direction === 'asc'" class="ml-1 h-4 w-4" />
                    <ChevronDown v-else-if="filters.sort === 'section' && filters.direction === 'desc'" class="ml-1 h-4 w-4" />
                    <ChevronsUpDown v-else class="ml-1 h-4 w-4 text-gray-300" />
                  </div>
                </th>
                <th class="cursor-pointer border-b p-3 font-semibold text-gray-700" @click="toggleSort('oldno')">
                  <div class="flex items-center">
                    Old No
                    <ChevronUp v-if="filters.sort === 'oldno' && filters.direction === 'asc'" class="ml-1 h-4 w-4" />
                    <ChevronDown v-else-if="filters.sort === 'oldno' && filters.direction === 'desc'" class="ml-1 h-4 w-4" />
                    <ChevronsUpDown v-else class="ml-1 h-4 w-4 text-gray-300" />
                  </div>
                </th>
                <th class="cursor-pointer border-b p-3 font-semibold text-gray-700" @click="toggleSort('status')">
                  <div class="flex items-center">
                    Status
                    <ChevronUp v-if="filters.sort === 'status' && filters.direction === 'asc'" class="ml-1 h-4 w-4" />
                    <ChevronDown v-else-if="filters.sort === 'status' && filters.direction === 'desc'" class="ml-1 h-4 w-4" />
                    <ChevronsUpDown v-else class="ml-1 h-4 w-4 text-gray-300" />
                  </div>
                </th>
                <th class="border-b p-3 font-semibold text-gray-700">Category</th>
                <th class="border-b p-3 font-semibold text-gray-700">Owner Name</th>
                <th class="border-b p-3 font-semibold text-gray-700">Plot Size</th>
                <th class="border-b p-3 font-semibold text-gray-700">Last Burial</th>
                <th v-if="!serverArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="grave in props.data?.data"
                :key="grave.id"
                :id="`grave-row-${grave.id}`"
                :class="['transition even:bg-gray-50 hover:bg-blue-50', highlightedRowId === grave.id ? 'highlight-row' : '']"
              >
                <td class="p-2">
                  <div class="flex items-center gap-2">
                    <template v-if="!serverArchived">
                      <Button
                        v-if="canUpdateAnyGrave"
                        @click="router.visit('/graveyard/temporary-graves/' + grave.id + '/edit')"
                        :disabled="grave.status === 'occupied'"
                        :class="[
                          'rounded-full p-2 transition',
                          grave.status === 'occupied'
                            ? 'cursor-not-allowed bg-gray-100 text-gray-400 opacity-50'
                            : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200',
                        ]"
                        :title="grave.status === 'occupied' ? 'Cannot edit occupied grave' : 'Edit grave'"
                      >
                        <Pencil class="h-[1rem] w-[1rem]" />
                      </Button>
                    </template>
                    <template v-else>
                      <Button
                        v-if="canRestoreGrave"
                        @click="restoreGrave(grave.id)"
                        class="rounded-full bg-green-100 p-2 text-green-700 transition hover:bg-green-200"
                      >
                        <RotateCcw class="h-[1rem] w-[1rem]" />
                      </Button>
                    </template>
                  </div>
                </td>
                <td class="p-6">{{ grave.section }}-{{ grave.row_no }}-{{ grave.grave_no }}</td>
                <td class="p-6">{{ grave.oldno }}</td>
                <td class="p-2">
                  <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold" :class="getStatusClass(grave.status)">
                    {{ grave.status.charAt(0).toUpperCase() + grave.status.slice(1) }}
                  </span>
                </td>
                <td class="p-2">{{ grave.grave_category?.name || '-' }}</td>
                <td class="p-2">{{ grave.owner_name || (grave.member ? grave.member.first_name + ' ' + grave.member.last_name : '-') }}</td>
                <td class="p-2">{{ grave.plot_size ? `${grave.plot_size} sq ft` : '-' }}</td>
                <td class="p-2">{{ grave.last_burial_date ? formatDate(grave.last_burial_date) : '-' }}</td>
                <td v-if="!serverArchived" class="p-2">
                  <template v-if="canDeleteAnyGrave">
                    <Button
                      @click="deleteGrave(grave)"
                      variant="destructive"
                      :disabled="grave.status === 'occupied'"
                      :class="[
                        'rounded-full p-2 transition',
                        grave.status === 'occupied'
                          ? 'cursor-not-allowed bg-gray-100 text-gray-400 opacity-50'
                          : 'bg-red-100 text-red-700 hover:bg-red-200',
                      ]"
                      :title="grave.status === 'occupied' ? 'Cannot delete occupied grave' : 'Delete grave'"
                    >
                      <Trash2 class="h-[1rem] w-[1rem]" />
                    </Button>
                  </template>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div v-else class="py-10 text-center text-gray-500">You do not have permission to view temporary graves.</div>

    <!-- Delete Modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="bg-opacity-50 absolute inset-0 bg-black" @click="showDeleteModal = false"></div>
        <div class="from-grey-900 via-grey-800 to-grey-600 relative z-10 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Temporary Grave</h3>
            <p>
              Are you sure you want to delete this temporary grave
              <span class="font-bold">{{ graveToDelete?.section }}-{{ graveToDelete?.row_no }}-{{ graveToDelete?.grave_no }}</span>
              ?
            </p>
            <div class="mt-6 flex justify-end space-x-2">
              <Button
                variant="secondary"
                type="button"
                @click="showDeleteModal = false"
                class="rounded-full bg-gray-100 px-6 py-2 text-gray-700 transition hover:bg-gray-200"
              >
                Cancel
              </Button>
              <Button
                variant="destructive"
                type="button"
                @click="confirmDelete"
                class="flex items-center gap-2 rounded-full bg-red-600 px-6 py-2 text-white shadow transition hover:bg-red-700"
              >
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
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ChevronDown, ChevronsUpDown, ChevronUp, Pencil, Plus, RotateCcw, Trash2 } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

const { can } = permissionHelpers();

// Permission checks
const canCreateGrave = can('create-temporary-grave');
const canReadAnyGrave = can('read-temporary-grave');
const canUpdateAnyGrave = can('update-temporary-grave');
const canDeleteAnyGrave = can('delete-temporary-grave');
const canRestoreGrave = can('restore-temporary-grave');

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
  { title: 'Temporary Graves', href: '/graveyard/temporary-graves' },
];

// Reactive state
const filters = ref({ ...(props.filters || 10) });
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
  router.get(
    props.fetchUrl,
    { ...filters.value, isArchived: isArchived.value ? 'true' : 'false' },
    {
      preserveState: true,
      preserveScroll: true,
      only: ['data', 'filters'],
    },
  );
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
    router.delete('/graveyard/temporary-graves/' + graveToDelete.value.id, {
      onSuccess: () => {
        showDeleteModal.value = false;
        graveToDelete.value = null;
        highlightRow(graveToDelete.value?.id);
      },
    });
  }
};

const restoreGrave = (id: number) => {
  router.post(
    '/graveyard/temporary-graves/' + id + '/restore',
    {},
    {
      preserveScroll: true,
      only: ['data', 'filters'],
      onSuccess: () => {
        isArchived.value = false;
        highlightRow(id);
      },
    },
  );
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

const changePage = (page: number) => {
  router.get(
    props.fetchUrl,
    {
      ...filters.value,
      page,
      isArchived: isArchived.value ? 'true' : 'false',
    },
    {
      preserveState: true,
      preserveScroll: true,
      only: ['data', 'filters'],
    },
  );
};

const handlePageChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  if (target) {
    changePage(Number(target.value));
  }
};

// Watch for changes in props
watch(
  () => props.filters,
  (newFilters) => {
    filters.value = { ...newFilters };
    isArchived.value = String(newFilters?.isArchived) === 'true';
  },
  { deep: true },
);

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
  box-shadow:
    0 2px 8px 0 rgba(239, 68, 68, 0.25),
    0 1.5px 4px 0 rgba(0, 0, 0, 0.1);
  position: relative;
  transition:
    background 0.2s,
    box-shadow 0.2s;
}
.switch-checkbox[data-state='checked'] {
  background: #2563eb;
}
.switch-checkbox input[type='checkbox'] {
  opacity: 0;
  width: 100%;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
  margin: 0;
  cursor: pointer;
}
.switch-checkbox [data-slot='checkbox-indicator'] {
  position: absolute;
  left: 0.125rem;
  top: 0.125rem;
  width: 1rem;
  height: 1rem;
  border-radius: 9999px;
  background: #fff;
  transition: left 0.2s;
}
.switch-checkbox[data-state='checked'] [data-slot='checkbox-indicator'] {
  left: 1.375rem;
}
.highlight-row {
  animation: highlight-fade 2s;
  background-color: #fef08a !important;
}
@keyframes highlight-fade {
  0% {
    background-color: #fde047;
  }
  100% {
    background-color: inherit;
  }
}
</style>
