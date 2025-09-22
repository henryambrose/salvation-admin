<script setup lang="ts">
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, RotateCcw, Trash, Eye, DollarSign } from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';
import { Column } from '@/types';

const { can } = permissionHelpers();

interface ContributionType {
  id: number;
  name: string;
}

const props = defineProps<{
  contributions: any;
  contributionTypes: ContributionType[];
  statusOptions: Record<string, string>;
  filters: any;
  stats: any;
}>();

// Permission checks
const canCreateCommunityContribution = can('create-fund-community-contribution') || true;
const canReadAnyCommunityContribution = can('read-fund-community-contribution') || true;
const canUpdateAnyCommunityContribution = can('update-fund-community-contribution') || true;
const canDeleteAnyCommunityContribution = can('delete-fund-community-contribution') || true;
const canRestoreCommunityContribution = can('restore-fund-community-contribution') || true;

// Define columns for sorting
const columns: Column[] = [
  { key: 'collection_date', label: 'Date', sortable: true },
  { key: 'contribution_type_id', label: 'Type', sortable: false },
  { key: 'amount', label: 'Amount', sortable: true },
  { key: 'description', label: 'Description', sortable: false },
  { key: 'status', label: 'Status', sortable: true },
];

const showDeleteModal = ref(false);
const deletingContribution = ref<any | null>(null);
const showRestoreModal = ref(false);
const restoringContribution = ref<any | null>(null);

// Reactive filters
const search = ref(props.filters.search || '');
const contributionTypeId = ref(props.filters.contribution_type_id || '');
const status = ref(props.filters.status || '');
const year = ref(props.filters.year || new Date().getFullYear());
const perPage = ref(props.filters.perPage || 10);
const isArchived = ref(props.filters.isArchived === 'true');

// Breadcrumbs
const breadcrumbs = [
  { label: 'Fund Management', href: '/fund' },
  { label: 'Community Contributions', href: null },
];

// Watch for filter changes
watch([search, contributionTypeId, status, year, perPage, isArchived],
  () => {
    nextTick(() => {
      applyFilters();
    });
  },
  { deep: true }
);

const applyFilters = () => {
  const queryParams = {
    search: search.value || undefined,
    contribution_type_id: contributionTypeId.value || undefined,
    status: status.value || undefined,
    year: year.value || undefined,
    perPage: perPage.value,
    isArchived: isArchived.value ? 'true' : undefined,
    page: 1,
  };

  router.get('/fund/community-contributions', queryParams, {
    preserveState: true,
    replace: true,
  });
};

const clearFilters = () => {
  search.value = '';
  contributionTypeId.value = '';
  status.value = '';
  year.value = new Date().getFullYear();
  perPage.value = 10;
  isArchived.value = false;
};

const confirmDelete = (contribution: any) => {
  deletingContribution.value = contribution;
  showDeleteModal.value = true;
};

const deleteContribution = () => {
  if (!deletingContribution.value) return;

  router.delete(`/fund/community-contributions/${deletingContribution.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingContribution.value = null;
    },
  });
};

const confirmRestore = (contribution: any) => {
  restoringContribution.value = contribution;
  showRestoreModal.value = true;
};

const restoreContribution = () => {
  if (!restoringContribution.value) return;

  router.post(`/fund/community-contributions/${restoringContribution.value.id}/restore`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      showRestoreModal.value = false;
      restoringContribution.value = null;
    },
  });
};

const getStatusBadgeClass = (status: string) => {
  const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
  switch (status) {
    case 'recorded':
      return `${baseClasses} bg-yellow-100 text-yellow-800`;
    case 'verified':
      return `${baseClasses} bg-green-100 text-green-800`;
    case 'archived':
      return `${baseClasses} bg-gray-100 text-gray-800`;
    default:
      return `${baseClasses} bg-gray-100 text-gray-800`;
  }
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  });
};

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
    maximumFractionDigits: 2
  }).format(amount);
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Community Contributions" />

    <!-- Stats Cards -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
          <DollarSign class="h-8 w-8 text-green-500" />
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Amount</p>
            <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.total_amount) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Collections</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total_count }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Verified Amount</p>
            <p class="text-2xl font-semibold text-green-600">{{ formatCurrency(stats.verified_amount) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Pending Verification</p>
            <p class="text-2xl font-semibold text-yellow-600">{{ stats.pending_count }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Actions -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <!-- Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
          <input
            v-model="search"
            type="text"
            placeholder="Search contributions..."
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          />
        </div>

        <!-- Contribution Type Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
          <select
            v-model="contributionTypeId"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
            <option value="">All Types</option>
            <option v-for="type in contributionTypes" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
        </div>

        <!-- Status Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            v-model="status"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
            <option value="">All Status</option>
            <option v-for="(label, value) in statusOptions" :key="value" :value="value">
              {{ label }}
            </option>
          </select>
        </div>

        <!-- Year Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
          <select
            v-model="year"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
            <option v-for="y in 5" :key="y" :value="new Date().getFullYear() - y + 1">
              {{ new Date().getFullYear() - y + 1 }}
            </option>
          </select>
        </div>

        <!-- Actions -->
        <div class="flex items-end space-x-2">
          <Button @click="clearFilters" variant="outline" size="sm">
            Clear
          </Button>
          <Button
            v-if="canCreateCommunityContribution"
            @click="router.visit('/fund/community-contributions/create')"
            size="sm"
          >
            <Plus class="mr-2 h-4 w-4" />
            Add New
          </Button>
        </div>
      </div>

      <!-- Archive Toggle -->
      <div class="mt-4 flex items-center">
        <Checkbox
          v-model="isArchived"
          id="archived"
          class="mr-2"
        />
        <label for="archived" class="text-sm text-gray-700">Show archived contributions</label>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Type
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Amount
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Description
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Collected By
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="contribution in contributions.data" :key="contribution.id" :class="{ 'bg-red-50': contribution.deleted_at }">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(contribution.collection_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ contribution.contribution_type?.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ contribution.formatted_amount }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-900">
                <div class="max-w-xs truncate">{{ contribution.description || '-' }}</div>
                <div v-if="contribution.location" class="text-xs text-gray-500">{{ contribution.location }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusBadgeClass(contribution.status)">
                  {{ statusOptions[contribution.status] }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ contribution.collected_by?.name || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end space-x-2">
                  <Button
                    v-if="canReadAnyCommunityContribution"
                    @click="router.visit(`/fund/community-contributions/${contribution.id}`)"
                    variant="outline"
                    size="sm"
                  >
                    <Eye class="h-4 w-4" />
                  </Button>

                  <Button
                    v-if="canUpdateAnyCommunityContribution && !contribution.deleted_at"
                    @click="router.visit(`/fund/community-contributions/${contribution.id}/edit`)"
                    variant="outline"
                    size="sm"
                  >
                    <Pencil class="h-4 w-4" />
                  </Button>

                  <Button
                    v-if="canRestoreCommunityContribution && contribution.deleted_at"
                    @click="confirmRestore(contribution)"
                    variant="outline"
                    size="sm"
                  >
                    <RotateCcw class="h-4 w-4" />
                  </Button>

                  <Button
                    v-if="canDeleteAnyCommunityContribution && !contribution.deleted_at"
                    @click="confirmDelete(contribution)"
                    variant="destructive"
                    size="sm"
                  >
                    <Trash class="h-4 w-4" />
                  </Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <p class="text-sm text-gray-700">
              Showing {{ contributions.from || 0 }} to {{ contributions.to || 0 }} of {{ contributions.total || 0 }} results
            </p>
          </div>
          <div class="flex space-x-2">
            <Button
              v-if="contributions.prev_page_url"
              @click="router.visit(contributions.prev_page_url)"
              variant="outline"
              size="sm"
            >
              Previous
            </Button>
            <Button
              v-if="contributions.next_page_url"
              @click="router.visit(contributions.next_page_url)"
              variant="outline"
              size="sm"
            >
              Next
            </Button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Confirm Delete</h3>
            <p class="text-sm text-gray-500">
              Are you sure you want to delete this community contribution? This action cannot be undone.
            </p>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <Button @click="deleteContribution" variant="destructive" class="ml-3">
              Delete
            </Button>
            <Button @click="showDeleteModal = false" variant="outline">
              Cancel
            </Button>
          </div>
        </div>
      </div>
    </div>

    <!-- Restore Confirmation Modal -->
    <div v-if="showRestoreModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Confirm Restore</h3>
            <p class="text-sm text-gray-500">
              Are you sure you want to restore this community contribution?
            </p>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <Button @click="restoreContribution" class="ml-3">
              Restore
            </Button>
            <Button @click="showRestoreModal = false" variant="outline">
              Cancel
            </Button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>