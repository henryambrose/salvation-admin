<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SearchDropdown } from '@/components/ui/searchDropdown';
import ViewMemberModal from '@/components/ViewMemberModal.vue';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Column, Member } from '@/types';
import { router } from '@inertiajs/vue3';
import { ArchiveIcon, Pencil, Plus, Trash, ZapIcon } from 'lucide-vue-next';
import { computed, ref, watch, nextTick, onMounted } from 'vue';
import { Checkbox } from '@/components/ui/checkbox';

const { can } = permissionHelpers();

const props = defineProps({
  communities: Array<{ id: string | number; name: string }>,
  relationships: Array<{ id: string | number; name: string }>,
  ageGroups: Array<{ id: string | number; name: string }>,
  bloodGroups: Array<{ id: string | number; name: string }>,
  genders: Array<{ id: string | number; name: string }>,
  members: Object,
  totalCount: Number,
  filters: Object,
  fetchUrl: String,
  canViewAnyMember: Boolean,
  canCreateMember: Boolean,
  canEditMember: Boolean,
  canDeleteMember: Boolean,
  pagination: {
    type: Object,
    default: () => ({ currentPage: 1, lastPage: 1 }),
  },
});

const showViewModal = ref(false);
const selectedMember = ref(null);
const showFilters = ref(false);

function openViewModal(member: any) {
  selectedMember.value = member;
  showViewModal.value = true;
}

const enhancedMembers = computed<Record<string, any>>(() => {
  return {
    ...props.members,
    data: props.members?.data.map((item: any) => ({
      ...item,
      added_on: item.created_at ? new Date(item.created_at).toLocaleDateString() : '',
      last_updated: item.updated_at ? new Date(item.updated_at).toLocaleDateString() : '',
    })),
  };
});

// Only show key columns in the table
const columns: Column[] = [
  { key: 'first_name', label: 'First Name', sortable: true },
  { key: 'last_name', label: 'Last Name', sortable: true },
  { key: 'family_no', label: 'Family No', sortable: true },
  { key: 'member_no', label: 'Member No', sortable: true },
  { key: 'church_code', label: 'Church', sortable: true },
  { key: 'contact_no', label: 'Contact No', sortable: true },
  { key: 'community_cluster_id', label: 'Cluster', sortable: true },
  { key: 'community_id', label: 'Community Name', sortable: true },
  { key: 'age', label: 'Age', sortable: false },
  { key: 'relationship_id', label: 'Relationship', sortable: true },
  { key: 'blood_group_id', label: 'Blood Group', sortable: true },
  { key: 'gender_id', label: 'Gender', sortable: true },
  { key: 'date_of_birth', label: 'Date of Birth', sortable: true },
];

const breadcrumbs = [{ title: 'Members', href: '/member/index' }];

function editMember(member: Member) {
  router.get(route('member.edit', member.id));
}

const showDeleteModal = ref(false);
const deletingMember = ref<any>(null);

function openDeleteModal(member: any) {
  deletingMember.value = member;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (deletingMember.value) {
    router.delete(route('member.destroy', deletingMember.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false;
        deletingMember.value = null;
      },
    });
  }
}

function restoreMember(id: number) {
  router.post(route('member.restore', id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      fetch();
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

function getRelationshipName(relationshipId: number | string) {
  if (!relationshipId) return '';
  const relationship = props.relationships?.find(r => r.id == relationshipId);
  return relationship?.name || '';
}

function getBloodGroupName(bloodGroupId: number | string) {
  if (!bloodGroupId) return '';
  const bloodGroup = props.bloodGroups?.find(b => b.id == bloodGroupId);
  return bloodGroup?.name || '';
}

function getGenderName(genderId: number | string) {
  if (!genderId) return '';
  const gender = props.genders?.find(g => g.id == genderId);
  return gender?.name || '';
}

const canCreateMember = can('create-member');
const canReadAnyMember = can('read-member');
const canUpdateAnyMember = can('update-member');
const canDeleteAnyMember = can('delete-member');

const searchColumnsOptions = computed(() => {
  return columns
    .filter((col) => col.filterable)
    .map((col) => ({
      id: col.key,
      name: col.label,
    }));
});

const search = ref(props.filters?.search || '');
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
const isArchived = ref(props.filters?.isArchived || false);

watch(
  [search, sort, direction, perPage, communityId, relationship, ageGroup, bloodGroup, gender, filterColumnKey, filterColumnValue, isArchived],
  () => {
    fetch();
  },
  { immediate: false, deep: false },
);

function fetch(page = 1) {
  if (props.fetchUrl) {
    router.get(
      props.fetchUrl,
      {
        search: search.value,
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
        isArchived: isArchived.value ? 'true' : 'false', // send as string
        page,
      },
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

async function downloadXLS() {
  const params = new URLSearchParams({
    search: search.value,
    communityId: communityId.value,
    relationship: relationship.value,
    ageGroup: ageGroup.value,
    bloodGroup: bloodGroup.value,
    gender: gender.value,
    isArchived: isArchived.value,
    format: 'xls'
  });
  
  try {
    const response = await window.fetch(`/member/exportxls?${params.toString()}`, {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'members.csv';
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
  } catch (error) {
    console.error('Download failed:', error);
    alert('Download failed. Please try again.');
  }
}

const highlightedRowId = ref<number|null>(null);

function scrollToRow(rowId: number) {
  console.log('scrollToRow', rowId);
  nextTick(() => {
    const el = document.getElementById(`member-row-${rowId}`);
    console.log('el', el);
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
    // Optionally, scroll immediately if data is already loaded
    scrollToRow(Number(highlightId));
  }
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Members" />
    <DatatableHeader>
      <div class="mb-2 flex flex-wrap items-center gap-2 justify-between">
        <div class="flex flex-1 items-center gap-2">
          <input v-model="search" type="text" class="flex-1 rounded-full border border-gray-300 px-3 py-2" placeholder="Search name or family no..." />
          <button @click="showFilters = !showFilters" class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 text-sm">
            {{ showFilters ? 'Hide Filters' : 'More Filters' }}
          </button>
        </div>
        <div class="flex items-center gap-2">
          <Button @click="downloadXLS" class="px-3 py-2 rounded-full bg-blue-600 text-white">Export</Button>
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <Checkbox v-model="isArchived" class="switch-checkbox" />
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
      <div class="mb-2 text-sm text-gray-600">
        Showing <span class="font-semibold">{{ totalCount || 0 }}</span> members
        <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
      </div>
    </DatatableHeader>

    <div class="overflow-x-auto">
      <div v-if="canReadAnyMember">
        <div class="datatable2 mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
          <!-- Enhanced Search & Filters -->
    

          <!-- Table -->
          <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full border-collapse text-left">
              <thead>
                <tr class="bg-blue-50">
                  <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
                  <th v-for="col in columns" :key="col.key" @click="col.sortable ? changeSort(col.key) : null" class="cursor-pointer border-b p-3 font-semibold text-gray-700 hover:bg-blue-100 transition">
                    {{ col.label }}
                    <span v-if="col.sortable && sort === col.key">
                      {{ direction === 'asc' ? '▲' : '▼' }}
                    </span>
                  </th>
                  <th v-if="!isArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in enhancedMembers.data" :key="item.id" :id="`member-row-${item.id}`" :class="['even:bg-gray-50 hover:bg-blue-50 transition', highlightedRowId === item.id ? 'highlight-row' : '']">
                  <!-- View + Edit or Restore -->
                  <td class="p-2">
                    <div class="flex gap-2">
                      <template v-if="!isArchived">
                        <Button @click="openViewModal(item)" class="rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                          <component :is="ZapIcon" />
                          <span>View</span>
                        </Button>
                        <Button v-if="canUpdateAnyMember && !item.deleted_at" @click="editMember(item)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                          <component :is="Pencil" />
                          <span>Edit</span>
                        </Button>
                      </template>
                      <template v-else>
                        <Button @click="restoreMember(item.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
                          Restore
                        </Button>
                      </template>
                    </div>
                  </td>
                  <!-- Main table data -->
                  <td v-for="col in columns" :key="col.key" class="p-2">
                    <template v-if="['created_at', 'updated_at', 'date_of_birth'].includes(col.key)">
                      {{ formatDate(item[col.key]) }}
                    </template>
                    <template v-else-if="col.key === 'age'">
                      {{ calculateAge(item.date_of_birth) }}
                    </template>
                    <template v-else-if="col.key === 'relationship_id'">
                      {{ getRelationshipName(item.relationship_id) }}
                    </template>
                    <template v-else-if="col.key === 'blood_group_id'">
                      {{ getBloodGroupName(item.blood_group_id) }}
                    </template>
                    <template v-else-if="col.key === 'gender_id'">
                      {{ getGenderName(item.gender_id) }}
                    </template>
                    <template v-else-if="col.key === 'church_code'">
                      <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ item[col.key] || 'SAL' }}
                      </span>
                    </template>
                    <template v-else>
                      {{ item[col.key] }}
                    </template>
                  </td>
                  <!-- Delete -->
                  <td v-if="!isArchived" class="p-2">
                    <template v-if="canDeleteAnyMember && !item.deleted_at">
                      <Button variant="destructive" @click="openDeleteModal(item)" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
                        <component :is="Trash" />
                        <span>Delete</span>
                      </Button>
                    </template>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-6 flex items-center gap-2">
            <button v-if="enhancedMembers.prev_page_url" @click="fetch(enhancedMembers.current_page - 1)" class="rounded-full border border-gray-300 bg-white px-4 py-1 text-gray-700 shadow hover:bg-blue-50 transition">
              Prev
            </button>
            <button v-if="enhancedMembers.next_page_url" @click="fetch(enhancedMembers.current_page + 1)" class="rounded-full border border-gray-300 bg-white px-4 py-1 text-gray-700 shadow hover:bg-blue-50 transition">
              Next
            </button>
            <span v-if="enhancedMembers.current_page && enhancedMembers.last_page" class="ml-auto text-sm text-gray-500">
              Page {{ enhancedMembers.current_page }} of {{ enhancedMembers.last_page }}
            </span>
          </div>
        </div>
      </div>
      <div v-else class="py-10 text-center text-gray-500">You do not have permission to view members.</div>
    </div>
  </AppLayout>

  <!-- Member Details Modal -->
  <ViewMemberModal v-model="showViewModal" :member="selectedMember" :familyIncomeRange="null" />

  <!-- Delete Modal -->
  <transition name="fade">
    <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
      <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
        <div class="rounded-lg bg-white p-6">
          <h3 class="mb-4 text-xl font-semibold">Delete Member</h3>
          <p>
            Are you sure you want to delete <span class="font-bold">{{ deletingMember?.first_name }} {{ deletingMember?.last_name }}</span>?
          </p>
          <div class="mt-6 flex justify-end space-x-2">
            <Button
              variant="secondary"
              type="button"
              @click="showDeleteModal = false"
              class="rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition px-6 py-2"
            >
              Cancel
            </Button>
            <Button
              variant="destructive"
              type="button"
              :disabled="false"
              @click="confirmDelete"
              class="rounded-full bg-red-600 text-white shadow hover:bg-red-700 transition px-6 py-2 flex items-center gap-2"
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
  background: #ef4444; /* Tailwind red-500 */
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
