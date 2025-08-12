<script setup lang="ts">
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { nextTick, ref, watch, computed } from 'vue';
import { Head, router, useForm, usePage} from '@inertiajs/vue3';
import axios from 'axios';
import { Plus, Pencil, Trash, Download } from 'lucide-vue-next';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

const props = defineProps({
  cellsAndAssociationMembers: Object,
  cellsAndAssociations: {
    type: Array,
    default: () => [],
  },
  filters: Object,
  fetchUrl: String,
});

const page = usePage();

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'cells_and_association_name', label: 'Cell Association Name', sortable: true },
  { key: 'member_name', label: 'Member Name', sortable: true },
];

const breadcrumbs = [{ title: 'Cells Association Members', href: '/cells-and-association-members' }];
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showCreateModal = ref(false);
const editingItem = ref<any>(null);
const deletingItem = ref<Record<string, any>>();
const isArchived = ref(props.filters?.isArchived === 'true');
const highlightedRowId = ref<number>(-1);

const editForm = useForm<{ id: string | number; cells_and_association_id: any; member_id: any }>({
  id: '',
  cells_and_association_id: null,
  member_id: null,
});

const createForm = useForm<{ cells_and_association_id: any; member_id: any }>({
  cells_and_association_id: null,
  member_id: null,
});

// Member search functionality
const searchMembers = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
const searchTimeout = ref<number | null>(null);

// Filter functionality
const selectedCellAssociation = ref(props.filters?.cellAssociation || '');

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

const enhancedCellsAndAssociationMembers = computed(() => {
  const c = props.cellsAndAssociationMembers || {};
  return {
    data: c.data || [],
    prev_page_url: c.prev_page_url ?? c.meta?.prev_page_url,
    next_page_url: c.next_page_url ?? c.meta?.next_page_url,
    current_page: c.current_page ?? c.meta?.current_page,
    last_page: c.last_page ?? c.meta?.last_page,
    total: c.total ?? c.meta?.total,
  };
});



watch([search, sort, direction, perPage, isArchived, selectedCellAssociation], () => {
  fetch();
});

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`cells-association-member-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

function fetch(pageNum = 1) {
  router.get(
    props.fetchUrl || '',
    {
      search: search.value,
      sort: sort.value,
      direction: direction.value,
      perPage: perPage.value,
      isArchived: isArchived.value ? 'true' : 'false',
      cellAssociation: selectedCellAssociation.value,
      page: pageNum,
    },
    {
      preserveState: true,
      replace: true,
    },
  );
}

function openEditModal(row: any) {
  editingItem.value = row;
  editForm.id = row.id;
  editForm.cells_and_association_id = props.cellsAndAssociations.find((ca: any) => ca.id === row.cells_and_association_id) || null;
  // For edit, we need to fetch the member details
  if (row.member_id) {
    fetchMemberById(Number(row.member_id)).then(member => {
      if (member) {
        editForm.member_id = member;
      } else {
        // If member not found by search, create a placeholder object
        editForm.member_id = {
          id: row.member_id,
          name: row.member_name || 'Member not found'
        };
      }
    });
  }
  showEditModal.value = true;
}

function submitEdit() {
  if (!editForm.cells_and_association_id || !editForm.member_id) return;

  const formData = {
    cells_and_association_id: editForm.cells_and_association_id.id || editForm.cells_and_association_id,
    member_id: editForm.member_id.id || editForm.member_id,
    perPage: perPage.value,
    page: enhancedCellsAndAssociationMembers.value.current_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  };

  editForm.transform(() => formData);

  editForm.put(`/cells-and-association-members/${editForm.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingItem.value = null;
      highlightedRowId.value = Number(editForm.id);
      nextTick(() => scrollToRow(Number(editForm.id)));
    },
  });
}

function openCreateModal() {
  showCreateModal.value = true;
}

function submitCreate() {
  if (!createForm.cells_and_association_id || !createForm.member_id) return;

  const formData = {
    cells_and_association_id: createForm.cells_and_association_id.id || createForm.cells_and_association_id,
    member_id: createForm.member_id.id || createForm.member_id,
    perPage: perPage.value,
    page: enhancedCellsAndAssociationMembers.value.last_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  };

  createForm.transform(() => formData);

  createForm.post('/cells-and-association-members', {
    preserveScroll: true,
    onSuccess: () => {
      createForm.reset();
      showCreateModal.value = false;
      nextTick(() => {
        fetch(enhancedCellsAndAssociationMembers.value.last_page);
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
  router.delete(`/cells-and-association-members/${deletingItem.value?.id || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingItem.value = undefined;
    },
  });
}

function restoreItem(id: number) {
  router.post(`/cells-and-association-members/${id}/restore`, {}, {
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
    cellAssociation: selectedCellAssociation.value || '',
  });
  
  // Use window.location.href for direct download
  window.location.href = `/cells-and-association-members/export?${params.toString()}`;
}

// Member search functions
async function searchMembersByName(searchTerm: string) {
  if (searchTerm.length < 3) {
    searchResults.value = [];
    return;
  }

  isSearching.value = true;
  try {
    const response = await axios.get('/api/members/search', {
      params: { search: searchTerm }
    });
    searchResults.value = response.data;
  } catch (error) {
    console.error('Error searching members:', error);
    searchResults.value = [];
  } finally {
    isSearching.value = false;
  }
}

async function fetchMemberById(memberId: number) {
  try {
    // First try to get the member directly by ID
    const response = await axios.get(`/api/members/${memberId}`);
    return response.data;
  } catch (error) {
    // If direct fetch fails, try search approach
    try {
      const searchResponse = await axios.get('/api/members/search', {
        params: { search: memberId.toString() }
      });
      return searchResponse.data.find((member: any) => member.id === memberId) || null;
    } catch (searchError) {
      console.error('Error fetching member:', searchError);
      return null;
    }
  }
}

function handleMemberSearch(searchTerm: string) {
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  
  searchTimeout.value = setTimeout(() => {
    searchMembersByName(searchTerm);
  }, 300);
}

watch(() => enhancedCellsAndAssociationMembers.value.data, (rows) => {
  if (highlightedRowId.value !== -1) {
    let rowId = highlightedRowId.value;
    if (rowId === -1 && rows.length) {
      rowId = rows[rows.length - 1].id;
    }
    scrollToRow(rowId);
    highlightedRowId.value = -1;
  }
});

import { permissionHelpers } from '@/composables/permissionHelpers';
const { can } = permissionHelpers();

const canCreateCellsAndAssociationMember = can('create-cells-and-association-member');
const canReadAnyCellsAndAssociationMember = can('read-cells-and-association-member');
const canUpdateAnyCellsAndAssociationMember = can('update-cells-and-association-member');
const canDeleteAnyCellsAndAssociationMember = can('delete-cells-and-association-member');
const canExportCellsAndAssociationMember = can('read-cells-and-association-member');
const canRestoreCellsAndAssociationMember = can('restore-cells-and-association-member');
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
         <Head title="Cells Association Members" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
                 <h2 class="text-2xl font-bold text-blue-700">Cells Association Members</h2>
                 <div class="flex gap-2">
           <Button v-if="canExportCellsAndAssociationMember" @click="downloadExcel" class="flex items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-white shadow hover:bg-green-700 transition">
             <component :is="Download" />
             <span>Export Excel</span>
           </Button>
           <Button v-if="canCreateCellsAndAssociationMember" @click="openCreateModal" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
             <component :is="Plus" />
             <span>Add Cells Association Member</span>
           </Button>
         </div>
      </div>
      <div class="mb-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative">
            <input 
              v-model="search" 
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
          <select v-model="selectedCellAssociation" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
            <option value="">All Cell Associations</option>
            <option v-for="ca in props.cellsAndAssociations" :key="(ca as any).id" :value="(ca as any).id">{{ (ca as any).name }}</option>
          </select>
          <select v-model="perPage" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
            <option :value="2">2</option>
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
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

    <div v-if="canReadAnyCellsAndAssociationMember">
      <!-- Compact pagination with inline stats above the table -->
      <div class="mb-2 flex items-center justify-between gap-3 bg-gray-50 px-3 py-1.5 rounded border border-gray-100 text-xs">
        <!-- Left side: Total records info -->
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ enhancedCellsAndAssociationMembers.total || 0 }}</span> total members
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>
        
        <!-- Center: Pagination controls -->
        <div class="flex items-center gap-2">
          <button 
            v-if="enhancedCellsAndAssociationMembers.prev_page_url" 
            @click="fetch(enhancedCellsAndAssociationMembers.current_page - 1)" 
            class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
          >
            ← Prev
          </button>
          
          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select 
              v-if="enhancedCellsAndAssociationMembers.last_page && enhancedCellsAndAssociationMembers.last_page > 1"
              :value="enhancedCellsAndAssociationMembers.current_page" 
              @change="(event) => fetch(Number((event.target as HTMLSelectElement).value))"
              class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            >
              <option v-for="page in enhancedCellsAndAssociationMembers.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ enhancedCellsAndAssociationMembers.last_page }}</span>
          </div>
          
          <button 
            v-if="enhancedCellsAndAssociationMembers.next_page_url" 
            @click="fetch(enhancedCellsAndAssociationMembers.current_page + 1)" 
            class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
          >
            Next →
          </button>
        </div>
        
        <!-- Right side: Additional info -->
        <div class="text-gray-500">
          <span class="px-2 py-1 bg-cyan-100 text-cyan-800 rounded-full text-xs font-medium">
            Members
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
                <th v-for="col in columns" :key="col.key" class="border-b p-3 font-semibold text-gray-700 cursor-pointer"
                    @click="col.sortable ? (sort === col.key ? direction = (direction === 'asc' ? 'desc' : 'asc') : (sort = col.key, direction = 'asc'), fetch()) : null">
                  {{ col.label }}
                  <span v-if="col.sortable && sort === col.key">
                    {{ direction === 'asc' ? '▲' : '▼' }}
                  </span>
                </th>
                <th v-if="!isArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in enhancedCellsAndAssociationMembers.data" :key="row.id" :id="`cells-association-member-row-${row.id}`" :class="['even:bg-gray-50 hover:bg-blue-50 transition', highlightedRowId === row.id ? 'highlight-row' : '']">
                <td class="p-2">
                  <div class="flex gap-2">
                    <template v-if="!isArchived">
                      <Button v-if="canUpdateAnyCellsAndAssociationMember" @click="openEditModal(row)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                        <component :is="Pencil" />
                        <span>Edit</span>
                      </Button>
                    </template>
                    <template v-else>
                      <Button v-if="canRestoreCellsAndAssociationMember" @click="restoreItem(row.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
                        Restore
                      </Button>
                    </template>
                  </div>
                </td>
                <td v-for="col in columns" :key="col.key" class="p-2">
                  {{ row[col.key] }}
                </td>
                <td v-if="!isArchived" class="p-2">
                  <template v-if="canDeleteAnyCellsAndAssociationMember">
                    <Button @click="openDeleteModal(row)" variant="destructive" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
                      <component :is="Trash" />
                      <span>Delete</span>
                    </Button>
                  </template>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <transition name="fade">
      <div v-if="showEditModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
                         <h2 class="mb-6 text-2xl font-bold text-gray-900">Edit Cells Association Member</h2>
                         <form @submit.prevent="submitEdit">
               <div class="mb-4">
                 <label class="mb-2 block font-medium text-gray-700">Cell Association</label>
                 <Multiselect v-model="editForm.cells_and_association_id" :options="props.cellsAndAssociations" label="name" track-by="id" placeholder="Select Cell Association" />
                 <div v-if="!editForm.cells_and_association_id" class="mt-1 text-sm text-red-500">Please select a cell association.</div>
               </div>

               <div class="mb-4">
                 <label class="mb-2 block font-medium text-gray-700">Member</label>
                 <Multiselect 
                   v-model="editForm.member_id" 
                   :options="searchResults" 
                   label="name" 
                   track-by="id" 
                   placeholder="Type at least 3 characters to search members..." 
                   :searchable="true"
                   :loading="isSearching"
                   @search-change="handleMemberSearch"
                 />
                 <div v-if="!editForm.member_id" class="mt-1 text-sm text-red-500">Please select a member.</div>
               </div>

              <div class="flex justify-end space-x-2">
                <Button
                  variant="destructive"
                  type="button"
                  @click="showEditModal = false"
                  class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition px-6 py-2"
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  :disabled="editForm.processing"
                  class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2 flex items-center gap-2"
                >
                  {{ editForm.processing ? 'Saving...' : 'Save' }}
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>

    <!-- Create Modal -->
    <transition name="fade">
      <div v-if="showCreateModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
                         <h2 class="mb-6 text-2xl font-bold text-gray-900">Create Cells Association Member</h2>
                         <form @submit.prevent="submitCreate">
               <div class="mb-4">
                 <label class="mb-2 block font-medium text-gray-700">Cell Association</label>
                 <Multiselect v-model="createForm.cells_and_association_id" :options="props.cellsAndAssociations" label="name" track-by="id" placeholder="Select Cell Association" />
                 <div v-if="!createForm.cells_and_association_id" class="mt-1 text-sm text-red-500">Please select a cell association.</div>
               </div>

               <div class="mb-4">
                 <label class="mb-2 block font-medium text-gray-700">Member</label>
                 <Multiselect 
                   v-model="createForm.member_id" 
                   :options="searchResults" 
                   label="name" 
                   track-by="id" 
                   placeholder="Type at least 3 characters to search members..." 
                   :searchable="true"
                   :loading="isSearching"
                   @search-change="handleMemberSearch"
                 />
                 <div v-if="!createForm.member_id" class="mt-1 text-sm text-red-500">Please select a member.</div>
               </div>

              <div class="flex justify-end space-x-2">
                <Button
                  variant="destructive"
                  type="button"
                  @click="showCreateModal = false"
                  class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition px-6 py-2"
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  :disabled="createForm.processing"
                  class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2 flex items-center gap-2"
                >
                  {{ createForm.processing ? 'Creating...' : 'Create' }}
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>

         <!-- Delete Modal -->
     <transition name="fade">
       <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
         <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
           <div class="rounded-lg bg-white p-6">
                           <h3 class="mb-4 text-xl font-semibold">Delete Cells Association Member</h3>
                            <p class="mb-2">
                 Are you sure you want to delete this Cells Association Member?
               </p>
             <div class="mb-4 p-3 bg-gray-50 rounded-lg">
               <p class="text-sm text-gray-600"><strong>Cell Association:</strong> {{ deletingItem?.cells_and_association_name }}</p>
               <p class="text-sm text-gray-600"><strong>Member:</strong> {{ deletingItem?.member_name }}</p>
             </div>
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