<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Download, Eye, FileText, Plus, Search } from 'lucide-vue-next';
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { useCertificateState } from '@/composables/useCertificateState';

interface Certificate {
  id: number;
  certificate_number: string;
  certificate_type_id: number;
  certificate_type: string; // accessor for display (code)
  certificate_type_name?: string; // accessor for display name
  formatted_type?: string; // accessor for formatted type name
  member: {
    id: number;
    first_name: string;
    middle_name?: string;
    last_name: string;
    member_no: string;
    family_no: string;
  };
  template?: {
    id: number;
    name: string;
  };
  issued_date: string;
  issuer?: {
    id: number;
    name: string;
  };
  file_path?: string;
  download_count: number;
  created_at: string;
}

const props = defineProps<{
  certificates: {
    data: Certificate[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    next_page_url?: string;
    prev_page_url?: string;
  };
  filters: {
    search?: string;
    type?: string;
    sort?: string;
    direction?: string;
    per_page?: number;
  };
  canGenerateCertificates: boolean;
  canViewCertificateHistory: boolean;
  canReprintCertificates: boolean;
}>();

// State management
const { certificateState, saveState, getState } = useCertificateState();

// Filter reactive variables - use stored state if available, otherwise use props
const search = ref(props.filters?.search || certificateState.search || '');
const certificateType = ref(props.filters?.type || certificateState.certificateType || '');
const sort = ref(props.filters?.sort || certificateState.sort || 'created_at');
const direction = ref(props.filters?.direction || certificateState.direction || 'desc');
const perPage = ref(props.filters?.per_page || certificateState.perPage || 10);
const showFilters = ref(false);

// Certificate types for filtering
const certificateTypes = [
  { value: '', label: 'All Types' },
  { value: 'baptism', label: 'Baptism' },
  { value: 'confirmation', label: 'Confirmation' },
  { value: 'marriage', label: 'Marriage' },
  { value: 'membership', label: 'Membership' },
  { value: 'death', label: 'Death' },
];

// Watch for filter changes and fetch new data
let searchTimeout: number;
watch(
  [search, certificateType, sort, direction, perPage],
  () => {
    // Save state when filters change
    saveState({
      search: search.value,
      type: certificateType.value,
      sort: sort.value,
      direction: direction.value,
      per_page: perPage.value,
    });

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      fetch();
    }, 300);
  },
  { immediate: false },
);

// Save state when component unmounts (navigating away)
onBeforeUnmount(() => {
  saveState({
    search: search.value,
    type: certificateType.value,
    sort: sort.value,
    direction: direction.value,
    per_page: perPage.value,
    page: props.certificates.current_page,
  });
});

const breadcrumbs = [{ title: 'Certificates', href: '/certificates' }];

// Computed properties
const enhancedCertificates = computed(() => {
  return props.certificates.data.map((cert) => ({
    ...cert,
    member_full_name: `${cert.member.first_name} ${cert.member.middle_name || ''} ${cert.member.last_name}`.trim(),
    formatted_issued_date: new Date(cert.issued_date).toLocaleDateString('en-GB'),
    formatted_created_date: new Date(cert.created_at).toLocaleDateString('en-GB'),
    type_badge_class: getCertificateTypeBadgeClass(cert.certificate_type),
  }));
});

function getCertificateTypeBadgeClass(type: string): string {
  const classes = {
    baptism: 'bg-blue-100 text-blue-800',
    confirmation: 'bg-green-100 text-green-800',
    marriage: 'bg-pink-100 text-pink-800',
    membership: 'bg-purple-100 text-purple-800',
    death: 'bg-gray-100 text-gray-800',
  };
  return classes[type as keyof typeof classes] || 'bg-gray-100 text-gray-800';
}

function fetch(page = 1) {
  router.get(
    '/certificates',
    {
      search: search.value,
      type: certificateType.value,
      sort: sort.value,
      direction: direction.value,
      per_page: perPage.value,
      page,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      only: ['certificates', 'filters'],
    },
  );
}

function changeSort(field: string) {
  if (sort.value === field) {
    direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  } else {
    sort.value = field;
    direction.value = 'asc';
  }
}

function clearSearch() {
  search.value = '';
  certificateType.value = '';
  fetch(1);
}

function generateNewCertificate() {
  router.get('/certificates/generate', {}, {
    preserveState: true,
    preserveScroll: true,
  });
}


function viewCertificate(certificate: Certificate) {
  router.get(`/certificates/${certificate.id}`, {}, {
    preserveState: true,
    preserveScroll: true,
  });
}

function downloadCertificate(certificate: Certificate) {
  if (!certificate.file_path) return;

  window.open(`/certificates/${certificate.id}/download`, '_blank');
}

function reprintCertificate(certificate: Certificate) {
  router.post(
    `/certificates/${certificate.id}/reprint`,
    {},
    {
      preserveState: true,
      onSuccess: () => {
        // Refresh the list to show updated download count
        fetch(props.certificates.current_page);
      },
    },
  );
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Certificate Management" />

    <!-- Header Section -->
    <div class="mb-6">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Certificate Management</h1>
          <p class="mt-1 text-gray-600">Manage and track issued certificates for members</p>
        </div>

        <div class="flex items-center gap-3">
          <Button
            v-if="canGenerateCertificates"
            @click="generateNewCertificate"
            class="flex items-center gap-2 bg-green-600 text-white hover:bg-green-700"
          >
            <Plus class="h-4 w-4" />
            Generate Certificate
          </Button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
      <Card>
        <CardContent class="p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Certificates</p>
              <p class="text-2xl font-bold text-gray-900">{{ props.certificates.total }}</p>
            </div>
            <FileText class="h-8 w-8 text-blue-600" />
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent class="p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">This Month</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ enhancedCertificates.filter((c) => new Date(c.created_at).getMonth() === new Date().getMonth()).length }}
              </p>
            </div>
            <FileText class="h-8 w-8 text-green-600" />
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent class="p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Most Common</p>
              <p class="text-lg font-bold text-gray-900 capitalize">
                {{ props.certificates.data.length > 0 ? 'Baptism' : 'N/A' }}
              </p>
            </div>
            <FileText class="h-8 w-8 text-purple-600" />
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent class="p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Downloads</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ enhancedCertificates.reduce((total, cert) => total + cert.download_count, 0) }}
              </p>
            </div>
            <Download class="h-8 w-8 text-orange-600" />
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Search and Filter Section -->
    <Card class="mb-6">
      <CardContent class="p-4">
        <div class="flex flex-col gap-4 sm:flex-row">
          <!-- Search Input -->
          <div class="relative flex-1">
            <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
            <input
              v-model="search"
              type="text"
              placeholder="Search by member name, certificate number..."
              class="w-full rounded-lg border border-gray-300 py-2 pr-4 pl-10 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
              @keydown.escape="clearSearch"
            />
            <button v-if="search" @click="clearSearch" class="absolute top-1/2 right-3 -translate-y-1/2 transform text-gray-400 hover:text-gray-600">
              ✕
            </button>
          </div>

          <!-- Certificate Type Filter -->
          <select
            v-model="certificateType"
            class="rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
          >
            <option v-for="type in certificateTypes" :key="type.value" :value="type.value">
              {{ type.label }}
            </option>
          </select>

          <!-- Per Page -->
          <select v-model="perPage" class="rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            <option :value="10">10 per page</option>
            <option :value="25">25 per page</option>
            <option :value="50">50 per page</option>
            <option :value="100">100 per page</option>
          </select>
        </div>
      </CardContent>
    </Card>

    <!-- Certificates Table -->
    <Card>
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="border-b bg-gray-50">
              <tr>
                <th
                  @click="changeSort('certificate_number')"
                  class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase hover:bg-gray-100"
                >
                  Certificate #
                  <span v-if="sort === 'certificate_number'">
                    {{ direction === 'asc' ? '▲' : '▼' }}
                  </span>
                </th>
                <th
                  @click="changeSort('certificate_type')"
                  class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase hover:bg-gray-100"
                >
                  Type
                  <span v-if="sort === 'certificate_type'">
                    {{ direction === 'asc' ? '▲' : '▼' }}
                  </span>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Member</th>
                <th
                  @click="changeSort('issued_date')"
                  class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase hover:bg-gray-100"
                >
                  Issued Date
                  <span v-if="sort === 'issued_date'">
                    {{ direction === 'asc' ? '▲' : '▼' }}
                  </span>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Template</th>
                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Downloads</th>
                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="certificate in enhancedCertificates" :key="certificate.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ certificate.certificate_number }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <Badge :class="certificate.type_badge_class">
                    {{ certificate.certificate_type_name }}
                  </Badge>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ certificate.member_full_name }}</div>
                  <div class="text-sm text-gray-500">Member #{{ certificate.member.member_no }} | Family #{{ certificate.member.family_no }}</div>
                </td>
                <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">
                  {{ certificate.formatted_issued_date }}
                </td>
                <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">
                  {{ certificate.template?.name || 'Default Template' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                    {{ certificate.download_count }} downloads
                  </span>
                </td>
                <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <Button @click="viewCertificate(certificate)" variant="outline" size="sm" class="flex items-center gap-1">
                      <Eye class="h-4 w-4" />
                      View
                    </Button>

                    <Button
                      v-if="certificate.file_path"
                      @click="downloadCertificate(certificate)"
                      variant="outline"
                      size="sm"
                      class="flex items-center gap-1"
                    >
                      <Download class="h-4 w-4" />
                      Download
                    </Button>

                    <Button
                      v-if="canReprintCertificates"
                      @click="reprintCertificate(certificate)"
                      variant="outline"
                      size="sm"
                      class="flex items-center gap-1"
                    >
                      <FileText class="h-4 w-4" />
                      Reprint
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty state -->
        <div v-if="enhancedCertificates.length === 0" class="py-12 text-center">
          <FileText class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No certificates found</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by generating a new certificate.</p>
          <div class="mt-6">
            <Button
              v-if="canGenerateCertificates"
              @click="generateNewCertificate"
              class="flex items-center gap-2 bg-green-600 text-white hover:bg-green-700"
            >
              <Plus class="h-4 w-4" />
              Generate Certificate
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Pagination -->
    <div v-if="props.certificates.last_page > 1" class="mt-6 flex items-center justify-between">
      <div class="text-sm text-gray-700">
        Showing {{ (props.certificates.current_page - 1) * props.certificates.per_page + 1 }} to
        {{ Math.min(props.certificates.current_page * props.certificates.per_page, props.certificates.total) }}
        of {{ props.certificates.total }} results
      </div>

      <div class="flex items-center gap-2">
        <Button v-if="props.certificates.prev_page_url" @click="fetch(props.certificates.current_page - 1)" variant="outline" size="sm">
          Previous
        </Button>

        <span class="text-sm text-gray-700"> Page {{ props.certificates.current_page }} of {{ props.certificates.last_page }} </span>

        <Button v-if="props.certificates.next_page_url" @click="fetch(props.certificates.current_page + 1)" variant="outline" size="sm">
          Next
        </Button>
      </div>
    </div>

  </AppLayout>
</template>
