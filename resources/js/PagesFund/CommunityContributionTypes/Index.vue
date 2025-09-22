<script setup lang="ts">
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Column } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, RotateCcw, Trash } from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';

const { can } = permissionHelpers();

const props = defineProps({
  contributionTypes: Object,
  filters: Object,
  fetchUrl: String,
});

// Permission checks
const canCreateContributionType = can('create-fund-community-contribution-type') || true;
const canReadAnyContributionType = can('read-fund-community-contribution-type') || true;
const canUpdateAnyContributionType = can('update-fund-community-contribution-type') || true;
const canDeleteAnyContributionType = can('delete-fund-community-contribution-type') || true;
const canRestoreContributionType = can('restore-fund-community-contribution-type') || true;

const columns: Column[] = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'description', label: 'Description', sortable: false },
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
const highlightedRowId = ref<number | null>(null);

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`contribution-type-row-${rowId}`);
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
  sort_order: 0,
  is_active: true,
});

const editForm = useForm({
  name: '',
  description: '',
  sort_order: 0,
  is_active: true,
});

// Validation function to check for duplicate type names
const validateTypeName = (name: string, excludeId?: number) => {
  if (!name || name.trim() === '') {
    return 'The contribution type name cannot be empty.';
  }

  const existingTypes = enhancedContributionTypes.value.data;
  const duplicate = existingTypes.find((type: any) => {
    if (excludeId && type.id === excludeId) return false;
    return type.name.toLowerCase() === name.toLowerCase();
  });

  return duplicate ? 'A contribution type with this name already exists.' : null;
};

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

const partialOnly = ['contributionTypes', 'filters'];
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

function restoreContributionType(id: number) {
  router.post(
    route('fund.community-contribution-types.restore', id),
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

function fetch(page = 1) {
  const url = props.fetchUrl || '/fund/community-contribution-types';
  router.get(
    url,
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

// Add sortBy function
function sortBy(column: string) {
  if (sort.value === column) {
    direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  } else {
    sort.value = column;
    direction.value = 'asc';
  }
  fetch();
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
    // Modal closed - reset form
    form.reset();
    form.clearErrors();
  }
});

// Watch for changes in type name to validate duplicates
watch(
  () => form.name,
  () => {
    // Don't validate if the form is empty (during reset)
    if (!form.name || form.name.trim() === '') {
      if (form.errors.name) form.clearErrors('name');
      return;
    }

    const error = validateTypeName(form.name);
    if (error) {
      form.setError('name', error);
    } else {
      if (form.errors.name) form.clearErrors('name');
    }
  },
);

// Watch for changes in edit form type name
watch(
  () => editForm.name,
  () => {
    // Don't validate if the form is empty (during reset)
    if (!editForm.name || editForm.name.trim() === '') {
      if (editForm.errors.name) editForm.clearErrors('name');
      return;
    }

    const error = validateTypeName(editForm.name, editingType.value?.id);
    if (error) {
      editForm.setError('name', error);
    } else {
      if (editForm.errors.name) editForm.clearErrors('name');
    }
  },
);

const enhancedContributionTypes = computed(() => {
  const types = props.contributionTypes || {};
  return {
    data: types.data || [],
    prev_page_url: types.prev_page_url ?? types.meta?.prev_page_url,
    next_page_url: types.next_page_url ?? types.meta?.next_page_url,
    current_page: types.current_page ?? types.meta?.current_page,
    last_page: types.last_page ?? types.meta?.last_page,
  };
});

function submit() {
  form.transform((data) => ({
    ...data,
    perPage: perPage.value,
    page: enhancedContributionTypes.value.last_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  form.post('/fund/community-contribution-types', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      form.clearErrors();
      showModal.value = false;
      nextTick(() => {
        fetch(enhancedContributionTypes.value.last_page);
        highlightedRowId.value = -1;
      });
    },
    onError: () => {
      // Keep modal open on error
    },
  });
}

function openEditModal(row: any) {
  editingType.value = row;
  editForm.name = row.name;
  editForm.description = row.description || '';
  editForm.sort_order = row.sort_order || 0;
  editForm.is_active = row.is_active;
  showEditModal.value = true;
}

function submitEdit() {
  const editedId = editingType.value?.id;
  editForm.transform((data) => ({
    ...data,
    perPage: perPage.value,
    page: enhancedContributionTypes.value.current_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  editForm.put(`/fund/community-contribution-types/${editedId || ''}`, {
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

  router.delete(route('fund.community-contribution-types.destroy', deletedId), {
    data: {
      perPage: perPage.value,
      page: enhancedContributionTypes.value.current_page,
      search: search.value,
      sort: sort.value,
      direction: direction.value,
      isArchived: isArchived.value ? 'true' : 'false',
    },
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
  { title: 'Community Contribution Types', href: '/fund/community-contribution-types' },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Community Contribution Types" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Community Contribution Types</h2>
        <Button
          v-if="canCreateContributionType"
          @click="openCreateModal"
          class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
        >
          <Plus class="h-[1rem] w-[1rem]" />
          <span>Add Contribution Type</span>
        </Button>
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

    <div v-if="canReadAnyContributionType">
      <!-- Compact pagination with inline stats above the table -->
      <div class="mb-2 flex items-center justify-between gap-3 rounded border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs">
        <!-- Left side: Total records info -->
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ contributionTypes?.total || 0 }}</span> total contribution types
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>

        <!-- Center: Pagination controls -->
        <div class="flex items-center gap-2">
          <button
            v-if="contributionTypes?.prev_page_url"
            @click="fetch(contributionTypes.current_page - 1)"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
          >
            ← Prev
          </button>

          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select
              v-if="contributionTypes?.last_page && contributionTypes.last_page > 1"
              :value="contributionTypes?.current_page"
              @change="handlePageChange"
              class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50 focus:border-blue-500 focus:ring-1 focus:ring-[#3b82f6]"
            >
              <option v-for="page in contributionTypes.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ contributionTypes?.last_page }}</span>
          </div>

          <button
            v-if="contributionTypes?.next_page_url"
            @click="fetch(contributionTypes.current_page + 1)"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
          >
            Next →
          </button>
        </div>

        <!-- Right side: Additional info -->
        <div class="text-gray-500">
          <span class="rounded-full bg-teal-100 px-2 py-1 text-xs font-medium text-teal-800"> Contribution Types </span>
        </div>
      </div>

      <div class="mt-4 rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
        <!-- Table content -->
        <div class="overflow-x-auto rounded-xl border border-gray-100">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr class="bg-blue-50">
                <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
                <th
                  v-for="col in columns"
                  :key="col.key"
                  class="border-b p-3 font-semibold text-gray-700"
                  :class="col.sortable ? 'cursor-pointer hover:bg-blue-100' : ''"
                  @click="col.sortable ? sortBy(col.key) : null"
                >
                  <div class="flex items-center gap-1">
                    {{ col.label }}
                    <template v-if="col.sortable">
                      <span v-if="sort === col.key" class="text-blue-600">
                        {{ direction === 'asc' ? '↑' : '↓' }}
                      </span>
                      <span v-else class="text-gray-400">↕</span>
                    </template>
                  </div>
                </th>
                <th v-if="!serverArchived" class="border-b p-3 font-semibold text-gray-700">Delete</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="row in enhancedContributionTypes.data"
                :key="row.id"
                :id="`contribution-type-row-${row.id}`"
                :class="['transition even:bg-gray-50 hover:bg-blue-50', highlightedRowId === row.id ? 'highlight-row' : '']"
              >
                <td class="p-2">
                  <template v-if="!serverArchived">
                    <div class="flex items-center gap-2">
                      <Button
                        v-if="canUpdateAnyContributionType"
                        @click="openEditModal(row)"
                        class="rounded-full bg-yellow-100 p-2 text-yellow-700 transition hover:bg-yellow-200"
                      >
                        <Pencil class="h-[1rem] w-[1rem]" />
                      </Button>
                    </div>
                  </template>
                  <template v-else>
                    <Button
                      v-if="canRestoreContributionType"
                      @click="restoreContributionType(row.id)"
                      class="rounded-full bg-green-100 p-2 text-green-700 transition hover:bg-green-200"
                    >
                      <RotateCcw class="h-[1rem] w-[1rem]" />
                    </Button>
                  </template>
                </td>
                <td v-for="col in columns" :key="col.key" class="p-2">
                  <template v-if="col.key === 'is_active'">
                    <span
                      :class="[
                        'rounded-full px-2 py-1 text-xs font-medium',
                        row[col.key] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800',
                      ]"
                    >
                      {{ row[col.key] ? 'Active' : 'Inactive' }}
                    </span>
                  </template>
                  <template v-else>
                    {{ row[col.key] || '-' }}
                  </template>
                </td>
                <td v-if="!serverArchived" class="p-2">
                  <template v-if="canDeleteAnyContributionType">
                    <Button
                      @click="openDeleteModal(row)"
                      variant="destructive"
                      class="rounded-full bg-red-100 p-2 text-red-700 transition hover:bg-red-200"
                    >
                      <Trash class="h-[1rem] w-[1rem]" />
                    </Button>
                  </template>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div v-else class="py-10 text-center text-gray-500">You do not have permission to view contribution types.</div>

    <!-- Create Modal -->
    <transition name="fade">
      <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div
          class="bg-opacity-50 absolute inset-0 bg-black"
          @click="
            () => {
              showModal = false;
              form.reset();
              form.clearErrors();
            }
          "
        ></div>
        <div class="from-grey-900 via-grey-800 to-grey-600 relative z-10 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Create Contribution Type</h3>
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="form.name" type="text" />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Description</label>
                <textarea
                  v-model="form.description"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200"
                  rows="3"
                ></textarea>
                <div v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</div>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Sort Order</label>
                <Input v-model="form.sort_order" type="number" min="0" />
                <div v-if="form.errors.sort_order" class="mt-1 text-sm text-red-500">{{ form.errors.sort_order }}</div>
              </div>
              <div class="mb-3">
                <label class="flex items-center gap-2">
                  <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]" />
                  <span class="text-sm font-medium">Active</span>
                </label>
              </div>
              <div class="flex justify-end space-x-2">
                <Button
                  variant="destructive"
                  type="button"
                  @click="
                    () => {
                      showModal = false;
                      form.reset();
                      form.clearErrors();
                    }
                  "
                  class="rounded-full bg-red-100 px-6 py-2 text-red-700 transition hover:bg-red-200"
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  :disabled="form.processing || form.errors.name"
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
            <h3 class="mb-4 text-xl font-semibold">Edit Contribution Type</h3>
            <form @submit.prevent="submitEdit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="editForm.name" type="text" />
                <div v-if="editForm.errors.name" class="mt-1 text-sm text-red-500">{{ editForm.errors.name }}</div>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Description</label>
                <textarea
                  v-model="editForm.description"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200"
                  rows="3"
                ></textarea>
                <div v-if="editForm.errors.description" class="mt-1 text-sm text-red-500">{{ editForm.errors.description }}</div>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Sort Order</label>
                <Input v-model="editForm.sort_order" type="number" min="0" />
                <div v-if="editForm.errors.sort_order" class="mt-1 text-sm text-red-500">{{ editForm.errors.sort_order }}</div>
              </div>
              <div class="mb-3">
                <label class="flex items-center gap-2">
                  <input v-model="editForm.is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]" />
                  <span class="text-sm font-medium">Active</span>
                </label>
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
                  :disabled="editForm.processing || editForm.errors.name"
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
            <h3 class="mb-4 text-xl font-semibold">Delete Contribution Type</h3>
            <p>
              Are you sure you want to delete <span class="font-bold">{{ deletingType?.name }}</span
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