<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SearchDropdown } from '@/components/ui/searchDropdown';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Member } from '@/types';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
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

const enhancedMembers = computed<Record<string, any>>(() => {
  return {
    ...props.members,
    data: props.members?.data.map((item: any) => ({
      ...item,
      //   community_id: item.community?.name || '',
      //   community_cluster_id: item.communityCluster?.name || '',
      added_on: item.created_at ? new Date(item.created_at).toLocaleDateString() : '',
      last_updated: item.updated_at ? new Date(item.updated_at).toLocaleDateString() : '',
      //   cells_and_association_id: item.cellsAndAssociation?.name || '',
      //    blood_group_id: item.bloodGroup?.name || '',
      //    designation_id: item.designation?.name || '',
      //    family_income_range_id: item.familyIncomeRange?.name || '',
      //    status_id: item.status?.name || '',
      //    gender_id: item.gender?.name || '',
      //    relationship_id: item.relationship?.name || '',
    })),
  };
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'first_name', label: 'First Name', sortable: true, filterable: true },
  { key: 'last_name', label: 'Last Name', sortable: true, filterable: true },
  { key: 'community_id', label: 'Community ID', sortable: true },
  { key: 'community_cluster_id', label: 'Community Cluster ID', sortable: true },
  { key: 'new_olsc_id', label: 'New OLSC ID', sortable: true, filterable: true },
  { key: 'old_olsc_id', label: 'Old OLSC ID', sortable: true, filterable: true },
  { key: 'new_sal_id', label: 'New SAL ID', sortable: true, filterable: true },
  { key: 'old_sal_id', label: 'Old SAL ID', sortable: true, filterable: true },
  { key: 'aadhar', label: 'Aadhar', sortable: true },
  { key: 'family_no', label: 'Family No', sortable: true, filterable: true },
  { key: 'status_id', label: 'Status ID', sortable: true, filterable: true },
  { key: 'relationship_id', label: 'Relationship ID', sortable: true },
  { key: 'gender_id', label: 'Gender ID', sortable: true, filterable: true },
  { key: 'date_of_birth', label: 'Date of Birth', sortable: true },
  { key: 'permanent_add1', label: 'Permanent Address Line 1', sortable: true },
  // { key: 'permanent_add2', label: 'Permanent Address Line 2', sortable: true },
  // { key: 'permanent_add3', label: 'Permanent Address Line 3', sortable: true },
  // { key: 'permanent_town_id', label: 'Permanent Town ID', sortable: true },
  // { key: 'permanent_pincode', label: 'Permanent Pincode', sortable: true },
  // { key: 'permanent_state_id', label: 'Permanent State ID', sortable: true },
  // { key: 'permanent_country_id', label: 'Permanent Country ID', sortable: true },
  { key: 'current_add1', label: 'Current Address Line 1', sortable: true },
  // { key: 'current_add2', label: 'Current Address Line 2', sortable: true },
  // { key: 'current_add3', label: 'Current Address Line 3', sortable: true },
  // { key: 'current_town_id', label: 'Current Town ID', sortable: true },
  // { key: 'current_pincode', label: 'Current Pincode', sortable: true },
  // { key: 'current_state_id', label: 'Current State ID', sortable: true },
  // { key: 'current_country_id', label: 'Current Country ID', sortable: true },
  { key: 'contact_no', label: 'Contact No', sortable: true, filterable: true },
  { key: 'email', label: 'Email', sortable: true, filterable: true },
  { key: 'blood_group_id', label: 'Blood Group ID', sortable: true, filterable: true },
  { key: 'cells_and_association_id', label: 'Cells and Association ID', sortable: true, filterable: true },
  { key: 'school_name', label: 'School Name', sortable: true },
  { key: 'college_name', label: 'College Name', sortable: true },
  { key: 'latest_qualifications', label: 'Latest Qualifications', sortable: true },
  { key: 'company_name', label: 'Company Name', sortable: true },
  { key: 'designation_id', label: 'Designation ID', sortable: true, filterable: true },
  { key: 'family_income_range_id', label: 'Family Income Range ID', sortable: true, filterable: true },
  // { key: 'baptism_date', label: 'Baptism Date', sortable: true },
  // { key: 'baptism_reg_no', label: 'Baptism Reg No', sortable: true },
  // { key: 'baptism_parish', label: 'Baptism Parish', sortable: true },
  // { key: 'confirmation_date', label: 'Confirmation Date', sortable: true },
  // { key: 'confirmation_reg_no', label: 'Confirmation Reg No', sortable: true },
  // { key: 'confirmation_parish', label: 'Confirmation Parish', sortable: true },
  { key: 'marriage_date', label: 'Marriage Date', sortable: true },
  { key: 'marriage_reg_no', label: 'Marriage Reg No', sortable: true },
  { key: 'marriage_parish', label: 'Marriage Parish', sortable: true },
  { key: 'death_date', label: 'Death Date', sortable: true },
  { key: 'deaths_reg_no', label: 'Deaths Reg No', sortable: true },
  { key: 'death_parish', label: 'Death Parish', sortable: true },
  { key: 'age', label: 'Age', sortable: true, filterable: true },
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

// Avoid infinite loop: use immediate: false and deep: false for watch
// watch(
//   [search, sort, direction, perPage],
//   () => {
//     fetch();
//   },
//   { immediate: false, deep: false }
// );

// Watch inputs
watch(
  [search, sort, direction, perPage, communityId, filterColumnKey, filterColumnValue],
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
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div class="col-span-1 row-span-1">
          <SearchDropdown
            id="community_id"
            v-model="communityId"
            :options="props.communities || []"
            class="mt-1 block w-full"
            placeholder="Select Community"
            @focus-out="fetch"
          />
        </div>
        <div class="col-span-1 row-span-1">
          <SearchDropdown
            id="search_by_column_key"
            v-model="filterColumnKey"
            :options="searchColumnsOptions"
            class="mt-1 block w-full"
            placeholder="Search By Column"
            @focus-out="fetch()"
          />
        </div>
        <div class="col-span-1 row-span-1">
          <Input id="search_by_column_value" v-model="filterColumnValue" class="mt-1 block w-full" placeholder="Enter value" @focus-out="fetch()" />
        </div>
      </div>

      <!-- Pagination Info -->
      <!-- <div v-if="props.pagination" class="mb-2 text-sm text-gray-600">
                Page {{ props.pagination.currentPage }} of {{ props.pagination.lastPage }}
            </div> -->
    </DatatableHeader>
    <div class="overflow-x-auto" style="max-width: 100vw">
      <!-- <DataTable
                v-if="canReadAnyMember"
                :data="enhancedMembers"
                :columns="columns"
                :filters="filters"
                :fetch-url="fetchUrl"
                :has-actions="canUpdateAnyMember || canDeleteAnyMember"
            >
                <template #actions="{ row }">
                    <Button
                        v-if="canUpdateAnyMember"
                        class="btn btn-secondary mr-2"
                        @click="editMember(row)"
                    >
                        <component :is="Pencil" />
                        <span>Edit</span>
                    </Button>
                    <Button
                        v-if="canDeleteAnyMember"
                        class="btn btn-secondary mr-2"
                        @click="deleteMember(row.id)"
                    >
                        <component :is="Trash" />
                        <span>Delete</span>
                    </Button>
                </template>
            </DataTable> -->
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
          </div>
          <div class="overflow-x-auto" style="max-width: 100vw">
            <table class="overflow-x-auto border text-left">
              <thead>
                <tr>
                  <th v-for="col in columns" :key="col.key" @click="col.sortable ? changeSort(col.key) : null" class="cursor-pointer border p-2">
                    {{ col.label }}
                    <span v-if="col.sortable && sort === col.key">
                      {{ direction === 'asc' ? '▲' : '▼' }}
                    </span>
                  </th>
                  <th v-if="canUpdateAnyMember || canDeleteAnyMember" class="border p-2 text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in enhancedMembers.data" :key="item.id">
                  <td v-for="col in columns" :key="col.key" class="border p-2">
                    {{ item[col.key] }}
                  </td>
                  <td v-if="canUpdateAnyMember || canDeleteAnyMember" class="border p-2 text-right">
                    <Button v-if="canUpdateAnyMember" class="btn btn-secondary mr-2" @click="editMember(item)">
                      <component :is="Pencil" />
                      <span>Edit</span>
                    </Button>
                    <Button v-if="canDeleteAnyMember" class="btn btn-secondary mr-2" @click="deleteMember(item.id)">
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
            <!-- Page count beside Next button, right aligned -->
            <span v-if="enhancedMembers.current_page && enhancedMembers.last_page" class="ml-auto text-sm" style="margin-left: auto; display: block">
              Page {{ enhancedMembers.current_page }} of {{ enhancedMembers.last_page }}
            </span>
          </div>
        </div>
      </div>
      <div v-else class="py-10 text-center text-gray-500">You do not have permission to view members.</div>
    </div>
  </AppLayout>
</template>
