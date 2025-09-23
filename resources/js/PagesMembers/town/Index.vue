<script setup lang="ts">
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import SearchDropdown from '@/components/ui/searchDropdown/SearchDropdown.vue';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, watch } from 'vue';

const { can } = permissionHelpers();

const props = defineProps<{
  towns: { data: any[]; meta?: any };
  cities: { id: string | number; name: string }[];
  filters: any;
  fetchUrl: string;
}>();

// Permission checks
const canCreateTown = can('create-town');
const canReadAnyTown = can('read-town');
const canUpdateAnyTown = can('update-town');
const canDeleteAnyTown = can('delete-town');
const canExportTown = can('read-town');
const canRestoreTown = can('restore-town');

const columns = [
  { key: 'name', label: 'Town Name', sortable: true },
  { key: 'pincode', label: 'Pincode', sortable: true },
  { key: 'city', label: 'City Name', sortable: true },
];

const breadcrumbs = [{ title: 'Towns', href: '/town/index' }];

const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editingTown = ref<Record<string, any>>();
const deletingTown = ref<Record<string, any>>();
const isArchived = ref(String(props.filters?.isArchived) === 'true');
const serverArchived = computed(() => String(props.filters?.isArchived) === 'true');
const highlightedRowId = ref<number | null>(null);
const partialOnly = ['towns', 'filters'];
const searchTimeout = ref<number | null>(null);

const form = useForm({
  name: '',
  pincode: '',
  city_id: '',
});

const editForm = useForm({
  name: '',
  pincode: '',
  city_id: '',
});

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

function fetch(page = 1) {
  if (!props.fetchUrl) return;

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
      only: partialOnly,
    },
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

function handlePageChange(event: Event) {
  const target = event.target as HTMLSelectElement;
  if (target) {
    fetch(Number(target.value));
  }
}

const enhancedTowns = computed(() => {
  const c = props.towns || {};
  const meta = c.meta || {};
  return {
    data: c.data || [],
    prev_page_url: meta.prev_page_url,
    next_page_url: meta.next_page_url,
    current_page: meta.current_page || 1,
    last_page: meta.last_page || 1,
    total: meta.total || 0,
  };
});

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`town-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

function submit() {
  form.transform((data) => ({
    ...data,
    perPage: perPage.value,
    page: enhancedTowns.value.last_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  form.post('/town', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showModal.value = false;
      nextTick(() => {
        fetch(enhancedTowns.value.last_page);
        highlightedRowId.value = -1;
      });
    },
  });
}

function openCreateModal() {
  showModal.value = true;
}

function openEditModal(row: any) {
  editingTown.value = row;
  editForm.name = row.name;
  editForm.pincode = row.pincode;
  editForm.city_id = row.city_id;
  showEditModal.value = true;
}

function submitEdit() {
  const editedId = editingTown.value?.id;
  editForm.transform((data) => ({
    ...data,
    perPage: perPage.value,
    page: enhancedTowns.value.current_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  editForm.put(`/town/${editingTown.value?.id || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingTown.value = undefined;
      highlightedRowId.value = editedId;
      nextTick(() => scrollToRow(editedId));
    },
  });
}

function openDeleteModal(row: any) {
  deletingTown.value = row;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (!deletingTown.value) return;
  const deletedId = deletingTown.value?.id;
  router.delete(`/town/${deletedId || ''}`, {
    data: {
      perPage: perPage.value,
      page: enhancedTowns.value.current_page,
      search: search.value,
      sort: sort.value,
      direction: direction.value,
      isArchived: isArchived.value ? 'true' : 'false',
    },
    preserveScroll: true,
    only: partialOnly,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingTown.value = undefined;
      highlightedRowId.value = deletedId + 1;
      nextTick(() => scrollToRow(deletedId + 1));
    },
  });
}

function restoreTown(id: number) {
  router.post(
    `/town/${id}/restore`,
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

watch(
  () => enhancedTowns.value.data,
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
    <Head title="Towns" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Towns</h2>
        <Button
          v-if="canCreateTown"
          @click="openCreateModal"
          class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
        >
          <span>➕ Add Town</span>
        </Button>
      </div>
      <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-lg bg-gray-50 px-4 py-3">
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
          <select v-model="perPage" @change="handlePageChange" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>
        <label class="flex cursor-pointer items-center gap-2 select-none">
          <Checkbox v-model="isArchived" class="switch-checkbox" />
          <span class="text-sm font-medium">Show Archived</span>
        </label>
      </div>
    </DatatableHeader>

    <div v-if="canReadAnyTown">
      <!-- Compact pagination with inline stats above the table -->
      <div class="mb-2 flex items-center justify-between gap-3 rounded border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs">
        <!-- Left side: Total records info -->
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ enhancedTowns.total || 0 }}</span> total towns
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>

        <!-- Center: Pagination controls -->
        <div class="flex items-center gap-2">
          <button
            v-if="enhancedTowns.prev_page_url"
            @click="fetch(enhancedTowns.current_page - 1)"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
          >
            ← Prev
          </button>

          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select
              v-if="enhancedTowns.last_page && enhancedTowns.last_page > 1"
              :value="enhancedTowns.current_page"
              @change="handlePageChange"
              class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50 focus:border-blue-500 focus:ring-1 focus:ring-[#3b82f6]"
            >
              <option v-for="page in enhancedTowns.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ enhancedTowns.last_page }}</span>
          </div>

          <button
            v-if="enhancedTowns.next_page_url"
            @click="fetch(enhancedTowns.current_page + 1)"
            class="rounded border border-gray-300 bg-[#ffffff] px-2 py-1 text-gray-700 transition hover:bg-blue-50"
          >
            Next →
          </button>
        </div>

        <!-- Right side: Additional info -->
        <div class="text-gray-500">
          <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800"> Towns </span>
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
                v-for="row in enhancedTowns.data"
                :key="row.id"
                :id="`town-row-${row.id}`"
                :class="['transition even:bg-gray-50 hover:bg-blue-50', highlightedRowId === row.id ? 'highlight-row' : '']"
              >
                <td class="p-2">
                  <template v-if="!serverArchived">
                    <Button
                      v-if="canUpdateAnyTown"
                      @click="openEditModal(row)"
                      class="rounded-full bg-yellow-100 text-yellow-700 transition hover:bg-yellow-200"
                    >
                      Edit
                    </Button>
                  </template>
                  <template v-else>
                    <Button
                      v-if="canRestoreTown"
                      @click="restoreTown(row.id)"
                      class="rounded-full bg-green-100 text-green-700 transition hover:bg-green-200"
                    >
                      Restore
                    </Button>
                  </template>
                </td>
                <td v-for="col in columns" :key="col.key" class="p-2">
                  <span>
                    {{ col.key === 'city' ? row.city?.name || '' : row[col.key] }}
                  </span>
                </td>
                <td v-if="!serverArchived" class="p-2">
                  <template v-if="canDeleteAnyTown">
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
    <div v-else class="py-10 text-center text-gray-500">You do not have permission to view towns.</div>

    <!-- Create Modal -->
    <transition name="fade">
      <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Create Town</h3>
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="form.name" type="text" />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Pincode</label>
                <Input v-model="form.pincode" type="text" />
                <div v-if="form.errors.pincode" class="mt-1 text-sm text-red-500">{{ form.errors.pincode }}</div>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">City</label>
                <SearchDropdown
                  v-model="form.city_id"
                  :options="props.cities"
                  class="w-full rounded-lg border border-gray-200 px-4 py-2 text-lg focus:ring-2 focus:ring-blue-200 focus:outline-none"
                  placeholder="Search city..."
                />
                <div v-if="form.errors.city_id" class="mt-1 text-sm text-red-500">{{ form.errors.city_id }}</div>
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
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Edit Town</h3>
            <form @submit.prevent="submitEdit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="editForm.name" type="text" />
                <div v-if="editForm.errors.name" class="mt-1 text-sm text-red-500">{{ editForm.errors.name }}</div>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Pincode</label>
                <Input v-model="editForm.pincode" type="text" />
                <div v-if="editForm.errors.pincode" class="mt-1 text-sm text-red-500">{{ editForm.errors.pincode }}</div>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">City</label>
                <SearchDropdown
                  v-model="editForm.city_id"
                  :options="props.cities"
                  class="w-full rounded-lg border border-gray-200 px-4 py-2 text-lg focus:ring-2 focus:ring-blue-200 focus:outline-none"
                  placeholder="Search city..."
                />
                <div v-if="editForm.errors.city_id" class="mt-1 text-sm text-red-500">{{ editForm.errors.city_id }}</div>
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
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Town</h3>
            <p>
              Are you sure you want to delete <span class="font-bold">{{ deletingTown?.name }}</span
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
