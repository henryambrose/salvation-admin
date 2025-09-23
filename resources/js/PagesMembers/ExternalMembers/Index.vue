<script setup lang="ts">
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Column } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ArchiveIcon, Download, Pencil, Plus, Trash } from 'lucide-vue-next';
import { computed, nextTick, onMounted, ref, watch } from 'vue';

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

const showFilters = ref(false);
// Ensure filters is an object, not an array
const filters = Array.isArray(props.filters) ? {} : props.filters || {};

const search = ref(filters.search || '');
const familySearch = ref(filters.familySearch || '');
const sortField = ref(filters.sort || 'first_name');
const sortDirection = ref(filters.direction || 'asc');
const perPage = ref(filters.perPage || 15);
const relationship = ref(filters.relationship || '');
const filterColumnKey = ref(filters.filterColumnKey || '');
const filterColumnValue = ref(filters.filterColumnValue || '');
const isArchived = ref(String(filters.isArchived) === 'true');
const serverArchived = computed(() => String(filters.isArchived) === 'true');
const partialOnly = ['externalMembers', 'filters'];

// Debounced search to prevent too many API calls
let searchTimeout: number;

watch(
  [search, familySearch, sortField, sortDirection, perPage, relationship, filterColumnKey, filterColumnValue, isArchived],
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
      sort: sortField.value,
      direction: sortDirection.value,
      perPage: perPage.value,
      relationship: relationship.value,
      filterColumnKey: filterColumnKey.value,
      filterColumnValue: filterColumnValue.value,
      isArchived: isArchived.value ? 'true' : 'false',
      page,
    };

    router.get(props.fetchUrl, params, {
      preserveState: true,
      replace: true,
      only: partialOnly,
    });
  }
}

function changeSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
  fetch();
}

function handleSearchInput() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetch();
  }, 300);
}

function handleFamilySearchInput() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetch();
  }, 300);
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
        sort: sortField.value,
        direction: sortDirection.value,
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

function addNewExternalMember() {
  router.get(route('external-members.create'));
}

const showDeleteModal = ref(false);
const deletingMember = ref<any>(null);

function openDeleteModal(member: any) {
  deletingMember.value = member;
  showDeleteModal.value = true;
}

function restoreExternalMember(member: any) {
  router.post(
    route('external-members.restore', member.id),
    {},
    {
      onSuccess: () => {
        isArchived.value = false;
      },
    },
  );
}

function downloadCsv() {
  const params = new URLSearchParams({
    search: search.value || '',
    sort: sortField.value || 'first_name',
    direction: sortDirection.value || 'asc',
    perPage: 'all',
    isArchived: isArchived.value ? 'true' : 'false',
  });
  window.location.href = `${window.location.origin}/external-members/export?${params.toString()}`;
}

function viewFamilyTree(member: any) {
  router.get(route('member.family-tree', { id: member.id, type: 'external' }));
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

watch(
  () => enhancedExternalMembers.value.data,
  (rows) => {
    if (highlightedRowId.value) {
      let rowId = highlightedRowId.value;
      if (rowId === -1 && rows.length) {
        rowId = rows[rows.length - 1].id;
      }
      scrollToRow(rowId);
      highlightedRowId.value = null;
    }
  },
);

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
    const deletedId = deletingMember.value.id;
    router.delete(route('external-members.destroy', deletedId), {
      data: {
        perPage: perPage.value,
        page: enhancedExternalMembers.value.current_page,
        search: search.value,
        familySearch: familySearch.value,
        relationship: relationship.value,
        sort: sortField.value,
        direction: sortDirection.value,
        isArchived: isArchived.value ? 'true' : 'false',
      },
      preserveScroll: true,
      only: partialOnly,
      onSuccess: () => {
        showDeleteModal.value = false;
        deletingMember.value = null;
        highlightedRowId.value = deletedId + 1;
        nextTick(() => scrollToRow(deletedId + 1));
      },
    });
  }
}

const copiedItem = ref<{ id: string; type: string } | null>(null);

function copyToClipboard(text: string, type: string, memberId: number) {
  if (!text || text === '—') return;

  navigator.clipboard
    .writeText(text)
    .then(() => {
      // Set copied state for visual feedback
      copiedItem.value = { id: `${type}-${memberId}`, type };

      // Show success toast
      const toast = document.createElement('div');
      toast.className =
        'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform transition-all duration-300 flex items-center gap-2';
      toast.innerHTML = `
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
      </svg>
      ${type} copied to clipboard!
    `;
      document.body.appendChild(toast);

      // Remove toast after 2 seconds
      setTimeout(() => {
        toast.remove();
      }, 2000);

      // Clear copied state after animation
      setTimeout(() => {
        copiedItem.value = null;
      }, 1000);
    })
    .catch((err) => {
      console.error('Failed to copy: ', err);
      // Fallback for older browsers
      const textArea = document.createElement('textarea');
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand('copy');
      document.body.removeChild(textArea);

      // Show success feedback
      const toast = document.createElement('div');
      toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform transition-all duration-300';
      toast.textContent = `${type} copied to clipboard!`;
      document.body.appendChild(toast);

      setTimeout(() => {
        toast.remove();
      }, 2000);
    });
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="External Members" />

    <DatatableHeader>
      <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
        <div class="flex flex-1 items-center gap-2">
          <div class="relative flex-1">
            <input
              v-model="search"
              type="text"
              class="w-full rounded-full border border-gray-300 px-3 py-2 pr-8"
              placeholder="Search name or family no..."
              @input="handleSearchInput"
              @keydown.escape="clearSearch"
            />
            <button v-if="search" @click="clearSearch" class="absolute top-1/2 right-2 -translate-y-1/2 transform text-gray-400 hover:text-gray-600">
              ✕
            </button>
          </div>
          <div class="relative">
            <input
              v-model="familySearch"
              type="text"
              class="w-48 rounded-full border border-gray-300 px-3 py-2 pr-8"
              placeholder="Search by family no..."
              @input="handleFamilySearchInput"
              @keydown.escape="clearSearch"
            />
            <button
              v-if="familySearch"
              @click="clearSearch"
              class="absolute top-1/2 right-2 -translate-y-1/2 transform text-gray-400 hover:text-gray-600"
            >
              ✕
            </button>
          </div>
          <!-- More Filters Button - Exact same styling as member index -->
          <Tooltip>
            <TooltipTrigger asChild>
              <Button
                @click="showFilters = !showFilters"
                class="rounded border border-blue-200 bg-blue-100 px-3 py-2 text-sm font-medium text-blue-800 transition-colors hover:border-blue-300 hover:bg-blue-200 hover:text-blue-900"
              >
                {{ showFilters ? 'Hide Filters' : 'More Filters' }}
              </Button>
            </TooltipTrigger>
            <TooltipContent>
              <p>Toggle advanced filters</p>
            </TooltipContent>
          </Tooltip>
        </div>
        <div class="flex items-center gap-2">
          <Button
            v-if="props.canViewAnyExternalMember"
            @click="addNewExternalMember"
            class="flex items-center gap-2 rounded-full bg-green-600 px-3 py-2 text-white transition hover:bg-green-700"
          >
            <component :is="Plus" />
            <span>Add New Member</span>
          </Button>
          <Button
            @click="downloadCsv"
            class="flex items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-white shadow transition hover:bg-green-700"
          >
            <component :is="Download" />
            <span>Export CSV</span>
          </Button>

          <label class="flex cursor-pointer items-center gap-2 select-none">
            <Checkbox v-model="isArchived" class="switch-checkbox" />
            <span class="text-sm font-medium">Show Archived</span>
          </label>
        </div>
      </div>
      <!-- Dropdowns - Exact same styling as member index -->
      <transition name="fade">
        <div v-if="showFilters" class="mb-2 flex flex-wrap gap-2">
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
        <!-- Compact pagination with inline stats above the table -->
        <div class="mb-2 flex items-center justify-between gap-3 rounded border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs">
          <!-- Left side: Total records info -->
          <div class="text-gray-600">
            Showing <span class="font-semibold">{{ enhancedExternalMembers.total || 0 }}</span> total external members
            <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
          </div>

          <!-- Center: Pagination controls -->
          <div class="flex items-center gap-2">
            <button
              v-if="enhancedExternalMembers.prev_page_url"
              @click="fetch(enhancedExternalMembers.current_page - 1)"
              class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
            >
              ← Prev
            </button>

            <div class="flex items-center gap-1 text-gray-600">
              <span>Page</span>
              <!-- Pagination dropdown - Exact same styling as member index -->
              <select
                v-if="enhancedExternalMembers.last_page && enhancedExternalMembers.last_page > 1"
                :value="enhancedExternalMembers.current_page"
                @change="(event) => fetch(Number((event.target as HTMLSelectElement).value))"
                class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50 focus:border-blue-500 focus:ring-1 focus:ring-[#3b82f6]"
              >
                <option v-for="page in enhancedExternalMembers.last_page" :key="page" :value="page">
                  {{ page }}
                </option>
              </select>
              <span>of {{ enhancedExternalMembers.last_page }}</span>
            </div>

            <button
              v-if="enhancedExternalMembers.next_page_url"
              @click="fetch(enhancedExternalMembers.current_page + 1)"
              class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
            >
              Next →
            </button>
          </div>

          <!-- Right side: Additional info (can be customized) -->
          <div class="text-gray-500">
            <span class="rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-800"> External Members </span>
          </div>
        </div>

        <div class="datatable2 mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
          <!-- Table -->
          <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full border-collapse text-left">
              <thead>
                <tr class="bg-blue-50">
                  <th class="border-b p-3 font-semibold whitespace-nowrap text-gray-700">Actions</th>
                  <th
                    v-for="col in columns"
                    :key="col.key"
                    @click="col.sortable ? changeSort(col.key) : null"
                    class="cursor-pointer border-b p-3 font-semibold whitespace-nowrap text-gray-700 transition hover:bg-blue-100"
                  >
                    {{ col.label }}
                    <span v-if="col.sortable && sortField === col.key">
                      {{ sortDirection === 'asc' ? '▲' : '▼' }}
                    </span>
                  </th>
                  <th v-if="!serverArchived" class="border-b p-3 font-semibold whitespace-nowrap text-gray-700">Delete</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="member in enhancedExternalMembers.data"
                  :key="member.id"
                  :id="`external-member-row-${member.id}`"
                  :class="['transition even:bg-gray-50 hover:bg-blue-50', highlightedRowId === member.id ? 'highlight-row' : '']"
                >
                  <!-- Actions -->
                  <td class="p-2 whitespace-nowrap">
                    <div class="flex gap-2">
                      <template v-if="!serverArchived">
                        <TooltipProvider>
                          <Tooltip>
                            <TooltipTrigger asChild>
                              <Button
                                v-if="canEditExternalMember && !member.deleted_at"
                                @click="editExternalMember(member)"
                                class="rounded-full bg-yellow-100 text-yellow-700 transition hover:bg-yellow-200"
                              >
                                <component :is="Pencil" />
                              </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                              <p>Edit external member details</p>
                            </TooltipContent>
                          </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider>
                          <Tooltip>
                            <TooltipTrigger asChild>
                              <Button
                                @click="viewFamilyTree(member)"
                                class="rounded-full bg-green-100 text-green-700 transition hover:bg-green-200"
                                title="View Family Tree"
                              >
                                <svg class="h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"
                                  ></path>
                                  <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"
                                  ></path>
                                </svg>
                              </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                              <p>View family tree</p>
                            </TooltipContent>
                          </Tooltip>
                        </TooltipProvider>
                      </template>
                      <template v-else>
                        <TooltipProvider>
                          <Tooltip>
                            <TooltipTrigger asChild>
                              <Button
                                v-if="canRestoreExternalMember"
                                @click="restoreExternalMember(member)"
                                class="rounded-full bg-green-100 text-green-700 transition hover:bg-green-200"
                              >
                                <component :is="ArchiveIcon" />
                              </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                              <p>Restore external member</p>
                            </TooltipContent>
                          </Tooltip>
                        </TooltipProvider>
                      </template>
                    </div>
                  </td>

                  <!-- Data columns -->
                  <td v-for="col in columns" :key="col.key" class="overflow-hidden p-2 whitespace-nowrap">
                    <template v-if="col.key === 'family_no'">
                      <span
                        v-if="member[col.key]"
                        @click="copyToClipboard(member[col.key], 'Family Number', member.id)"
                        :class="[
                          'cursor-pointer rounded-full px-2 py-1 font-mono text-xs font-medium transition-all duration-200 select-none',
                          copiedItem?.id === `Family Number-${member.id}`
                            ? 'scale-105 bg-green-200 text-green-900 shadow-md'
                            : 'bg-green-100 text-green-800 hover:scale-105 hover:bg-green-200',
                        ]"
                        :title="`Click to copy: ${member[col.key]}`"
                      >
                        {{ member[col.key] }}
                        <svg class="ml-1 inline h-3 w-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                          ></path>
                        </svg>
                      </span>
                      <span v-else class="rounded-full bg-gray-100 px-2 py-1 font-mono text-xs font-medium text-gray-500"> — </span>
                    </template>
                    <template v-else-if="col.key === 'external_member_no'">
                      <span
                        v-if="member[col.key]"
                        @click="copyToClipboard(member[col.key], 'Member Number', member.id)"
                        :class="[
                          'cursor-pointer rounded-full px-2 py-1 font-mono text-xs font-medium transition-all duration-200 select-none',
                          copiedItem?.id === `Member Number-${member.id}`
                            ? 'scale-105 bg-purple-200 text-purple-900 shadow-md'
                            : 'bg-purple-100 text-purple-800 hover:scale-105 hover:bg-purple-200',
                        ]"
                        :title="`Click to copy: ${member[col.key]}`"
                      >
                        {{ member[col.key] }}
                        <svg class="ml-1 inline h-3 w-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                          ></path>
                        </svg>
                      </span>
                      <span v-else class="rounded-full bg-gray-100 px-2 py-1 font-mono text-xs font-medium text-gray-500"> — </span>
                    </template>
                    <template v-else-if="col.key === 'relationship_id'">
                      <!-- Display relationship name instead of ID -->
                      <span v-if="member.relationship?.name" class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">
                        {{ member.relationship.name }}
                      </span>
                      <span v-else class="text-gray-400">—</span>
                    </template>
                    <template v-else-if="col.key === 'father_id'">
                      <span v-if="member.father_data?.name" class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">
                        {{ member.father_data.name }}
                        <span class="text-xs text-gray-500">({{ member.father_data.type }})</span>
                      </span>
                      <span v-else class="text-gray-400">—</span>
                    </template>
                    <template v-else-if="col.key === 'mother_id'">
                      <span v-if="member.mother_data?.name" class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">
                        {{ member.mother_data.name }}
                        <span class="text-xs text-gray-500">({{ member.mother_data.type }})</span>
                      </span>
                      <span v-else class="text-gray-400">—</span>
                    </template>
                    <template v-else-if="col.key === 'spouse_id'">
                      <span v-if="member.spouse_data?.name" class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">
                        {{ member.spouse_data.name }}
                        <span class="text-xs text-gray-500">({{ member.spouse_data.type }})</span>
                      </span>
                      <span v-else class="text-gray-400">—</span>
                    </template>
                    <template v-else-if="col.key === 'address'">
                      <span class="block max-w-xs truncate" :title="member[col.key] || '—'">
                        {{ member[col.key] || '—' }}
                      </span>
                    </template>
                    <template v-else>
                      <span class="block truncate" :title="member[col.key]">{{ member[col.key] || '—' }}</span>
                    </template>
                  </td>

                  <!-- Delete -->
                  <td v-if="!serverArchived" class="p-2 whitespace-nowrap">
                    <TooltipProvider>
                      <Tooltip>
                        <TooltipTrigger asChild>
                          <Button
                            variant="destructive"
                            @click="openDeleteModal(member)"
                            class="rounded-full bg-red-100 text-red-700 transition hover:bg-red-200"
                          >
                            <component :is="Trash" />
                          </Button>
                        </TooltipTrigger>
                        <TooltipContent>
                          <p>Delete external member</p>
                        </TooltipContent>
                      </Tooltip>
                    </TooltipProvider>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div v-else class="py-10 text-center text-gray-500">You do not have permission to view members.</div>
    </div>
  </AppLayout>
  <transition name="fade">
    <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
      <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
        <div class="rounded-lg bg-[#ffffff] p-6">
          <h3 class="mb-4 text-xl font-semibold">Delete Member</h3>
          <p>
            Are you sure you want to delete <span class="font-bold">{{ deletingMember?.first_name }} {{ deletingMember?.last_name }}</span
            >?
          </p>
          <div class="mt-6 flex justify-end space-x-2">
            <Button
              variant="secondary"
              type="button"
              @click="showDeleteModal = false"
              class="rounded-full bg-gray-100 px-6 py-2 text-gray-700 transition hover:bg-gray-200"
            >
              Cancel
            </Button>
            <Button
              variant="destructive"
              type="button"
              :disabled="false"
              @click="confirmDelete"
              class="flex items-center gap-2 rounded-full bg-red-600 px-6 py-2 text-white shadow transition hover:bg-red-700"
            >
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

/* Update the highlight-row styles */
.highlight-row {
  background-color: #fef3c7 !important;
  /* Remove the box-shadow border */
  animation: highlight-fade 2s ease-out;
}

@keyframes highlight-fade {
  0% {
    background-color: #fde047; /* Tailwind yellow-300 */
  }
  100% {
    background-color: #fef3c7; /* Tailwind yellow-200 */
  }
}

/* Copy animation styles */
@keyframes copyPulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1);
  }
}

.copy-animation {
  animation: copyPulse 0.3s ease-in-out;
}
</style>
