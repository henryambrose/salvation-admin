<template>
  <div class="py-6">
    <div class="mx-auto max-w-7xl">
      <div class="overflow-hidden bg-[#ffffff] shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Contributions</h1>
            <Link
              href="/fund/annual-contributions/create"
              class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-blue-900"
            >
              Add New Contribution
            </Link>
          </div>

          <!-- Quick Stats -->
          <!-- <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-4">
            <div class="rounded-lg bg-blue-50 p-6">
              <h3 class="text-lg font-medium text-blue-900">Total This Year</h3>
              <p class="text-3xl font-bold text-blue-600">₹{{ formatCurrency(stats?.total_amount || 0) }}</p>
              <p class="text-sm text-blue-700">{{ stats?.current_year || new Date().getFullYear() }}</p>
            </div>

            <div class="rounded-lg bg-green-50 p-6">
              <h3 class="text-lg font-medium text-green-900">Total Families</h3>
              <p class="text-3xl font-bold text-green-600">{{ stats?.total_families || 0 }}</p>
              <p class="text-sm text-green-700">Contributing</p>
            </div>

            <div class="rounded-lg bg-purple-50 p-6">
              <h3 class="text-lg font-medium text-purple-900">Monthly Average</h3>
              <p class="text-3xl font-bold text-purple-600">₹{{ formatCurrency(stats?.monthly_average || 0) }}</p>
              <p class="text-sm text-purple-700">Per Month</p>
            </div>

            <div class="rounded-lg bg-orange-50 p-6">
              <h3 class="text-lg font-medium text-orange-900">Pending</h3>
              <p class="text-3xl font-bold text-orange-600">{{ stats?.pending_count || 0 }}</p>
              <p class="text-sm text-orange-700">Families</p>
            </div>
          </div> -->

          <!-- Advanced Filters -->
          <div class="mb-6 rounded-lg bg-gray-50 p-6">
            <h3 class="mb-4 text-lg font-medium text-gray-900">Advanced Filters</h3>
            <form @submit.prevent="applyFilters" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
              <!-- Family Number -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Family Number</label>
                <input
                  v-model="filters.family_no"
                  type="text"
                  placeholder="Search family..."
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                />
              </div>

              <!-- Year -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Year</label>
                <select
                  v-model="filters.year"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                >
                  <option value="">All Years</option>
                  <option v-for="year in filterOptions?.years || []" :key="year" :value="year">{{ year }}</option>
                </select>
              </div>

              <!-- Status -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select
                  v-model="filters.status"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                >
                  <option value="">All Statuses</option>
                  <option v-for="status in filterOptions?.statuses || []" :key="status.value" :value="status.value">
                    {{ status.label }}
                  </option>
                </select>
              </div>

              <!-- Fund Category -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Fund Category</label>
                <select
                  v-model="filters.fund_category_id"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                >
                  <option value="">All Categories</option>
                  <option v-for="category in filterOptions?.fund_categories || []" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>

              <!-- Payment Method -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Payment Method</label>
                <select
                  v-model="filters.payment_method_id"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                >
                  <option value="">All Methods</option>
                  <option v-for="method in filterOptions?.payment_methods || []" :key="method.id" :value="method.id">
                    {{ method.name }}
                  </option>
                </select>
              </div>

              <!-- Date Range -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">From Date</label>
                <input
                  v-model="filters.date_from"
                  type="date"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                />
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">To Date</label>
                <input
                  v-model="filters.date_to"
                  type="date"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                />
              </div>

              <!-- Filter Actions -->
              <div class="flex items-end space-x-2">
                <button
                  type="submit"
                  class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                >
                  Apply Filters
                </button>
                <button
                  type="button"
                  @click="clearFilters"
                  class="rounded-md bg-gray-500 px-4 py-2 text-white hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:outline-none"
                >
                  Clear
                </button>
              </div>
            </form>
          </div>

          <!-- Bulk Actions -->
          <div
            v-if="selectedContributions.length > 0 && contributions?.data && Array.isArray(contributions.data) && contributions.data.length > 0"
            class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <span class="text-sm font-medium text-yellow-800"> {{ selectedContributions.length }} contribution(s) selected </span>
              </div>
              <div class="flex items-center space-x-2">
                <select
                  v-model="bulkAction"
                  class="rounded-md border border-yellow-300 px-3 py-2 focus:ring-2 focus:ring-yellow-500 focus:outline-none"
                >
                  <option value="">Select Action</option>
                  <option value="paid">Mark as Paid</option>
                  <option value="partial">Mark as Partial</option>
                  <option value="pending">Mark as Pending</option>
                  <option value="cancelled">Mark as Cancelled</option>
                </select>
                <button
                  @click="executeBulkAction"
                  :disabled="!bulkAction"
                  class="rounded-md bg-yellow-600 px-4 py-2 text-white hover:bg-yellow-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                  Apply
                </button>
                <button @click="clearSelection" class="rounded-md bg-gray-500 px-4 py-2 text-white hover:bg-gray-600">Clear Selection</button>
              </div>
            </div>
          </div>

          <!-- Data Table -->
          <div v-if="contributions?.data && Array.isArray(contributions.data) && contributions.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                    <input
                      type="checkbox"
                      :checked="isAllSelected"
                      @change="toggleSelectAll"
                      class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
                      v-if="contributions?.data && Array.isArray(contributions.data) && contributions.data.length > 0"
                    />
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Family</th>
                  <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Year</th>
                  <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Amount</th>
                  <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Paid By</th>
                  <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Category</th>
                  <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Payment Date</th>
                  <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-[#ffffff]">
                <tr v-for="contribution in contributions?.data || []" :key="contribution.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <input
                      type="checkbox"
                      :value="contribution.id"
                      v-model="selectedContributions"
                      class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
                    />
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ contribution.family_no }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ contribution.year }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">₹{{ formatCurrency(contribution.amount) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(contribution.status)" class="inline-flex rounded-full px-2 py-1 text-xs font-semibold">
                      {{ getStatusLabel(contribution.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ contribution.paid_by_display || 'N/A' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ contribution.fund_category?.name || 'N/A' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(contribution.payment_date) }}</div>
                  </td>
                  <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                    <div class="flex space-x-2">
                      <Link :href="`/fund/annual-contributions/${contribution.id}`" class="text-blue-600 hover:text-blue-900"> View </Link>
                      <Link :href="`/fund/annual-contributions/${contribution.id}/edit`" class="text-indigo-600 hover:text-indigo-900"> Edit </Link>
                      <button @click="deleteContribution(contribution.id)" class="text-red-600 hover:text-red-900">Delete</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="contributions?.total > 0" class="flex items-center justify-between border-t border-gray-200 bg-[#ffffff] px-4 py-3 sm:px-6">
              <div class="flex flex-1 justify-between sm:hidden">
                <Link
                  v-if="contributions?.prev_page_url && contributions.prev_page_url !== '#'"
                  :href="contributions.prev_page_url"
                  class="relative inline-flex items-center rounded-md border border-gray-300 bg-[#ffffff] px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                  Previous
                </Link>
                <Link
                  v-if="contributions?.next_page_url && contributions.next_page_url !== '#'"
                  :href="contributions.next_page_url"
                  class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-[#ffffff] px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ contributions?.from || 0 }}</span>
                    to
                    <span class="font-medium">{{ contributions?.to || 0 }}</span>
                    of
                    <span class="font-medium">{{ contributions?.total || 0 }}</span>
                    results
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm">
                    <template v-for="link in contributions?.links || []" :key="link.label">
                      <Link
                        v-if="link.url"
                        :href="link.url || '#'"
                        :class="[
                          'relative inline-flex items-center border px-4 py-2 text-sm font-medium',
                          link.active ? 'z-10 border-blue-500 bg-blue-50 text-blue-600' : 'border-gray-300 bg-[#ffffff] text-gray-500 hover:bg-gray-50',
                        ]"
                        v-html="link.label"
                      />
                    </template>
                  </nav>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="rounded-lg bg-gray-50 p-8 text-center">
            <div class="text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No contributions found</h3>
              <p class="mt-1 text-sm text-gray-500">
                {{ hasActiveFilters ? 'Try adjusting your filters or' : 'Get started by' }} adding the first  contribution.
              </p>
              <div class="mt-6">
                <Link
                  href="/fund/annual-contributions/create"
                  class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-[#3b82f6] focus:ring-offset-2 focus:outline-none"
                >
                  Add Contribution
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

defineOptions({
  layout: AppLayout,
});

const page = usePage();
const props = withDefaults(
  defineProps<{
    contributions?: any;
    stats?: any;
    filterOptions?: any;
    filters?: any;
  }>(),
  {
    contributions: () => ({
      data: [],
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0,
      from: null,
      to: null,
      links: [],
    }),
    stats: () => ({
      total_families: 0,
      total_contributions: 0,
      total_amount: 0,
      pending_count: 0,
      partial_count: 0,
      paid_count: 0,
      monthly_average: 0,
      current_year: new Date().getFullYear(),
    }),
    filterOptions: () => ({
      years: [],
      statuses: [
        { value: 'pending', label: 'Pending' },
        { value: 'partial', label: 'Partial' },
        { value: 'paid', label: 'Paid' },
        { value: 'cancelled', label: 'Cancelled' },
        { value: 'refunded', label: 'Refunded' },
      ],
      fund_categories: [],
      payment_methods: [],
    }),
    filters: () => ({}),
  },
);

// Create reactive references with proper defaults
const contributions = ref(
  props.contributions || {
    data: [],
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: null,
    to: null,
    links: [],
  },
);

const stats = ref(
  props.stats || {
    total_families: 0,
    total_contributions: 0,
    total_amount: 0,
    pending_count: 0,
    partial_count: 0,
    paid_count: 0,
    monthly_average: 0,
    current_year: new Date().getFullYear(),
  },
);

const filterOptions = ref(
  props.filterOptions || {
    years: [],
    statuses: [
      { value: 'pending', label: 'Pending' },
      { value: 'partial', label: 'Partial' },
      { value: 'paid', label: 'Paid' },
      { value: 'cancelled', label: 'Cancelled' },
      { value: 'refunded', label: 'Refunded' },
    ],
    fund_categories: [],
    payment_methods: [],
  },
);

// Reactive state
const selectedContributions = ref<number[]>([]);
const bulkAction = ref('');
const filters = ref(props.filters || {});

// Computed properties
const isAllSelected = computed(() => {
  return (
    contributions.value?.data &&
    Array.isArray(contributions.value.data) &&
    contributions.value.data.length > 0 &&
    selectedContributions.value.length === contributions.value.data.length
  );
});

const hasActiveFilters = computed(() => {
  return filters.value && Object.values(filters.value).some((value) => value !== '' && value !== null && value !== undefined);
});

// Methods
const formatCurrency = (amount: number) => {
  try {
    return new Intl.NumberFormat('en-IN').format(amount || 0);
  } catch (error) {
    return '0';
  }
};

const formatDate = (date: string) => {
  try {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-IN');
  } catch (error) {
    return 'N/A';
  }
};

const getStatusLabel = (status: string) => {
  try {
    if (!status) return 'Unknown';
    const statusMap: Record<string, string> = {
      pending: 'Pending',
      partial: 'Partial',
      paid: 'Paid',
      cancelled: 'Cancelled',
      refunded: 'Refunded',
    };
    return statusMap[status] || status;
  } catch (error) {
    return 'Unknown';
  }
};

const getStatusBadgeClass = (status: string) => {
  try {
    if (!status) return 'bg-gray-100 text-gray-800';
    const statusClasses: Record<string, string> = {
      pending: 'bg-yellow-100 text-yellow-800',
      partial: 'bg-blue-100 text-blue-800',
      paid: 'bg-green-100 text-green-800',
      cancelled: 'bg-red-100 text-red-800',
      refunded: 'bg-gray-100 text-gray-800',
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
  } catch (error) {
    return 'bg-gray-100 text-gray-800';
  }
};

const applyFilters = () => {
  router.get('/fund/annual-contributions', filters.value, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  filters.value = {};
  router.get(
    '/fund/annual-contributions',
    {},
    {
      preserveState: true,
      preserveScroll: true,
    },
  );
};

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedContributions.value = [];
  } else {
    selectedContributions.value = contributions.value?.data?.map((item: any) => item.id) || [];
  }
};

const clearSelection = () => {
  selectedContributions.value = [];
  bulkAction.value = '';
};

const executeBulkAction = () => {
  if (!bulkAction.value || selectedContributions.value.length === 0) return;

  router.post(
    '/fund/annual-contributions/bulk-update',
    {
      ids: selectedContributions.value,
      status: bulkAction.value,
    },
    {
      onSuccess: () => {
        clearSelection();
      },
    },
  );
};

const deleteContribution = (id: number) => {
  if (confirm('Are you sure you want to delete this contribution?')) {
    router.delete(`/fund/annual-contributions/${id}`);
  }
};

// Initialize filters from props
onMounted(() => {
  filters.value = props.filters || {};
});

// Watch for prop changes and update reactive references
watch(
  () => props.contributions,
  (newContributions) => {
    if (newContributions) {
      contributions.value = newContributions;
    }
  },
  { immediate: true },
);

watch(
  () => props.stats,
  (newStats) => {
    if (newStats) {
      stats.value = newStats;
    }
  },
  { immediate: true },
);

watch(
  () => props.filterOptions,
  (newFilterOptions) => {
    if (newFilterOptions) {
      filterOptions.value = newFilterOptions;
    }
  },
  { immediate: true },
);
</script>
