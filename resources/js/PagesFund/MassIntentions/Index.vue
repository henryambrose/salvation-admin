<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Pencil, Trash, RotateCcw, Plus } from 'lucide-vue-next';
import { computed, ref, watch, nextTick } from 'vue';
import { permissionHelpers } from '@/composables/permissionHelpers';

const { can } = permissionHelpers();

const props = defineProps({
  massIntentions: Object,
  massTypes: Array,
  massIntentionTypes: Array,
  filters: Object,
});

// Permission checks
const canCreateMassIntention = can('create-fund-mass-intention') || true;
const canReadAnyMassIntention = can('read-fund-mass-intention') || true;
const canUpdateAnyMassIntention = can('update-fund-mass-intention') || true;
const canDeleteAnyMassIntention = can('delete-fund-mass-intention') || true;
const canRestoreMassIntention = can('restore-fund-mass-intention') || true;

const showDeleteModal = ref(false);
const deletingIntention = ref<Record<string, any> | null>(null);
const isArchived = ref(String(props.filters?.isArchived) === 'true');
const serverArchived = computed(() => String(props.filters?.isArchived) === 'true');
const highlightedRowId = ref<number|null>(null);

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`mass-intention-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

// Local filters state
const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const massTypeId = ref(props.filters?.mass_type_id || '');
const massIntentionTypeId = ref(props.filters?.mass_intention_type_id || '');
const startDate = ref(props.filters?.start_date || '');
const endDate = ref(props.filters?.end_date || '');
const perPage = ref(props.filters?.per_page || 10);

const partialOnly = ['massIntentions', 'filters'];
const searchTimeout = ref<number | null>(null);

function clearSearch() {
  search.value = '';
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  fetch();
}

function restoreMassIntention(id: number) {
  router.post(route('fund.mass-intentions.restore', id), {}, {
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      highlightedRowId.value = id;
      nextTick(() => scrollToRow(id));
    },
  });
}

function fetch(page = 1) {
  router.get(
    route('fund.mass-intentions.index'),
    {
      search: search.value,
      status: status.value,
      mass_type_id: massTypeId.value,
      mass_intention_type_id: massIntentionTypeId.value,
      start_date: startDate.value,
      end_date: endDate.value,
      per_page: perPage.value,
      isArchived: isArchived.value ? 'true' : 'false',
      page,
    },
    { preserveState: true, preserveScroll: true, replace: true, only: partialOnly },
  );
}

watch(
  [search, status, massTypeId, massIntentionTypeId, startDate, endDate, perPage, isArchived],
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

const enhancedMassIntentions = computed(() => {
  const intentions = props.massIntentions || {};
  return {
    data: intentions.data || [],
    prev_page_url: intentions.prev_page_url,
    next_page_url: intentions.next_page_url,
    current_page: intentions.current_page,
    last_page: intentions.last_page,
    total: intentions.total,
    from: intentions.from,
    to: intentions.to,
  };
});

function openDeleteModal(row: any) {
  deletingIntention.value = row;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (!deletingIntention.value) return;
  const deletedId = deletingIntention.value.id;

  router.delete(route('fund.mass-intentions.destroy', deletedId), {
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingIntention.value = null;
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
  status.value = '';
  massTypeId.value = '';
  massIntentionTypeId.value = '';
  startDate.value = '';
  endDate.value = '';
  perPage.value = 10;
}

// Utility functions
function formatDate(dateString: string) {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-IN');
}

function getStatusBadgeClass(status: string) {
  const classes = {
    'pending': 'px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full',
    'confirmed': 'px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full',
    'completed': 'px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full',
    'cancelled': 'px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full'
  };
  return classes[status as keyof typeof classes] || classes.pending;
}

const breadcrumbs = [
  { title: 'Fund', href: '/fund' },
  { title: 'Mass Intentions', href: '/fund/mass-intentions' }
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Mass Intentions" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Mass Intentions</h2>
        <Button v-if="canCreateMassIntention" @click="router.visit(route('fund.mass-intentions.create'))" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
          <Plus class="w-[1rem] h-[1rem]" />
          <span>Book New Intention</span>
        </Button>
          </div>

      <!-- Filters Section -->
      <div class="mb-4 rounded-lg bg-gray-50 p-4">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
          <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input v-model="search" type="text" placeholder="Search intentions..." class="w-full rounded-full border border-gray-300 px-3 py-1 pr-8 focus:ring-2 focus:ring-blue-200" @keydown.escape="clearSearch" />
            <button v-if="search" @click="clearSearch" class="absolute right-2 top-8 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">✕</button>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select v-model="status" class="w-full rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
                  <option value="">All Statuses</option>
                  <option value="pending">Pending</option>
                  <option value="confirmed">Confirmed</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mass Type</label>
            <select v-model="massTypeId" class="w-full rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
                  <option value="">All Types</option>
                                <option v-for="type in massTypes" :key="(type as any).id" :value="(type as any).id">
                {{ (type as any).name }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Intention Type</label>
            <select v-model="massIntentionTypeId" class="w-full rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
                  <option value="">All Types</option>
                                <option v-for="type in massIntentionTypes" :key="(type as any).id" :value="(type as any).id">
                {{ (type as any).name }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input v-model="startDate" type="date" class="w-full rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200" />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input v-model="endDate" type="date" class="w-full rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200" />
              </div>
              
              <div class="flex items-end">
            <button @click="clearFilters" class="w-full px-4 py-1 bg-gray-500 text-white rounded-full hover:bg-gray-600 transition">
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
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <Checkbox v-model="isArchived" class="switch-checkbox" />
            <span class="text-sm font-medium">Show Archived</span>
          </label>
        </div>
      </div>
    </DatatableHeader>
    
    <div v-if="canReadAnyMassIntention">
      <!-- Compact pagination with inline stats above the table -->
      <div class="mb-2 flex items-center justify-between gap-3 bg-gray-50 px-3 py-1.5 rounded border border-gray-100 text-xs">
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ enhancedMassIntentions.total || 0 }}</span> total mass intentions
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>
        
        <div class="flex items-center gap-2">
          <button 
            v-if="enhancedMassIntentions.prev_page_url" 
            @click="fetch(enhancedMassIntentions.current_page - 1)" 
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
          >
            ← Prev
          </button>
          
          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select 
              v-if="enhancedMassIntentions.last_page && enhancedMassIntentions.last_page > 1"
              :value="enhancedMassIntentions.current_page" 
              @change="handlePageChange"
              class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
            >
              <option v-for="page in enhancedMassIntentions.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ enhancedMassIntentions.last_page }}</span>
          </div>
          
          <button 
            v-if="enhancedMassIntentions.next_page_url" 
            @click="fetch(enhancedMassIntentions.current_page + 1)" 
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
          >
            Next →
          </button>
        </div>
        
        <div class="text-gray-500">
          <span class="px-2 py-1 bg-teal-100 text-teal-800 rounded-full text-xs font-medium">
            Mass Intentions
          </span>
        </div>
      </div>

      <div class="mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
        <div class="overflow-x-auto rounded-xl border border-gray-100">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr class="bg-blue-50">
                <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
                <th class="border-b p-3 font-semibold text-gray-700">Person</th>
                <th class="border-b p-3 font-semibold text-gray-700">Family No</th>
                <th class="border-b p-3 font-semibold text-gray-700">Mass Date & Type</th>
                <th class="border-b p-3 font-semibold text-gray-700">Intention Type</th>
                <th class="border-b p-3 font-semibold text-gray-700">Amount</th>
                <th class="border-b p-3 font-semibold text-gray-700">Status</th>
                <th class="border-b p-3 font-semibold text-gray-700">Created</th>
                <th v-if="!serverArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
                 </tr>
               </thead>
            <tbody>
              <tr v-for="intention in enhancedMassIntentions.data" :key="intention.id" :id="`mass-intention-row-${intention.id}`" :class="['even:bg-gray-50 hover:bg-blue-50 transition', highlightedRowId === intention.id ? 'highlight-row' : '']">
                <td class="p-2">
                  <template v-if="!serverArchived">
                    <div class="flex items-center gap-2">
                      <Button v-if="canUpdateAnyMassIntention" @click="router.visit(route('fund.mass-intentions.edit', intention.id))" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition p-2">
                        <Pencil class="w-[1rem] h-[1rem]" />
                      </Button>
                     </div>
                  </template>
                  <template v-else>
                    <Button v-if="canRestoreMassIntention" @click="restoreMassIntention(intention.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition p-2">
                      <RotateCcw class="w-[1rem] h-[1rem]" />
                    </Button>
                  </template>
                   </td>
                <td class="p-2">
                      <div class="text-sm font-medium text-gray-900">
                        <div v-if="intention.member">
                          {{ intention.member.first_name }} {{ intention.member.last_name }}
                        </div>
                        <div v-else>
                          {{ intention.non_member_name }}
                        </div>
                      </div>
                                            <div v-if="intention.special_instructions" class="text-sm text-gray-500">{{ intention.special_instructions }}</div>
                    </td>
                <td class="p-2">
                      <div class="text-sm text-gray-900">
                        <div v-if="intention.member">
                          {{ intention.member.family_no }}
                        </div>
                        <div v-else class="text-gray-400">
                          -
                        </div>
                      </div>
                    </td>
                <td class="p-2">
                    <div class="text-sm text-gray-900">{{ formatDate(intention.mass_date) }}</div>
                    <div v-if="intention.mass_type" class="text-sm text-gray-500">{{ intention.mass_type.name }}</div>
                  </td>
                <td class="p-2">
                  <div class="text-sm text-gray-900">{{ intention.mass_intention_type?.name || '-' }}</div>
                  </td>
                <td class="p-2">
                    <div class="text-sm font-medium text-gray-900">₹{{ intention.amount }}</div>
                  </td>
                <td class="p-2">
                    <span :class="getStatusBadgeClass(intention.status)">
                      {{ intention.status }}
                    </span>
                  </td>
                <td class="p-2 text-sm text-gray-500">
                     {{ formatDate(intention.created_at) }}
                   </td>
                <td v-if="!serverArchived" class="p-2">
                  <template v-if="canDeleteAnyMassIntention">
                    <Button @click="openDeleteModal(intention)" variant="destructive" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition p-2">
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

    <!-- Delete Modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Mass Intention</h3>
            <p>
              Are you sure you want to delete this mass intention for 
              <span class="font-bold">
                {{ deletingIntention?.member?.first_name }} {{ deletingIntention?.member?.last_name || deletingIntention?.non_member_name }}
              </span>?
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
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
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
  background-color: #fef08a !important;
}
@keyframes highlight-fade {
  0% { background-color: #fde047; }
  100% { background-color: inherit; }
}
</style>
