<script setup lang="ts">
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Column } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, RotateCcw, Trash } from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';

const { can } = permissionHelpers();
const { success, error } = useToast();

interface ContributionType {
  id: number;
  name: string;
}

interface CommunityContribution {
  id: number;
  amount: number;
  formatted_amount: string;
  description: string | null;
  location: string | null;
  collection_date: string;
  status: string;
  contribution_type?: ContributionType;
  collected_by?: { name: string };
  deleted_at: string | null;
}

interface PaginatedData {
  data: CommunityContribution[];
  current_page: number;
  last_page: number;
  prev_page_url: string | null;
  next_page_url: string | null;
  total: number;
  from: number;
  to: number;
}

interface Stats {
  total_amount: number;
  total_count: number;
  verified_amount: number;
  pending_count: number;
}

const props = defineProps<{
  contributions: PaginatedData;
  contributionTypes: ContributionType[];
  stats: Stats;
  filters: {
    search?: string;
    contribution_type_id?: string;
    status?: string;
    year?: number;
    perPage?: number;
    isArchived?: string;
  };
}>();

// Permission checks
const canCreateCommunityContribution = can('create-community-contribution');
const canReadAnyCommunityContribution = can('read-community-contribution');
const canUpdateAnyCommunityContribution = can('update-community-contribution');
const canDeleteAnyCommunityContribution = can('delete-community-contribution');
const canRestoreCommunityContribution = can('restore-community-contribution');

// Define columns for sorting
const columns: Column[] = [
  { key: 'collection_date', label: 'Date', sortable: true },
  { key: 'contribution_type_id', label: 'Type', sortable: false },
  { key: 'amount', label: 'Amount', sortable: true },
  { key: 'description', label: 'Description', sortable: false },
  { key: 'status', label: 'Status', sortable: true },
  { key: 'collected_by', label: 'Collected By', sortable: false },
];

// UI state
const showDeleteModal = ref(false);
const showRestoreModal = ref(false);
const deletingContribution = ref<CommunityContribution | null>(null);
const restoringContribution = ref<CommunityContribution | null>(null);
const highlightedRowId = ref<number | null>(null);

// Breadcrumbs
const breadcrumbs = [
  { title: 'Fund Management', href: '/fund' },
  { title: 'Community Contributions', href: '/fund/community-contribution' },
];

// Status options
const statusOptions: Record<string, string> = {
  recorded: 'Recorded',
  verified: 'Verified',
};

// Local filters state
const search = ref(props.filters.search || '');
const contributionTypeId = ref(props.filters.contribution_type_id || '');
const status = ref(props.filters.status || '');
const year = ref(props.filters.year || new Date().getFullYear());
const perPage = ref(props.filters.perPage || 10);
const isArchived = ref(String(props.filters.isArchived) === 'true');
const serverArchived = computed(() => String(props.filters.isArchived) === 'true');

const partialOnly = ['contributions', 'filters'];
const searchTimeout = ref<number | null>(null);

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`community-contribution-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

function clearSearch() {
  search.value = '';
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  fetch();
}

function restoreCommunityContribution(id: number) {
  router.post(
    `/fund/community-contributions/${id}/restore`,
    {},
    {
      preserveScroll: true,
      only: partialOnly,
      onSuccess: () => {
        highlightedRowId.value = id;
        nextTick(() => scrollToRow(id));
        success('Community contribution restored successfully');
      },
      onError: () => {
        error('Failed to restore community contribution');
      },
    },
  );
}

function fetch(page = 1) {
  router.get(
    '/fund/community-contributions',
    {
      search: search.value,
      contribution_type_id: contributionTypeId.value,
      status: status.value,
      year: year.value,
      perPage: perPage.value,
      isArchived: isArchived.value ? 'true' : 'false',
      page,
    },
    { preserveState: true, preserveScroll: true, replace: true, only: partialOnly },
  );
}

watch(
  [search, contributionTypeId, status, year, perPage, isArchived],
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

const enhancedContributions = computed(() => {
  const contributions = props.contributions || {};
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

function openDeleteModal(contribution: CommunityContribution) {
  deletingContribution.value = contribution;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (!deletingContribution.value) return;
  const deletedId = deletingContribution.value.id;

  router.delete(`/fund/community-contributions/${deletedId}`, {
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingContribution.value = null;
      highlightedRowId.value = deletedId + 1;
      nextTick(() => scrollToRow(deletedId + 1));
      success('Community contribution deleted successfully');
    },
    onError: () => {
      error('Failed to delete community contribution');
    },
  });
}

function openRestoreModal(contribution: CommunityContribution) {
  restoringContribution.value = contribution;
  showRestoreModal.value = true;
}

function confirmRestore() {
  if (!restoringContribution.value) return;

  restoreCommunityContribution(restoringContribution.value.id);
  showRestoreModal.value = false;
  restoringContribution.value = null;
}

function handlePageChange(event: Event) {
  const target = event.target as HTMLSelectElement;
  if (target) {
    fetch(Number(target.value));
  }
}

function clearFilters() {
  search.value = '';
  contributionTypeId.value = '';
  status.value = '';
  year.value = new Date().getFullYear();
  perPage.value = 10;
}

function getStatusBadgeClass(status: string) {
  const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
  switch (status) {
    case 'recorded':
      return `${baseClasses} bg-yellow-100 text-yellow-800`;
    case 'verified':
      return `${baseClasses} bg-green-100 text-green-800`;
    default:
      return `${baseClasses} bg-gray-100 text-gray-800`;
  }
}

function formatDate(dateString: string) {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-IN');
}

function formatCurrency(amount: number) {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
    minimumFractionDigits: 2,
  }).format(amount);
}

function viewContribution(contribution: CommunityContribution) {
  router.visit(`/fund/community-contributions/${contribution.id}`);
}

function editContribution(contribution: CommunityContribution) {
  router.visit(`/fund/community-contributions/${contribution.id}/edit`);
}

function createContribution() {
  router.visit('/fund/community-contributions/create');
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Community Contributions" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Community Contributions</h2>
        <div class="flex items-center gap-3">
          <Button
            v-if="canCreateCommunityContribution"
            @click="createContribution"
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
            <label class="mb-1 block text-xs font-medium text-gray-600">Type</label>
            <select
              v-model="contributionTypeId"
              class="w-full rounded-full border border-gray-300 px-3 py-1 text-sm focus:ring-2 focus:ring-blue-200"
            >
              <option value="">All Types</option>
              <option v-for="type in contributionTypes" :key="type.id" :value="type.id">
                {{ type.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Status</label>
            <select v-model="status" class="w-full rounded-full border border-gray-300 px-3 py-1 text-sm focus:ring-2 focus:ring-blue-200">
              <option value="">All Status</option>
              <option v-for="(label, value) in statusOptions" :key="value" :value="value">
                {{ label }}
              </option>
            </select>
          </div>

          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Year</label>
            <select v-model="year" class="w-full rounded-full border border-gray-300 px-3 py-1 text-sm focus:ring-2 focus:ring-blue-200">
              <option v-for="y in 5" :key="y" :value="new Date().getFullYear() - y + 1">
                {{ new Date().getFullYear() - y + 1 }}
              </option>
            </select>
          </div>

          <!-- <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Per Page</label>
            <select v-model="perPage" class="w-full rounded-full border border-gray-300 px-3 py-1 text-sm focus:ring-2 focus:ring-blue-200">
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
              <option :value="100">100</option>
            </select>
          </div> -->

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

    <div v-if="canReadAnyCommunityContribution">
      <!-- Compact pagination with inline stats above the table -->
      <div class="mb-2 flex items-center justify-between gap-3 rounded border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs">
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ enhancedContributions.total || 0 }}</span> total contributions
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>

        <div class="flex items-center gap-2">
          <button
            v-if="enhancedContributions.prev_page_url"
            @click="fetch(enhancedContributions.current_page - 1)"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
          >
            ← Prev
          </button>

          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select
              v-if="enhancedContributions.last_page && enhancedContributions.last_page > 1"
              :value="enhancedContributions.current_page"
              @change="handlePageChange"
              class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
            >
              <option v-for="page in enhancedContributions.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ enhancedContributions.last_page }}</span>
          </div>

          <button
            v-if="enhancedContributions.next_page_url"
            @click="fetch(enhancedContributions.current_page + 1)"
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
                <th v-for="col in columns" :key="col.key" class="border-b p-3 font-semibold text-gray-700">
                  {{ col.label }}
                </th>
                <th v-if="!serverArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="contribution in enhancedContributions.data"
                :key="contribution.id"
                :id="`community-contribution-row-${contribution.id}`"
                :class="[
                  'transition even:bg-gray-50 hover:bg-blue-50',
                  highlightedRowId === contribution.id ? 'highlight-row' : '',
                  contribution.deleted_at ? 'bg-red-50' : '',
                ]"
              >
                <td class="p-2">
                  <template v-if="!serverArchived">
                    <div class="flex items-center gap-2">
                      <!-- <Button
                        v-if="canReadAnyCommunityContribution"
                        @click="viewContribution(contribution)"
                        class="rounded-full bg-blue-100 p-2 text-blue-700 transition hover:bg-blue-200"
                      >
                        <Eye class="h-[1rem] w-[1rem]" />
                      </Button> -->
                      <Button
                        v-if="canUpdateAnyCommunityContribution && !contribution.deleted_at"
                        @click="editContribution(contribution)"
                        class="rounded-full bg-yellow-100 p-2 text-yellow-700 transition hover:bg-yellow-200"
                      >
                        <Pencil class="h-[1rem] w-[1rem]" />
                      </Button>
                    </div>
                  </template>
                  <template v-else>
                    <Button
                      v-if="canRestoreCommunityContribution"
                      @click="openRestoreModal(contribution)"
                      class="rounded-full bg-green-100 p-2 text-green-700 transition hover:bg-green-200"
                    >
                      <RotateCcw class="h-[1rem] w-[1rem]" />
                    </Button>
                  </template>
                </td>
                <td class="p-2">
                  <div class="text-sm text-gray-900">{{ formatDate(contribution.collection_date) }}</div>
                </td>
                <td class="p-2">
                  <div class="text-sm text-gray-900">{{ contribution.contribution_type?.name || '-' }}</div>
                </td>
                <td class="p-2">
                  <div class="text-sm font-medium text-gray-900">
                    {{ contribution.formatted_amount || formatCurrency(contribution.amount || 0) }}
                  </div>
                </td>
                <td class="p-2">
                  <div class="max-w-xs truncate text-sm text-gray-900">{{ contribution.description || '-' }}</div>
                  <div v-if="contribution.location" class="text-xs text-gray-500">{{ contribution.location }}</div>
                </td>
                <td class="p-2">
                  <span :class="getStatusBadgeClass(contribution.status)">
                    {{ statusOptions[contribution.status] }}
                  </span>
                </td>
                <td class="p-2">
                  <div class="text-sm text-gray-900">{{ contribution.collected_by?.name || '-' }}</div>
                </td>
                <td v-if="!serverArchived" class="p-2">
                  <template v-if="canDeleteAnyCommunityContribution && !contribution.deleted_at">
                    <Button @click="openDeleteModal(contribution)" class="rounded-full bg-red-100 p-2 text-red-700 transition hover:bg-red-200">
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
            <h3 class="mb-4 text-xl font-semibold">Delete Community Contribution</h3>
            <p>
              Are you sure you want to delete this contribution of
              <span class="font-bold">{{ deletingContribution?.formatted_amount }}</span
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

    <!-- Restore Modal -->
    <transition name="fade">
      <div v-if="showRestoreModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Restore Community Contribution</h3>
            <p>
              Are you sure you want to restore this contribution of
              <span class="font-bold">{{ restoringContribution?.formatted_amount }}</span
              >?
            </p>
            <div class="mt-6 flex justify-end space-x-2">
              <Button
                type="button"
                @click="showRestoreModal = false"
                class="rounded-full bg-gray-100 px-6 py-2 text-gray-700 transition hover:bg-gray-200"
              >
                Cancel
              </Button>
              <Button
                type="button"
                @click="confirmRestore"
                class="rounded-full bg-green-600 px-6 py-2 text-white shadow transition hover:bg-green-700"
              >
                Restore
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
