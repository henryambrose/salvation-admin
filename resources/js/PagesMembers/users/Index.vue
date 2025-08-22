<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch, computed, nextTick } from 'vue';
import { Pencil, Plus, Trash, Shield } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import { Checkbox } from '@/components/ui/checkbox';
import { permissionHelpers } from '@/composables/permissionHelpers';

const { can } = permissionHelpers();

const props = defineProps({
  users: {
    type: Object,
    default: () => ({ data: [] }),
  },
  roles: {
    type: Array,
    default: () => [],
  },
  filters: Object,
  fetchUrl: String,
});

// Permission checks
const canCreateUser = can('create-user');
const canReadAnyUser = can('read-user');
const canUpdateAnyUser = can('update-user');
const canDeleteAnyUser = can('delete-user');
const canRestoreUser = can('restore-user');
const canExportUser = can('read-user');

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'created_at', label: 'Created At', sortable: true },
];

const breadcrumbs = [{ title: 'Users', href: '/users' }];

const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showRoleModal = ref(false);
const editingItem = ref<any>(null);
const deletingItem = ref<any>(null);
const selectedUser = ref<any>(null);
const selectedRoles = ref<number[]>([]);
const showSuccessMessage = ref(false);
const highlightedRowId = ref<number | null>(null);
const isArchived = ref(props.filters?.isArchived === 'true');


const form = useForm({
  name: '',
  email: '',
  password: '',
});

const editForm = useForm({
  name: '',
  email: '',
  password: '',
});

const roleForm = useForm({
  name: '',
  email: '',
  roles: []
});

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.perPage || 10);
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');

const enhancedUsers = computed(() => {
  const c = props.users || {};
  return {
    data: c.data || [],
    prev_page_url: c.prev_page_url ?? c.meta?.prev_page_url,
    next_page_url: c.next_page_url ?? c.meta?.next_page_url,
    current_page: c.current_page ?? c.meta?.current_page,
    last_page: c.last_page ?? c.meta?.last_page,
    total: c.total ?? c.meta?.total, // Add this line
  };
});

// Available roles from backend
const availableRoles = computed(() => props.roles || []);

watch([search, sort, direction, perPage, isArchived], () => {
  fetch();
});

function scrollToRow(rowId: number) {
  nextTick(() => {
    const el = document.getElementById(`user-row-${rowId}`);
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
function openCreateModal() {
  form.reset();
  showModal.value = true;
}

function submitCreate() {
  form.transform(data => ({
    ...data,
    perPage: perPage.value,
    page: enhancedUsers.value.last_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  form.post('/users', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showModal.value = false;
      nextTick(() => {
        fetch(enhancedUsers.value.last_page);
        highlightedRowId.value = -1;
      });
    },
  });
}

function openEditModal(row: any) {
  editingItem.value = row;
  editForm.name = row.name;
  editForm.email = row.email;
  editForm.password = '';
  showEditModal.value = true;
}

function submitEdit() {
  const editedId = editingItem.value?.id;
  editForm.transform(data => ({
    ...data,
    perPage: perPage.value,
    page: enhancedUsers.value.last_page,
    search: search.value,
    sort: sort.value,
    direction: direction.value,
    isArchived: isArchived.value ? 'true' : 'false',
  }));
  editForm.put(`/users/${editedId || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingItem.value = null;
      highlightedRowId.value = editedId;
      nextTick(() => scrollToRow(editedId));
    },
  });
}

function openDeleteModal(row: any) {
  deletingItem.value = row;
  showDeleteModal.value = true;
}

function openRoleModal(user: any) {
  selectedUser.value = user;
  selectedRoles.value = user.roles?.map((role: any) => role.id) || [];
  showRoleModal.value = true;
}

function assignRoles() {
  // Set all required user data, not just roles
  roleForm.name = selectedUser.value.name;
  roleForm.email = selectedUser.value.email;
  roleForm.roles = selectedRoles.value;
  
  // Debug logging
  console.log('Assigning roles for user:', selectedUser.value.name, 'Roles:', selectedRoles.value);
  
  roleForm.put(route('users.update', selectedUser.value.id), {
    onSuccess: () => {
      // Close the modal
      showRoleModal.value = false;
      selectedUser.value = null;
      selectedRoles.value = [];
      
      // Show success message briefly
      showSuccessMessage.value = true;
      setTimeout(() => {
        showSuccessMessage.value = false;
      }, 3000);
      
      // Refresh the user data without page reload
      fetch();
    },
    onError: () => {
      // Keep modal open on error so user can fix and retry
      // Error handling is already built into the form
    }
  });
}

function confirmDelete() {
  if (deletingItem.value) {
    const deletedId = deletingItem.value.id;
    router.delete(route('users.destroy', deletedId), {
      data: {
        perPage: perPage.value,
        page: enhancedUsers.value.current_page,
        search: search.value,
        sort: sort.value,
        direction: direction.value,
        isArchived: isArchived.value ? 'true' : 'false',
      },
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false;
        deletingItem.value = null;
        highlightedRowId.value = deletedId+1;
        nextTick(() => scrollToRow(deletedId+1));
      },
    });
  }
}

function restoreUser(id: string) {
  router.post(`/users/${id}/restore`, {}, {
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

function formatDate(dateStr: string) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-GB'); // dd/mm/yyyy
}



function handlePageChange(event: Event) {
  const target = event.target as HTMLSelectElement;
  if (target) {
    fetch(Number(target.value));
  }
}




</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">

    <Head title="Users" />
    <DatatableHeader>
      <!-- Success Message -->
      <div v-if="showSuccessMessage" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center justify-between">
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
          </svg>
          <span class="font-medium">User roles updated successfully!</span>
        </div>
        <button @click="showSuccessMessage = false" class="text-green-500 hover:text-green-700">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
        </button>
      </div>
      
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Users</h2>
        <Button v-if="canCreateUser" @click="openCreateModal"
          class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
          <span>➕ Add User</span>
        </Button>
      </div>
      <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-lg bg-gray-50 px-4 py-3">
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative">
            <input v-model="search" @keyup.enter="fetch()" type="text"
              class="rounded-full border border-gray-300 px-3 py-1 pr-8 focus:ring-2 focus:ring-blue-200"
              placeholder="Search..." @keydown.escape="clearSearch" />
            <button v-if="search" @click="clearSearch"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
              ✕
            </button>
          </div>
          <select v-model="perPage" @change="handlePageChange"
            class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>
        <label class="flex items-center gap-2 cursor-pointer select-none">
          <Checkbox v-model="isArchived" class="switch-checkbox" />
          <span class="text-sm font-medium">Show Archived</span>
        </label>
      </div>
    </DatatableHeader>
    <div v-if="canReadAnyUser">
      <!-- Compact pagination with inline stats above the table -->
      <div
        class="mb-2 flex items-center justify-between gap-3 bg-gray-50 px-3 py-1.5 rounded border border-gray-100 text-xs">
        <!-- Left side: Total records info -->
        <div class="text-gray-600">
          Showing <span class="font-semibold">{{ enhancedUsers.total || 0 }}</span> total users
          <span v-if="search" class="text-blue-600">for "{{ search }}"</span>
        </div>

        <!-- Center: Pagination controls -->
        <div class="flex items-center gap-2">
          <button v-if="enhancedUsers.prev_page_url" @click="fetch(enhancedUsers.current_page - 1)"
            class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition">
            ← Prev
          </button>

          <div class="flex items-center gap-1 text-gray-600">
            <span>Page</span>
            <select v-if="enhancedUsers.last_page && enhancedUsers.last_page > 1" :value="enhancedUsers.current_page"
              @change="handlePageChange"
              class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
              <option v-for="page in enhancedUsers.last_page" :key="page" :value="page">
                {{ page }}
              </option>
            </select>
            <span>of {{ enhancedUsers.last_page }}</span>
          </div>

          <button v-if="enhancedUsers.next_page_url" @click="fetch(enhancedUsers.current_page + 1)"
            class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-700 hover:bg-blue-50 transition">
            Next →
          </button>
        </div>

        <!-- Right side: Additional info -->
        <div class="text-gray-500">
          <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
            Users
          </span>
        </div>
      </div>

      <div class="mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
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
              <tr v-for="row in enhancedUsers.data" :key="row.id" :id="`user-row-${row.id}`" :class="['even:bg-gray-50 hover:bg-blue-50 transition', highlightedRowId === row.id ? 'highlight-row' : '']">
                <td class="p-2">
                  <div class="flex gap-2">
                    <template v-if="!isArchived">
                      <Button v-if="canUpdateAnyUser" @click="openEditModal(row)"
                        class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                        <component :is="Pencil" />
                        Edit
                      </Button>
                      <Button @click="openRoleModal(row)"
                        class="rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                        <component :is="Shield" />
                        Roles
                      </Button>
                    </template>
                    <template v-else>
                      <Button v-if="canRestoreUser" @click="restoreUser(row.id)"
                        class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
                        Restore
                      </Button>
                    </template>
                  </div>
                </td>
                <td v-for="col in columns" :key="col.key" class="p-2">
                  <template v-if="col.key === 'created_at'">
                    {{ formatDate(row[col.key]) }}
                  </template>
                  <template v-else>
                    {{ row[col.key] }}
                  </template>
                </td>
                <td v-if="!isArchived" class="p-2">
                  <template v-if="canDeleteAnyUser">
                    <Button @click="openDeleteModal(row)" variant="destructive"
                      class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
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
    <div v-else class="py-10 text-center text-gray-500">You do not have permission to view users.</div>
    <!-- Create Modal -->
    <transition name="fade">
      <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div
          class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Add User</h3>
            <form @submit.prevent="submitCreate">
              <div class="mb-3">
                <Label for="name">Name</Label>
                <Input id="name" v-model="form.name" type="text" required />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
              </div>
              <div class="mb-3">
                <Label for="email">Email</Label>
                <Input id="email" v-model="form.email" type="email" required />
                <div v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</div>
              </div>
              <div class="mb-3">
                <Label for="password">Password</Label>
                <Input id="password" v-model="form.password" type="password" required />
                <div v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</div>
              </div>
              <div class="flex justify-end space-x-2">
                <Button type="button" variant="destructive" @click="showModal = false"
                  class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition px-6 py-2">Cancel</Button>
                <Button type="submit" :disabled="form.processing"
                  class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2 flex items-center gap-2">
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
      <div v-if="showEditModal"
        class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div
          class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Edit User</h3>
            <form @submit.prevent="submitEdit">
              <div class="mb-3">
                <Label for="edit-name">Name</Label>
                <Input id="edit-name" v-model="editForm.name" type="text" required />
                <div v-if="editForm.errors.name" class="mt-1 text-sm text-red-500">{{ editForm.errors.name }}</div>
              </div>
              <div class="mb-3">
                <Label for="edit-email">Email</Label>
                <Input id="edit-email" v-model="editForm.email" type="email" required />
                <div v-if="editForm.errors.email" class="mt-1 text-sm text-red-500">{{ editForm.errors.email }}</div>
              </div>
              <div class="mb-3">
                <Label for="edit-password">New Password (leave blank to keep current)</Label>
                <Input id="edit-password" v-model="editForm.password" type="password" />
                <div v-if="editForm.errors.password" class="mt-1 text-sm text-red-500">{{ editForm.errors.password }}
                </div>
              </div>
              <div class="flex justify-end space-x-2">
                <Button type="button" variant="destructive" @click="showEditModal = false"
                  class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition px-6 py-2">Cancel</Button>
                <Button type="submit" :disabled="editForm.processing"
                  class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2 flex items-center gap-2">
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
      <div v-if="showDeleteModal"
        class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div
          class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete User</h3>
            <p>Are you sure you want to delete <span class="font-bold">{{ deletingItem?.name }}</span>?</p>
            <div class="mt-6 flex justify-end space-x-2">
              <Button type="button" variant="secondary" @click="showDeleteModal = false"
                class="rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition px-6 py-2">Cancel</Button>
              <Button type="button" variant="destructive" :disabled="false" @click="confirmDelete"
                class="rounded-full bg-red-600 text-white shadow hover:bg-red-700 transition px-6 py-2 flex items-center gap-2">Delete</Button>
            </div>
          </div>
        </div>
      </div>
    </transition>
    
    <!-- Role Management Modal -->
    <transition name="fade">
      <div v-if="showRoleModal"
        class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div
          class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Manage Roles for {{ selectedUser?.name }}</h3>
            <p class="mb-4 text-sm text-gray-600">Assign or remove roles for this user</p>
            
            <!-- Current Roles Display -->
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Current Roles:</h4>
              <div class="flex flex-wrap gap-2">
                <span v-if="!selectedUser?.roles || selectedUser?.roles.length === 0" 
                      class="text-gray-500 text-sm">No roles assigned</span>
                <span v-for="role in selectedUser?.roles" :key="role.id"
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ role.name }}
                    </span>
              </div>
            </div>
            
            <!-- Role Assignment Form -->
            <form @submit.prevent="assignRoles">
              <div class="mb-4">
                <Label for="roles">Select Roles:</Label>
                <div class="mt-2 space-y-2 max-h-40 overflow-y-auto">
                  <label v-for="role in availableRoles" :key="role.id" 
                         class="flex items-center space-x-3 p-2 rounded border border-gray-200 hover:bg-gray-50">
                    <input type="checkbox" :value="role.id" v-model="selectedRoles" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                    <div>
                      <span class="text-sm font-medium text-gray-900">{{ role.name }}</span>
                      <p class="text-xs text-gray-500">{{ role.permissions?.length || 0 }} permissions</p>
                    </div>
                  </label>
                </div>
              </div>
              
              <div class="flex justify-end space-x-2">
                <Button type="button" variant="secondary" @click="showRoleModal = false"
                  :disabled="roleForm.processing"
                  class="rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition px-6 py-2">
                  {{ roleForm.processing ? 'Processing...' : 'Cancel' }}
                </Button>
                <Button type="submit" :disabled="roleForm.processing"
                  class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2 flex items-center gap-2">
                  {{ roleForm.processing ? 'Saving...' : 'Save Roles' }}
                </Button>
              </div>
            </form>
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
  background: #ef4444;
  /* Tailwind red-500 */
  box-shadow: 0 2px 8px 0 rgba(239, 68, 68, 0.25), 0 1.5px 4px 0 rgba(0, 0, 0, 0.10);
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
