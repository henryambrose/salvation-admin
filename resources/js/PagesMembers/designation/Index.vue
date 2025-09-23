<script setup lang="ts">
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Download, Pencil, Plus, Trash } from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';

const props = defineProps({
  designations: {
    type: Object,
    default: () => ({ data: [] }),
  },
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'name', label: 'Designation Name', sortable: true },
  // { key: 'description', label: 'Description', sortable: false },
];

const breadcrumbs = [{ title: 'Designations', href: '/designation/index' }];
const partialOnly = ['designations', 'filters'];
const searchTimeout = ref<number | null>(null);
const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editingDesignation = ref<Record<string, any>>();
const deletingDesignation = ref<Record<string, any>>();
const highlightedRowId = ref<number | null>(null);
const isArchived = ref(String(props.filters?.isArchived) === 'true');
const serverArchived = computed(() => String(props.filters?.isArchived) === 'true');

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

const enhancedDesignations = computed(() => {
  const c = props.designations || {};
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
    const el = document.getElementById(`designation-row-${rowId}`);
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
      only: partialOnly,
    },
  );
}

function submit() {
  form.transform((data) => ({
    ...data,
    perPage: perPage.value,
    page: enhancedDesignations.value.last_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  form.post('/designation', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showModal.value = false;
      nextTick(() => {
        fetch(enhancedDesignations.value.last_page);
        highlightedRowId.value = -1;
      });
    },
  });
}

function openEditModal(row: any) {
  editingDesignation.value = row;
  editForm.name = row.name;
  showEditModal.value = true;
}

function submitEdit() {
  const editedId = editingDesignation.value?.id;
  editForm.transform((data) => ({
    ...data,
    perPage: perPage.value,
    page: enhancedDesignations.value.current_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  editForm.put(`/designation/${editedId || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingDesignation.value = undefined;
      highlightedRowId.value = editedId;
      nextTick(() => scrollToRow(editedId));
    },
  });
}

function openDeleteModal(row: any) {
  deletingDesignation.value = row;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (!deletingDesignation.value) return;
  const deletedId = deletingDesignation.value?.id;
  router.delete(`/designation/${deletedId || ''}`, {
    data: {
      perPage: perPage.value,
      page: enhancedDesignations.value.current_page,
      search: search.value,
      sort: sort.value,
      direction: direction.value,
      isArchived: isArchived.value ? 'true' : 'false',
    },
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingDesignation.value = undefined;
      highlightedRowId.value = deletedId + 1;
      nextTick(() => scrollToRow(deletedId + 1));
    },
  });
}

function restoreDesignation(id: number) {
  router.post(
    `/designation/${id}/restore`,
    {},
    {
      preserveScroll: true,
      only: partialOnly,
      onSuccess: () => {
        isArchived.value = false;
      },
    },
  );
}

function clearSearch() {
  search.value = '';
  // Force immediate fetch to clear results
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
      {
        preserveState: false,
        replace: true,
      },
    );
  }
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
  window.location.href = `${window.location.origin}/designation/export?${params.toString()}`;
}

function handlePageChange(event: Event) {
  const target = event.target as HTMLSelectElement;
  if (target) {
    fetch(Number(target.value));
  }
}

import { permissionHelpers } from '@/composables/permissionHelpers';
const { can } = permissionHelpers();

const canCreateDesignation = can('create-designation');
const canReadAnyDesignation = can('read-designation');
const canUpdateAnyDesignation = can('update-designation');
const canDeleteAnyDesignation = can('delete-designation');
const canExportDesignation = can('read-designation');
const canRestoreDesignation = can('restore-designation');

watch(
  () => enhancedDesignations.value.data,
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
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Designations" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Designations</h2>
        <div class="btn-group flex space-x-2">
          <Button
            v-if="canExportDesignation"
            @click="downloadCsv"
            class="flex items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-white shadow transition hover:bg-green-700"
          >
            <component :is="Download" />
            <span>Export CSV</span>
          </Button>
          <Button
            v-if="canCreateDesignation"
            @click="showModal = true"
            class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
          >
            <component :is="Plus" />
            <span>Add Designation</span>
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
            <button v-if="search" @click="clearSearch" class="absolute top-1/2 right-2 -translate-y-1/2 transform text-gray-400 hover:text-gray-600">
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
          <label class="flex cursor-pointer items-center gap-2 select-none">
            <Checkbox v-model="isArchived" class="switch-checkbox" />
            <span class="text-sm font-medium">Show Archived</span>
          </label>
        </div>
      </div>
    </DatatableHeader>

    <div v-if="canReadAnyDesignation">
      <!-- Compact pagination with inline stats above the table -->
      <div class="mb-2 flex items-center justify-between gap-3 rounded border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs">
        <!-- Left side: Total records info -->
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ enhancedDesignations.total || 0 }}</span> total designations
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>

        <!-- Center: Pagination controls -->
        <div class="flex items-center gap-2">
          <button
            v-if="enhancedDesignations.prev_page_url"
            @click="fetch(enhancedDesignations.current_page! - 1)"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
          >
            ← Prev
          </button>

          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select
              v-if="enhancedDesignations.last_page && enhancedDesignations.last_page > 1"
              :value="enhancedDesignations.current_page"
              @change="handlePageChange"
              class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50 focus:border-blue-500 focus:ring-1 focus:ring-[#3b82f6]"
            >
              <option v-for="page in enhancedDesignations.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ enhancedDesignations.last_page }}</span>
          </div>

          <button
            v-if="enhancedDesignations.next_page_url"
            @click="fetch(enhancedDesignations.current_page! + 1)"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
          >
            Next →
          </button>
        </div>

        <!-- Right side: Additional info -->
        <div class="text-gray-500">
          <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-800"> Designations </span>
        </div>
      </div>

      <div class="mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
        <!-- Table content remains the same -->
        <div class="overflow-x-auto rounded-xl border border-gray-100">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr class="bg-blue-50">
                <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
                <th
                  v-for="col in columns"
                  :key="col.key"
                  class="cursor-pointer border-b p-3 font-semibold text-gray-700"
                  @click="
                    col.sortable
                      ? (sort === col.key ? (direction = direction === 'asc' ? 'desc' : 'asc') : ((sort = col.key), (direction = 'asc')), fetch())
                      : null
                  "
                >
                  {{ col.label }}
                  <span v-if="col.sortable && sort === col.key">
                    {{ direction === 'asc' ? '▲' : '▼' }}
                  </span>
                </th>
                <th v-if="!serverArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="row in enhancedDesignations.data"
                :key="row.id"
                :id="`designation-row-${row.id}`"
                :class="['transition even:bg-gray-50 hover:bg-blue-50', highlightedRowId === row.id ? 'highlight-row' : '']"
              >
                <td class="p-2">
                  <div class="flex gap-2">
                    <template v-if="!serverArchived">
                      <Button
                        v-if="canUpdateAnyDesignation"
                        @click="openEditModal(row)"
                        class="rounded-full bg-yellow-100 text-yellow-700 transition hover:bg-yellow-200"
                      >
                        <component :is="Pencil" />
                        <span>Edit</span>
                      </Button>
                    </template>
                    <template v-else>
                      <Button
                        v-if="canRestoreDesignation"
                        @click="restoreDesignation(row.id)"
                        class="rounded-full bg-green-100 text-green-700 transition hover:bg-green-200"
                      >
                        Restore
                      </Button>
                    </template>
                  </div>
                </td>
                <td v-for="col in columns" :key="col.key" class="p-2">
                  {{ row[col.key] }}
                </td>
                <td v-if="!serverArchived" class="p-2">
                  <template v-if="canDeleteAnyDesignation">
                    <Button
                      @click="openDeleteModal(row)"
                      variant="destructive"
                      class="rounded-full bg-red-100 text-red-700 transition hover:bg-red-200"
                    >
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

    <!-- Create Modal -->
    <transition name="fade">
      <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Create Designation</h3>
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
                  class="rounded-full bg-red-100 px-6 py-2 text-red-700 transition hover:bg-red-200"
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  :disabled="form.processing"
                  class="flex items-center gap-2 rounded-full bg-blue-600 px-6 py-2 text-white shadow transition hover:bg-blue-700"
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
            <h3 class="mb-4 text-xl font-semibold">Edit Designation</h3>
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
                  class="rounded-full bg-red-100 px-6 py-2 text-red-700 transition hover:bg-red-200"
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  :disabled="editForm.processing"
                  class="flex items-center gap-2 rounded-full bg-blue-600 px-6 py-2 text-white shadow transition hover:bg-blue-700"
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
            <h3 class="mb-4 text-xl font-semibold">Delete Designation</h3>
            <p>
              Are you sure you want to delete <span class="font-bold">{{ deletingDesignation?.name }}</span
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
@keyframes highlight-fade {
  0% {
    background-color: #fde047;
  }
  100% {
    background-color: inherit;
  }
}
</style>
