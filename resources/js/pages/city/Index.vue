<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref, watch, computed, nextTick, onMounted } from 'vue';
import { Plus, Download } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import { permissionHelpers } from '@/composables/permissionHelpers';

const { can } = permissionHelpers();

const props = defineProps<{
  cities: any;
  states: { id: string | number; name: string }[];
  filters: any;
  fetchUrl: string;
}>();

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'City Name', sortable: true },
  { key: 'state', label: 'State Name', sortable: true },
];

const breadcrumbs = [{ title: 'Cities', href: '/city/index' }];

const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editingCity = ref<Record<string, any>>();
const deletingCity = ref<Record<string, any>>();
const isArchived = ref(false);
const highlightedRowId = ref<number|null>(null);

const form = useForm({
  name: '',
  state_id: '',
});

const editForm = useForm({
  name: '',
  state_id: '',
});

const search = ref(props.filters?.search || '');
const stateId = ref(props.filters?.stateId || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

function fetch(page = 1) {
  if (props.fetchUrl) {
    router.get(
      props.fetchUrl,
      {
        search: search.value,
        stateId: stateId.value,
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

watch([search, stateId, sort, direction, perPage, isArchived], () => {
  fetch();
});

const enhancedCities = computed(() => {
  const c = props.cities || {};
  return {
    data: c.data || [],
    prev_page_url: c.prev_page_url ?? c.meta?.prev_page_url,
    next_page_url: c.next_page_url ?? c.meta?.next_page_url,
    current_page: c.current_page ?? c.meta?.current_page,
    last_page: c.last_page ?? c.meta?.last_page,
  };
});

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`city-row-${rowId}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-row');
      setTimeout(() => el.classList.remove('highlight-row'), 2000);
    }
  });
}

function submit() {
  form.post('/city', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showModal.value = false;
      nextTick(() => {
        fetch(enhancedCities.value.last_page);
        highlightedRowId.value = -1;
      });
    },
  });
}

function submitEdit() {
  const editedId = editingCity.value?.id;
  editForm.put(`/city/${editingCity.value?.id || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingCity.value = undefined;
      highlightedRowId.value = editedId;
      nextTick(() => scrollToRow(editedId));
    },
  });
}

function openEditModal(city: any) {
  editingCity.value = city;
  editForm.name = city.name;
  editForm.state_id = city.state_id;
  showEditModal.value = true;
}

function openDeleteModal(city: any) {
  deletingCity.value = city;
  showDeleteModal.value = true;
}

function deleteCity() {
  router.delete(`/city/${deletingCity.value?.id || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingCity.value = undefined;
    },
  });
}

function restoreCity(id: number) {
  router.post(`/city/${id}/restore`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      fetch();
    },
  });
}

function downloadExcel() {
  const params = new URLSearchParams({
    search: search.value || '',
    stateId: stateId.value || '',
    sort: sort.value || 'id',
    direction: direction.value || 'asc',
    perPage: 'all',
    isArchived: isArchived.value ? 'true' : 'false',
  });
  
  // Use window.location.href for direct download
  window.location.href = `${window.location.origin}/city/export?${params.toString()}`;
}

watch(() => enhancedCities.value.data, (rows) => {
  if (highlightedRowId.value) {
    let rowId = highlightedRowId.value;
    if (rowId === -1 && rows.length) {
      rowId = rows[rows.length - 1].id;
    }
    scrollToRow(rowId);
    highlightedRowId.value = null;
  }
});

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

// Permission helpers
const canCreateCity = can('create-city');
const canReadAnyCity = can('read-city');
const canUpdateAnyCity = can('update-city');
const canDeleteAnyCity = can('delete-city');
const canExportCity = can('read-city');
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Cities" />
    <DatatableHeader>
      <div class="mb-2 flex flex-wrap items-center gap-2 justify-between">
        <div class="flex flex-1 items-center gap-2">
          <div class="flex-1 relative">
                         <input 
               v-model="search" 
               type="text" 
               class="w-full rounded-full border border-gray-300 px-3 py-2 pr-8" 
               placeholder="Search city or state..." 
               @keydown.escape="search = ''"
             />
            <button 
              v-if="search" 
              @click="search = ''" 
              class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              ✕
            </button>
          </div>
          
          <!-- State Filter Dropdown -->
          <div class="relative">
                         <select 
               v-model="stateId" 
               class="rounded-full border border-gray-300 px-3 py-2 pr-8 appearance-none bg-white"
             >
              <option value="">All States</option>
              <option v-for="state in states" :key="state.id" :value="state.id">
                {{ state.name }}
              </option>
            </select>
            <div class="absolute right-2 top-1/2 transform -translate-y-1/2 pointer-events-none">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <Button v-if="canCreateCity" @click="showModal = true" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
            <component :is="Plus" />
            <span>Add New City</span>
          </Button>
          
          <Button v-if="canExportCity" @click="downloadExcel" class="flex items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-white shadow hover:bg-green-700 transition">
            <component :is="Download" />
            <span>Export Excel</span>
          </Button>
          
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <Checkbox v-model="isArchived" class="switch-checkbox" />
            <span class="text-sm text-gray-600">Show Archived</span>
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
            <tr v-for="row in enhancedCities.data" :key="row.id" :id="`city-row-${row.id}`" :class="['even:bg-gray-50 hover:bg-blue-50 transition', highlightedRowId === row.id ? 'highlight-row' : '']">
              <td class="p-2">
                <template v-if="!isArchived">
                  <Button v-if="canUpdateAnyCity" @click="openEditModal(row)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                    Edit 
                  </Button>
                </template>
                <template v-else>
                  <Button v-if="canUpdateAnyCity" @click="restoreCity(row.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
                    Restore
                  </Button>
                </template>
              </td>
              <td v-for="col in columns" :key="col.key" class="p-2">
                <span>
                  {{ col.key === 'state' ? (row.state?.name || '') : row[col.key] }}
                </span>
              </td>
              <td class="p-2">
                <template v-if="!isArchived">
                  <Button v-if="canDeleteAnyCity" @click="openDeleteModal(row)" variant="destructive" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
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
          v-if="enhancedCities.prev_page_url" 
          @click="fetch(enhancedCities.current_page - 1)" 
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
            v-if="enhancedCities.last_page && enhancedCities.last_page > 1"
            :value="enhancedCities.current_page" 
            @change="fetch(Number($event.target.value))"
            class="rounded-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow hover:bg-blue-50 transition focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option v-for="page in enhancedCities.last_page" :key="page" :value="page">
              {{ page }}
            </option>
          </select>
          <span v-if="enhancedCities.last_page" class="text-sm text-gray-600">of {{ enhancedCities.last_page }}</span>
        </div>
        
        <button 
          v-if="enhancedCities.next_page_url" 
          @click="fetch(enhancedCities.current_page + 1)" 
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
        <span v-if="enhancedCities.total">Total: {{ enhancedCities.total }} records</span>
      </div>
    </div>

         <!-- Create Modal -->
     <transition name="fade">
       <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
         <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
           <div class="rounded-lg bg-white p-6">
             <h3 class="mb-4 text-xl font-semibold">Create City</h3>
             <form @submit.prevent="submit">
               <div class="mb-3">
                 <label class="mb-1 block text-sm font-medium">Name</label>
                 <Input v-model="form.name" type="text" />
                 <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
               </div>
               <div class="mb-3">
                 <label class="mb-1 block text-sm font-medium">State</label>
                 <select v-model="form.state_id" class="w-full rounded-lg border border-gray-200 px-4 py-2 text-lg focus:outline-none focus:ring-2 focus:ring-blue-200">
                   <option value="" disabled>Select State</option>
                   <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                 </select>
                 <div v-if="form.errors.state_id" class="mt-1 text-sm text-red-500">{{ form.errors.state_id }}</div>
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
             <h3 class="mb-4 text-xl font-semibold">Edit City</h3>
             <form @submit.prevent="submitEdit">
               <div class="mb-3">
                 <label class="mb-1 block text-sm font-medium">Name</label>
                 <Input v-model="editForm.name" type="text" />
                 <div v-if="editForm.errors.name" class="mt-1 text-sm text-red-500">{{ editForm.errors.name }}</div>
               </div>
               <div class="mb-3">
                 <label class="mb-1 block text-sm font-medium">State</label>
                 <select v-model="editForm.state_id" class="w-full rounded-lg border border-gray-200 px-4 py-2 text-lg focus:outline-none focus:ring-2 focus:ring-blue-200">
                   <option value="" disabled>Select State</option>
                   <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                 </select>
                 <div v-if="editForm.errors.state_id" class="mt-1 text-sm text-red-500">{{ editForm.errors.state_id }}</div>
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
             <h3 class="mb-4 text-xl font-semibold">Delete City</h3>
             <p>
               Are you sure you want to delete <span class="font-bold">{{ deletingCity?.name }}</span>?
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
                 @click="deleteCity"
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