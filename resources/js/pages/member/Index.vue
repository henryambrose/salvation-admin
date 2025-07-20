<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SearchDropdown } from '@/components/ui/searchDropdown';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Member } from '@/types';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';

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

const enhancedMembers = computed(() => {
  return {
    ...props.members,
    data: props.members?.data.map((item: any) => ({
      ...item,
      community_name: item.community?.name || '',
      community_cluster_name: item.community_cluster?.name || '',
      added_on: item.created_at ? new Date(item.created_at).toLocaleDateString() : '',
      last_updated: item.updated_at ? new Date(item.updated_at).toLocaleDateString() : '',
    })),
  };
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'first_name', label: 'First Name', sortable: true },
  { key: 'last_name', label: 'Last Name', sortable: true },
  { key: 'community_name', label: 'Community Name', sortable: true },
  { key: 'community_cluster_name', label: 'Community Cluster Name', sortable: true },
  { key: 'community_id', label: 'Community ID', sortable: true },
  { key: 'community_cluster_id', label: 'Community Cluster ID', sortable: true },
  { key: 'new_olsc_id', label: 'New OLSC ID', sortable: true },
  { key: 'old_sal_id', label: 'Old SAL ID', sortable: true },
  { key: 'aadhar', label: 'Aadhar', sortable: true },
  { key: 'family_no', label: 'Family No', sortable: true },
  { key: 'status_id', label: 'Status ID', sortable: true },
  { key: 'relationship_id', label: 'Relationship ID', sortable: true },
  { key: 'gender_id', label: 'Gender ID', sortable: true },
  { key: 'date_of_birth', label: 'Date of Birth', sortable: true },
  { key: 'permanent_add1', label: 'Permanent Address Line 1', sortable: true },
  { key: 'permanent_add2', label: 'Permanent Address Line 2', sortable: true },
  { key: 'permanent_add3', label: 'Permanent Address Line 3', sortable: true },
  { key: 'permanent_town_id', label: 'Permanent Town ID', sortable: true },
  { key: 'permanent_pincode', label: 'Permanent Pincode', sortable: true },
  { key: 'permanent_state_id', label: 'Permanent State ID', sortable: true },
  { key: 'permanent_country_id', label: 'Permanent Country ID', sortable: true },
  { key: 'current_add1', label: 'Current Address Line 1', sortable: true },
  { key: 'current_add2', label: 'Current Address Line 2', sortable: true },
  { key: 'current_add3', label: 'Current Address Line 3', sortable: true },
  { key: 'current_town_id', label: 'Current Town ID', sortable: true },
  { key: 'current_pincode', label: 'Current Pincode', sortable: true },
  { key: 'current_state_id', label: 'Current State ID', sortable: true },
  { key: 'current_country_id', label: 'Current Country ID', sortable: true },
  { key: 'contact_no', label: 'Contact No', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'blood_group_id', label: 'Blood Group ID', sortable: true },
  { key: 'cells_and_association_id', label: 'Cells and Association ID', sortable: true },
  { key: 'school_name', label: 'School Name', sortable: true },
  { key: 'college_name', label: 'College Name', sortable: true },
  { key: 'latest_qualifications', label: 'Latest Qualifications', sortable: true },
  { key: 'company_name', label: 'Company Name', sortable: true },
  { key: 'designation_id', label: 'Designation ID', sortable: true },
  { key: 'family_income_range_id', label: 'Family Income Range ID', sortable: true },
  { key: 'baptism_date', label: 'Baptism Date', sortable: true },
  { key: 'baptism_reg_no', label: 'Baptism Reg No', sortable: true },
  { key: 'baptism_parish', label: 'Baptism Parish', sortable: true },
  { key: 'confirmation_date', label: 'Confirmation Date', sortable: true },
  { key: 'confirmation_reg_no', label: 'Confirmation Reg No', sortable: true },
  { key: 'confirmation_parish', label: 'Confirmation Parish', sortable: true },
  { key: 'marriage_date', label: 'Marriage Date', sortable: true },
  { key: 'marriage_reg_no', label: 'Marriage Reg No', sortable: true },
  { key: 'marriage_parish', label: 'Marriage Parish', sortable: true },
  { key: 'death_date', label: 'Death Date', sortable: true },
  { key: 'deaths_reg_no', label: 'Deaths Reg No', sortable: true },
  { key: 'death_parish', label: 'Death Parish', sortable: true },
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

const communityId = ref('');
const filters = reactive({
  column: '',
  value: '',
});

const searchColumnsOptions = computed(() => {
  return columns.map((col) => ({
    id: col.key,
    name: col.label,
  }));
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Members" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Members</h2>
        <div class="btn-group flex space-x-2">
          <Button v-if="canCreateMember" as="a" href="/member/create">
            <component :is="Plus" />
            <span>Add Member</span>
          </Button>
        </div>
      </div>
      <div class="mb-4">
        <div class="flex w-full flex-wrap gap-3">
          <!-- Community Search Dropdown -->
          <div class="">
            <Label for="community_id" class="block text-sm font-medium text-gray-700">Community</Label>
            <SearchDropdown
              id="community_id"
              v-model="communityId"
              :options="props.communities || []"
              class="block w-full"
              placeholder="Select Community"
            />
          </div>

          <!-- Search by Column Name -->
          <div class="">
            <Label for="search_by_column_key" class="block text-sm font-medium text-gray-700">Search By Column</Label>
            <SearchDropdown
              id="search_by_column_key"
              v-model="filters.column"
              :options="searchColumnsOptions"
              class="block w-full"
              placeholder="Search By Column"
            />
          </div>

          <!-- Search by Column Value -->
          <div class="">
            <Label for="search_by_column_value" class="block text-sm font-medium text-gray-700">Search Value</Label>
            <Input id="search_by_column_value" v-model="filters.value" class="block w-full" placeholder="Enter value" />
          </div>
        </div>
      </div>
      <!-- Pagination Info -->
      <!-- <div v-if="props.pagination" class="mb-2 text-sm text-gray-600">
                Page {{ props.pagination.currentPage }} of {{ props.pagination.lastPage }}
            </div> -->
    </DatatableHeader>
    <DataTable
      v-if="canReadAnyMember"
      :data="enhancedMembers"
      :columns="columns"
      :filters="filters"
      :fetch-url="fetchUrl"
      :has-actions="canUpdateAnyMember || canDeleteAnyMember"
    >
      <template #actions="{ row }">
        <div class="flex flex-wrap gap-3">
          <Button v-if="canUpdateAnyMember" variant="secondary" @click="editMember(row)">
            <component :is="Pencil" />
            <span>Edit</span>
          </Button>
          <Button v-if="canDeleteAnyMember" variant="destructive" @click="deleteMember(row.id)">
            <component :is="Trash" />
            <span>Delete</span>
          </Button>
        </div>
      </template>
    </DataTable>
    <div v-else class="py-10 text-center text-gray-500">You do not have permission to view members.</div>
  </AppLayout>
</template>
