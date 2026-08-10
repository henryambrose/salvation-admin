<script setup lang="ts">
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { Download, Plus } from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

const props = defineProps({
  s_c_c_heads: {
    type: Object,
    default: () => ({ data: [] }),
  },
  communities: {
    type: Array as () => Array<{ id: string | number; name: string }>,
    default: () => [],
  },
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'community_name', label: 'Community Name', sortable: true },
  { key: 'member_full_name', label: 'Member Name', sortable: true },
];

const partialOnly = ['s_c_c_heads', 'filters'];
const searchTimeout = ref<number | null>(null);
const breadcrumbs = [{ title: 'SCC Heads', href: '/scc-head/index' }];
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showCreateModal = ref(false);
const editingSCCHead = ref<any>(null);
const deletingItem = ref<Record<string, any>>();
const modalMembers = ref<any[]>([]);
const isLoadingMembers = ref(false);
const highlightedRowId = ref<number | null>(null);
const isArchived = ref(String(props.filters?.isArchived) === 'true');
const serverArchived = computed(() => String(props.filters?.isArchived) === 'true');
const editForm = useForm<{ id: string | number; member_id: any; community_id: any }>({
  id: '',
  member_id: null,
  community_id: null,
});
const createForm = useForm<{ member_id: any; community_id: any }>({
  member_id: null,
  community_id: null,
});

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

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
        page: 1,
      },
      { preserveState: false, replace: true },
    );
  }
}

const enhancedSCCHeads = computed(() => {
  const c = props.s_c_c_heads || {};
  return {
    data: c.data || [],
    prev_page_url: c.prev_page_url ?? c.meta?.prev_page_url,
    next_page_url: c.next_page_url ?? c.meta?.next_page_url,
    current_page: c.current_page ?? c.meta?.current_page,
    last_page: c.last_page ?? c.meta?.last_page,
    total: c.total ?? c.meta?.total,
  };
});

watch(
  [search, sort, direction, perPage, isArchived],
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

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`scc-head-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
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
      page,
    },
    {
      preserveState: true,
      replace: true,
      only: partialOnly,
    },
  );
}

watch(
  () => editForm.community_id,
  async (newVal: any) => {
    if (newVal) {
      const { data } = await axios.get(`/api/community/${newVal.id}/members`);
      modalMembers.value = data;
      editForm.member_id = null;
    } else {
      modalMembers.value = [];
      editForm.member_id = null;
    }
  },
);

watch(
  () => createForm.community_id,
  async (newVal: any) => {
    if (newVal) {
      const { data } = await axios.get(`/api/community/${newVal.id}/members`);
      modalMembers.value = data;
      createForm.member_id = null;
    } else {
      modalMembers.value = [];
      createForm.member_id = null;
    }
  },
);

// Async search function for member dropdowns
async function searchMembers(query: string) {
  const communityId = editForm.community_id?.id || createForm.community_id?.id;
  if (!communityId) return;

  if (!query || query.length < 2) {
    // Reload default list
    const { data } = await axios.get(`/api/community/${communityId}/members`);
    modalMembers.value = data;
    return;
  }

  isLoadingMembers.value = true;
  try {
    const { data } = await axios.get(`/api/community/${communityId}/members`, {
      params: { search: query },
    });
    modalMembers.value = data;
  } catch (error) {
    console.error('Failed to search members:', error);
  } finally {
    isLoadingMembers.value = false;
  }
}

watch(
  () => enhancedSCCHeads.value.data,
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

function openEditModal(row: any) {
  editingSCCHead.value = row;
  // Set the full community object
  editForm.community_id = props.communities.find((c) => c.id === row.community_id) || null;
  showEditModal.value = true;
  nextTick(async () => {
    if (editForm.community_id) {
      const { data } = await axios.get(`/api/community/${editForm.community_id.id}/members`);
      modalMembers.value = data;
      // Set the full member object
      editForm.member_id = modalMembers.value.find((m) => m.id === row.member_id) || null;
    } else {
      modalMembers.value = [];
      editForm.member_id = null;
    }
  });
}

function submitEdit() {
  if (!editForm.member_id || !editForm.community_id) return;
  const editedId = editingSCCHead.value?.id;
  editForm.transform((data) => ({
    ...data,
    perPage: perPage.value,
    page: enhancedSCCHeads.value.current_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
    community_id: editForm.community_id ? editForm.community_id.id : null,
    member_id: editForm.member_id ? editForm.member_id.id : null,
  }));
  editForm.put(`/scc-head/${editedId || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingSCCHead.value = undefined;
      nextTick(() => {
        fetch(enhancedSCCHeads.value.current_page);
        highlightedRowId.value = editedId;
      });
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
  // Check for duplicate community
  const existingCommunity = enhancedSCCHeads.value.data.find((head: any) => head.community_id === createForm.community_id?.id);

  if (existingCommunity) {
    createForm.setError('community_id', 'This community already has an SCC Head assigned.');
    return;
  }

  createForm.transform((data) => ({
    ...data,
    perPage: perPage.value,
    page: enhancedSCCHeads.value.last_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
    community_id: createForm.community_id ? createForm.community_id.id : null,
    member_id: createForm.member_id ? createForm.member_id.id : null,
  }));
  createForm.post('/scc-head', {
    preserveScroll: true,
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
      nextTick(() => {
        fetch(enhancedSCCHeads.value.last_page);
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
  if (!deletingItem.value) return;
  const deletedId = deletingItem.value?.id;
  router.delete(`/scc-head/${deletedId || ''}`, {
    data: {
      perPage: perPage.value,
      page: enhancedSCCHeads.value.current_page,
      search: search.value,
      sort: sort.value,
      direction: direction.value,
      isArchived: isArchived.value ? 'true' : 'false',
    },
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingItem.value = undefined;
      highlightedRowId.value = deletedId + 1;
      nextTick(() => scrollToRow(deletedId + 1));
    },
  });
}

function restoreSCCHead(id: number) {
  router.post(
    `/scc-head/${id}/restore`,
    {},
    {
      preserveScroll: true,
      only: partialOnly,
      onSuccess: () => {
        isArchived.value = false;
      },
    },
  );
  isArchived.value = false;
}

function downloadCsv() {
  const params = new URLSearchParams({
    search: search.value || '',
    sort: String(sort.value || 'id'),
    direction: String(direction.value || 'asc'),
    perPage: 'all',
    isArchived: isArchived.value ? 'true' : 'false',
  });

  // Use window.location.href for direct download
  window.location.href = `${window.location.origin}/scc-head/export?${params.toString()}`;
}

import { permissionHelpers } from '@/composables/permissionHelpers';
const { can } = permissionHelpers();

const canCreateSCCHead = can('create-scc-head');
const canReadAnySCCHead = can('read-scc-head');
const canExportSCCHead = can('read-scc-head');

// Focus first input when create modal opens
watch(showCreateModal, (isOpen) => {
  if (isOpen) {
    nextTick(() => {
      const input = document.querySelector('[data-create-input] input') as HTMLElement;
      if (input) input.focus();
    });
  }
});

// Focus first input when edit modal opens
watch(showEditModal, (isOpen) => {
  if (isOpen) {
    nextTick(() => {
      const input = document.querySelector('[data-edit-input] input') as HTMLElement;
      if (input) input.focus();
    });
  }
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="SSC Heads" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">SCC Head</h2>
        <div class="btn-group flex space-x-2">
          <Button
            v-if="canExportSCCHead"
            @click="downloadCsv"
            class="flex items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-white shadow transition hover:bg-green-700"
          >
            <component :is="Download" />
            <span>Export CSV</span>
          </Button>
          <Button
            v-if="canCreateSCCHead"
            @click="openCreateModal"
            class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
          >
            <component :is="Plus" />
            <span>Add SCC Head</span>
          </Button>
        </div>
      </div>
      <div class="mb-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative">
            <input
              v-model="search"
              @keyup.enter="fetch()"
              type="text"
              class="rounded-full border border-gray-300 px-3 py-1 pr-8 focus:ring-2 focus:ring-blue-200"
              placeholder="Search..."
              @keydown.escape="clearSearch"
            />
            <button v-if="search" @click="clearSearch" class="absolute top-1/2 right-2 -translate-y-1/2 transform text-gray-400 hover:text-gray-600">
              ✕
            </button>
          </div>
          <select v-model="perPage" @change="fetch()" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>
        <div class="flex items-center gap-4">
          <label class="flex cursor-pointer items-center gap-2 select-none">
            <Checkbox v-model="isArchived" class="switch-checkbox" />
            <span class="text-sm font-medium">Show Archived</span>
          </label>
        </div>
      </div>
    </DatatableHeader>

    <div v-if="canReadAnySCCHead">
      <!-- Compact pagination with inline stats above the table -->
      <div class="mb-2 flex items-center justify-between gap-3 rounded border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs">
        <!-- Left side: Total records info -->
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ enhancedSCCHeads.total || 0 }}</span> total SCC heads
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>

        <!-- Center: Pagination controls -->
        <div class="flex items-center gap-2">
          <button
            v-if="enhancedSCCHeads.prev_page_url"
            @click="fetch(enhancedSCCHeads.current_page - 1)"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
          >
            ← Prev
          </button>

          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select
              v-if="enhancedSCCHeads.last_page && enhancedSCCHeads.last_page > 1"
              :value="enhancedSCCHeads.current_page"
              @change="(event) => fetch(Number((event.target as HTMLSelectElement).value))"
              class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50 focus:border-blue-500 focus:ring-1 focus:ring-[#3b82f6]"
            >
              <option v-for="page in enhancedSCCHeads.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ enhancedSCCHeads.last_page }}</span>
          </div>

          <button
            v-if="enhancedSCCHeads.next_page_url"
            @click="fetch(enhancedSCCHeads.current_page + 1)"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
          >
            Next →
          </button>
        </div>

        <!-- Right side: Additional info -->
        <div class="text-gray-500">
          <span class="rounded-full bg-violet-100 px-2 py-1 text-xs font-medium text-violet-800"> SCC Heads </span>
        </div>
      </div>

      <div class="mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
        <!-- Table content remains the same -->
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
                v-for="row in enhancedSCCHeads.data"
                :key="row.id"
                :id="`scc-head-row-${row.id}`"
                :class="['transition even:bg-gray-50 hover:bg-blue-50', highlightedRowId === row.id ? 'highlight-row' : '']"
              >
                <td class="p-2">
                  <template v-if="!serverArchived">
                    <Button @click="openEditModal(row)" class="rounded-full bg-yellow-100 text-yellow-700 transition hover:bg-yellow-200">
                      Edit
                    </Button>
                  </template>
                  <template v-else>
                    <Button @click="restoreSCCHead(row.id)" class="rounded-full bg-green-100 text-green-700 transition hover:bg-green-200">
                      Restore
                    </Button>
                  </template>
                </td>
                <td v-for="col in columns" :key="col.key" class="p-2">
                  <span>
                    {{ row[col.key] }}
                  </span>
                </td>
                <td class="p-2">
                  <template v-if="!serverArchived">
                    <Button
                      @click="openDeleteModal(row)"
                      variant="destructive"
                      class="rounded-full bg-red-100 text-red-700 transition hover:bg-red-200"
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
    </div>

    <!-- Remove the old pagination section -->
    <!-- <div class="mt-6 flex items-center justify-between gap-4"> ... </div> -->
    <transition name="fade">
      <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="w-full max-w-full min-w-[400px] rounded-2xl bg-[#ffffff] p-8 shadow-2xl sm:w-[420px]">
          <h2 class="mb-6 text-2xl font-bold text-gray-900">Edit SCC Head</h2>
          <form @submit.prevent="submitEdit">
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Community</label>
              <Multiselect
                v-model="editForm.community_id"
                :options="props.communities"
                label="name"
                track-by="id"
                placeholder="Select Community"
                data-edit-input
              />
            </div>
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Member</label>
              <Multiselect
                v-model="editForm.member_id"
                :options="modalMembers"
                label="name"
                track-by="id"
                placeholder="Type to search members..."
                :searchable="true"
                :internal-search="false"
                :loading="isLoadingMembers"
                @search-change="searchMembers"
                :disabled="!editForm.community_id"
              />
              <div v-if="!editForm.member_id" class="mt-1 text-sm text-red-500">Please select a member.</div>
            </div>
            <div class="flex justify-end gap-3">
              <button
                type="button"
                @click="showEditModal = false"
                class="rounded-full bg-red-100 px-6 py-2 font-semibold text-red-700 transition hover:bg-red-200"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="editForm.processing"
                class="rounded-full bg-blue-600 px-6 py-2 font-semibold text-white transition hover:bg-blue-700"
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
        <div class="w-full max-w-full min-w-[400px] rounded-2xl bg-[#ffffff] p-8 shadow-2xl sm:w-[420px]">
          <h2 class="mb-6 text-2xl font-bold text-gray-900">Create SCC Member</h2>
          <form @submit.prevent="submitCreate">
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Community</label>
              <Multiselect
                v-model="createForm.community_id"
                :options="props.communities"
                label="name"
                track-by="id"
                placeholder="Select Community"
                data-create-input
              />
              <div v-if="createForm.errors.community_id || createForm.errors['community_id']" class="mt-1 text-sm text-red-500">
                {{ createForm.errors.community_id || createForm.errors['community_id'] }}
              </div>
            </div>
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Member</label>
              <Multiselect
                v-model="createForm.member_id"
                :options="modalMembers"
                label="name"
                track-by="id"
                placeholder="Type to search members..."
                :searchable="true"
                :internal-search="false"
                :loading="isLoadingMembers"
                @search-change="searchMembers"
                :disabled="!createForm.community_id"
              />
              <div v-if="createForm.errors.member_id" class="mt-1 text-sm text-red-500">
                {{ createForm.errors.member_id }}
              </div>
            </div>
            <div class="flex justify-end gap-3">
              <button
                type="button"
                @click="closeCreateModal"
                class="rounded-full bg-red-100 px-6 py-2 font-semibold text-red-700 transition hover:bg-red-200"
              >
                Cancel
              </button>
              <button type="submit" class="rounded-full bg-blue-600 px-6 py-2 font-semibold text-white transition hover:bg-blue-700">Create</button>
            </div>
          </form>
        </div>
        s
      </div>
    </transition>
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete SCC Member</h3>
            <p>Are you sure you want to delete this SCC Head?</p>
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
  </AppLayout>
</template>

<style>
.switch-checkbox {
  width: 2.5rem;
  height: 1.25rem;
  border-radius: 9999px;
  background: #ef4444; /* Tailwind red-500 */
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
.highlight-row {
  animation: highlight-fade 2s;
  background-color: #fef08a !important; /* Tailwind yellow-200 */
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
