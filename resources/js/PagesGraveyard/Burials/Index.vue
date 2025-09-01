<script setup lang="ts">
import { Head, usePage, Link, router, useForm } from '@inertiajs/vue3';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { Column } from '@/types';
import { Input } from '@/components/ui/input';
import { Pencil, Trash, RotateCcw, Plus, Eye, Calendar, User, MapPin, DollarSign } from 'lucide-vue-next';
import { computed, ref, watch, nextTick } from 'vue';
import { permissionHelpers } from '@/composables/permissionHelpers';

const { can } = permissionHelpers();

const props = defineProps({
  data: Object,
  filters: Object,
  fetchUrl: String,
  genders: Array,
  parishes: Array,
  paymentMethods: Array,
});

// Permission checks
const canCreateBurial = can('create-graveyard-burial') || true;
const canReadAnyBurial = can('read-graveyard-burial') || true;
const canUpdateAnyBurial = can('update-graveyard-burial') || true;
const canDeleteAnyBurial = can('delete-graveyard-burial') || true;
const canRestoreBurial = can('restore-graveyard-burial') || true;

const columns: Column[] = [
  { key: 'permit_no', label: 'Permit No', sortable: true },
  { key: 'deceased_name', label: 'Deceased Name', sortable: false },
  { key: 'died_on', label: 'Date of Death', sortable: true },
  { key: 'buried_on', label: 'Burial Date', sortable: true },
  { key: 'grave_type', label: 'Grave Type', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
  { key: 'payment_status', label: 'Payment', sortable: true },
  { key: 'total_amount', label: 'Amount', sortable: true },
];

const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showDetailsModal = ref(false);

const editingBurial = ref<Record<string, any>>();
const deletingBurial = ref<Record<string, any> | null>(null);
const viewingBurial = ref<Record<string, any> | null>(null);
const isArchived = ref(String(props.filters?.isArchived) === 'true');
const serverArchived = computed(() => String(props.filters?.isArchived) === 'true');
const highlightedRowId = ref<number|null>(null);

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`burial-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || 'buried_on');
const direction = ref(props.filters?.direction || 'desc');
const graveType = ref(props.filters?.grave_type || '');
const status = ref(props.filters?.status || '');
const paymentStatus = ref(props.filters?.payment_status || '');
const parishId = ref(props.filters?.parish_id || '');
const startDate = ref(props.filters?.start_date || '');
const endDate = ref(props.filters?.end_date || '');

const partialOnly = ['data', 'filters'];
const searchTimeout = ref<number | null>(null);

function clearSearch() {
  search.value = '';
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  if (props.fetchUrl) {
    router.get(
      props.fetchUrl,
      {
        search: '',
        sort: sort.value,
        direction: direction.value,
        perPage: perPage.value,
        isArchived: isArchived.value ? 'true' : 'false',
        grave_type: graveType.value,
        status: status.value,
        payment_status: paymentStatus.value,
        parish_id: parishId.value,
        start_date: startDate.value,
        end_date: endDate.value,
        page: 1,
      },
      { preserveState: false, replace: true },
    );
  }
}

function restoreBurial(id: number) {
  router.post(route('graveyard.burials.restore', id), {}, {
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      isArchived.value = false;
    },
  });
}

function fetch(page = 1) {
  if (!props.fetchUrl) return;
  router.get(
    props.fetchUrl,
    {
      search: search.value,
      sort: sort.value,
      direction: direction.value,
      perPage: perPage.value,
      isArchived: isArchived.value ? 'true' : 'false',
      grave_type: graveType.value,
      status: status.value,
      payment_status: paymentStatus.value,
      parish_id: parishId.value,
      start_date: startDate.value,
      end_date: endDate.value,
      page,
    },
    { preserveState: true, preserveScroll: true, replace: true, only: partialOnly },
  );
}

watch(
  [search, sort, direction, perPage, isArchived, graveType, status, paymentStatus, parishId, startDate, endDate],
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

const enhancedData = computed(() => {
  const burials = props.data || {};
  return {
    data: burials.data || [],
    prev_page_url: burials.prev_page_url ?? burials.meta?.prev_page_url,
    next_page_url: burials.next_page_url ?? burials.meta?.next_page_url,
    current_page: burials.current_page ?? burials.meta?.current_page,
    last_page: burials.last_page ?? burials.meta?.last_page,
    total: burials.total ?? burials.meta?.total,
  };
});

function formatDate(date: string) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('en-IN', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

function formatCurrency(amount: number) {
  if (!amount) return '₹0';
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
}

function getDeceasedName(burial: any) {
  return `${burial.dead_first_name} ${burial.dead_last_name}`;
}

function getStatusBadgeClass(status: string) {
  const statusMap: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'confirmed': 'bg-blue-100 text-blue-800',
    'completed': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800',
  };
  return statusMap[status] || 'bg-gray-100 text-gray-800';
}

function getPaymentStatusBadgeClass(status: string) {
  const statusMap: Record<string, string> = {
    'pending': 'bg-red-100 text-red-800',
    'partial': 'bg-yellow-100 text-yellow-800',
    'paid': 'bg-green-100 text-green-800',
  };
  return statusMap[status] || 'bg-gray-100 text-gray-800';
}

function openDetailsModal(burial: any) {
  viewingBurial.value = burial;
  showDetailsModal.value = true;
}

function openDeleteModal(burial: any) {
  deletingBurial.value = burial;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (!deletingBurial.value) return;
  const deletedId = deletingBurial.value.id;

  router.delete(route('graveyard.burials.destroy', deletedId), {
    data: {
      perPage: perPage.value,
      page: enhancedData.value.current_page,
      search: search.value,
      sort: sort.value,
      direction: direction.value,
      isArchived: isArchived.value ? 'true' : 'false',
      grave_type: graveType.value,
      status: status.value,
      payment_status: paymentStatus.value,
      parish_id: parishId.value,
      start_date: startDate.value,
      end_date: endDate.value,
    },
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingBurial.value = null;
    },
  });
}

function handlePageChange(event: Event) {
  const target = event.target as HTMLSelectElement;
  if (target) {
    fetch(Number(target.value));
  }
}

const breadcrumbs = [
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Burials', href: '/graveyard/burials' }
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Burial Management" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Burial Management</h2>
        <Button v-if="canCreateBurial" as-child class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
          <Link href="/graveyard/burials/create">
            <Plus class="w-[1rem] h-[1rem]" />
            <span>New Burial</span>
          </Link>
        </Button>
      </div>
      
      <!-- Filters Section -->
      <div class="mb-4 space-y-3 rounded-lg bg-gray-50 px-4 py-3">
        <!-- First Row: Search and Per Page -->
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative">
          <input
            v-model="search"
              @keyup.enter="fetch()" 
            type="text"
              class="rounded-full border border-gray-300 px-3 py-1 pr-8 focus:ring-2 focus:ring-blue-200" 
              placeholder="Search by name, permit no..." 
              @keydown.escape="clearSearch" 
            />
            <button v-if="search" @click="clearSearch" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">✕</button>
          </div>
          <select v-model="perPage" @change="fetch()" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>

        <!-- Second Row: Advanced Filters -->
        <div class="flex flex-wrap items-center gap-3">
          <select v-model="graveType" @change="fetch()" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
            <option value="">All Grave Types</option>
            <option value="permanent">Permanent</option>
            <option value="temporary">Temporary</option>
          </select>
          
          <select v-model="status" @change="fetch()" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
          
          <select v-model="paymentStatus" @change="fetch()" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
            <option value="">All Payment Status</option>
            <option value="pending">Pending</option>
            <option value="partial">Partial</option>
            <option value="paid">Paid</option>
          </select>
          
          <select v-model="parishId" @change="fetch()" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
            <option value="">All Parishes</option>
            <option v-for="parish in parishes" :key="parish.id" :value="parish.id">
              {{ parish.name }}
            </option>
          </select>
        </div>

        <!-- Third Row: Date Filters and Archive Toggle -->
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
              <label class="text-sm font-medium">From:</label>
              <input 
                v-model="startDate" 
                @change="fetch()" 
                type="date" 
                class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200"
              />
            </div>
            <div class="flex items-center gap-2">
              <label class="text-sm font-medium">To:</label>
              <input 
                v-model="endDate" 
                @change="fetch()" 
                type="date" 
                class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200"
              />
            </div>
          </div>
          
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 cursor-pointer select-none">
              <Checkbox v-model="isArchived" class="switch-checkbox" />
              <span class="text-sm font-medium">Show Archived</span>
          </label>
          </div>
        </div>
      </div>
    </DatatableHeader>

    <div v-if="canReadAnyBurial">
      <!-- Compact pagination with inline stats above the table -->
      <div class="mb-2 flex items-center justify-between gap-3 bg-gray-50 px-3 py-1.5 rounded border border-gray-100 text-xs">
        <!-- Left side: Total records info -->
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ enhancedData?.total || 0 }}</span> total burials
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>
        
        <!-- Center: Pagination controls -->
        <div class="flex items-center gap-2">
          <button 
            v-if="enhancedData?.prev_page_url" 
            @click="fetch(enhancedData.current_page - 1)" 
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
          >
            ← Prev
          </button>
          
          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select 
              v-if="enhancedData?.last_page && enhancedData.last_page > 1"
              :value="enhancedData?.current_page" 
              @change="handlePageChange"
              class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 hover:bg-blue-50 transition focus:ring-1 focus:ring-[#3b82f6] focus:border-blue-500"
            >
              <option v-for="page in enhancedData.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ enhancedData?.last_page }}</span>
          </div>
          
          <button 
            v-if="enhancedData?.next_page_url" 
            @click="fetch(enhancedData.current_page + 1)" 
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
          >
            Next →
          </button>
        </div>
        
        <!-- Right side: Additional info -->
        <div class="text-gray-500">
          <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
            Burial Records
          </span>
        </div>
      </div>

      <div class="mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
        <!-- Table content -->
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
                v-for="burial in enhancedData.data" 
                :key="burial.id" 
                :id="`burial-row-${burial.id}`" 
                :class="['even:bg-gray-50 hover:bg-blue-50 transition', highlightedRowId === burial.id ? 'highlight-row' : '']"
              >
                <td class="p-2">
                  <template v-if="!serverArchived">
                    <div class="flex items-center gap-2">
                      <Button @click="openDetailsModal(burial)" class="rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition p-2">
                        <Eye class="w-[1rem] h-[1rem]" />
                      </Button>
                      <Button v-if="canUpdateAnyBurial" as-child class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition p-2">
                        <Link :href="`/graveyard/burials/${burial.id}/edit`">
                          <Pencil class="w-[1rem] h-[1rem]" />
                        </Link>
                      </Button>
                    </div>
                  </template>
                  <template v-else>
                    <Button v-if="canRestoreBurial" @click="restoreBurial(burial.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition p-2">
                      <RotateCcw class="w-[1rem] h-[1rem]" />
                    </Button>
                  </template>
                </td>
                
                <td class="p-2">
                  <div class="text-sm font-medium text-gray-900">{{ burial.permit_no || '-' }}</div>
                </td>
                
                <td class="p-2">
                  <div class="text-sm font-medium text-gray-900">{{ getDeceasedName(burial) }}</div>
                  <div class="text-xs text-gray-500">{{ burial.gender?.name }} • Age: {{ burial.age || '-' }}</div>
                </td>
                
                <td class="p-2">
                  <div class="text-sm text-gray-900">{{ formatDate(burial.died_on) }}</div>
                </td>
                
                <td class="p-2">
                  <div class="text-sm text-gray-900">{{ formatDate(burial.buried_on) }}</div>
                </td>
                
                <td class="p-2">
                  <span :class="[
                    'px-2 py-1 rounded-full text-xs font-medium capitalize',
                    burial.grave_type === 'permanent' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'
                  ]">
                    {{ burial.grave_type }}
                  </span>
                </td>
                
                <td class="p-2">
                  <span :class="[
                    'px-2 py-1 rounded-full text-xs font-medium capitalize',
                    getStatusBadgeClass(burial.status)
                  ]">
                    {{ burial.status }}
                  </span>
                </td>
                
                <td class="p-2">
                  <span :class="[
                    'px-2 py-1 rounded-full text-xs font-medium capitalize',
                    getPaymentStatusBadgeClass(burial.payment_status)
                  ]">
                    {{ burial.payment_status }}
                  </span>
            </td>
                
                <td class="p-2">
                  <div class="text-sm font-medium text-gray-900">{{ formatCurrency(burial.total_amount) }}</div>
            </td>
                
                <td v-if="!serverArchived" class="p-2">
                  <template v-if="canDeleteAnyBurial">
                    <Button @click="openDeleteModal(burial)" variant="destructive" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition p-2">
                      <Trash class="w-[1rem] h-[1rem]" />
                    </Button>
                  </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
    </div>
    <div v-else class="py-10 text-center text-gray-500">You do not have permission to view burials.</div>

    <!-- Details Modal -->
    <transition name="fade">
      <div v-if="showDetailsModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="showDetailsModal = false"></div>
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-2xl rounded-lg bg-gradient-to-r p-[2px] shadow-lg relative z-10">
          <div class="rounded-lg bg-[#ffffff] p-6 max-h-[80vh] overflow-y-auto">
            <h3 class="mb-4 text-xl font-semibold">Burial Details</h3>
            <div v-if="viewingBurial" class="space-y-4">
              <!-- Deceased Information -->
              <div class="border-b pb-4">
                <h4 class="font-semibold text-blue-700 mb-2 flex items-center gap-2">
                  <User class="w-4 h-4" />
                  Deceased Information
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div><strong>Name:</strong> {{ getDeceasedName(viewingBurial) }}</div>
                  <div><strong>Gender:</strong> {{ viewingBurial.gender?.name || '-' }}</div>
                  <div><strong>Date of Birth:</strong> {{ formatDate(viewingBurial.date_of_birth) }}</div>
                  <div><strong>Age:</strong> {{ viewingBurial.age || '-' }} years {{ viewingBurial.months || 0 }} months {{ viewingBurial.days || 0 }} days</div>
                  <div><strong>Date of Death:</strong> {{ formatDate(viewingBurial.died_on) }}</div>
                  <div><strong>Burial Date:</strong> {{ formatDate(viewingBurial.buried_on) }}</div>
                  <div class="col-span-2"><strong>Cause of Death:</strong> {{ viewingBurial.cause_of_death || '-' }}</div>
                </div>
              </div>

              <!-- Grave Information -->
              <div class="border-b pb-4">
                <h4 class="font-semibold text-blue-700 mb-2 flex items-center gap-2">
                  <MapPin class="w-4 h-4" />
                  Grave Information
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div><strong>Grave Type:</strong> {{ viewingBurial.grave_type }}</div>
                  <div><strong>Status:</strong> 
                    <span :class="['px-2 py-1 rounded-full text-xs font-medium capitalize ml-1', getStatusBadgeClass(viewingBurial.status)]">
                      {{ viewingBurial.status }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Administrative Information -->
              <div class="border-b pb-4">
                <h4 class="font-semibold text-blue-700 mb-2 flex items-center gap-2">
                  <Calendar class="w-4 h-4" />
                  Administrative Information
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div><strong>Permit No:</strong> {{ viewingBurial.permit_no || '-' }}</div>
                  <div><strong>Parish:</strong> {{ viewingBurial.parish?.name || '-' }}</div>
                  <div><strong>Minister:</strong> {{ viewingBurial.minister || '-' }}</div>
                  <div><strong>Contact:</strong> {{ viewingBurial.contact_no || '-' }}</div>
                  <div><strong>Applicant:</strong> {{ viewingBurial.applicant_name || (viewingBurial.applicant_member ? `${viewingBurial.applicant_member.first_name} ${viewingBurial.applicant_member.last_name}` : '-') }}</div>
                  <div><strong>Relationship:</strong> {{ viewingBurial.relationship || '-' }}</div>
                </div>
              </div>

              <!-- Payment Information -->
              <div>
                <h4 class="font-semibold text-blue-700 mb-2 flex items-center gap-2">
                  <DollarSign class="w-4 h-4" />
                  Payment Information
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div><strong>Total Amount:</strong> {{ formatCurrency(viewingBurial.total_amount) }}</div>
                  <div><strong>Payment Status:</strong> 
                    <span :class="['px-2 py-1 rounded-full text-xs font-medium capitalize ml-1', getPaymentStatusBadgeClass(viewingBurial.payment_status)]">
                      {{ viewingBurial.payment_status }}
                    </span>
                  </div>
                  <div class="col-span-2"><strong>Payment Remarks:</strong> {{ viewingBurial.payment_remarks || '-' }}</div>
                </div>
              </div>
            </div>
            
            <div class="mt-6 flex justify-end">
              <Button @click="showDetailsModal = false" class="rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition px-6 py-2">
                Close
              </Button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Delete Modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Burial Record</h3>
            <p>
              Are you sure you want to delete the burial record for 
              <span class="font-bold">{{ deletingBurial ? getDeceasedName(deletingBurial) : '' }}</span>?
            </p>
            <div class="mt-6 flex justify-end space-x-2">
              <Button
                type="button"
                @click="showDeleteModal = false"
                class="rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition px-6 py-2"
              >
                Cancel
              </Button>
              <Button
                type="button"
                @click="confirmDelete"
                class="rounded-full bg-red-600 text-white shadow hover:bg-red-700 transition px-6 py-2"
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
  box-shadow: 0 2px 8px 0 rgba(239, 68, 68, 0.25), 0 1.5px 4px 0 rgba(0,0,0,0.10);
  position: relative;
  transition: background 0.2s, box-shadow 0.2s;
}
.switch-checkbox[data-state="checked"] {
  background: #2563eb;
}
.switch-checkbox input[type="checkbox"] {
  opacity: 0;
  width: 100%;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
  margin: 0;
  cursor: pointer;
}
.switch-checkbox [data-slot="checkbox-indicator"] {
  position: absolute;
  left: 0.125rem;
  top: 0.125rem;
  width: 1rem;
  height: 1rem;
  border-radius: 9999px;
  background: #fff;
  transition: left 0.2s;
}
.switch-checkbox[data-state="checked"] [data-slot="checkbox-indicator"] {
  left: 1.375rem;
}
.highlight-row {
  animation: highlight-fade 2s;
  background-color: #fef08a !important; /* Tailwind yellow-200 */
}
@keyframes highlight-fade {
  0% { background-color: #fde047; }
  100% { background-color: inherit; }
}
</style>