<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';

import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Column } from '@/types';
import { router } from '@inertiajs/vue3';
import { ArchiveIcon, Pencil, Plus, Trash, ZapIcon, Download } from 'lucide-vue-next';
import { computed, ref, watch, nextTick, onMounted } from 'vue';
import { Checkbox } from '@/components/ui/checkbox';

const { can } = permissionHelpers();
const page = usePage();

const props = defineProps({
  externalMembers: Object,
  relationships: Array<{ id: string | number; name: string }>,
  totalCount: Number,
  filters: Object,
  fetchUrl: String,
  canViewAnyExternalMember: Boolean,
  canCreateExternalMember: Boolean,
  canEditExternalMember: Boolean,
  canDeleteExternalMember: Boolean,
  canRestoreExternalMember: Boolean,
  
  pagination: {
    type: Object,
    default: () => ({ currentPage: 1, lastPage: 1 }),
  },
});

// Use permission helper for all permission checks
const canViewAnyExternalMember = computed(() => {
  const result = can('list-external-member');
  return result;
});

const canCreateExternalMember = computed(() => {
  const result = can('create-external-member');
  return result;
});

const canEditExternalMember = computed(() => {
  const result = can('update-external-member');
  return result;
});

const canDeleteExternalMember = computed(() => {
  const result = can('delete-external-member');
  console.log('canDeleteExternalMember:', result);
  return result;
});

const canRestoreExternalMember = computed(() => {
  const result = can('restore-external-member');
  return result;
});

const showFilters = ref(false);
const search = ref(props.filters?.search || '');
const familySearch = ref(props.filters?.familySearch || '');
const sort = ref(props.filters?.sort || 'first_name');
const direction = ref(props.filters?.direction || 'asc');
const perPage = ref(props.filters?.perPage || 15);
const relationship = ref(props.filters?.relationship || '');
const filterColumnKey = ref(props.filters?.filterColumnKey || '');
const filterColumnValue = ref(props.filters?.filterColumnValue || '');
const isArchived = ref(props.filters?.isArchived || false);

// Debounced search to prevent too many API calls
let searchTimeout: number;

watch(
  [search, familySearch, sort, direction, perPage, relationship, filterColumnKey, filterColumnValue, isArchived],
  (newValues, oldValues) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      fetch();
    }, 300); // 300ms debounce
  },
  { immediate: false, deep: false },
);

function fetch(page = 1) {
  if (props.fetchUrl) {
    const params = {
      search: search.value,
      familySearch: familySearch.value,
      sort: sort.value,
      direction: direction.value,
      perPage: perPage.value,
      relationship: relationship.value,
      filterColumnKey: filterColumnKey.value,
      filterColumnValue: filterColumnValue.value,
      isArchived: isArchived.value ? 'true' : 'false',
      page,
    };

    router.get(
      props.fetchUrl,
      params,
      {
        preserveState: true,
        replace: true,
      },
    );
  }
}

function changeSort(field: string) {
  if (sort.value === field) {
    direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  } else {
    sort.value = field;
    direction.value = 'asc';
  }
  fetch();
}

function toggleisArchived() {
  isArchived.value = !isArchived.value;
}

// Search handling functions
function handleSearchInput() {
  // The watch will handle the debounced search
}

function handleFamilySearchInput() {
  // The watch will handle the debounced search
}

function clearSearch() {
  search.value = '';
  clearTimeout(searchTimeout);
  if (props.fetchUrl) {
    router.get(
      props.fetchUrl,
      {
        search: '',
        familySearch: familySearch.value,
        sort: sort.value,
        direction: direction.value,
        perPage: perPage.value,
        relationship: relationship.value,
        filterColumnKey: filterColumnKey.value,
        filterColumnValue: filterColumnValue.value,
        isArchived: isArchived.value ? 'true' : 'false',
      },
      {
        preserveState: false,
        replace: true,
      },
    );
  }
}

const enhancedExternalMembers = computed<Record<string, any>>(() => {
  let members = [];
  if (props.externalMembers?.data) {
    members = props.externalMembers.data.map((item: any) => ({
      ...item,
      added_on: item.created_at ? new Date(item.created_at).toLocaleDateString() : '',
      last_updated: item.updated_at ? new Date(item.updated_at).toLocaleDateString() : '',
    }));
  } else if (Array.isArray(props.externalMembers)) {
    members = props.externalMembers.map((item: any) => ({
      ...item,
      added_on: item.created_at ? new Date(item.created_at).toLocaleDateString() : '',
      last_updated: item.updated_at ? new Date(item.updated_at).toLocaleDateString() : '',
    }));
  }

  return {
    data: members,
    current_page: props.externalMembers?.current_page || 1,
    last_page: props.externalMembers?.last_page || 1,
    per_page: props.externalMembers?.per_page || 15,
    total: props.externalMembers?.total || members.length,
    from: props.externalMembers?.from || 1,
    to: props.externalMembers?.to || members.length,
    prev_page_url: props.externalMembers?.prev_page_url || null,
    next_page_url: props.externalMembers?.next_page_url || null,
  };
});

// Define columns matching member index structure
const columns: Column[] = [
  { key: 'first_name', label: 'First Name', sortable: true },
  { key: 'last_name', label: 'Last Name', sortable: true },
  { key: 'family_no', label: 'Family No', sortable: true },
  { key: 'external_member_no', label: 'External Member No', sortable: true },
  { key: 'address', label: 'Address', sortable: false },
];

const breadcrumbs = [{ title: 'External Members', href: '/external-members' }];

function editExternalMember(member: any) {
  router.get(route('external-members.edit', member.id));
}

function viewExternalMember(member: any) {
  router.get(route('external-members.show', member.id));
}

function addNewExternalMember() {
  router.get(route('external-members.create'));
}

function viewFamilyTreeForExternal(member: any) {
    router.get(route('member.family-tree', {id: member.id, type: 'external'}));
  }

const showDeleteModal = ref(false);
const deletingMember = ref<any>(null);

function openDeleteModal(member: any) {
  deletingMember.value = member;
  showDeleteModal.value = true;
}


function restoreExternalMember(member: any) {
  router.post(route('external-members.restore', member.id), {}, {
    onSuccess: () => {
      fetch();
    },
  });
}

function downloadExcel() {
  const params = {
    search: search.value,
    familySearch: familySearch.value,
    sort: sort.value,
    direction: direction.value,
    relationship: relationship.value,
    filterColumnKey: filterColumnKey.value,
    filterColumnValue: filterColumnValue.value,
    isArchived: isArchived.value ? 'true' : 'false',
  };

  const queryString = new URLSearchParams(params).toString();
  window.open(`/external-members/export?${queryString}`, '_blank');
}

function getRelationshipName(relationshipId: number | string) {
  if (!relationshipId) return '';
  const relationship = props.relationships?.find(r => r.id == relationshipId);
  return relationship?.name || '';
}

const highlightedRowId = ref<number | null>(null);

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`external-member-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

watch(() => enhancedExternalMembers.value.data, (rows) => {
  if (highlightedRowId.value) {
    let rowId = highlightedRowId.value;
    if (rowId === -1 && rows.length) {
      rowId = rows[rows.length - 1].id;
    }
    scrollToRow(rowId);
    highlightedRowId.value = null;
  }
});

onMounted(() => {
  const params = new URLSearchParams(window.location.search);
  const highlightId = params.get('highlightId');
  if (highlightId) {
    highlightedRowId.value = Number(highlightId);
    scrollToRow(Number(highlightId));
  }
});
function confirmDelete() {
  if (deletingMember.value) {
    router.delete(route('external-members.destroy', deletingMember.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false;
        deletingMember.value = null;
      },
    });
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">

    <Head title="External Members" />

    <DatatableHeader>
      <div class="mb-2 flex flex-wrap items-center gap-2 justify-between">
        <div class="flex flex-1 items-center gap-2">
          <div class="flex-1 relative">
            <input v-model="search" type="text" class="w-full rounded-full border border-gray-300 px-3 py-2 pr-8"
              placeholder="Search name or family no..." @input="handleSearchInput" @keydown.escape="clearSearch" />
            <button v-if="search" @click="clearSearch"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
              ✕
            </button>
          </div>
          <div class="relative">
            <input v-model="familySearch" type="text" class="w-48 rounded-full border border-gray-300 px-3 py-2 pr-8"
              placeholder="Search by family no..." @input="handleFamilySearchInput"
              @keydown.escape="clearSearch" />
            <button v-if="familySearch" @click="clearSearch"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
              ✕
            </button>
          </div>
          <button @click="showFilters = !showFilters" class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 text-sm">
            {{ showFilters ? 'Hide Filters' : 'More Filters' }}
          </button>
        </div>
        <div class="flex items-center gap-2">
          <Button v-if="props.canViewAnyExternalMember" @click="addNewExternalMember"
            class="px-3 py-2 rounded-full bg-green-600 text-white hover:bg-green-700 transition flex items-center gap-2">
            <component :is="Plus" />
            <span>Add New Member</span>
          </Button>
          <Button @click="downloadExcel"
            class="flex items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-white shadow hover:bg-green-700 transition">
            <component :is="Download" />
            <span>Export Excel</span>
          </Button>

          <label class="flex items-center gap-2 cursor-pointer select-none">
            <Checkbox v-model="isArchived" class="switch-checkbox" />
            <span class="text-sm font-medium">Show Archived</span>
          </label>
        </div>
      </div>
      <transition name="fade">
        <div v-if="showFilters" class="flex flex-wrap gap-2 mb-2">
          <select v-model="relationship" class="rounded border px-2 py-1 text-sm">
            <option value="">All Relationships</option>
            <option v-for="r in props.relationships" :key="r.id" :value="r.id">{{ r.name }}</option>
          </select>

          <select v-model="perPage" class="rounded border px-2 py-1 text-sm">
            <option :value="10">10 per page</option>
            <option :value="25">25 per page</option>
            <option :value="50">50 per page</option>
            <option :value="100">100 per page</option>
          </select>
        </div>
      </transition>
    </DatatableHeader>

    <div class="overflow-x-auto">
      <div v-if="canViewAnyExternalMember">
        <!-- Always show content for now to test -->
        <div class="datatable2 mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
          <!-- Table -->
          <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full border-collapse text-left">
              <thead>
                <tr class="bg-blue-50">
                  <th class="border-b p-3 font-semibold text-gray-700 whitespace-nowrap">Actions</th>
                  <th v-for="col in columns" :key="col.key" @click="col.sortable ? changeSort(col.key) : null"
                    class="cursor-pointer border-b p-3 font-semibold text-gray-700 hover:bg-blue-100 transition whitespace-nowrap">
                    {{ col.label }}
                    <span v-if="col.sortable && sort === col.key">
                      {{ direction === 'asc' ? '▲' : '▼' }}
                    </span>
                  </th>
                  <th v-if="!isArchived" class="border-b p-3 font-semibold text-gray-700 whitespace-nowrap">Delete</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="member in enhancedExternalMembers.data" :key="member.id"
                  :id="`external-member-row-${member.id}`" :class="[
                    'even:bg-gray-50 hover:bg-blue-50 transition',
                    highlightedRowId === member.id ? 'highlight-row' : ''
                  ]">
                  <!-- Actions -->
                  <td class="p-2 whitespace-nowrap">
                    <div class="flex gap-2">
                      <template v-if="!isArchived">
                        <Button v-if="canViewAnyExternalMember" @click="viewExternalMember(member)"
                          class="rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                          <component :is="ZapIcon" />
                        </Button>
                        <Button v-if="canEditExternalMember && !member.deleted_at" @click="editExternalMember(member)"
                          class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                          <component :is="Pencil" />
                        </Button>
                        <Button
                          @click="viewFamilyTreeForExternal(member)"
                          class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition"
                          title="View Family Tree"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                          </svg>
                        </Button>
                      </template>
                      <template v-else>
                        <Button v-if="canRestoreExternalMember" @click="restoreExternalMember(member)"
                          class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
                          Restore
                        </Button>
                      </template>
                    </div>
                  </td>

                  <!-- Data columns -->
                  <td v-for="col in columns" :key="col.key" class="p-2 whitespace-nowrap overflow-hidden">
                    <template v-if="col.key === 'family_no'">
                      <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium font-mono">
                        {{ member[col.key] || '—' }}
                      </span>
                    </template>
                    <template v-else-if="col.key === 'external_member_no'">
                      <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium font-mono">
                        {{ member[col.key] || '—' }}
                      </span>
                    </template>
                    <template v-else-if="col.key === 'relationship_id'">
                      <!-- Display relationship name instead of ID -->
                      <span v-if="member.relationship?.name" class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ member.relationship.name }}
                      </span>
                      <span v-else class="text-gray-400">—</span>
                    </template>
                    <template v-else-if="col.key === 'father_id'">
                      <span v-if="member.father_data?.name" class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ member.father_data.name }}
                        <span class="text-xs text-gray-500">({{ member.father_data.type }})</span>
                      </span>
                      <span v-else class="text-gray-400">—</span>
                    </template>
                    <template v-else-if="col.key === 'mother_id'">
                      <span v-if="member.mother_data?.name" class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ member.mother_data.name }}
                        <span class="text-xs text-gray-500">({{ member.mother_data.type }})</span>
                      </span>
                      <span v-else class="text-gray-400">—</span>
                    </template>
                    <template v-else-if="col.key === 'spouse_id'">
                      <span v-if="member.spouse_data?.name" class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ member.spouse_data.name }}
                        <span class="text-xs text-gray-500">({{ member.spouse_data.type }})</span>
                      </span>
                      <span v-else class="text-gray-400">—</span>
                    </template>
                    <template v-else-if="col.key === 'address'">
                      <span class="block truncate max-w-xs" :title="member[col.key] || '—'">
                        {{ member[col.key] || '—' }}
                      </span>
                    </template>
                    <template v-else>
                      <span class="block truncate" :title="member[col.key]">{{ member[col.key] || '—' }}</span>
                    </template>
                  </td>

                  <!-- Delete -->
                  <td v-if="!isArchived" class="p-2 whitespace-nowrap">
                    <template v-if="canDeleteExternalMember && !member.deleted_at">
                      <Button variant="destructive" @click="openDeleteModal(member)"
                        class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
                        <component :is="Trash" />
                      </Button>
                    </template>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
              <button v-if="enhancedExternalMembers.prev_page_url"
                @click="fetch(enhancedExternalMembers.current_page - 1)"
                class="rounded-full border border-gray-300 bg-white px-4 py-2 text-gray-700 shadow hover:bg-blue-50 transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Prev
              </button>

              <!-- Page Number Dropdown -->
              <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Page</span>
                <select v-if="enhancedExternalMembers.last_page && enhancedExternalMembers.last_page > 1"
                  :value="enhancedExternalMembers.current_page"
                  @change="(event) => fetch(Number((event.target as HTMLSelectElement).value))"
                  class="rounded-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow hover:bg-blue-50 transition focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                  <option v-for="page in enhancedExternalMembers.last_page" :key="page" :value="page">
                    {{ page }}
                  </option>
                </select>
                <span v-if="enhancedExternalMembers.last_page" class="text-sm text-gray-600">of {{
                  enhancedExternalMembers.last_page }}</span>
              </div>

              <button v-if="enhancedExternalMembers.next_page_url"
                @click="fetch(enhancedExternalMembers.current_page + 1)"
                class="rounded-full border border-gray-300 bg-white px-4 py-2 text-gray-700 shadow hover:bg-blue-50 transition flex items-center gap-1">
                Next
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>

            <!-- Total Records Info -->
            <div class="text-sm text-gray-500">
              <span v-if="enhancedExternalMembers.total">Total: {{ enhancedExternalMembers.total }} records</span>
            </div>
          </div>
        </div>
      </div>
    <div v-else class="py-10 text-center text-gray-500">You do not have permission to view members.</div>
    </div>
 </AppLayout>
 <transition name="fade">
    <div v-if="showDeleteModal"
      class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
      <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
        <div class="rounded-lg bg-white p-6">
          <h3 class="mb-4 text-xl font-semibold">Delete Member</h3>
          <p>
            Are you sure you want to delete <span class="font-bold">{{ deletingMember?.first_name }} {{
              deletingMember?.last_name }}</span>?
          </p>
          <div class="mt-6 flex justify-end space-x-2">
            <Button variant="secondary" type="button" @click="showDeleteModal = false"
              class="rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition px-6 py-2">
              Cancel
            </Button>
            <Button variant="destructive" type="button" :disabled="false" @click="confirmDelete"
              class="rounded-full bg-red-600 text-white shadow hover:bg-red-700 transition px-6 py-2 flex items-center gap-2">
              Delete
            </Button>
          </div>
        </div>
      </div>
    </div>
  </transition>
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
  box-shadow: 0 2px 8px 0 rgba(239, 68, 68, 0.25), 0 1.5px 4px 0 rgba(0, 0, 0, 0.10);
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
  0% {
    background-color: #fde047;
  }

  100% {
    background-color: inherit;
  }
}
</style>
