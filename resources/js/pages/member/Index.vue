<script setup lang="ts">
import { router, Head, usePage, Link } from '@inertiajs/vue3';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import ViewMemberModal from '@/components/ViewMemberModal.vue';
import AddMemberModal from '@/components/AddMemberModal.vue';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Column, Member, FamilyStats } from '@/types';
import { } from '@inertiajs/vue3';
import { ArchiveIcon, Pencil, Plus, Trash, ZapIcon, Download } from 'lucide-vue-next';
import { computed, ref, watch, nextTick, onMounted } from 'vue';
import { Checkbox } from '@/components/ui/checkbox';

const { can } = permissionHelpers();
const page = usePage();

const props = defineProps({
  communities: Array<{ id: string | number; name: string }>,
  relationships: Array<{ id: string | number; name: string }>,
  ageGroups: Array<{ id: string | number; name: string }>,
  bloodGroups: Array<{ id: string | number; name: string }>,
  genders: Array<{ id: string | number; name: string }>,
  communityClusters: Array<{ id: string | number; name: string; community_id: string | number }>,
  members: Object,
  totalCount: Number,
  familyStats: Object as () => FamilyStats,
  filters: Object,
  fetchUrl: String,
  canViewAnyMember: Boolean,
  canCreateMember: Boolean,
  canEditMember: Boolean,
  canDeleteMember: Boolean,
  canRestoreMember: Boolean,
  pagination: {
    type: Object,
    default: () => ({ currentPage: 1, lastPage: 1 }),
  },
});

const showViewModal = ref(false);
const selectedMember = ref(null);
const showAddModal = ref(false);
const showFilters = ref(false);


function openViewModal(member: any) {
  selectedMember.value = member;
  showViewModal.value = true;
}

const familyStats = computed(() => {
  return props.familyStats || { totalFamilies: 0, totalMembers: 0, averageMembersPerFamily: 0 };
});

const enhancedMembers = computed<Record<string, any>>(() => {
  let members = props.members?.data.map((item: any) => ({
    ...item,
    added_on: item.created_at ? new Date(item.created_at).toLocaleDateString() : '',
    last_updated: item.updated_at ? new Date(item.updated_at).toLocaleDateString() : '',
  }));

  return {
    ...props.members,
    data: members
  };
});

// Only show key columns in the table
const columns: Column[] = [
  { key: 'first_name', label: 'First Name', sortable: true },
  { key: 'last_name', label: 'Last Name', sortable: true },
  { key: 'old_sal_id', label: 'Old SAL ID', sortable: true },
  { key: 'family_no', label: 'Family No', sortable: true },
  { key: 'member_no', label: 'Member No', sortable: true },
  { key: 'contact_no_1', label: 'Contact No 1', sortable: true },
  { key: 'contact_no_2', label: 'Contact No 2', sortable: true },
  { key: 'community_cluster_id', label: 'Cluster', sortable: true },
  { key: 'community_id', label: 'Community Name', sortable: true },
  { key: 'age', label: 'Age', sortable: false },
  { key: 'date_of_birth', label: 'Date of Birth', sortable: true },
  { key: 'relationship_id', label: 'Relationship', sortable: true },
  { key: 'blood_group_id', label: 'Blood Group', sortable: true },
  { key: 'gender_id', label: 'Gender', sortable: true },

];

const breadcrumbs = [{ title: 'Members', href: '/member/index' }];

function editMember(member: any) {
  router.get(route('member.edit', member.id));
}

function viewFamilyTree(member: any) {
  router.get(route('member.family-tree', { id: member.id, type: 'internal' }));
}

function addNewMember() {
  showAddModal.value = true;
}

const showDeleteModal = ref(false);
const deletingMember = ref<any>(null);

function openDeleteModal(member: any) {
  deletingMember.value = member;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (!deletingMember.value) return;
  const deletedId = deletingMember.value.id;

  router.delete(route('member.destroy', deletedId), {
    data: {
      perPage: perPage.value,
      page: enhancedMembers.value.current_page,
      search: search.value,
      familySearch: familySearch.value,
      sort: sort.value,
      direction: direction.value,
      communityId: communityId.value,
      relationship: relationship.value,
      ageGroup: ageGroup.value,
      bloodGroup: bloodGroup.value,
      gender: gender.value,
      filterColumnKey: filterColumnKey.value,
      filterColumnValue: filterColumnValue.value,
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

function restoreMember(id: number) {
  router.post(route('member.restore', id), {}, {
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      isArchived.value = false;
    },
  });
}
function formatDate(dateStr: string) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-GB'); // dd/mm/yyyy
}

function calculateAge(dateStr: string) {
  if (!dateStr) return '';
  const birthDate = new Date(dateStr);
  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  return age;
}

const search = ref(props.filters?.search || '');
const familySearch = ref('');
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');
const perPage = ref(props.filters?.perPage || 10);
const communityId = ref(props.filters?.communityId || '');
const relationship = ref(props.filters?.relationship || '');
const ageGroup = ref(props.filters?.ageGroup || '');
const bloodGroup = ref(props.filters?.bloodGroup || '');
const gender = ref(props.filters?.gender || '');
const filterColumnKey = ref(props.filters?.filterColumnKey || '');
const filterColumnValue = ref(props.filters?.filterColumnValue || '');
const isArchived = ref(String(props.filters?.isArchived) === 'true');
const serverArchived = computed(() => String(props.filters?.isArchived) === 'true');
const partialOnly = ['members', 'familyStats', 'filters', 'totalCount'];
// Debounced search to prevent too many API calls
let searchTimeout: number;

watch(
  [search, familySearch, sort, direction, perPage, communityId, relationship, ageGroup, bloodGroup, gender, filterColumnKey, filterColumnValue, isArchived],
  (newValues, oldValues) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
   
      fetch();
    }, 300); // 300ms debounce
  },
  { immediate: false, deep: false },
);

function fetch(page = 1) {
  if (!props.fetchUrl) return;
  router.get(
    props.fetchUrl,
    {
      search: search.value,
      familySearch: familySearch.value,
      sort: sort.value,
      direction: direction.value,
      perPage: perPage.value,
      communityId: communityId.value,
      relationship: relationship.value,
      ageGroup: ageGroup.value,
      bloodGroup: bloodGroup.value,
      gender: gender.value,
      filterColumnKey: filterColumnKey.value,
      filterColumnValue: filterColumnValue.value,
      isArchived: isArchived.value ? 'true' : 'false',
      page,
    },
    { preserveState: true, preserveScroll: true, replace: true, only: partialOnly },
  );
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
// Search handling functions
function handleSearchInput() {

  // The watch will handle the debounced search
}

function handleFamilySearchInput() {
  // The watch will handle the debounced search
}

function clearSearch() {
  search.value = '';
  // Force immediate fetch to clear results
  clearTimeout(searchTimeout);
  // Force fresh request without preserving state
  if (props.fetchUrl) {
    router.get(
      props.fetchUrl,
      {
        search: '',
        familySearch: familySearch.value,
        sort: sort.value,
        direction: direction.value,
        perPage: perPage.value,
        communityId: communityId.value,
        relationship: relationship.value,
        ageGroup: ageGroup.value,
        bloodGroup: bloodGroup.value,
        gender: gender.value,
        filterColumnKey: filterColumnKey.value,
        filterColumnValue: filterColumnValue.value,
        isArchived: isArchived.value ? 'true' : 'false',
        page: 1,
      },
      {
        preserveState: false,
        replace: true,
        only: partialOnly,
      },
    );
  }
}

function clearFamilySearch() {
  familySearch.value = '';
  // Force immediate fetch to clear results
  clearTimeout(searchTimeout);
  // Force fresh request without preserving state
  if (props.fetchUrl) {
    router.get(
      props.fetchUrl,
      {
        search: search.value,
        familySearch: '',
        sort: sort.value,
        direction: direction.value,
        perPage: perPage.value,
        communityId: communityId.value,
        relationship: relationship.value,
        ageGroup: ageGroup.value,
        bloodGroup: bloodGroup.value,
        gender: gender.value,
        filterColumnKey: filterColumnKey.value,
        filterColumnValue: filterColumnValue.value,
        isArchived: isArchived.value ? 'true' : 'false',
        page: 1,
      },
      {
        preserveState: false,
        replace: true,
        only: partialOnly,
      },
    );
  }
}

function downloadExcel() {
  const params = new URLSearchParams({
    search: search.value || '',
    familySearch: familySearch.value || '',
    communityId: communityId.value || '',
    relationship: relationship.value || '',
    ageGroup: ageGroup.value || '',
    bloodGroup: bloodGroup.value || '',
    gender: gender.value || '',
          sort: String(sort.value || 'id'),
      direction: String(direction.value || 'asc'),
    perPage: 'all',
    isArchived: isArchived.value ? 'true' : 'false',
  });

  // Use window.location.href for direct download
  window.location.href = `${window.location.origin}/member/export?${params.toString()}`;
}

const highlightedRowId = ref<number | null>(null);

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`member-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

watch(() => enhancedMembers.value.data, (rows) => {
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
  // Check for highlightId in query string
  const params = new URLSearchParams(window.location.search);
  const highlightId = params.get('highlightId');
  if (highlightId) {
    highlightedRowId.value = Number(highlightId);
    scrollToRow(Number(highlightId));
  }
});

// Add these reactive variables
const copiedItem = ref<{ id: string; type: string } | null>(null);

// Enhanced copy function with visual feedback
function copyToClipboard(text: string, type: string, memberId: number) {
  if (!text || text === '—') return;
  
  navigator.clipboard.writeText(text).then(() => {
    // Set copied state for visual feedback
    copiedItem.value = { id: `${type}-${memberId}`, type };
    
    // Show success toast
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform transition-all duration-300 flex items-center gap-2';
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
  }).catch(err => {
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

    <Head title="Members" />
    
    <!-- Wrap everything in TooltipProvider -->
    <TooltipProvider>
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
                @keydown.escape="clearFamilySearch" />
              <button v-if="familySearch" @click="clearFamilySearch"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                ✕
              </button>
            </div>
            <Tooltip>
              <TooltipTrigger asChild>
                <Button @click="showFilters = !showFilters" class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 text-sm">
                  {{ showFilters ? 'Hide Filters' : 'More Filters' }}
                </Button>
              </TooltipTrigger>
              <TooltipContent>
                <p>Toggle advanced filters</p>
              </TooltipContent>
            </Tooltip>
          </div>
          <div class="flex items-center gap-2">
            <Tooltip>
              <TooltipTrigger asChild>
                <Button v-if="props.canCreateMember" @click="addNewMember"
                  class="px-3 py-2 rounded-full bg-green-600 text-white hover:bg-green-700 transition flex items-center gap-2">
                  <component :is="Plus" />
                  <span>Add New Member</span>
                </Button>
              </TooltipTrigger>
              <TooltipContent>
                <p>Create a new member record</p>
              </TooltipContent>
            </Tooltip>

            <Tooltip>
              <TooltipTrigger asChild>
                <Button v-if="can('read-member')" @click="downloadExcel"
                  class="flex items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-white shadow hover:bg-green-700 transition">
                  <component :is="Download" />
                  <span>Export CSV</span>
                </Button>
              </TooltipTrigger>
              <TooltipContent>
                <p>Download member data as CSV file</p>
              </TooltipContent>
            </Tooltip>
            
            <Tooltip>
              <TooltipTrigger asChild>
                <Link
                  v-if="can('read-data-verification') || can('update-data-verification')"
                  :href="route('member.data-verification')"
                  class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>Data Verification</span>
                </Link>
              </TooltipTrigger>
              <TooltipContent>
                <p>Verify and validate member data</p>
              </TooltipContent>
            </Tooltip>

            <label class="flex items-center gap-2 cursor-pointer select-none">
              <Tooltip>
                <TooltipTrigger asChild>
                  <Checkbox v-model="isArchived" class="switch-checkbox" />
                </TooltipTrigger>
                <TooltipContent>
                  <p>Show archived members in the list</p>
                </TooltipContent>
              </Tooltip>
              <span class="text-sm font-medium">Show Archived</span>
            </label>
          </div>
        </div>
        <transition name="fade">
          <div v-if="showFilters" class="flex flex-wrap gap-2 mb-2">
            <select v-model="communityId" class="rounded border px-2 py-1 text-sm">
              <option value="">All Communities</option>
              <option v-for="c in props.communities" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <select v-model="relationship" class="rounded border px-2 py-1 text-sm">
              <option value="">All Relationships</option>
              <option v-for="r in props.relationships" :key="r.id" :value="r.id">{{ r.name }}</option>
            </select>
            <select v-model="ageGroup" class="rounded border px-2 py-1 text-sm">
              <option value="">All Age Groups</option>
              <option v-for="a in props.ageGroups" :key="a.id" :value="a.id">{{ a.name }}</option>
            </select>
            <select v-model="bloodGroup" class="rounded border px-2 py-1 text-sm">
              <option value="">All Blood Groups</option>
              <option v-for="b in props.bloodGroups" :key="b.id" :value="b.id">{{ b.name }}</option>
            </select>
            <select v-model="gender" class="rounded border px-2 py-1 text-sm">
              <option value="">All Genders</option>
              <option v-for="g in props.genders" :key="g.id" :value="g.id">{{ g.name }}</option>
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

      <div v-if="props.canViewAnyMember">
        <!-- Compact pagination with inline stats -->
        <div class="mb-2 flex items-center justify-between gap-3 bg-gray-50 px-3 py-1.5 rounded border border-gray-100 text-xs">
          <!-- Left side: Total members info -->
          <div class="text-gray-600">
            Showing <span class="font-semibold">{{ familyStats.totalMembers || 0 }}</span> total members
            <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
          </div>
          
          <!-- Center: Pagination controls -->
          <div class="flex items-center gap-2">
            <Tooltip>
              <TooltipTrigger asChild>
                <button 
                  v-if="enhancedMembers.prev_page_url" 
                  @click="fetch(enhancedMembers.current_page! - 1)" 
                  class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
                >
                  ← Prev
                </button>
              </TooltipTrigger>
              <TooltipContent>
                <p>Go to the previous page</p>
              </TooltipContent>
            </Tooltip>
            
            <div class="flex items-center gap-1 text-gray-600">
              <span>Page</span>
              <select 
                v-if="enhancedMembers.last_page && enhancedMembers.last_page > 1"
                :value="enhancedMembers.current_page" 
                @change="(event) => fetch(Number((event.target as HTMLSelectElement).value))"
                class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
              >
                <option v-for="page in enhancedMembers.last_page" :key="page" :value="page">
                  {{ page }}
                </option>
              </select>
              <span>of {{ enhancedMembers.last_page }}</span>
            </div>
            
            <Tooltip>
              <TooltipTrigger asChild>
                <button 
                  v-if="enhancedMembers.next_page_url" 
                  @click="fetch(enhancedMembers.current_page! + 1)" 
                  class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
                >
                  Next →
                </button>
              </TooltipTrigger>
              <TooltipContent>
                <p>Go to the next page</p>
              </TooltipContent>
            </Tooltip>
          </div>
          
          <!-- Right side: Family stats -->
          <div class="flex items-center gap-2 text-gray-600">
            <Tooltip>
              <TooltipTrigger asChild>
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                  {{ familyStats.totalFamilies }} Families
                </span>
              </TooltipTrigger>
              <TooltipContent>
                <p>Total number of families in the database</p>
              </TooltipContent>
            </Tooltip>
            <Tooltip>
              <TooltipTrigger asChild>
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                  {{ familyStats.averageMembersPerFamily }} Avg/Family
                </span>
              </TooltipTrigger>
              <TooltipContent>
                <p>Average number of members per family</p>
              </TooltipContent>
            </Tooltip>
          </div>
        </div>

        <div class="datatable2 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
          <!-- Table content -->
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
                  <th v-if="!serverArchived" class="border-b p-3 font-semibold text-gray-700 whitespace-nowrap">Delete</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="member in enhancedMembers.data" :key="member.id" :id="`member-row-${member.id}`" :class="[
                  'even:bg-gray-50 hover:bg-blue-50 transition',
                  highlightedRowId === member.id ? 'highlight-row' : ''
                ]">
                  <!-- View + Edit or Restore -->
                  <td class="p-2 whitespace-nowrap">
                    <div class="flex gap-2">
                      <template v-if="!serverArchived">
                        <!-- View More Details Button -->
                        <Tooltip>
                          <TooltipTrigger asChild>
                            <Button @click="openViewModal(member)"
                              class="rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                              <component :is="ZapIcon" />
                            </Button>
                          </TooltipTrigger>
                          <TooltipContent>
                            <p>View more details about this member</p>
                          </TooltipContent>
                        </Tooltip>

                        <!-- Edit Button -->
                        <Tooltip v-if="canEditMember && !member.deleted_at">
                          <TooltipTrigger asChild>
                            <Button @click="editMember(member)"
                              class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                              <component :is="Pencil" />
                            </Button>
                          </TooltipTrigger>
                          <TooltipContent>
                            <p>Edit this member's information</p>
                          </TooltipContent>
                        </Tooltip>

                        <!-- Family Tree Button -->
                        <Tooltip>
                          <TooltipTrigger asChild>
                            <Button @click="viewFamilyTree(member)"
                              class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                              </svg>
                            </Button>
                          </TooltipTrigger>
                          <TooltipContent>
                            <p>View family tree and relationships</p>
                          </TooltipContent>
                        </Tooltip>
                      </template>
                      
                      <!-- Restore Button -->
                      <template v-else>
                        <Tooltip v-if="canRestoreMember">
                          <TooltipTrigger asChild>
                            <Button @click="restoreMember(member.id)"
                              class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
                              Restore
                            </Button>
                          </TooltipTrigger>
                          <TooltipContent>
                            <p>Restore this deleted member</p>
                          </TooltipContent>
                        </Tooltip>
                      </template>
                    </div>
                  </td>
                  <!-- Main table data -->
                  <td v-for="col in columns" :key="col.key" class="p-2 whitespace-nowrap overflow-hidden">

                    <template v-if="['created_at', 'updated_at', 'date_of_birth'].includes(col.key)">
                      {{ formatDate(member[col.key]) }}
                    </template>
                    <template v-else-if="col.key === 'community_cluster_id'">
                      <span class="block truncate" :title="member.community_cluster_id || '—'">{{
                        member.community_cluster_id || '—' }}</span>
                    </template>
                    <template v-else-if="col.key === 'community_id'">
                      <span class="block truncate" :title="member.community_id || '—'">{{ member.community_id || '—'
                        }}</span>
                    </template>
                    <template v-else-if="col.key === 'age'">
                      {{ calculateAge(member.date_of_birth) }}
                    </template>
                    <template v-else-if="col.key === 'relationship_id'">
                      <span class="block truncate" :title="member.relationship_id || '—'">{{ member.relationship_id ||
                        '—' }}</span>
                    </template>
                    <template v-else-if="col.key === 'blood_group_id'">
                      <span class="block truncate" :title="member.blood_group_id || '—'">{{ member.blood_group_id || '—'
                        }}</span>
                    </template>
                    <template v-else-if="col.key === 'gender_id'">
                      <span class="block truncate" :title="member.gender_id || '—'">{{ member.gender_id || '—' }}</span>
                    </template>
                    <template v-else-if="col.key === 'church_code'">
                      <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ member[col.key] || page.props.church_code }}
                      </span>
                    </template>
                    <template v-else-if="col.key === 'family_no'">
                      <span 
                        v-if="member[col.key]"
                        @click="copyToClipboard(member[col.key], 'Family Number', member.id)"
                        :class="[
                          'px-2 py-1 rounded-full text-xs font-medium font-mono cursor-pointer transition-all duration-200 select-none',
                          copiedItem?.id === `Family Number-${member.id}` 
                            ? 'bg-green-200 text-green-900 scale-105 shadow-md' 
                            : 'bg-green-100 text-green-800 hover:bg-green-200 hover:scale-105'
                        ]"
                        :title="`Click to copy: ${member[col.key]}`"
                      >
                        {{ member[col.key] }}
                        <svg class="inline w-3 h-3 ml-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                      </span>
                      <span v-else class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium font-mono">
                        —
                      </span>
                    </template>
                    <template v-else-if="col.key === 'member_no'">
                      <span 
                        v-if="member[col.key]"
                        @click="copyToClipboard(member[col.key], 'Member Number', member.id)"
                        :class="[
                          'px-2 py-1 rounded-full text-xs font-medium font-mono cursor-pointer transition-all duration-200 select-none',
                          copiedItem?.id === `Member Number-${member.id}` 
                            ? 'bg-purple-200 text-purple-900 scale-105 shadow-md' 
                            : 'bg-purple-100 text-purple-800 hover:bg-purple-200 hover:scale-105'
                        ]"
                        :title="`Click to copy: ${member[col.key]}`"
                      >
                        {{ member[col.key] }}
                        <svg class="inline w-3 h-3 ml-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                      </span>
                      <span v-else class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium font-mono">
                        —
                      </span>
                    </template>
                    <template v-else>
                      <span class="block truncate" :title="member[col.key]">{{ member[col.key] }}</span>
                    </template>
                  </td>
                  <!-- Delete -->
                  <td v-if="!serverArchived" class="p-2 whitespace-nowrap">
                    <Tooltip v-if="canDeleteMember && !member.deleted_at">
                      <TooltipTrigger asChild>
                        <Button variant="destructive" @click="openDeleteModal(member)"
                          class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
                          <component :is="Trash" />
                        </Button>
                      </TooltipTrigger>
                      <TooltipContent>
                        <p>Permanently delete this member</p>
                      </TooltipContent>
                    </Tooltip>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div v-else class="py-10 text-center text-gray-500">You do not have permission to view members.</div>
    </TooltipProvider>

    <!-- Member Details Modal -->
    <ViewMemberModal v-model="showViewModal" :member="selectedMember" :incomeRange="null" />

    <!-- Add Member Modal -->
    <AddMemberModal v-model="showAddModal" :communities="props.communities || []"
      :relationships="props.relationships || []" :communityClusters="props.communityClusters || []" :towns="[]" />

    <!-- Delete Modal with tooltip -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-60 fixed inset-0 z-50 flex items-center justify-center bg-black p-4">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
          <h3 class="mb-4 text-xl font-semibold">Delete Member</h3>
          <p class="mb-2">
            Are you sure you want to delete this member?
          </p>
          <div class="mb-4 p-3 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600"><strong>Name:</strong> {{ deletingMember?.first_name }} {{ deletingMember?.last_name }}</p>
            <p class="text-sm text-gray-600"><strong>Member No:</strong> {{ deletingMember?.member_no }}</p>
          </div>
          <div class="mt-6 flex justify-end space-x-2">
            <Tooltip>
              <TooltipTrigger asChild>
                <Button
                  variant="secondary"
                  type="button"
                  @click="showDeleteModal = false"
                  class="rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition px-6 py-2">
                  Cancel
                </Button>
              </TooltipTrigger>
              <TooltipContent>
                <p>Cancel the deletion</p>
              </TooltipContent>
            </Tooltip>

            <Tooltip>
              <TooltipTrigger asChild>
                <Button
                  variant="destructive"
                  type="button"
                  :disabled="false"
                  @click="confirmDelete"
                  class="rounded-full bg-red-600 text-white shadow hover:bg-red-700 transition px-6 py-2 flex items-center gap-2">
                  <component :is="Trash" />
                  Delete
                </Button>
              </TooltipTrigger>
              <TooltipContent>
                <p>Permanently delete this member</p>
              </TooltipContent>
            </Tooltip>
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
  /* Tailwind red-500 */
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
  /* Tailwind yellow-200 */
}

@keyframes highlight-fade {
  0% {
    background-color: #fde047;
  }

  100% {
    background-color: inherit;
  }
}

.bg-gray-25 {
  background-color: #fafafa;
}

.family-header {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  border-bottom: 2px solid #3b82f6;
}

.family-member-row {
  background-color: #fafafa;
  border-left: 3px solid #e5e7eb;
}

.family-member-row:hover {
  background-color: #f3f4f6;
  border-left-color: #3b82f6;
}
</style>
