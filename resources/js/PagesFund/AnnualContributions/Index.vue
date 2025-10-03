<script setup lang="ts">
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Column } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, RotateCcw, Trash } from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';

const { can } = permissionHelpers();

interface Category {
  id: number;
  name: string;
}

interface PaymentMethod {
  id: number;
  name: string;
}

const props = defineProps<{
  annualContributions: any;
  years?: any[];
  categories: Category[];
  paymentMethods: PaymentMethod[];
  filters: any;
}>();

// Permission checks
const canCreateAnnualContribution = can('create-fund-annual-contribution') || true;
const canReadAnyAnnualContribution = can('read-fund-annual-contribution') || true;
const canUpdateAnyAnnualContribution = can('update-fund-annual-contribution') || true;
const canDeleteAnyAnnualContribution = can('delete-fund-annual-contribution') || true;
const canRestoreAnnualContribution = can('restore-fund-annual-contribution') || true;

// Define columns for sorting
const columns: Column[] = [
  { key: 'family_no', label: 'Family & Name', sortable: true },
  { key: 'start_date', label: 'Period', sortable: true },
  { key: 'fund_category_id', label: 'Category', sortable: false },
  { key: 'amount', label: 'Amount', sortable: true },
  { key: 'payment_method_id', label: 'Payment', sortable: false },
  { key: 'created_at', label: 'Date', sortable: true },
];

const showDeleteModal = ref(false);
const deletingContribution = ref<Record<string, any> | null>(null);
const isArchived = ref(String(props.filters?.isArchived) === 'true');
const serverArchived = computed(() => String(props.filters?.isArchived) === 'true');
const highlightedRowId = ref<number | null>(null);

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`annual-contribution-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

// Local filters state
const search = ref(props.filters?.search || '');
const familyNo = ref(props.filters?.family_no || '');
const startDate = ref(props.filters?.start_date || '');
const endDate = ref(props.filters?.end_date || '');
const categoryId = ref(props.filters?.category_id || '');
const paymentMethodId = ref(props.filters?.payment_method_id || '');
const perPage = ref(props.filters?.per_page || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

const partialOnly = ['annualContributions', 'filters'];
const searchTimeout = ref<number | null>(null);

function clearSearch() {
  search.value = '';
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  fetch();
}

function restoreAnnualContribution(id: number) {
  router.post(
    route('fund.annual-contributions.restore', id),
    {},
    {
      preserveScroll: true,
      only: partialOnly,
      onSuccess: () => {
        highlightedRowId.value = id;
        nextTick(() => scrollToRow(id));
      },
    },
  );
}

function fetch(page = 1) {
  router.get(
    route('fund.annual-contributions.index'),
    {
      search: search.value,
      family_no: familyNo.value,
      start_date: startDate.value,
      end_date: endDate.value,
      category_id: categoryId.value,
      payment_method_id: paymentMethodId.value,
      per_page: perPage.value,
      sort: sort.value,
      direction: direction.value,
      isArchived: isArchived.value ? 'true' : 'false',
      page,
    },
    { preserveState: true, preserveScroll: true, replace: true, only: partialOnly },
  );
}

// Add sortBy function
function sortBy(column: string) {
  if (sort.value === column) {
    direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  } else {
    sort.value = column;
    direction.value = 'asc';
  }
  fetch();
}

watch(
  [search, familyNo, startDate, endDate, categoryId, paymentMethodId, perPage, sort, direction, isArchived],
  () => {
    if (searchTimeout.value) {
      clearTimeout(searchTimeout.value);
    }
    searchTimeout.value = window.setTimeout(() => {
      fetch();
    }, 300);
  },
  { immediate: false, deep: false },
);

const enhancedAnnualContributions = computed(() => {
  const contributions = props.annualContributions || {};
  return {
    data: contributions.data || [],
    prev_page_url: contributions.prev_page_url,
    next_page_url: contributions.next_page_url,
    current_page: contributions.current_page,
    last_page: contributions.last_page,
    total: contributions.total,
    from: contributions.from,
    to: contributions.to,
  };
});

function openDeleteModal(row: any) {
  deletingContribution.value = row;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (!deletingContribution.value) return;
  const deletedId = deletingContribution.value.id;

  router.delete(route('fund.annual-contributions.destroy', deletedId), {
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingContribution.value = null;
      highlightedRowId.value = deletedId + 1;
      nextTick(() => scrollToRow(deletedId + 1));
    },
  });
}

function handlePageChange(event: Event) {
  const target = event.target as HTMLSelectElement;
  if (target) {
    fetch(Number(target.value));
  }
}

function clearFilters() {
  search.value = '';
  familyNo.value = '';
  startDate.value = '';
  endDate.value = '';
  categoryId.value = '';
  paymentMethodId.value = '';
  perPage.value = 10;
}

function exportToCSV() {
  const params = new URLSearchParams({
    search: search.value,
    family_no: familyNo.value,
    start_date: startDate.value,
    end_date: endDate.value,
    category_id: categoryId.value,
    payment_method_id: paymentMethodId.value,
    isArchived: isArchived.value ? 'true' : 'false',
  });

  window.location.href = `${window.location.origin}/fund/annual-contributions/export?${params.toString()}`;
}

// Utility functions
function formatDate(dateString: string) {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-IN');
}

function formatCurrency(amount: number) {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
    minimumFractionDigits: 0,
  }).format(amount);
}

const breadcrumbs = [
  { title: 'Fund', href: '/fund' },
  { title: 'Annual Contributions', href: '/fund/annual-contributions' },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Annual Contributions" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Annual Contributions</h2>
        <div class="flex items-center gap-3">
          <Button
            @click="exportToCSV"
            class="flex items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-white shadow transition hover:bg-green-700"
          >
            <svg class="h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              ></path>
            </svg>
            <span>Export CSV</span>
          </Button>
          <Button
            v-if="canCreateAnnualContribution"
            @click="router.visit(route('fund.annual-contributions.create'))"
            class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
          >
            <Plus class="h-[1rem] w-[1rem]" />
            <span>Add Contribution</span>
          </Button>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="mb-4 rounded-lg bg-gray-50 p-3">
        <div class="grid grid-cols-6 gap-3">
          <div class="relative">
            <label class="mb-1 block text-xs font-medium text-gray-600">Search</label>
            <input
              v-model="search"
              type="text"
              placeholder="Search..."
              class="w-full rounded-full border border-gray-300 px-3 py-1 pr-7 text-sm focus:ring-2 focus:ring-blue-200"
              @keydown.escape="clearSearch"
            />
            <button
              v-if="search"
              @click="clearSearch"
              class="absolute top-7 right-2 -translate-y-1/2 transform text-xs text-gray-400 hover:text-gray-600"
            >
              ✕
            </button>
          </div>

          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Family No</label>
            <input
              v-model="familyNo"
              type="text"
              placeholder="Family No..."
              class="w-full rounded-full border border-gray-300 px-3 py-1 text-sm focus:ring-2 focus:ring-blue-200"
            />
          </div>

          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Start Date</label>
            <input
              v-model="startDate"
              type="date"
              class="w-full rounded-full border border-gray-300 px-3 py-1 text-sm focus:ring-2 focus:ring-blue-200"
            />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">End Date</label>
            <input
              v-model="endDate"
              type="date"
              class="w-full rounded-full border border-gray-300 px-3 py-1 text-sm focus:ring-2 focus:ring-blue-200"
            />
          </div>

          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Category</label>
            <select v-model="categoryId" class="w-full rounded-full border border-gray-300 px-3 py-1 text-sm focus:ring-2 focus:ring-blue-200">
              <option value="">All Categories</option>
              <option v-for="category in categories || []" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Payment</label>
            <select v-model="paymentMethodId" class="w-full rounded-full border border-gray-300 px-3 py-1 text-sm focus:ring-2 focus:ring-blue-200">
              <option value="">All Methods</option>
              <option v-for="method in paymentMethods || []" :key="method.id" :value="method.id">
                {{ method.name }}
              </option>
            </select>
          </div>

          <div class="flex items-end">
            <button @click="clearFilters" class="w-full rounded-full bg-gray-500 px-3 py-1 text-sm text-white transition hover:bg-gray-600">
              Clear
            </button>
          </div>
        </div>
      </div>

      <div class="mb-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
        <div class="flex flex-wrap items-center gap-3">
          <select v-model="perPage" @change="fetch()" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
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

    <div v-if="canReadAnyAnnualContribution">
      <!-- Compact pagination with inline stats above the table -->
      <div class="mb-2 flex items-center justify-between gap-3 rounded border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs">
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ enhancedAnnualContributions.total || 0 }}</span> total contributions
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>

        <div class="flex items-center gap-2">
          <button
            v-if="enhancedAnnualContributions.prev_page_url"
            @click="fetch(enhancedAnnualContributions.current_page - 1)"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
          >
            ← Prev
          </button>

          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select
              v-if="enhancedAnnualContributions.last_page && enhancedAnnualContributions.last_page > 1"
              :value="enhancedAnnualContributions.current_page"
              @change="handlePageChange"
              class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
            >
              <option v-for="page in enhancedAnnualContributions.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ enhancedAnnualContributions.last_page }}</span>
          </div>

          <button
            v-if="enhancedAnnualContributions.next_page_url"
            @click="fetch(enhancedAnnualContributions.current_page + 1)"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
          >
            Next →
          </button>
        </div>

        <div class="text-gray-500">
          <span class="rounded-full bg-teal-100 px-2 py-1 text-xs font-medium text-teal-800"> Contributions </span>
        </div>
      </div>

      <div class="mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
        <div class="overflow-x-auto rounded-xl border border-gray-100">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr class="bg-blue-50">
                <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
                <th
                  v-for="col in columns"
                  :key="col.key"
                  class="border-b p-3 font-semibold text-gray-700"
                  :class="col.sortable ? 'cursor-pointer hover:bg-blue-100' : ''"
                  @click="col.sortable ? sortBy(col.key) : null"
                >
                  <div class="flex items-center gap-1">
                    {{ col.label }}
                    <template v-if="col.sortable">
                      <span v-if="sort === col.key" class="text-blue-600">
                        {{ direction === 'asc' ? '↑' : '↓' }}
                      </span>
                      <span v-else class="text-gray-400">↕</span>
                    </template>
                  </div>
                </th>
                <th v-if="!serverArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="contribution in enhancedAnnualContributions.data"
                :key="contribution.id"
                :id="`annual-contribution-row-${contribution.id}`"
                :class="['transition even:bg-gray-50 hover:bg-blue-50', highlightedRowId === contribution.id ? 'highlight-row' : '']"
              >
                <td class="p-2">
                  <template v-if="!serverArchived">
                    <div class="flex items-center gap-2">
                      <Button
                        v-if="canUpdateAnyAnnualContribution"
                        @click="router.visit(route('fund.annual-contributions.edit', contribution.id))"
                        class="rounded-full bg-yellow-100 p-2 text-yellow-700 transition hover:bg-yellow-200"
                      >
                        <Pencil class="h-[1rem] w-[1rem]" />
                      </Button>
                    </div>
                  </template>
                  <template v-else>
                    <Button
                      v-if="canRestoreAnnualContribution"
                      @click="restoreAnnualContribution(contribution.id)"
                      class="rounded-full bg-green-100 p-2 text-green-700 transition hover:bg-green-200"
                    >
                      <RotateCcw class="h-[1rem] w-[1rem]" />
                    </Button>
                  </template>
                </td>
                <td class="p-2">
                  <div class="text-sm font-medium text-gray-900">
                    {{ contribution.family_no }}
                  </div>
                  <div v-if="contribution.member" class="text-xs text-gray-500">
                    {{ contribution.member.first_name }} {{ contribution.member.last_name }}
                  </div>
                  <div v-else-if="contribution.paid_by_name" class="text-xs text-gray-500">
                    {{ contribution.paid_by_name }}
                  </div>
                </td>
                <td class="p-2">
                  <div class="text-sm text-gray-900">{{ formatDate(contribution.start_date) }} - {{ formatDate(contribution.end_date) }}</div>
                </td>
                <td class="p-2">
                  <div class="text-sm text-gray-900">{{ contribution.fund_category?.name || '-' }}</div>
                </td>
                <td class="p-2">
                  <div class="text-sm font-medium text-gray-900">{{ formatCurrency(contribution.amount) }}</div>
                </td>
                <td class="p-2">
                  <div class="text-sm text-gray-900">{{ contribution.payment_method?.name || '-' }}</div>
                </td>
                <td class="p-2 text-sm text-gray-500">
                  {{ formatDate(contribution.created_at) }}
                </td>

                <td v-if="!serverArchived" class="p-2">
                  <template v-if="canDeleteAnyAnnualContribution">
                    <Button
                      @click="openDeleteModal(contribution)"
                      variant="destructive"
                      class="rounded-full bg-red-100 p-2 text-red-700 transition hover:bg-red-200"
                    >
                      <Trash class="h-[1rem] w-[1rem]" />
                    </Button>
                  </template>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Annual Contribution</h3>
            <p>
              Are you sure you want to delete this contribution for
              <span class="font-bold">Family {{ deletingContribution?.family_no }}</span
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
              <Button type="button" @click="confirmDelete" class="rounded-full bg-red-600 px-6 py-2 text-white shadow transition hover:bg-red-700">
                Delete
              </Button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </AppLayout>
</template>

<style>
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
