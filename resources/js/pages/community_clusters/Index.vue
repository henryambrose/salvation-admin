<script setup lang="ts">

import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { nextTick, ref, watch, computed } from 'vue';
import { Head, router, useForm, usePage} from '@inertiajs/vue3';
import axios from 'axios';
import { Plus, Download } from 'lucide-vue-next';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

const props = defineProps({
  communityClusters: Object,
  clusters: {
    type: Array,
    default: () => [],
  },
  communities: {
    type: Array,
    default: () => [],
  },
  members: {
    type: Array,
    default: () => [],
  },
  filters: Object,
  fetchUrl: String,
});

const page = usePage();

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'community_name', label: 'Community Name', sortable: true },
  { key: 'cluster_name', label: 'Cluster Name', sortable: true },
  { key: 'member_name', label: 'Co-ordinator', sortable: true },
];


const breadcrumbs = [{ title: 'Community Clusters', href: '/community-clusters' }];
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showCreateModal = ref(false);
const editingSCCHead = ref<any>(null);
const deletingItem = ref<Record<string, any>>();
const modalMember = ref<any[]>([]);
const isArchived = ref(props.filters?.isArchived === 'true');
const highlightedRowId = ref<number|null>(null)


const editForm = useForm<{ id: string | number; cluster_id: any; community_id: any; member_id: any }>({
  id: '',
  cluster_id: null,
  community_id: null,
  member_id: null,
});
const createForm = useForm<{ cluster_id: any; community_id: any; member_id: any }>({
  cluster_id: null,
  community_id: null,
  member_id: null,
});

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

function handlePageChange(event: Event) {
  const target = event.target as HTMLSelectElement;
  if (target) {
    fetch(Number(target.value));
  }
}

const enhancedCommunityClusters = computed(() => {
  const c = props.communityClusters || {};
  return {
    data: c.data || [],
    prev_page_url: c.prev_page_url ?? c.meta?.prev_page_url,
    next_page_url: c.next_page_url ?? c.meta?.next_page_url,
    current_page: c.current_page ?? c.meta?.current_page,
    last_page: c.last_page ?? c.meta?.last_page,
    total: c.total ?? c.meta?.total, // Add this line
  };
});

const filteredMembers = computed(() => {
  if (!createForm.community_id) return [];
  const communityId = createForm.community_id.id || createForm.community_id;
  return props.members.filter((member: any) => Number(member.community_id) === Number(communityId));
});

const filteredEditMembers = computed(() => {
  if (!editForm.community_id) return [];
  const communityId = editForm.community_id.id || editForm.community_id;
  return props.members.filter((member: any) => Number(member.community_id) === Number(communityId));
});

// Filter clusters based on selected community (if needed)
const filteredClusters = computed(() => {
  if (!createForm.community_id) return props.clusters;
  // For now, return all clusters since the relationship between communities and clusters
  // might be through community_clusters table
  return props.clusters;
});

watch([search, sort, direction, perPage, isArchived], () => {
  fetch();
});

// Watch for community changes in create form to reset member selection
watch(() => createForm.community_id, (newCommunity) => {
  // Reset member and cluster selection when community changes
  createForm.member_id = null;
  createForm.cluster_id = null;
});

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`community-cluster-row-${rowId}`);
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
        isArchived: isArchived.value ? 'true' : 'false',
        page,
      },
      {
        preserveState: true,
        replace: true,
      },
    );
  }
}



watch(() => enhancedCommunityClusters.value.data, (rows) => {
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
  editingSCCHead.value = row;
  // Set the full community object
  editForm.community_id = props.communities.find((c: any) => c.id === row.community_id) || null;
  // Set the full cluster object
  editForm.cluster_id = props.clusters.find((c: any) => c.id === row.cluster_id) || null;
  // Set the full member object
  editForm.member_id = props.members.find((m: any) => m.id === row.member_id) || null;
  showEditModal.value = true;
  
  // Fetch members for this specific community
  if (row.community_id) {
    axios.get(`/api/community/${row.community_id}/members`)
      .then(response => {
        modalMember.value = response.data;
        if (row.member_id) {
          editForm.member_id = response.data.find((m: any) => m.id === row.member_id) || null;
        }
      })
      .catch(error => {
        console.error('Error fetching members:', error);
      });
  }
}

function submitEdit() {
  if (!editForm.cluster_id || !editForm.community_id) return;
  const editedId = editingSCCHead.value?.id;
  editForm.transform(data => ({
    ...data,
    perPage: perPage.value,
    page: enhancedCommunityClusters.value.current_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
    community_id: editForm.community_id ? editForm.community_id.id : null,
    cluster_id: editForm.cluster_id ? editForm.cluster_id.id : null,
    member_id: editForm.member_id ? editForm.member_id.id : null,
  }));
  editForm.put(`/community-clusters/${editedId || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingSCCHead.value = undefined;
      nextTick(() => {
        fetch(enhancedCommunityClusters.value.current_page);
        highlightedRowId.value = editedId;
      });
    },
    onError: () => {
      // Form errors will be automatically displayed
    },
  });
}
function openCreateModal() {
  showCreateModal.value = true;
  createForm.reset();
  modalMember.value = [];
}
function closeCreateModal() {
  showCreateModal.value = false;
}
function submitCreate() {
  createForm.transform(data => ({
    ...data,
    perPage: perPage.value,
    page: enhancedCommunityClusters.value.last_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
    community_id: createForm.community_id ? createForm.community_id.id : null,
    cluster_id: createForm.cluster_id ? createForm.cluster_id.id : null,
    member_id: createForm.member_id ? createForm.member_id.id : null,
  }));
  createForm.post('/community-clusters', {
    preserveScroll: true,
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
      nextTick(() => {
        fetch(enhancedCommunityClusters.value.last_page);
        highlightedRowId.value = -1;
      });
    },
    onError: () => {
    },
  });
}

function openDeleteModal(row: any) {
  deletingItem.value = row;
  showDeleteModal.value = true;
}

function confirmDelete() {
  router.delete(`/community-clusters/${deletingItem.value?.id || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingItem.value = undefined;
      fetch();
    },
  });
}

function restoreSCCHead(id: number) {
  router.post(`/community-clusters/${id}/restore`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      fetch();
    },
  });
}

function clearSearch() {
  search.value = '';
  // Force immediate fetch to clear results
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
      {
        preserveState: false,
        replace: true,
      },
    );
  }
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
  window.location.href = `${window.location.origin}/community-clusters/export?${params.toString()}`;
}

import { permissionHelpers } from '@/composables/permissionHelpers';
const { can } = permissionHelpers();

const canCreateCommunityCluster = can('create-community-cluster');
const canReadAnyCommunityCluster = can('read-community-cluster');
const canUpdateAnyCommunityCluster = can('update-community-cluster');
const canDeleteAnyCommunityCluster = can('delete-community-cluster');
const canExportCommunityCluster = can('read-community-cluster');
</script>
<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Community Clusters" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Community Clusters</h2>
        <div class="btn-group flex space-x-2">
          <Button v-if="canExportCommunityCluster" @click="downloadExcel" class="flex items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-white shadow hover:bg-green-700 transition">
            <component :is="Download" />
            <span>Export Excel</span>
          </Button>
          <Button v-if="canCreateCommunityCluster" @click="openCreateModal" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700">
            <component :is="Plus" />
            <span>Add Community Cluster</span>
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
            <button 
              v-if="search" 
              @click="clearSearch" 
              class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              ✕
            </button>
          </div>
          <select v-model="perPage" @change="(event) => fetch(Number((event.target as HTMLSelectElement).value))" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
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
    <div v-if="canReadAnyCommunityCluster">
      <!-- Compact pagination with inline stats above the table -->
      <div class="mb-2 flex items-center justify-between gap-3 bg-gray-50 px-3 py-1.5 rounded border border-gray-100 text-xs">
        <!-- Left side: Total records info -->
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ enhancedCommunityClusters.total || 0 }}</span> total community clusters
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>
        
        <!-- Center: Pagination controls -->
        <div class="flex items-center gap-2">
          <button 
            v-if="enhancedCommunityClusters.prev_page_url" 
            @click="fetch(enhancedCommunityClusters.current_page - 1)" 
            class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
          >
            ← Prev
          </button>
          
          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select 
              v-if="enhancedCommunityClusters.last_page && enhancedCommunityClusters.last_page > 1"
              :value="enhancedCommunityClusters.current_page" 
              @change="handlePageChange"
              class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            >
              <option v-for="page in enhancedCommunityClusters.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ enhancedCommunityClusters.last_page }}</span>
          </div>
          
          <button 
            v-if="enhancedCommunityClusters.next_page_url" 
            @click="fetch(enhancedCommunityClusters.current_page + 1)" 
            class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
          >
            Next →
          </button>
        </div>
        
        <!-- Right side: Additional info -->
        <div class="text-gray-500">
          <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-medium">
            Clusters
          </span>
        </div>
      </div>

      <div class="mt-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
        <!-- Table content remains the same -->
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
              <tr v-for="row in enhancedCommunityClusters.data" :key="row.id" :id="`scc-head-row-${row.id}`" :class="['even:bg-gray-50 hover:bg-blue-50 transition', highlightedRowId === row.id ? 'highlight-row' : '']">
                <td class="p-2">
                  <template v-if="!isArchived">
                    <Button v-if="canUpdateAnyCommunityCluster" @click="openEditModal(row)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                      Edit
                    </Button>
                  </template>
                  <template v-if="isArchived">
                    <Button v-if="canUpdateAnyCommunityCluster" @click="restoreSCCHead(row.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
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
                  <template v-if="!isArchived">
                  <Button
                    v-if="canDeleteAnyCommunityCluster"
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
    </div>
    <div v-else class="py-10 text-center text-gray-500">You do not have permission to view community clusters.</div>
    <!-- Enhanced Pagination -->
    <!-- //bg-black bg-opacity-20 -->
    <transition name="fade">
      <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="w-full max-w-full min-w-[400px] rounded-2xl bg-white p-8 shadow-2xl sm:w-[420px]">
          <h2 class="mb-6 text-2xl font-bold text-gray-900">Edit SCC Head</h2>
          <form @submit.prevent="submitEdit">
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Community</label>
              <Multiselect v-model="editForm.community_id" :options="props.communities" label="name" track-by="id" placeholder="Select Community" :disabled="true" />
            </div>
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Cluster</label>
              <Multiselect v-model="editForm.cluster_id" :options="props.clusters" label="name" track-by="id" placeholder="Select Cluster" :disabled="true" />
              <div v-if="!editForm.cluster_id" class="mt-1 text-sm text-red-500">Please select a cluster.</div>
            </div>
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Member</label>
              <Multiselect v-model="editForm.member_id" :options="modalMember" label="name" track-by="id" placeholder="Select Member" />
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
        <div class="w-full max-w-full min-w-[400px] rounded-2xl bg-white p-8 shadow-2xl sm:w-[420px]">
          <h2 class="mb-6 text-2xl font-bold text-gray-900">Create Community Cluster</h2>
          
          <!-- Error Alert -->
          <div v-if="page.props.errors && Object.keys(page.props.errors).length > 0" class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Error</h3>
                <div class="mt-2 text-sm text-red-700">
                  <ul class="list-disc pl-5 space-y-1">
                    <li v-for="(error, key) in page.props.errors" :key="key">{{ error }}</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          
          <form @submit.prevent="submitCreate">
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Community</label>
              <Multiselect v-model="createForm.community_id" :options="props.communities" label="name" track-by="id" placeholder="Select Community" />
              <div v-if="createForm.errors.community_id || createForm.errors['community_id']" class="mt-1 text-sm text-red-500">
                {{ createForm.errors.community_id || createForm.errors['community_id'] }}
              </div>
            </div>
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Cluster</label>
              <Multiselect v-model="createForm.cluster_id" :options="filteredClusters" label="name" track-by="id" placeholder="Select Cluster" :disabled="!createForm.community_id" />
              <div v-if="createForm.errors.cluster_id" class="mt-1 text-sm text-red-500">
                {{ createForm.errors.cluster_id }}
              </div>
            </div>
            <div class="mb-6">
              <label class="mb-2 block font-medium text-gray-700">Member (Optional)</label>
              <Multiselect v-model="createForm.member_id" :options="filteredMembers" label="name" track-by="id" placeholder="Select Member" :disabled="!createForm.community_id" />
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
            <h3 class="mb-4 text-xl font-semibold">Delete Community Cluster</h3>
            <p>
              Are you sure you want to delete this Community Cluster ?
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