<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, EyeOff, FileText, Globe, Plus, QrCode, Search, Share2, Undo2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface ObituaryPage {
  id: number;
  uuid: string;
  service_type: 'basic' | 'premium';
  is_public: boolean;
  is_published: boolean;
  published_at?: string;
  view_count: number;
  qr_scan_count: number;
  created_at: string;
  deleted_at?: string;
  can_be_accessed_publicly: boolean;
  payment_status: 'pending' | 'completed' | 'failed' | 'refunded';
  can_be_published: boolean;
  permanent_grave_booking?: {
    id: number;
    booking_reference: string;
    valid_member: {
      first_name: string;
      last_name: string;
    };
  };
  temporary_grave_booking?: {
    id: number;
    booking_reference: string;
    dead_first_name: string;
    dead_last_name: string;
  };
}

interface Props {
  obituaries?: {
    data: ObituaryPage[];
    links?: any[];
    meta?: any;
  };
  filters?: {
    search?: string;
    service_type?: string;
  };
}

const props = defineProps<Props>();

const { success, error, warning } = useToast();

// Initialize permission helpers
const { can } = permissionHelpers();

// Permission checks
const canPublishObituary = can('publish-obituary-page');
const canUnpublishObituary = can('unpublish-obituary-page');

const search = ref(props.filters?.search || '');
const serviceType = ref(props.filters?.service_type || 'all');

const performSearch = () => {
  const params: any = {
    search: search.value,
  };

  if (serviceType.value && serviceType.value !== 'all') {
    params.service_type = serviceType.value;
  }

  router.get('/graveyard/obituaries', params, {
    preserveState: true,
    replace: true,
  });
};

// Debounced search function
let searchTimeout: number;

watch([search, serviceType], () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    performSearch();
  }, 500);
});

const getDeceasedName = (obituary: ObituaryPage): string => {
  if (obituary.permanent_grave_booking) {
    const member = obituary.permanent_grave_booking.valid_member;
    return `${member.first_name} ${member.last_name}`;
  }
  if (obituary.temporary_grave_booking) {
    const booking = obituary.temporary_grave_booking;
    return `${booking.dead_first_name} ${booking.dead_last_name}`;
  }
  return 'Unknown';
};

const getBookingReference = (obituary: ObituaryPage): string => {
  return obituary.permanent_grave_booking?.booking_reference || obituary.temporary_grave_booking?.booking_reference || 'N/A';
};

const serviceTypeColors = {
  basic: 'bg-gray-100 text-gray-800',
  premium: 'bg-purple-100 text-purple-800',
};

const paymentStatusColors = {
  pending: 'bg-orange-100 text-orange-800',
  completed: 'bg-green-100 text-green-800',
  failed: 'bg-red-100 text-red-800',
  refunded: 'bg-gray-100 text-gray-800',
};

const viewObituaryPage = (uuid: string) => {
  window.open(`/obituary/${uuid}`, '_blank');
};

const copyShareLink = (uuid: string, obituary?: any) => {
  const url = `${window.location.origin}/obituary/${uuid}`;
  navigator.clipboard.writeText(url);

  if (obituary && !obituary.can_be_accessed_publicly) {
    warning(
      `Link copied! Note: This obituary page requires payment completion before it can be viewed publicly. Current status: ${obituary.payment_status}`,
    );
  } else {
    success('Share link copied to clipboard!');
  }
};

const publishObituary = (obituaryUuid: string) => {
  router.post(
    `/graveyard/obituaries/${obituaryUuid}/publish`,
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        success('Obituary published successfully!');
        // Refresh the current page to show updated status
        router.reload({ only: ['obituaries'] });
      },
      onError: (errors) => {
        console.error('Error publishing obituary:', errors);
        error('Failed to publish obituary. Please try again.');
      },
    },
  );
};

const unpublishObituary = (obituaryUuid: string) => {
  router.post(
    `/graveyard/obituaries/${obituaryUuid}/unpublish`,
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        success('Obituary unpublished successfully!');
        // Refresh the current page to show updated status
        router.reload({ only: ['obituaries'] });
      },
      onError: (errors) => {
        console.error('Error unpublishing obituary:', errors);
        error('Failed to unpublish obituary. Please try again.');
      },
    },
  );
};

const upgradeObituaryToPremium = (obituary: ObituaryPage) => {
  router.post(
    `/graveyard/obituaries/${obituary.uuid}/upgrade-to-premium`,
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        success('Upgrade initiated! Redirecting to payment...');
      },
      onError: (errors) => {
        console.error('Error upgrading obituary:', errors);
        error('Failed to initiate upgrade. Please try again.');
      },
    },
  );
};

const restoreObituary = (obituaryUuid: string) => {
  if (!confirm('Are you sure you want to restore this obituary page?')) {
    return;
  }

  router.post(
    `/graveyard/obituaries/${obituaryUuid}/restore`,
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        success('Obituary restored successfully!');
        // Refresh the current page to show updated status
        router.reload({ only: ['obituaries'] });
      },
      onError: (errors) => {
        console.error('Error restoring obituary:', errors);
        error('Failed to restore obituary. Please try again.');
      },
    },
  );
};

const downloadQRCode = async (uuid: string) => {
  try {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Fetch the QR code file with proper headers
    const response = await fetch(`/graveyard/obituaries/${uuid}/qr-download`, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'image/png,image/*,*/*',
      },
      credentials: 'same-origin', // Include cookies for authentication
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({ error: 'Unknown error' }));
      throw new Error(errorData.error || 'Failed to download QR code');
    }

    // Get the blob from response
    const blob = await response.blob();

    // Create blob URL and trigger download
    const blobUrl = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = blobUrl;
    link.download = `obituary-${uuid}.png`;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();

    // Clean up
    setTimeout(() => {
      document.body.removeChild(link);
      window.URL.revokeObjectURL(blobUrl);
    }, 100);

    success('QR code downloaded successfully!');
  } catch (err: any) {
    console.error('QR code download error:', err);
    error(err.message || 'Failed to download QR code. Please try again.');
  }
};
</script>

<template>
  <Head title="Obituary Management" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-4 lg:px-6">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-base leading-6 font-semibold text-gray-900">Obituary Management</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage all obituary pages and memorial content</p>
              </div>
              <Button as-child class="bg-purple-600 hover:bg-purple-700">
                <Link href="/graveyard/obituaries/create">
                  <Plus class="mr-2 h-4 w-4" />
                  Create Obituary
                </Link>
              </Button>
            </div>
          </div>

          <!-- Filters -->
          <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
              <div class="flex flex-1 items-center space-x-4">
                <div class="relative max-w-[448px] flex-1">
                  <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                  <Input v-model="search" placeholder="Search by name or booking reference..." class="pl-10" />
                </div>
                <select
                  v-model="serviceType"
                  class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus:ring-ring flex h-9 w-[180px] items-center justify-between rounded-md border px-3 py-2 text-sm whitespace-nowrap shadow-sm focus:ring-1 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                >
                  <option value="all">All Types</option>
                  <option value="basic">Basic</option>
                  <option value="premium">Premium</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Obituary List -->
          <div class="p-6">
            <div v-if="!obituaries?.data?.length" class="py-12 text-center">
              <FileText class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">No obituaries found</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by creating a new obituary page.</p>
              <div class="mt-6">
                <Button as-child class="bg-purple-600 hover:bg-purple-700">
                  <Link href="/graveyard/obituaries/create">
                    <Plus class="mr-2 h-4 w-4" />
                    Create Obituary
                  </Link>
                </Button>
              </div>
            </div>

            <div v-else class="overflow-hidden rounded-lg border border-gray-200">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Deceased Person</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Service Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Statistics</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr
                    v-for="obituary in obituaries?.data || []"
                    :key="obituary.id"
                    :class="[obituary.deleted_at ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50']"
                  >
                    <!-- Deceased Person -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div :class="['text-sm font-medium', obituary.deleted_at ? 'text-red-600 line-through' : 'text-gray-900']">
                          {{ getDeceasedName(obituary) }}
                          <span v-if="obituary.deleted_at" class="ml-2 rounded-full bg-red-100 px-2 py-1 text-xs text-red-800"> DELETED </span>
                        </div>
                        <div class="text-sm text-gray-600">{{ getBookingReference(obituary) }}</div>
                      </div>
                    </td>

                    <!-- Service Type -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <Badge :class="serviceTypeColors[obituary.service_type]">
                        {{ obituary.service_type }}
                      </Badge>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="space-y-2">
                        <Badge :class="obituary.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'">
                          <Globe v-if="obituary.is_published" class="mr-1 h-3 w-3" />
                          <EyeOff v-else class="mr-1 h-3 w-3" />
                          {{ obituary.is_published ? 'Published' : 'Draft' }}
                        </Badge>
                        <Badge :class="paymentStatusColors[obituary.payment_status]" class="text-xs">
                          {{
                            obituary.payment_status === 'completed'
                              ? 'Paid'
                              : obituary.payment_status.charAt(0).toUpperCase() + obituary.payment_status.slice(1)
                          }}
                        </Badge>
                        <div v-if="obituary.is_published && obituary.published_at" class="text-xs text-gray-500">
                          Published {{ new Date(obituary.published_at).toLocaleDateString() }}
                        </div>
                      </div>
                    </td>

                    <!-- Statistics -->
                    <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-600">
                      <div class="space-y-1">
                        <div>Views: {{ obituary.view_count }}</div>
                        <div>QR Scans: {{ obituary.qr_scan_count }}</div>
                      </div>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="space-y-2">
                        <!-- Restore Button for Deleted Obituaries -->
                        <div v-if="obituary.deleted_at">
                          <Button
                            size="sm"
                            variant="outline"
                            @click="restoreObituary(obituary.uuid)"
                            class="w-full border-green-200 text-green-600 hover:bg-green-50"
                            title="Restore obituary"
                          >
                            <Undo2 class="mr-1 h-3 w-3" />
                            Restore
                          </Button>
                        </div>

                        <!-- Main Actions for Active Obituaries -->
                        <div v-else class="flex space-x-2">
                          <Button
                            size="sm"
                            variant="outline"
                            @click="viewObituaryPage(obituary.uuid)"
                            :disabled="!obituary.can_be_accessed_publicly"
                            :class="obituary.can_be_accessed_publicly ? '' : 'cursor-not-allowed opacity-50'"
                            :title="
                              obituary.can_be_accessed_publicly
                                ? 'View public obituary page'
                                : `Cannot view public page - Status: ${obituary.payment_status}`
                            "
                          >
                            <Eye class="h-3 w-3" />
                          </Button>
                          <Button size="sm" variant="outline" @click="copyShareLink(obituary.uuid, obituary)" title="Copy share link">
                            <Share2 class="h-3 w-3" />
                          </Button>
                          <Button size="sm" variant="outline" @click="downloadQRCode(obituary.uuid)" title="Download QR code">
                            <QrCode class="h-3 w-3" />
                          </Button>
                        </div>

                        <!-- Upgrade to Premium (Only for Active Obituaries) -->
                        <!-- <div v-if="!obituary.deleted_at && obituary.service_type === 'basic' && obituary.payment_status === 'completed'">
                          <Button
                            size="sm"
                            variant="outline"
                            @click="upgradeObituaryToPremium(obituary)"
                            class="w-full border-purple-200 text-purple-600 hover:bg-purple-50"
                          >
                            <ArrowUp class="mr-1 h-3 w-3" />
                            Upgrade to Premium
                          </Button>
                        </div> -->

                        <!-- Publish/Unpublish Actions (Only for Active Obituaries) -->
                        <div v-if="!obituary.deleted_at && (canPublishObituary || canUnpublishObituary)" class="flex space-x-2">
                          <Button
                            v-if="!obituary.is_published && canPublishObituary"
                            size="sm"
                            variant="default"
                            @click="publishObituary(obituary.uuid)"
                            class="bg-green-600 hover:bg-green-700"
                          >
                            <Globe class="mr-1 h-3 w-3" />
                            Publish
                          </Button>
                          <Button
                            v-else-if="obituary.is_published && canUnpublishObituary"
                            size="sm"
                            variant="outline"
                            @click="unpublishObituary(obituary.uuid)"
                            class="border-orange-200 text-orange-700 hover:bg-orange-50"
                          >
                            <EyeOff class="mr-1 h-3 w-3" />
                            Unpublish
                          </Button>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="obituaries?.meta?.last_page && obituaries.meta.last_page > 1" class="mt-6 flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ obituaries?.meta?.from }} to {{ obituaries?.meta?.to }} of {{ obituaries?.meta?.total }} results
              </div>
              <div class="flex space-x-1">
                <Button
                  v-for="link in obituaries?.links || []"
                  :key="link.label"
                  size="sm"
                  :variant="link.active ? 'default' : 'outline'"
                  :disabled="!link.url"
                  @click="link.url && router.visit(link.url)"
                  v-html="link.label"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
