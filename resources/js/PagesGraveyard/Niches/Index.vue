<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Niches" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Niches</h2>
        <Button
          v-if="canCreateNiche"
          @click="router.visit('/graveyard/niches/create')"
          class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
        >
          <Plus class="h-[1rem] w-[1rem]" />
          <span>Add Niche</span>
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
            v-model="filters.status"
            @change="applyFilters()"
            class="min-w-[120px] rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200"
          >
            <option value="">All Status</option>
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
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
        Showing <span class="font-semibold">{{ data?.total || 0 }}</span> total niches
        <span v-if="filters.search" class="text-blue-600">for "{{ filters.search }}"</span>
        <span v-if="filters.status" class="text-green-600">with status "{{ filters.status }}"</span>
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
        <span class="rounded-full bg-teal-100 px-2 py-1 text-xs font-medium text-teal-800"> Niches </span>
      </div>
    </div>

    <div v-if="canReadAnyNiche">
      <div class="mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
        <div class="overflow-x-auto rounded-xl border border-gray-100">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr class="bg-blue-50">
                <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
                <th class="cursor-pointer border-b p-3 font-semibold text-gray-700" @click="toggleSort('niche_no')">
                  <div class="flex items-center">
                    Niche No
                    <ChevronUp v-if="filters.sort === 'niche_no' && filters.direction === 'asc'" class="ml-1 h-4 w-4" />
                    <ChevronDown v-else-if="filters.sort === 'niche_no' && filters.direction === 'desc'" class="ml-1 h-4 w-4" />
                    <ChevronsUpDown v-else class="ml-1 h-4 w-4 text-gray-300" />
                  </div>
                </th>
                <th class="cursor-pointer border-b p-3 font-semibold text-gray-700" @click="toggleSort('sr_no')">
                  <div class="flex items-center">
                    Sr No
                    <ChevronUp v-if="filters.sort === 'sr_no' && filters.direction === 'asc'" class="ml-1 h-4 w-4" />
                    <ChevronDown v-else-if="filters.sort === 'sr_no' && filters.direction === 'desc'" class="ml-1 h-4 w-4" />
                    <ChevronsUpDown v-else class="ml-1 h-4 w-4 text-gray-300" />
                  </div>
                </th>
                <th class="cursor-pointer border-b p-3 font-semibold text-gray-700" @click="toggleSort('location')">
                  <div class="flex items-center">
                    Location
                    <ChevronUp v-if="filters.sort === 'location' && filters.direction === 'asc'" class="ml-1 h-4 w-4" />
                    <ChevronDown v-else-if="filters.sort === 'location' && filters.direction === 'desc'" class="ml-1 h-4 w-4" />
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
                <th class="border-b p-3 font-semibold text-gray-700">Owner Name</th>
                <th class="border-b p-3 font-semibold text-gray-700">Dimensions</th>
                <th class="border-b p-3 font-semibold text-gray-700">Last Occupation</th>
                <th v-if="!serverArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="niche in props.data?.data"
                :key="niche.id"
                :id="`niche-row-${niche.id}`"
                :class="['transition even:bg-gray-50 hover:bg-blue-50', highlightedRowId === niche.id ? 'highlight-row' : '']"
              >
                <td class="p-2">
                  <div class="flex items-center gap-2">
                    <template v-if="!serverArchived">
                      <Button
                        v-if="canReadAnyNiche"
                        @click="router.visit('/graveyard/niches/' + niche.id)"
                        class="rounded-full bg-blue-100 p-2 text-blue-700 transition hover:bg-blue-200"
                        title="View niche details"
                      >
                        <Eye class="h-[1rem] w-[1rem]" />
                      </Button>
                      <Button
                        v-if="canUpdateAnyNiche"
                        @click="router.visit('/graveyard/niches/' + niche.id + '/edit')"
                        class="rounded-full bg-yellow-100 p-2 text-yellow-700 transition hover:bg-yellow-200"
                        title="Edit niche"
                      >
                        <Pencil class="h-[1rem] w-[1rem]" />
                      </Button>
                    </template>
                    <template v-else>
                      <Button
                        v-if="canRestoreNiche"
                        @click="restoreNiche(niche.id)"
                        class="rounded-full bg-green-100 p-2 text-green-700 transition hover:bg-green-200"
                      >
                        <RotateCcw class="h-[1rem] w-[1rem]" />
                      </Button>
                    </template>
                  </div>
                </td>
                <td class="p-6">{{ niche.niche_no }}</td>
                <td class="p-6">{{ niche.sr_no }}</td>
                <td class="p-6">{{ niche.location }}</td>
                <td class="p-2">
                  <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold" :class="getStatusClass(niche.status)">
                    {{ niche.status.charAt(0).toUpperCase() + niche.status.slice(1) }}
                  </span>
                </td>
                <td class="p-2">{{ niche.owner_name || (niche.member ? niche.member.first_name + ' ' + niche.member.last_name : '-') }}</td>
                <td class="p-2">{{ getDimensions(niche) }}</td>
                <td class="p-2">{{ niche.last_occupation_date ? formatDate(niche.last_occupation_date) : '-' }}</td>
                <td v-if="!serverArchived" class="p-2">
                  <template v-if="canDeleteAnyNiche">
                    <Button
                      @click="deleteNiche(niche)"
                      variant="destructive"
                      class="rounded-full bg-red-100 p-2 text-red-700 transition hover:bg-red-200"
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
    <div v-else class="py-10 text-center text-gray-500">You do not have permission to view niches.</div>

    <!-- Delete Modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="bg-opacity-50 absolute inset-0 bg-black" @click="showDeleteModal = false"></div>
        <div class="from-grey-900 via-grey-800 to-grey-600 relative z-10 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Niche</h3>
            <p>
              Are you sure you want to delete this niche
              <span class="font-bold">N{{ nicheToDelete?.niche_no }}-{{ nicheToDelete?.sr_no }}</span
              >?
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
import { ChevronDown, ChevronsUpDown, ChevronUp, Eye, Pencil, Plus, RotateCcw, Trash2 } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

const { can } = permissionHelpers();

// Permission checks - using generic permissions for now
const canCreateNiche = can('create-niche') || can('create-permanent-grave');
const canReadAnyNiche = can('read-niche') || can('read-permanent-grave');
const canUpdateAnyNiche = can('update-niche') || can('update-permanent-grave');
const canDeleteAnyNiche = can('delete-niche') || can('delete-permanent-grave');
const canRestoreNiche = can('restore-niche') || can('restore-permanent-grave');

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
  { title: 'Niches', href: '/graveyard/niches' },
];

// Reactive state
const filters = ref({
  search: '',
  status: '',
  perPage: 10,
  sort: 'location',
  direction: 'asc',
  ...(props.filters || {}),
});
const highlightedRowId = ref<number | null>(null);
const showDeleteModal = ref(false);
const nicheToDelete = ref<any>(null);
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

const deleteNiche = (niche: any) => {
  nicheToDelete.value = niche;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (nicheToDelete.value) {
    router.delete('/graveyard/niches/' + nicheToDelete.value.id, {
      onSuccess: () => {
        showDeleteModal.value = false;
        nicheToDelete.value = null;
        highlightRow(nicheToDelete.value?.id);
      },
    });
  }
};

const restoreNiche = (id: number) => {
  router.post(
    '/graveyard/niches/' + id + '/restore',
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
    unavailable: 'bg-red-100 text-red-800',
  };
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const getDimensions = (niche: any) => {
  if (niche.size_width && niche.size_height && niche.size_depth) {
    return `${niche.size_width}" × ${niche.size_height}" × ${niche.size_depth}"`;
  }
  return '-';
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

// Watch for search changes with debounce
watch(
  () => filters.value.search,
  () => {
    debouncedSearch();
  },
);

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
