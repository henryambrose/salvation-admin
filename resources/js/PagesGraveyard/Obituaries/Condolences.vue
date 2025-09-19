<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { CheckCircle, Download, Search, XCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

defineOptions({
  layout: AppLayout,
});

const props = defineProps<{
  condolences?: any;
  filters?: any;
}>();

const { success, error } = useToast();

// Permission checks
const { can } = permissionHelpers();
const canApproveCondolences = can('approve-obituary-condolence');
const canRejectCondolences = can('reject-obituary-condolence');

// Search functionality
const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

// Watch for search changes and update URL
watch([searchQuery, statusFilter], ([newSearch, newStatus]: [string, string]) => {
  const params: Record<string, string> = {};
  if (newSearch) params.search = newSearch;
  if (newStatus) params.status = newStatus;

  router.get(route('graveyard.obituaries.condolences.index'), params, {
    preserveState: true,
    replace: true,
  });
});

// Actions
const approveCondolence = (condolenceId: number) => {
  router.patch(
    `/graveyard/obituaries/condolences/${condolenceId}/approve`,
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        success('Condolence approved successfully!');
        // Refresh the page to show updated status
        router.reload({ only: ['condolences'] });
      },
      onError: (errors) => {
        console.error('Error approving condolence:', errors);
        error('Failed to approve condolence. Please try again.');
      },
    },
  );
};
const getDeceasedName = (condolence: any): string => {
  if (condolence.obituary_page.permanent_grave_booking) {
    const member = condolence.obituary_page.permanent_grave_booking.valid_member;
    return `${member.first_name} ${member.last_name}`;
  }
  if (condolence.obituary_page.temporary_grave_booking) {
    const booking = condolence.obituary_page.temporary_grave_booking;
    return `${booking.dead_first_name} ${booking.dead_last_name}`;
  }
  return 'Unknown';
};

// Format date
const formatDate = (dateString: string): string => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-GB'); // dd/mm/yyyy format
};

// Export to CSV
const exportToCSV = () => {
  if (!props.condolences?.data?.length) {
    error('No data to export');
    return;
  }

  const headers = ['Deceased Name', 'Visitor Name', 'Relationship', 'Phone', 'Email', 'IP Address', 'Created Date', 'Status', 'Message'];

  const csvData = props.condolences.data.map((condolence: any) => [
    getDeceasedName(condolence),
    condolence.visitor_name || '',
    condolence.relationship || '',
    condolence.visitor_phone || '',
    condolence.visitor_email || '',
    condolence.visitor_ip || '',
    formatDate(condolence.created_at),
    condolence.is_approved ? 'Approved' : condolence.is_rejected ? 'Rejected' : 'Pending',
    `"${(condolence.message || '').replace(/"/g, '""')}"`, // Escape quotes in message
  ]);

  const csvContent = [headers.join(','), ...csvData.map((row: string[]) => row.join(','))].join('\n');

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = `condolences_${new Date().toISOString().split('T')[0]}.csv`;
  link.click();

  success('CSV export downloaded successfully!');
};
const rejectCondolence = (condolenceId: number) => {
  router.patch(
    `/graveyard/obituaries/condolences/${condolenceId}/reject`,
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        success('Condolence rejected successfully!');
        // Refresh the page to show updated status
        router.reload({ only: ['condolences'] });
      },
      onError: (errors) => {
        console.error('Error rejecting condolence:', errors);
        error('Failed to reject condolence. Please try again.');
      },
    },
  );
};
</script>

<template>
  <Head title="Manage Condolences" />

  <div class="py-12">
    <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
          <!-- Header -->
          <div class="mb-6 flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Manage Condolences</h1>
              <p class="mt-2 text-gray-600">Review and moderate obituary condolences</p>
            </div>
            <Button v-if="condolences?.data?.length" @click="exportToCSV" variant="outline" class="flex items-center space-x-2">
              <Download class="h-4 w-4" />
              <span>Export CSV</span>
            </Button>
          </div>

          <!-- Search and Filters -->
          <div class="mb-6 flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
            <div class="flex-1">
              <div class="relative">
                <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <Input v-model="searchQuery" placeholder="Search by deceased name, visitor name, phone, email, or IP..." class="pl-10" />
              </div>
            </div>
            <div class="w-full sm:w-48">
              <select
                v-model="statusFilter"
                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-2 text-sm file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
              >
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
          </div>

          <!-- Stats -->
          <div v-if="condolences?.data?.length" class="mb-4">
            <p class="text-sm text-gray-600">Showing {{ condolences.data.length }} of {{ condolences.total }} condolences</p>
          </div>

          <!-- Table -->
          <div v-if="!condolences?.data?.length" class="py-12 text-center">
            <p class="text-gray-500">No condolences found</p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Deceased Person</th>
                  <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Visitor Details</th>
                  <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Contact Info</th>
                  <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">IP & Date</th>
                  <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Status</th>
                  <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <template v-for="condolence in condolences.data" :key="condolence.id">
                  <!-- Main row -->
                  <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">
                      <div class="font-medium text-gray-900">
                        {{ getDeceasedName(condolence) }}
                      </div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <div class="font-medium text-gray-900">{{ condolence.visitor_name }}</div>
                      <div v-if="condolence.relationship" class="text-gray-500 capitalize">
                        {{ condolence.relationship }}
                      </div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <div v-if="condolence.visitor_phone" class="text-gray-900">📞 {{ condolence.visitor_phone }}</div>
                      <div v-if="condolence.visitor_email" class="text-gray-900">✉️ {{ condolence.visitor_email }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <div v-if="condolence.visitor_ip" class="font-mono text-gray-600">
                        {{ condolence.visitor_ip }}
                      </div>
                      <div class="text-gray-500">{{ formatDate(condolence.created_at) }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <span
                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                        :class="{
                          'bg-green-100 text-green-800': condolence.is_approved,
                          'bg-red-100 text-red-800': condolence.is_rejected,
                          'bg-yellow-100 text-yellow-800': !condolence.is_approved && !condolence.is_rejected,
                        }"
                      >
                        {{ condolence.is_approved ? 'Approved' : condolence.is_rejected ? 'Rejected' : 'Pending' }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <div
                        v-if="!condolence.is_approved && !condolence.is_rejected && (canApproveCondolences || canRejectCondolences)"
                        class="flex space-x-2"
                      >
                        <Button
                          v-if="canApproveCondolences"
                          size="sm"
                          variant="default"
                          @click="approveCondolence(condolence.id)"
                          class="bg-green-600 hover:bg-green-700"
                        >
                          <CheckCircle class="mr-1 h-3 w-3" />
                          Approve
                        </Button>
                        <Button
                          v-if="canRejectCondolences"
                          size="sm"
                          variant="outline"
                          @click="rejectCondolence(condolence.id)"
                          class="border-red-200 text-red-700 hover:bg-red-50"
                        >
                          <XCircle class="mr-1 h-3 w-3" />
                          Reject
                        </Button>
                      </div>
                      <div
                        v-else-if="!condolence.is_approved && !condolence.is_rejected && !canApproveCondolences && !canRejectCondolences"
                        class="text-xs text-gray-500"
                      >
                        No permission
                      </div>
                    </td>
                  </tr>
                  <!-- Message row -->
                  <tr class="bg-blue-50">
                    <td colspan="6" class="px-4 py-3">
                      <div class="text-sm">
                        <span class="font-medium text-gray-700">Message:</span>
                        <span class="ml-2 text-gray-900">{{ condolence.message }}</span>
                      </div>
                    </td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="condolences?.data?.length" class="mt-6 flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-700"> Showing {{ condolences.from }} to {{ condolences.to }} of {{ condolences.total }} results </span>
            </div>
            <div class="flex items-center space-x-2">
              <Button v-if="condolences.prev_page_url" variant="outline" size="sm" @click="router.get(condolences.prev_page_url)"> Previous </Button>
              <Button v-if="condolences.next_page_url" variant="outline" size="sm" @click="router.get(condolences.next_page_url)"> Next </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
