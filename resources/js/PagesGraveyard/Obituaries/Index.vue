<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, EyeOff, FileText, Globe, Plus, QrCode, Search, Share2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useToast } from '@/composables/useToast';

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
  can_be_accessed_publicly: boolean;
  payment_status: 'pending' | 'completed' | 'failed' | 'refunded';
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
                <div class="relative max-w-md flex-1">
                  <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                  <Input v-model="search" placeholder="Search by name or booking reference..." class="pl-10" />
                </div>
                <Select v-model="serviceType">
                  <SelectTrigger class="w-[180px]">
                    <SelectValue placeholder="Service Type" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All Types</SelectItem>
                    <SelectItem value="basic">Basic</SelectItem>
                    <SelectItem value="premium">Premium</SelectItem>
                  </SelectContent>
                </Select>
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

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              <Card v-for="obituary in obituaries?.data || []" :key="obituary.id" class="transition-shadow hover:shadow-md">
                <CardHeader class="pb-3">
                  <div class="flex items-start justify-between">
                    <div>
                      <CardTitle class="text-lg">{{ getDeceasedName(obituary) }}</CardTitle>
                      <p class="text-sm text-gray-600">{{ getBookingReference(obituary) }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                      <Badge :class="serviceTypeColors[obituary.service_type]">
                        {{ obituary.service_type }}
                      </Badge>
                      <Badge :class="obituary.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'">
                        <Globe v-if="obituary.is_published" class="mr-1 h-3 w-3" />
                        <EyeOff v-else class="mr-1 h-3 w-3" />
                        {{ obituary.is_published ? 'Published' : 'Draft' }}
                      </Badge>
                    </div>
                  </div>
                </CardHeader>
                <CardContent>
                  <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                      <span>Views: {{ obituary.view_count }}</span>
                      <span>QR Scans: {{ obituary.qr_scan_count }}</span>
                    </div>

                    <div class="flex space-x-2">
                      <Button
                        size="sm"
                        variant="outline"
                        @click="viewObituaryPage(obituary.uuid)"
                        :disabled="!obituary.can_be_accessed_publicly"
                        :class="obituary.can_be_accessed_publicly ? 'flex-1' : 'flex-1 cursor-not-allowed opacity-50'"
                        :title="
                          obituary.can_be_accessed_publicly
                            ? 'View public obituary page'
                            : `Cannot view public page - Status: ${obituary.payment_status}`
                        "
                      >
                        <Eye class="mr-1 h-3 w-3" />
                        {{ obituary.can_be_accessed_publicly ? 'View Public' : 'Not Available' }}
                      </Button>
                      <Button size="sm" variant="outline" @click="copyShareLink(obituary.uuid, obituary)">
                        <Share2 class="h-3 w-3" />
                      </Button>
                      <Button size="sm" variant="outline" as-child>
                        <Link :href="`/graveyard/obituaries/${obituary.uuid}`">
                          <QrCode class="mr-1 h-3 w-3" />
                          Manage
                        </Link>
                      </Button>
                    </div>

                    <!-- Publish/Unpublish Actions -->
                    <div v-if="canPublishObituary || canUnpublishObituary" class="flex space-x-2 border-t border-gray-100 pt-2">
                      <Button
                        v-if="!obituary.is_published && canPublishObituary"
                        size="sm"
                        variant="default"
                        @click="publishObituary(obituary.uuid)"
                        class="flex-1 bg-green-600 hover:bg-green-700"
                      >
                        <Globe class="mr-1 h-3 w-3" />
                        Publish
                      </Button>
                      <Button
                        v-else-if="obituary.is_published && canUnpublishObituary"
                        size="sm"
                        variant="outline"
                        @click="unpublishObituary(obituary.uuid)"
                        class="flex-1 border-orange-200 text-orange-700 hover:bg-orange-50"
                      >
                        <EyeOff class="mr-1 h-3 w-3" />
                        Unpublish
                      </Button>
                      <div v-if="obituary.is_published && obituary.published_at" class="self-center text-xs text-gray-500">
                        Published {{ new Date(obituary.published_at).toLocaleDateString() }}
                      </div>
                      <!-- Show status without buttons if user doesn't have permissions -->
                      <div v-if="!canPublishObituary && !canUnpublishObituary" class="self-center text-xs text-gray-500">
                        Status: {{ obituary.is_published ? 'Published' : 'Draft' }}
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
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
