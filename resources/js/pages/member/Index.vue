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
  { key: 'contact_no', label: 'Contact No', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'age', label: 'Age', sortable: true },
  { key: 'added_on', label: 'Added On', sortable: true },
  { key: 'last_updated', label: 'Last Updated', sortable: true },
];

const breadcrumbs = [{ title: 'Members', href: '/member/index' }];

function editMember(member: Member) {
  router.get(route('member.edit', member.id));
}

function deleteMember(id: Member['id']) {
  if (confirm('Delete this member?')) {
    router.delete(route('member.destroy', id));
  }
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
        <h2 class="text-2xl font-bold">Members</h2>
        <Button v-if="canCreateMember" as="a" href="/member/create" class="btn btn-secondary">
          <component :is="Plus" />
          <span>Add Member</span>
        </Button>
      </div>
    </DatatableHeader>

    <div class="overflow-x-auto">
      <div v-if="canReadAnyMember">
        <div class="datatable2 mt-4 rounded bg-white p-4 shadow">
          <div class="mb-2 flex items-center gap-2">
            <input v-model="search" type="text" class="rounded border px-2 py-1" placeholder="Search..." />
            <select v-model="perPage" class="rounded border px-2 py-1">
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
              class="mt-1 block w-full"
              placeholder="Select Community"
              @focus-out="fetch"
            />
            <SearchDropdown
              id="search_by_column_key"
              v-model="filterColumnKey"
              :options="searchColumnsOptions"
              class="mt-1 block w-full"
              placeholder="Search By Column"
              @focus-out="fetch()"
            />
            <Input id="search_by_column_value" v-model="filterColumnValue" class="mt-1 block w-full" placeholder="Enter value" @focus-out="fetch()" />
            <div class="flex items-center gap-2">
              <Button @click="toggleisArchived" :class="isArchived ? 'bg-red-800 text-white' : 'bg-gray-200 text-gray-700'">
                <component :is="ArchiveIcon" />
                <span>Archived</span>
              </Button>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full overflow-x-auto border text-left">
              <thead>
                <tr>
                  <th class="border p-2">Actions</th>
                  <th v-for="col in columns" :key="col.key" @click="col.sortable ? changeSort(col.key) : null" class="cursor-pointer border p-2">
                    {{ col.label }}
                    <span v-if="col.sortable && sort === col.key">
                      {{ direction === 'asc' ? '▲' : '▼' }}
                    </span>
                  </th>
                  <th class="border p-2">Delete</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in enhancedMembers.data" :key="item.id">
                  <!-- View + Edit -->
                  <td class="border p-2">
                    <div class="flex gap-2">
                      <Button @click="openViewModal(item)">
                        <component :is="ZapIcon" />
                        <span>View</span>
                      </Button>
                      <Button v-if="canUpdateAnyMember && !item.deleted_at" @click="editMember(item)">
                        <component :is="Pencil" />
                        <span>Edit</span>
                      </Button>
                    </div>
                  </td>

                  <!-- Main table data -->
                  <td v-for="col in columns" :key="col.key" class="border p-2">
                    {{ item[col.key] }}
                  </td>

                  <!-- Delete -->
                  <td class="border p-2">
                    <Button v-if="canDeleteAnyMember && !item.deleted_at" variant="destructive" @click="deleteMember(item.id)">
                      <component :is="Trash" />
                      <span>Delete</span>
                    </Button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-3 flex items-center gap-2">
            <button v-if="enhancedMembers.prev_page_url" @click="fetch(enhancedMembers.current_page - 1)" class="rounded border px-3 py-1">
              Prev
            </button>
            <button v-if="enhancedMembers.next_page_url" @click="fetch(enhancedMembers.current_page + 1)" class="rounded border px-3 py-1">
              Next
            </button>
            <span v-if="enhancedMembers.current_page && enhancedMembers.last_page" class="ml-auto text-sm">
              Page {{ enhancedMembers.current_page }} of {{ enhancedMembers.last_page }}
            </span>
          </div>
        </div>
      </div>
      <div v-else class="py-10 text-center text-gray-500">You do not have permission to view members.</div>
    </div>
  </AppLayout>

  <!-- Member Details Modal -->
  <ViewMemberModal v-model="showViewModal" :member="selectedMember">
    <template #extra="{ member }">
      <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
        <div><strong>College:</strong> {{ member.college_name }}</div>
        <div><strong>Company:</strong> {{ member.company_name }}</div>
        <div><strong>Designation:</strong> {{ member.designation_id }}</div>
        <div><strong>Blood Group:</strong> {{ member.blood_group_id }}</div>
      </div>
    </template>
  </ViewMemberModal>
</template>
