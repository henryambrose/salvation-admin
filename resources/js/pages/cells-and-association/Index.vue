<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref, watch, computed, nextTick } from 'vue';
import { Pencil, Plus, Trash } from 'lucide-vue-next';

const props = defineProps({
  cellsAndAssociations: {
    type: Object,
    default: () => ({ data: [] }),
  },
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Name', sortable: true },
];

const breadcrumbs = [{ title: 'Cells and Associations', href: '/cells-and-association/index' }];

const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editingCellsAndAssociation = ref<Record<string, any>>();
const deletingCellsAndAssociation = ref<Record<string, any>>();
const highlightedRowId = ref<number|null>(null);
const isArchived = ref(props.filters?.isArchived === 'true');

const form = useForm({
  name: '',
});

const editForm = useForm({
  name: '',
});

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

const enhancedCellsAndAssociations = computed(() => {
  const c = props.cellsAndAssociations || {};
  return {
    data: c.data || [],
    prev_page_url: c.prev_page_url ?? c.meta?.prev_page_url,
    next_page_url: c.next_page_url ?? c.meta?.next_page_url,
    current_page: c.current_page ?? c.meta?.current_page,
    last_page: c.last_page ?? c.meta?.last_page,
  };
});

watch([search, sort, direction, perPage, isArchived], () => {
  fetch();
});

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`cells-and-association-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

function fetch(page = 1) {
  router.get(
    props.fetchUrl || '',
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

function submit() {
  form.transform(data => ({
    ...data,
    perPage: perPage.value,
    page: enhancedCellsAndAssociations.value.last_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  form.post('/cells-and-association', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showModal.value = false;
      nextTick(() => {
        fetch(enhancedCellsAndAssociations.value.last_page);
        highlightedRowId.value = -1;
      });
    },
  });
}

function openEditModal(row: any) {
  editingCellsAndAssociation.value = row;
  editForm.name = row.name;
  showEditModal.value = true;
}

function submitEdit() {
  const editedId = editingCellsAndAssociation.value?.id;
  editForm.transform(data => ({
    ...data,
    perPage: perPage.value,
    page: enhancedCellsAndAssociations.value.current_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  editForm.put(`/cells-and-association/${editedId || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingCellsAndAssociation.value = undefined;
      highlightedRowId.value = editedId;
      nextTick(() => scrollToRow(editedId));
    },
  });
}

function openDeleteModal(row: any) {
  deletingCellsAndAssociation.value = row;
  showDeleteModal.value = true;
}

function confirmDelete() {
  router.delete(`/cells-and-association/${deletingCellsAndAssociation.value?.id || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingCellsAndAssociation.value = undefined;
    },
  });
}

function restoreCellsAndAssociation(id: number) {
  router.post(`/cells-and-association/${id}/restore`, {}, {
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

import { permissionHelpers } from '@/composables/permissionHelpers';
const { can } = permissionHelpers();

const canCreateCellsAndAssociation = can('create-cells-and-association');
const canReadAnyCellsAndAssociation = can('read-cells-and-association');
const canUpdateAnyCellsAndAssociation = can('update-cells-and-association');
const canDeleteAnyCellsAndAssociation = can('delete-cells-and-association');

watch(() => enhancedCellsAndAssociations.value.data, (rows) => {
  if (highlightedRowId.value) {
    let rowId = highlightedRowId.value;
    if (rowId === -1 && rows.length) {
      rowId = rows[rows.length - 1].id;
    }
    scrollToRow(rowId);
    highlightedRowId.value = null;
  }
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Cells and Associations" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Cells and Associations</h2>
        <Button v-if="canCreateCellsAndAssociation" @click="showModal = true" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
          <component :is="Plus" />
          <span>Add Cells and Association</span>
        </Button>
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

    <div v-if="canReadAnyCellsAndAssociation" class="mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
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
            <tr v-for="row in enhancedCellsAndAssociations.data" :key="row.id" :id="`cells-and-association-row-${row.id}`" :class="['even:bg-gray-50 hover:bg-blue-50 transition', highlightedRowId === row.id ? 'highlight-row' : '']">
              <td class="p-2">
                <div class="flex gap-2">
                  <template v-if="!isArchived">
                    <Button v-if="canUpdateAnyCellsAndAssociation" @click="openEditModal(row)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                      <component :is="Pencil" />
                      <span>Edit</span>
                    </Button>
                  </template>
                  <template v-else>
                    <Button @click="restoreCellsAndAssociation(row.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
                      Restore
                    </Button>
                  </template>
                </div>
              </td>
              <td v-for="col in columns" :key="col.key" class="p-2">
                {{ row[col.key] }}
              </td>
              <td v-if="!isArchived" class="p-2">
                <template v-if="canDeleteAnyCellsAndAssociation">
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

    <!-- Enhanced Pagination -->
    <div class="mt-6 flex items-center justify-between gap-4">
      <div class="flex items-center gap-2">
        <button 
          v-if="enhancedCellsAndAssociations.prev_page_url" 
          @click="fetch(enhancedCellsAndAssociations.current_page! - 1)" 
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
            v-if="enhancedCellsAndAssociations.last_page && enhancedCellsAndAssociations.last_page > 1"
            :value="enhancedCellsAndAssociations.current_page" 
            @change="fetch(Number($event.target.value))"
            class="rounded-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow hover:bg-blue-50 transition focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option v-for="page in enhancedCellsAndAssociations.last_page" :key="page" :value="page">
              {{ page }}
            </option>
          </select>
          <span v-if="enhancedCellsAndAssociations.last_page" class="text-sm text-gray-600">of {{ enhancedCellsAndAssociations.last_page }}</span>
        </div>
        
        <button 
          v-if="enhancedCellsAndAssociations.next_page_url" 
          @click="fetch(enhancedCellsAndAssociations.current_page! + 1)" 
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
        <span v-if="enhancedCellsAndAssociations.total">Total: {{ enhancedCellsAndAssociations.total }} records</span>
      </div>
    </div>

    <!-- Create Modal -->
    <transition name="fade">
      <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Create Cells and Association</h3>
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="form.name" type="text" />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
              </div>

              <div class="flex justify-end space-x-2">
                <Button
                  variant="destructive"
                  type="button"
                  @click="showModal = false"
                  class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition px-6 py-2"
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  :disabled="form.processing"
                  class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2 flex items-center gap-2"
                >
                  {{ form.processing ? 'Creating...' : 'Create' }}
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>

    <!-- Edit Modal -->
    <transition name="fade">
      <div v-if="showEditModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Edit Cells and Association</h3>
            <form @submit.prevent="submitEdit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="editForm.name" type="text" />
                <div v-if="editForm.errors.name" class="mt-1 text-sm text-red-500">{{ editForm.errors.name }}</div>
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

    <!-- Delete Modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Cells and Association</h3>
            <p>
              Are you sure you want to delete <span class="font-bold">{{ deletingCellsAndAssociation?.name }}</span>?
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