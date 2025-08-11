<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import {Plus, Download } from 'lucide-vue-next';
import { nextTick, ref, watch, computed } from 'vue';
import axios from 'axios';
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps({
  ppcHeads: {
    type: Object,
    default: () => ({ data: [] }),
  },
  communities: {
    type: Array as () => { id: string | number; name: string }[],
    default: () => [],
  },
  filters: Object,
  fetchUrl: String,
});
const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'community_name', label: 'Community Name', sortable: true },
  { key: 'member_full_name', label: 'Member Name', sortable: true },

];

const breadcrumbs = [{ title: 'PPC Heads', href: '/ppc-head/index' }];
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showCreateModal = ref(false);
const editingPPCHead = ref<any>(null);
const deletingItem = ref<Record<string, any>>();
const modalMembers = ref<any[]>([]);
const isArchived = ref(props.filters?.isArchived === 'true');
const highlightedRowId = ref<number|null>(null);

const editForm = useForm({
  id: '',
  member_id: '',
  community_id: '',
});
const createForm = useForm({
  member_id: '',
  community_id: '',
});

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

function clearSearch() {
  search.value = '';
  fetch();
}

const enhancedPPCHeads = computed(() => {
  const c = props.ppcHeads || {};
  return {
    data: c.data || [],
    prev_page_url: c.prev_page_url ?? c.meta?.prev_page_url,
    next_page_url: c.next_page_url ?? c.meta?.next_page_url,
    current_page: c.current_page ?? c.meta?.current_page,
    last_page: c.last_page ?? c.meta?.last_page,
    total: c.total ?? c.meta?.total,
  };
});

watch([search, sort, direction, perPage, isArchived], () => {
  fetch();
});

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`ppc-head-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

function fetch(page = 1) {
  if (props.fetchUrl) {
    router.get(
      props.fetchUrl,
      {
        search: search.value,
        sort: sort.value,
        direction: direction.value,
        perPage: perPage.value,
        isArchived: isArchived.value,
        page,
      },
      {
        preserveState: true,
        replace: true,
      },
    );
  }
}
watch(() => editForm.community_id, async (newVal, oldVal) => {
  if (newVal) {
    const { data } = await axios.get(`/api/ppc-community/${newVal}/members`);
    modalMembers.value = data;
    editForm.member_id = '';
  } else {
    modalMembers.value = [];
    editForm.member_id = '';
  }
});

watch(
  () => createForm.community_id,
  async (newVal) => {
    if (newVal) {
      const { data } = await axios.get(`/api/community/${newVal}/members`);
      modalMembers.value = data;
      createForm.member_id = '';
    } else {
      modalMembers.value = [];
      createForm.member_id = '';
    }
  }
);

watch(() => enhancedPPCHeads.value.data, (rows) => {
  if (highlightedRowId.value) {
    let rowId = highlightedRowId.value;
    if (rowId === -1 && rows.length) {
      rowId = rows[rows.length - 1].id;
    }
    scrollToRow(rowId);
    highlightedRowId.value = null;
  }
});

function openEditModal(row: any) {
  editingPPCHead.value = row;
  editForm.id = row.id;
  editForm.community_id = row.community_id;
  showEditModal.value = true;
  nextTick(async () => {
    if (editForm.community_id) {
      const { data } = await axios.get(`/api/ppc-community/${editForm.community_id}/members`);
      modalMembers.value = data;
      editForm.member_id = row.member_id;
    } else {
      modalMembers.value = [];
      editForm.member_id = '';
    }
  });
}



function submitEdit() {
  if (!editForm.member_id) {
    editForm.errors.member_id = 'Please select a member.';
    return;
  }
  const editedId = editingPPCHead.value?.id;
  editForm.transform(data => ({
    ...data,
    perPage: perPage.value,
    page: enhancedPPCHeads.value.current_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  editForm.put(`/ppc-head/${editForm.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingPPCHead.value = undefined;
      highlightedRowId.value = editedId;
      nextTick(() => scrollToRow(editedId));
    },
  });
}

function openCreateModal() {
  showCreateModal.value = true;
  createForm.reset();
  modalMembers.value = [];
}

function closeCreateModal() {
  showCreateModal.value = false;
}

function submitCreate() {
  if (!createForm.member_id || !createForm.community_id) return;
  
  // Check for duplicate community
  const existingCommunity = enhancedPPCHeads.value.data.some(
    (head: { community_id: number }) =>
      head.community_id === Number(createForm.community_id)
  );
  
  if (existingCommunity) {
    createForm.setError('community_id', 'This community already has a PPC Head assigned.');
    return;
  }
  
  createForm.transform(data => ({
    ...data,
    perPage: perPage.value,
    page: enhancedPPCHeads.value.last_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  createForm.post('/ppc-head', {
    preserveScroll: true,
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
      nextTick(() => {
        fetch(enhancedPPCHeads.value.last_page);
        highlightedRowId.value = -1;
      });
    },
  });
}

function openDeleteModal(row: any) {
  deletingItem.value = row;
  showDeleteModal.value = true;
}

function confirmDelete() {
  router.delete(`/ppc-head/${deletingItem.value?.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingItem.value = undefined;
      fetch();
    },
  });
}

function restorePPCHead(id: number) {
  router.post(`/ppc-head/${id}/restore`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      fetch();
    },
  });
}

function downloadExcel() {
  const params = new URLSearchParams({
    search: search.value || '',
    sort: sort.value || 'id',
    direction: direction.value || 'asc',
    perPage: 'all',
    isArchived: isArchived.value ? 'true' : 'false',
  });
  
  // Use window.location.href for direct download
  window.location.href = `${window.location.origin}/ppc-head/export?${params.toString()}`;
}

import { permissionHelpers } from '@/composables/permissionHelpers';
const { can } = permissionHelpers();

const canCreatePPCHead = can('create-p-p-c-head');
const canReadAnyPPCHead = can('read-p-p-c-head');
const canUpdateAnyPPCHead = can('update-p-p-c-head');
const canDeleteAnyPPCHead = can('delete-p-p-c-head');
const canExportPPCHead = can('read-p-p-c-head');

function onPageChange(e: Event) {
  const target = e.target as HTMLSelectElement | null;
  if (!target) return;
  fetch(Number(target.value));
}

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="PPC Heads" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">PPC Heads</h2>
        <div class="btn-group flex space-x-2">
          <Button v-if="canExportPPCHead" @click="downloadExcel" class="flex items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-white shadow hover:bg-green-700 transition">
            <component :is="Download" />
            <span>Export Excel</span>
          </Button>
          <Button v-if="canCreatePPCHead" @click="openCreateModal" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700">
            <component :is="Plus" />
            <span>Add PPC Head</span>
          </Button>
        </div>
      </div>
      <div class="mb-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative">
            <input v-model="search" @keyup.enter="fetch()" type="text" class="rounded-full border border-gray-300 px-3 py-1 pr-8 focus:ring-2 focus:ring-blue-200" placeholder="Search..." @keydown.escape="clearSearch" />
            <button v-if="search" @click="clearSearch" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">✕</button>
          </div>
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

    <div class="mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
      <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="w-full border-collapse text-left">
          <thead>
            <tr class="bg-blue-50">
              <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
              <th v-for="col in columns" :key="col.key" class="border-b p-3 font-semibold text-gray-700">
                {{ col.label }}
              </th>
              <th v-if="!isArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in enhancedPPCHeads.data" :key="row.id" :id="`ppc-head-row-${row.id}`" :class="['even:bg-gray-50 hover:bg-blue-50 transition', highlightedRowId === row.id ? 'highlight-row' : '']">
              <td class="p-2">
                <template v-if="!isArchived">
                  <Button @click="openEditModal(row)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                    Edit
                  </Button>
                </template>
                <template v-else>
                  <Button  @click="restorePPCHead(row.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
                    Restore
                  </Button>
                </template>
              </td>
              <td v-for="col in columns" :key="col.key" class="p-2">
                  {{ row[col.key] }}

              </td>
              <td v-if="!isArchived" class="p-2">
                <template v-if="!isArchived">
                  <Button
                    @click="openDeleteModal(row)"
                    variant="destructive"
                    class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition"
                  >
                    Delete
                  </Button>
                </template>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- Enhanced Pagination -->
    <div class="mt-6 flex items-center justify-between gap-4">
      <div class="flex items-center gap-2">
        <button 
          v-if="enhancedPPCHeads.prev_page_url" 
          @click="fetch(enhancedPPCHeads.current_page - 1)" 
          class="rounded-full border border-gray-300 bg-white px-4 py-2 text-gray-700 shadow hover:bg-blue-50 transition flex items-center gap-1"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
          Prev
        </button>
        
        <!-- Page Number Dropdown -->
        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-600">Page</span>
          <select 
            v-if="enhancedPPCHeads.last_page && enhancedPPCHeads.last_page > 1"
            :value="enhancedPPCHeads.current_page" 
            @change="onPageChange"
            class="rounded-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow hover:bg-blue-50 transition focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option v-for="page in enhancedPPCHeads.last_page" :key="page" :value="page">
              {{ page }}
            </option>
          </select>
          <span v-if="enhancedPPCHeads.last_page" class="text-sm text-gray-600">of {{ enhancedPPCHeads.last_page }}</span>
        </div>
        
        <button 
          v-if="enhancedPPCHeads.next_page_url" 
          @click="fetch(enhancedPPCHeads.current_page + 1)" 
          class="rounded-full border border-gray-300 bg-white px-4 py-2 text-gray-700 shadow hover:bg-blue-50 transition flex items-center gap-1"
        >
          Next
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      </div>
      
      <!-- Total Records Info -->
      <div class="text-sm text-gray-500">
        <span v-if="enhancedPPCHeads.total">Total: {{ enhancedPPCHeads.total }} records</span>
      </div>
    </div>
    <!-- bg-black bg-opacity-20 -->
    <transition name="fade">
      <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 min-w-[400px] max-w-full w-full sm:w-[420px]">
          <h2 class="mb-6 text-2xl font-bold text-gray-900">Edit PPC Head</h2>
          <form @submit.prevent="submitEdit">
            <div class="mb-6">
              <label class="block mb-2 font-medium text-gray-700">Community</label>
              <select
                v-model="editForm.community_id"
                class="w-full rounded-lg border border-gray-200 px-4 py-2 text-lg focus:outline-none focus:ring-2 focus:ring-blue-200"
              >
                <option value="" disabled>Select Community</option>
                <option v-for="c in props.communities" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
              <div v-if="editForm.errors.community_id" class="mt-1 text-sm text-red-500">
                {{ editForm.errors.community_id }}
              </div>
            </div>
            <div class="mb-6">
              <label class="block mb-2 font-medium text-gray-700">Member</label>
              <select
                v-model="editForm.member_id"
                class="w-full rounded-lg border border-gray-200 px-4 py-2 text-lg focus:outline-none focus:ring-2 focus:ring-blue-200"
              >
                <option value="" disabled>Select Member</option>
                <option v-for="m in modalMembers" :key="m.id" :value="m.id">{{ m.name }}</option>
              </select>
              <div v-if="!editForm.member_id" class="mt-1 text-sm text-red-500">
                Please select a member.
              </div>
            </div>
            <div class="flex justify-end gap-3">
              <button
                type="button"
                @click="showEditModal = false"
                class="rounded-full bg-red-100 text-red-700 px-6 py-2 font-semibold hover:bg-red-200 transition"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="editForm.processing"
                class="rounded-full bg-blue-600 text-white px-6 py-2 font-semibold hover:bg-blue-700 transition"
              >
                Save
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
    <transition name="fade">
      <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="w-full max-w-full min-w-[400px] rounded-2xl bg-white p-8 shadow-2xl sm:w-[420px]">
          <h2 class="mb-6 text-2xl font-bold text-gray-900">Create PPC Head</h2>
          <form @submit.prevent="submitCreate">
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Community</label>
              <select v-model.number="createForm.community_id" class="w-full rounded-lg border border-gray-200 px-4 py-2 text-lg focus:ring-2 focus:ring-blue-200 focus:outline-none">
                <option value="" disabled>Select Community</option>
                <option v-for="c in props.communities" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
              <div v-if="createForm.errors.community_id" class="mt-1 text-sm text-red-500">
                {{ createForm.errors.community_id }}
              </div>
            </div>
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Member</label>
              <select v-model="createForm.member_id" :disabled="!createForm.community_id" class="w-full rounded-lg border border-gray-200 px-4 py-2 text-lg focus:ring-2 focus:ring-blue-200 focus:outline-none">
                <option value="" disabled>Select Member</option>
                <option v-for="m in modalMembers" :key="m.id" :value="m.id">{{ m.name }}</option>
              </select>
              <div v-if="createForm.errors.member_id" class="mt-1 text-sm text-red-500">
                {{ createForm.errors.member_id }}
              </div>
            </div>
            <div class="flex justify-end gap-3">
              <button type="button" @click="closeCreateModal" class="rounded-full bg-red-100 px-6 py-2 font-semibold text-red-700 transition hover:bg-red-200">Cancel</button>
              <button type="submit" class="rounded-full bg-blue-600 px-6 py-2 font-semibold text-white transition hover:bg-blue-700">Create</button>
            </div>
          </form>
        </div>
      </div>
    </transition>
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete PPC Head</h3>
            <p>
              Are you sure you want to delete this PPC Head?
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
  </AppLayout>
</template>

<style>
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
