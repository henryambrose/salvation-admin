<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { Plus } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps({
  countries: {
    type: Object,
    default: () => ({ data: [] }),
  },
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Country Name', sortable: true },
  
];

const breadcrumbs = [{ title: 'Countries', href: '/country/index' }];

const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editingCountry = ref<Record<string, any>>();
const deletingCountry = ref<Record<string, any>>();
const isArchived = ref(false);

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

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

watch([search, sort, direction, perPage, isArchived], () => {
  fetch();
});

const enhancedCountries = computed(() => {
  const c = props.countries || {};
  return {
    data: c.data || [],
    prev_page_url: c.prev_page_url ?? c.meta?.prev_page_url,
    next_page_url: c.next_page_url ?? c.meta?.next_page_url,
    current_page: c.current_page ?? c.meta?.current_page,
    last_page: c.last_page ?? c.meta?.last_page,
  };
});

const form = useForm({
  name: '',
});

const editForm = useForm({
  name: '',
});

function submit() {
  form.post('/country', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showModal.value = false;
    },
  });
}

function openEditModal(row: any) {
  editingCountry.value = row;
  editForm.name = row.name;
  showEditModal.value = true;
}

function submitEdit() {
  editForm.put(`/country/${editingCountry.value?.id || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingCountry.value = undefined;
    },
  });
}

function openDeleteModal(row: any) {
  deletingCountry.value = row;
  showDeleteModal.value = true;
}

function confirmDelete() {
  router.delete(`/country/${deletingCountry.value?.id || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingCountry.value = undefined;
    },
  });
}

function restoreCountry(id: number) {
  router.post(`/country/${id}/restore`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      fetch();
    },
  });
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Countries" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Countries</h2>
        <Button @click="showModal = true" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
          <span>➕ Add Country</span>
          </Button>
        </div>
      <div class="mb-4 flex flex-wrap items-center gap-3 rounded-lg bg-gray-50 px-4 py-3">
        <input v-model="search" @keyup.enter="fetch()" type="text" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200" placeholder="Search..." />
        <select v-model="perPage" @change="fetch()" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
          <option :value="10">10</option>
          <option :value="25">25</option>
          <option :value="50">50</option>
          <option :value="100">100</option>
        </select>
      </div>
      <div class="flex items-center gap-4 mt-2 justify-end">
        <label class="flex items-center gap-2 cursor-pointer select-none">
          <Checkbox v-model="isArchived" class="switch-checkbox" />
          <span class="text-sm font-medium">Show Archived</span>
        </label>
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
              <th class="border-b p-3 font-semibold text-gray-700">Delete</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in enhancedCountries.data" :key="row.id" class="even:bg-gray-50 hover:bg-blue-50 transition">
              <td class="p-2">
                <template v-if="!isArchived">
                  <Button @click="openEditModal(row)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                    Edit
                  </Button>
                </template>
                <template v-else>
                  <Button @click="restoreCountry(row.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
                    Restore
                  </Button>
                </template>
              </td>
              <td v-for="col in columns" :key="col.key" class="p-2">
                {{ row[col.key] }}
              </td>
              <td class="p-2">
                <template v-if="!isArchived">
                  <Button @click="openDeleteModal(row)" variant="destructive" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
                    Delete
                  </Button>
                </template>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-6 flex items-center gap-2">
      <button v-if="enhancedCountries.prev_page_url" @click="fetch(enhancedCountries.current_page - 1)" class="rounded-full border border-gray-300 bg-white px-4 py-1 text-gray-700 shadow hover:bg-blue-50 transition">
        Prev
      </button>
      <button v-if="enhancedCountries.next_page_url" @click="fetch(enhancedCountries.current_page + 1)" class="rounded-full border border-gray-300 bg-white px-4 py-1 text-gray-700 shadow hover:bg-blue-50 transition">
        Next
      </button>
      <span v-if="enhancedCountries.current_page && enhancedCountries.last_page" class="ml-auto text-sm text-gray-500">
        Page {{ enhancedCountries.current_page }} of {{ enhancedCountries.last_page }}
      </span>
    </div>

    <!-- Create Modal -->
    <transition name="fade">
      <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Create Country</h3>
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
            <h3 class="mb-4 text-xl font-semibold">Edit Country</h3>
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
            <h3 class="mb-4 text-xl font-semibold">Delete Country</h3>
            <p>
              Are you sure you want to delete <span class="font-bold">{{ deletingCountry?.name }}</span>?
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
</style>
