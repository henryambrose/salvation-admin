<script setup lang="ts">
import { Head, usePage, Link, router, useForm } from '@inertiajs/vue3';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { Column } from '@/types';
import { Input } from '@/components/ui/input';
import { Pencil, Trash, RotateCcw, Plus } from 'lucide-vue-next';
import { computed, ref, watch, nextTick } from 'vue';
import { permissionHelpers } from '@/composables/permissionHelpers';

const { can } = permissionHelpers();

const props = defineProps({
  massTypes: Object,
  filters: Object,
  fetchUrl: String,
});

// Permission checks
const canCreateMassType = can('create-fund-mass-type') || true;
const canReadAnyMassType = can('read-fund-mass-type') || true;
const canUpdateAnyMassType = can('update-fund-mass-type') || true;
const canDeleteAnyMassType = can('delete-fund-mass-type') || true;
const canRestoreMassType = can('restore-fund-mass-type') || true;

const columns: Column[] = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Name', sortable: true },
  { key: 'description', label: 'Description', sortable: false },
  { key: 'default_time', label: 'Default Time', sortable: true },
  { key: 'sort_order', label: 'Sort Order', sortable: true },
  { key: 'is_active', label: 'Status', sortable: true },
];

const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

// Function to open create modal and reset form
function openCreateModal() {
  form.reset();
  form.clearErrors();
  showModal.value = true;
}

const editingType = ref<Record<string, any>>();
const deletingType = ref<Record<string, any> | null>(null);
const isArchived = ref(String(props.filters?.isArchived) === 'true');
const serverArchived = computed(() => String(props.filters?.isArchived) === 'true');
const highlightedRowId = ref<number|null>(null);

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`mass-type-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

const form = useForm({
  name: '',
  description: '',
  default_time: '',
  sort_order: 0,
  is_active: true,
});

const editForm = useForm({
  name: '',
  description: '',
  default_time: '',
  sort_order: 0,
  is_active: true,
});

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

const partialOnly = ['massTypes', 'filters'];
const searchTimeout = ref<number | null>(null);

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

function restoreMassType(id: number) {
  router.post(route('fund.mass-types.restore', id), {}, {
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      isArchived.value = false;
    },
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
    { preserveState: true, preserveScroll: true, replace: true, only: partialOnly },
  );
}

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

// Watch for modal state changes to reset form when closed
watch(showModal, (newValue) => {
  if (!newValue) {
    form.reset();
    form.clearErrors();
  }
});

const enhancedMassTypes = computed(() => {
  const types = props.massTypes || {};
  return {
    data: types.data || [],
    prev_page_url: types.prev_page_url ?? types.meta?.prev_page_url,
    next_page_url: types.next_page_url ?? types.meta?.next_page_url,
    current_page: types.current_page ?? types.meta?.current_page,
    last_page: types.last_page ?? types.meta?.last_page,
  };
});

function submit() {
  form.post('/fund/mass-types', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      form.clearErrors();
      showModal.value = false;
      nextTick(() => {
        fetch(enhancedMassTypes.value.last_page);
        highlightedRowId.value = -1;
      });
    },
  });
}

function openEditModal(row: any) {
  editingType.value = row;
  editForm.name = row.name;
  editForm.description = row.description || '';
  editForm.default_time = row.default_time || '';
  editForm.sort_order = row.sort_order || 0;
  editForm.is_active = row.is_active;
  showEditModal.value = true;
}

function submitEdit() {
  const editedId = editingType.value?.id;
  editForm.put(`/fund/mass-types/${editedId || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingType.value = undefined;
      highlightedRowId.value = editedId;
      nextTick(() => scrollToRow(editedId));
    },
  });
}

function openDeleteModal(row: any) {
  deletingType.value = row;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (!deletingType.value) return;
  const deletedId = deletingType.value.id;

  router.delete(route('fund.mass-types.destroy', deletedId), {
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingType.value = null;
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

const breadcrumbs = [
  { title: 'Fund', href: '/fund' },
  { title: 'Mass Types', href: '/fund/mass-types' }
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Mass Types" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Mass Types</h2>
        <Button v-if="canCreateMassType" @click="openCreateModal" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
          <Plus class="w-[1rem] h-[1rem]" />
          <span>Add Mass Type</span>
        </Button>
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
    
    <div v-if="canReadAnyMassType">
      <div class="mb-2 flex items-center justify-between gap-3 bg-gray-50 px-3 py-1.5 rounded border border-gray-100 text-xs">
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ massTypes?.total || 0 }}</span> total mass types
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>
        
        <div class="flex items-center gap-2">
          <button 
            v-if="massTypes?.prev_page_url" 
            @click="fetch(massTypes.current_page - 1)" 
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
          >
            ← Prev
          </button>
          
          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select 
              v-if="massTypes?.last_page && massTypes.last_page > 1"
              :value="massTypes?.current_page" 
              @change="handlePageChange"
              class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
            >
              <option v-for="page in massTypes.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ massTypes?.last_page }}</span>
          </div>
          
          <button 
            v-if="massTypes?.next_page_url" 
            @click="fetch(massTypes.current_page + 1)" 
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 hover:bg-blue-50 transition"
          >
            Next →
          </button>
        </div>
        
        <div class="text-gray-500">
          <span class="px-2 py-1 bg-teal-100 text-teal-800 rounded-full text-xs font-medium">
            Mass Types
          </span>
        </div>
      </div>

      <div class="mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
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
              <tr v-for="row in enhancedMassTypes.data" :key="row.id" :id="`mass-type-row-${row.id}`" :class="['even:bg-gray-50 hover:bg-blue-50 transition', highlightedRowId === row.id ? 'highlight-row' : '']">
                <td class="p-2">
                  <template v-if="!serverArchived">
                    <div class="flex items-center gap-2">
                      <Button v-if="canUpdateAnyMassType" @click="openEditModal(row)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition p-2">
                        <Pencil class="w-[1rem] h-[1rem]" />
                      </Button>
                    </div>
                  </template>
                  <template v-else>
                    <Button v-if="canRestoreMassType" @click="restoreMassType(row.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition p-2">
                      <RotateCcw class="w-[1rem] h-[1rem]" />
                    </Button>
                  </template>
                </td>
                <td v-for="col in columns" :key="col.key" class="p-2">
                  <template v-if="col.key === 'is_active'">
                    <span :class="[
                      'px-2 py-1 rounded-full text-xs font-medium',
                      row[col.key] 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-red-100 text-red-800'
                    ]">
                      {{ row[col.key] ? 'Active' : 'Inactive' }}
                    </span>
                  </template>
                  <template v-else-if="col.key === 'default_time'">
                    {{ row[col.key] ? new Date(`1970-01-01T${row[col.key]}`).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) : '-' }}
                  </template>
                  <template v-else>
                    {{ row[col.key] || '-' }}
                  </template>
                </td>
                <td v-if="!serverArchived" class="p-2">
                  <template v-if="canDeleteAnyMassType">
                    <Button @click="openDeleteModal(row)" variant="destructive" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition p-2">
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

    <!-- Create Modal -->
    <transition name="fade">
      <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="() => { showModal = false; form.reset(); form.clearErrors(); }"></div>
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg relative z-10">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Create Mass Type</h3>
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="form.name" type="text" />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Description</label>
                <textarea v-model="form.description" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Default Time</label>
                <Input v-model="form.default_time" type="time" />
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Sort Order</label>
                <Input v-model="form.sort_order" type="number" min="0" />
              </div>
              <div class="mb-3">
                <label class="flex items-center gap-2">
                  <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]" />
                  <span class="text-sm font-medium">Active</span>
                </label>
              </div>
              <div class="flex justify-end space-x-2">
                <Button
                  type="button"
                  @click="() => { showModal = false; form.reset(); form.clearErrors(); }"
                  class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition px-6 py-2"
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  :disabled="form.processing"
                  class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2"
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
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Edit Mass Type</h3>
            <form @submit.prevent="submitEdit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="editForm.name" type="text" />
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Description</label>
                <textarea v-model="editForm.description" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Default Time</label>
                <Input v-model="editForm.default_time" type="time" />
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Sort Order</label>
                <Input v-model="editForm.sort_order" type="number" min="0" />
              </div>
              <div class="mb-3">
                <label class="flex items-center gap-2">
                  <input v-model="editForm.is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]" />
                  <span class="text-sm font-medium">Active</span>
                </label>
              </div>
              <div class="flex justify-end space-x-2">
                <Button
                  type="button"
                  @click="showEditModal = false"
                  class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition px-6 py-2"
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  :disabled="editForm.processing"
                  class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2"
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
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Mass Type</h3>
            <p>
              Are you sure you want to delete <span class="font-bold">{{ deletingType?.name }}</span>?
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
