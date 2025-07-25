<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SearchDropdown } from '@/components/ui/searchDropdown';
import ViewMemberModal from '@/components/ViewMemberModal.vue';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Column, Member } from '@/types';
import { router } from '@inertiajs/vue3';
import { ArchiveIcon, Pencil, Plus, Trash, ZapIcon } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Checkbox } from '@/components/ui/checkbox';

const { can } = permissionHelpers();

const props = defineProps({
  communities: Array<{ id: string | number; name: string }>,
  members: Object,
  filters: Object,
  fetchUrl: String,
  canViewAnyMember: Boolean,
  canCreateMember: Boolean,
  canEditMember: Boolean,
  canDeleteMember: Boolean,
  pagination: {
    type: Object,
    default: () => ({ currentPage: 1, lastPage: 1 }),
  },
});

const showViewModal = ref(false);
const selectedMember = ref(null);

function openViewModal(member: any) {
  selectedMember.value = member;
  showViewModal.value = true;
}

const enhancedMembers = computed<Record<string, any>>(() => {
  return {
    ...props.members,
    data: props.members?.data.map((item: any) => ({
      ...item,
      added_on: item.created_at ? new Date(item.created_at).toLocaleDateString() : '',
      last_updated: item.updated_at ? new Date(item.updated_at).toLocaleDateString() : '',
    })),
  };
});

// Only show key columns in the table
const columns: Column[] = [
  { key: 'first_name', label: 'First Name', sortable: true },
  { key: 'last_name', label: 'Last Name', sortable: true },
  { key: 'family_no', label: 'Family No', sortable: true },
  { key: 'contact_no', label: 'Contact No', sortable: true },
  { key: 'community_cluster_id', label: 'Cluster', sortable: true },
  { key: 'community_id', label: 'Community Name', sortable: true },
  { key: 'date_of_birth', label: 'Date of Birth', sortable: true },
  { key: 'updated_at', label: 'Last Updated', sortable: true },
];

const breadcrumbs = [{ title: 'Members', href: '/member/index' }];

function editMember(member: Member) {
  router.get(route('member.edit', member.id));
}

const showDeleteModal = ref(false);
const deletingMember = ref<any>(null);

function openDeleteModal(member: any) {
  deletingMember.value = member;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (deletingMember.value) {
    router.delete(route('member.destroy', deletingMember.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false;
        deletingMember.value = null;
      },
    });
  }
}

function restoreMember(id: number) {
  router.post(route('member.restore', id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      fetch();
    },
  });
}

function formatDate(dateStr: string) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-GB'); // dd/mm/yyyy
}

const canCreateMember = can('create-member');
const canReadAnyMember = can('read-member');
const canUpdateAnyMember = can('update-member');
const canDeleteAnyMember = can('delete-member');

const searchColumnsOptions = computed(() => {
  return columns
    .filter((col) => col.filterable)
    .map((col) => ({
      id: col.key,
      name: col.label,
    }));
});

const search = ref(props.filters?.search || '');
const sort = ref(props.filters?.sort || '');
const direction = ref(props.filters?.direction || 'asc');
const perPage = ref(props.filters?.perPage || 10);
const communityId = ref(props.filters?.communityId || '');
const filterColumnKey = ref(props.filters?.filterColumnKey || '');
const filterColumnValue = ref(props.filters?.filterColumnValue || '');
const isArchived = ref(props.filters?.isArchived || false);

watch(
  [search, sort, direction, perPage, communityId, filterColumnKey, filterColumnValue, isArchived],
  () => {
    fetch();
  },
  { immediate: false, deep: false },
);

function fetch(page = 1) {
  if (props.fetchUrl) {
    router.get(
      props.fetchUrl,
      {
        search: search.value,
        sort: sort.value,
        direction: direction.value,
        perPage: perPage.value,
        communityId: communityId.value,
        filterColumnKey: filterColumnKey.value,
        filterColumnValue: filterColumnValue.value,
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

function changeSort(field: string) {
  if (sort.value === field) {
    direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  } else {
    sort.value = field;
    direction.value = 'asc';
  }
  fetch();
}

function toggleisArchived() {
  isArchived.value = !isArchived.value;
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Members" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Members</h2>
        <Button v-if="canCreateMember" as="a" href="/member/create" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
          <component :is="Plus" />
          <span>Add Member</span>
        </Button>
      </div>
      <div class="flex items-center gap-4 mt-2">
        <label class="flex items-center gap-2 cursor-pointer select-none">
          <Checkbox v-model="isArchived" class="switch-checkbox" />
          <span class="text-sm font-medium">Show Archived</span>
        </label>
      </div>
    </DatatableHeader>

    <div class="overflow-x-auto">
      <div v-if="canReadAnyMember">
        <div class="datatable2 mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
          <!-- Filters -->
          <div class="mb-4 flex flex-wrap items-center gap-3 rounded-lg bg-gray-50 px-4 py-3">
            <input v-model="search" type="text" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200" placeholder="Search..." />
            <select v-model="perPage" class="rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200">
              <option :value="2">2</option>
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
            </select>
            <SearchDropdown
              id="community_id"
              v-model="communityId"
              :options="props.communities || []"
              class="mt-1 block w-full max-w-xs"
              placeholder="Select Community"
              @focus-out="fetch"
            />
            <SearchDropdown
              id="search_by_column_key"
              v-model="filterColumnKey"
              :options="searchColumnsOptions"
              class="mt-1 block w-full max-w-xs"
              placeholder="Search By Column"
              @focus-out="fetch()"
            />
            <Input id="search_by_column_value" v-model="filterColumnValue" class="mt-1 block w-full max-w-xs rounded-full border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-blue-200" placeholder="Enter value" @focus-out="fetch()" />
          </div>

          <!-- Table -->
          <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full border-collapse text-left">
              <thead>
                <tr class="bg-blue-50">
                  <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
                  <th v-for="col in columns" :key="col.key" @click="col.sortable ? changeSort(col.key) : null" class="cursor-pointer border-b p-3 font-semibold text-gray-700 hover:bg-blue-100 transition">
                    {{ col.label }}
                    <span v-if="col.sortable && sort === col.key">
                      {{ direction === 'asc' ? '▲' : '▼' }}
                    </span>
                  </th>
                  <th class="border-b p-3 font-semibold text-gray-700">Delete</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in enhancedMembers.data" :key="item.id" class="even:bg-gray-50 hover:bg-blue-50 transition">
                  <!-- View + Edit or Restore -->
                  <td class="p-2">
                    <div class="flex gap-2">
                      <template v-if="!isArchived">
                        <Button @click="openViewModal(item)" class="rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                          <component :is="ZapIcon" />
                          <span>View</span>
                        </Button>
                        <Button v-if="canUpdateAnyMember && !item.deleted_at" @click="editMember(item)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                          <component :is="Pencil" />
                          <span>Edit</span>
                        </Button>
                      </template>
                      <template v-else>
                        <Button @click="restoreMember(item.id)" class="rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition">
                          Restore
                        </Button>
                      </template>
                    </div>
                  </td>
                  <!-- Main table data -->
                  <td v-for="col in columns" :key="col.key" class="p-2">
                    <template v-if="['created_at', 'updated_at', 'date_of_birth'].includes(col.key)">
                      {{ formatDate(item[col.key]) }}
                    </template>
                    <template v-else>
                      {{ item[col.key] }}
                    </template>
                  </td>
                  <!-- Delete -->
                  <td class="p-2">
                    <template v-if="!isArchived">
                      <Button v-if="canDeleteAnyMember && !item.deleted_at" variant="destructive" @click="openDeleteModal(item)" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
                        <component :is="Trash" />
                        <span>Delete</span>
                      </Button>
                    </template>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-6 flex items-center gap-2">
            <button v-if="enhancedMembers.prev_page_url" @click="fetch(enhancedMembers.current_page - 1)" class="rounded-full border border-gray-300 bg-white px-4 py-1 text-gray-700 shadow hover:bg-blue-50 transition">
              Prev
            </button>
            <button v-if="enhancedMembers.next_page_url" @click="fetch(enhancedMembers.current_page + 1)" class="rounded-full border border-gray-300 bg-white px-4 py-1 text-gray-700 shadow hover:bg-blue-50 transition">
              Next
            </button>
            <span v-if="enhancedMembers.current_page && enhancedMembers.last_page" class="ml-auto text-sm text-gray-500">
              Page {{ enhancedMembers.current_page }} of {{ enhancedMembers.last_page }}
            </span>
          </div>
        </div>
      </div>
      <div v-else class="py-10 text-center text-gray-500">You do not have permission to view members.</div>
    </div>
  </AppLayout>

  <!-- Member Details Modal -->
  <ViewMemberModal v-model="showViewModal" :member="selectedMember" />

  <!-- Delete Modal -->
  <transition name="fade">
    <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
      <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
        <div class="rounded-lg bg-white p-6">
          <h3 class="mb-4 text-xl font-semibold">Delete Member</h3>
          <p>
            Are you sure you want to delete <span class="font-bold">{{ deletingMember?.first_name }} {{ deletingMember?.last_name }}</span>?
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
  background: #e5e7eb;
  position: relative;
  transition: background 0.2s;
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
