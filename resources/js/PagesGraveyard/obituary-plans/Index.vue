<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Obituary Plans" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Obituary Plans</h2>
        <Button
          v-if="can.create"
          @click="router.visit('/graveyard/obituary-plans/create')"
          class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
        >
          <Plus class="h-[1rem] w-[1rem]" />
          <span>Add Plan</span>
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
              placeholder="Search plans..."
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
            v-model="filters.is_active"
            @change="applyFilters()"
            class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200"
          >
            <option value="">All Plans</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
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
        Showing <span class="font-semibold">{{ plans?.total || 0 }}</span> total obituary plans
        <span v-if="filters.search" class="text-blue-600">for "{{ filters.search }}"</span>
      </div>

      <!-- Center: Pagination controls -->
      <div class="flex items-center gap-2">
        <button
          v-if="plans?.prev_page_url"
          @click="changePage(plans.current_page - 1)"
          class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
        >
          ← Prev
        </button>

        <div class="flex items-center gap-1 text-gray-600">
          <span>Page</span>
          <select
            v-if="plans?.last_page && plans.last_page > 1"
            :value="plans?.current_page"
            @change="handlePageChange"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50 focus:border-blue-500 focus:ring-1 focus:ring-[#3b82f6]"
          >
            <option v-for="page in plans.last_page" :key="page" :value="page">
              {{ page }}
            </option>
          </select>
          <span>of {{ plans?.last_page }}</span>
        </div>

        <button
          v-if="plans?.next_page_url"
          @click="changePage(plans.current_page + 1)"
          class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
        >
          Next →
        </button>
      </div>

      <!-- Right side: Additional info -->
      <div class="text-gray-500">
        <span class="rounded-full bg-teal-100 px-2 py-1 text-xs font-medium text-teal-800"> Obituary Plans </span>
      </div>
    </div>

    <div class="mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
      <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="w-full border-collapse text-left">
          <thead>
            <tr class="bg-blue-50">
              <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
              <th class="border-b p-3 font-semibold text-gray-700">Name</th>
              <th class="border-b p-3 font-semibold text-gray-700">Duration</th>
              <th class="border-b p-3 font-semibold text-gray-700">Cost</th>
              <th class="border-b p-3 font-semibold text-gray-700">Status</th>
              <th class="border-b p-3 font-semibold text-gray-700">Order</th>
              <th class="border-b p-3 font-semibold text-gray-700">Usage</th>
              <th v-if="!serverArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="plan in plans?.data"
              :key="plan.id"
              :id="`plan-row-${plan.id}`"
              :class="['transition even:bg-gray-50 hover:bg-blue-50', highlightedRowId === plan.id ? 'highlight-row' : '']"
            >
              <td class="p-2">
                <div class="flex items-center gap-2">
                  <template v-if="!serverArchived">
                    <!-- <Button
                      @click="router.visit('/graveyard/obituary-plans/' + plan.id)"
                      class="rounded-full bg-blue-100 p-2 text-blue-700 transition hover:bg-blue-200"
                    >
                      <Eye class="h-[1rem] w-[1rem]" />
                    </Button> -->
                    <Button
                      @click="router.visit('/graveyard/obituary-plans/' + plan.id + '/edit')"
                      class="rounded-full bg-yellow-100 p-2 text-yellow-700 transition hover:bg-yellow-200"
                    >
                      <Pencil class="h-[1rem] w-[1rem]" />
                    </Button>
                  </template>
                  <template v-else>
                    <Button @click="restorePlan(plan.id)" class="rounded-full bg-green-100 p-2 text-green-700 transition hover:bg-green-200">
                      <RotateCcw class="h-[1rem] w-[1rem]" />
                    </Button>
                  </template>
                </div>
              </td>
              <td class="p-2">
                <div>
                  <div class="font-medium">{{ plan.name }}</div>
                  <div v-if="plan.description" class="max-w-xs truncate text-sm text-gray-500">
                    {{ plan.description }}
                  </div>
                </div>
              </td>
              <td class="p-2">
                <span
                  class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                  :class="plan.duration_in_days ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                >
                  {{ plan.formatted_duration }}
                </span>
              </td>
              <td class="p-2 font-medium">
                {{ plan.formatted_cost }}
              </td>
              <td class="p-2">
                <span
                  class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                  :class="plan.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ plan.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="p-2 text-center">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-sm font-medium text-gray-700">
                  {{ plan.sort_order }}
                </span>
              </td>
              <td class="p-2">
                <div class="text-sm">
                  <div>{{ plan.obituary_pages_count || 0 }} pages</div>
                  <div class="text-gray-500">{{ plan.obituary_payments_count || 0 }} payments</div>
                </div>
              </td>
              <td v-if="!serverArchived" class="p-2">
                <Button
                  @click="deletePlan(plan)"
                  variant="destructive"
                  class="rounded-full bg-red-100 p-2 text-red-700 transition hover:bg-red-200"
                  :disabled="(plan.obituary_pages_count || 0) > 0 || (plan.obituary_payments_count || 0) > 0"
                >
                  <Trash2 class="h-[1rem] w-[1rem]" />
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
        <div class="bg-opacity-50 absolute inset-0 bg-black" @click="showDeleteModal = false"></div>
        <div class="from-grey-900 via-grey-800 to-grey-600 relative z-10 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Obituary Plan</h3>
            <p>
              Are you sure you want to delete the plan
              <span class="font-bold">{{ planToDelete?.name }}</span
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
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, RotateCcw, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Props {
  plans: any;
  filters: any;
  can: any;
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Obituary Plans', href: '/graveyard/obituary-plans' },
];

// Reactive state
const filters = ref({ ...(props.filters || {}) });
const highlightedRowId = ref<number | null>(null);
const showDeleteModal = ref(false);
const planToDelete = ref<any>(null);
const isArchived = ref(String(props.filters?.isArchived) === 'true');
const serverArchived = computed(() => String(props.filters?.isArchived) === 'true');

// Clear search
const clearSearch = () => {
  filters.value.search = '';
  applyFilters();
};

const applyFilters = () => {
  router.get(
    '/graveyard/obituary-plans',
    {
      ...filters.value,
      page: 1, // Reset to first page when filtering
      isArchived: isArchived.value ? 'true' : 'false',
    },
    {
      preserveState: true,
      preserveScroll: true,
      only: ['plans', 'filters'],
    },
  );
};

const deletePlan = (plan: any) => {
  planToDelete.value = plan;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (planToDelete.value) {
    router.delete('/graveyard/obituary-plans/' + planToDelete.value.id, {
      onSuccess: () => {
        showDeleteModal.value = false;
        planToDelete.value = null;
        highlightRow(planToDelete.value?.id);
      },
    });
  }
};

const restorePlan = (id: number) => {
  router.post(
    '/graveyard/obituary-plans/' + id + '/restore',
    {},
    {
      preserveScroll: true,
      only: ['plans', 'filters'],
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

const changePage = (page: number) => {
  router.get(
    '/graveyard/obituary-plans',
    {
      ...filters.value,
      page,
      isArchived: isArchived.value ? 'true' : 'false',
    },
    {
      preserveState: true,
      preserveScroll: true,
      only: ['plans', 'filters'],
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
</style>
