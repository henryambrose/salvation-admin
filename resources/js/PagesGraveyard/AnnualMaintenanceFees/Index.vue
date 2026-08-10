<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Annual Maintenance Fees" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Annual Maintenance Fees</h2>
        <Button
          @click="router.visit('/graveyard/annual-maintenance-fees/create')"
          class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
        >
          <Plus class="h-[1rem] w-[1rem]" />
          <span>Add Annual Fee</span>
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
            v-model="filters.perPage"
            @change="applyFilters()"
            class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200"
          >
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
          <select
            v-model="filters.year"
            @change="applyFilters()"
            class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200"
          >
            <option value="">All Years</option>
            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
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

    <!-- Missing Year Fee Warning -->
    <div v-if="warning" class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
            <path
              fill-rule="evenodd"
              d="M8.485 3.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.19-1.458-1.515-2.625L8.485 3.495zM10 6a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 9a1 1 0 100-2 1 1 0 000 2z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-yellow-800">Missing Current Year Fee</h3>
          <div class="mt-2 text-sm text-yellow-700">
            <p>{{ warning.message }}</p>
            <div class="mt-2 flex flex-wrap gap-4">
              <span><strong>Permanent Graves:</strong> {{ warning.rates.permanent_grave }}</span>
              <span><strong>Niches:</strong> {{ warning.rates.niche }}</span>
            </div>
          </div>
          <div class="mt-3">
            <Button
              @click="router.visit('/graveyard/annual-maintenance-fees/create')"
              class="rounded border border-yellow-300 bg-yellow-100 px-3 py-1 text-sm text-yellow-800 hover:bg-yellow-200"
            >
              Set {{ warning.current_year }} Fees
            </Button>
          </div>
        </div>
      </div>
    </div>

    <!-- Compact pagination with inline stats above the table -->
    <div class="mb-2 flex items-center justify-between gap-3 rounded border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs">
      <!-- Left side: Total records info -->
      <div class="text-gray-600">
        Showing <span class="font-semibold">{{ data?.total || 0 }}</span> total maintenance fees
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
        <span class="rounded-full bg-teal-100 px-2 py-1 text-xs font-medium text-teal-800">Annual Maintenance Fees</span>
      </div>
    </div>

    <div class="mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
      <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="w-full border-collapse text-left">
          <thead>
            <tr class="bg-blue-50">
              <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
              <th class="cursor-pointer border-b p-3 font-semibold text-gray-700" @click="toggleSort('year')">
                <div class="flex items-center">
                  Year
                  <ChevronUp v-if="filters.sort === 'year' && filters.direction === 'asc'" class="ml-1 h-4 w-4" />
                  <ChevronDown v-else-if="filters.sort === 'year' && filters.direction === 'desc'" class="ml-1 h-4 w-4" />
                  <ChevronsUpDown v-else class="ml-1 h-4 w-4 text-gray-300" />
                </div>
              </th>
              <th class="border-b p-3 font-semibold text-gray-700">Permanent Grave</th>
              <th class="border-b p-3 font-semibold text-gray-700">Niche</th>
              <th class="border-b p-3 font-semibold text-gray-700">Status</th>
              <th class="border-b p-3 font-semibold text-gray-700">Delete</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="fee in data?.data" :key="fee.id" :id="`fee-row-${fee.id}`" class="transition even:bg-gray-50 hover:bg-blue-50">
              <td class="p-2">
                <div class="flex items-center gap-2">
                  <Button
                    @click="router.visit(`/graveyard/annual-maintenance-fees/${fee.id}/edit`)"
                    class="rounded-full bg-yellow-100 p-2 text-yellow-700 transition hover:bg-yellow-200"
                  >
                    <Pencil class="h-[1rem] w-[1rem]" />
                  </Button>
                </div>
              </td>
              <td class="p-3">
                <div class="text-sm font-medium text-gray-900">{{ fee.year }}</div>
              </td>
              <td class="p-3">
                <div class="text-sm text-gray-900">{{ fee.formatted_permanent_grave_amount }}</div>
              </td>
              <td class="p-3">
                <div class="text-sm text-gray-900">{{ fee.formatted_niche_amount }}</div>
              </td>
              <td class="p-3">
                <span
                  class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                  :class="fee.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ fee.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="p-2">
                <Button @click="deleteFee(fee)" class="rounded-full bg-red-100 p-2 text-red-700 transition hover:bg-red-200">
                  <Trash2 class="h-[1rem] w-[1rem]" />
                </Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Simple Delete Modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="bg-opacity-50 absolute inset-0 bg-black" @click="showDeleteModal = false"></div>
        <div class="from-grey-900 via-grey-800 to-grey-600 relative z-10 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Annual Maintenance Fee</h3>
            <p>
              Are you sure you want to delete the maintenance fee for
              <span class="font-bold">{{ feeToDelete?.year }}</span
              >?
            </p>
            <div class="mt-6 flex justify-end space-x-2">
              <Button
                type="button"
                @click="showDeleteModal = false"
                class="rounded-full bg-gray-100 px-6 py-2 text-gray-700 transition hover:bg-gray-200"
              >
                Cancel
              </Button>
              <Button
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
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ChevronDown, ChevronsUpDown, ChevronUp, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Props {
  data: any;
  filters: any;
  years: Array<number>;
  warning?: {
    message: string;
    current_year: number;
    fallback_year: number;
    rates: {
      permanent_grave: string;
      niche: string;
    };
  };
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Annual Maintenance Fees', href: '/graveyard/annual-maintenance-fees' },
];

// Reactive state
const filters = ref({ ...(props.filters || {}) });
const showDeleteModal = ref(false);
const feeToDelete = ref<any>(null);
const isArchived = ref(String(props.filters?.isArchived) === 'true');

const clearSearch = () => {
  filters.value.search = '';
  applyFilters();
};

const applyFilters = () => {
  router.get(
    '/graveyard/annual-maintenance-fees',
    { ...filters.value, isArchived: isArchived.value ? 'true' : 'false' },
    {
      preserveState: true,
      preserveScroll: true,
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

const changePage = (page: number) => {
  router.get(
    '/graveyard/annual-maintenance-fees',
    {
      ...filters.value,
      page,
      isArchived: isArchived.value ? 'true' : 'false',
    },
    {
      preserveState: true,
      preserveScroll: true,
    },
  );
};

const handlePageChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  if (target) {
    changePage(Number(target.value));
  }
};

const deleteFee = (fee: any) => {
  feeToDelete.value = fee;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (feeToDelete.value) {
    router.delete(`/graveyard/annual-maintenance-fees/${feeToDelete.value.id}`, {
      onSuccess: () => {
        showDeleteModal.value = false;
        feeToDelete.value = null;
      },
    });
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
</style>
