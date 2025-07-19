<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { TextareaInput } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Member, type BreadcrumbItem, type SharedData, type User } from '@/types';
import { ref, computed, onMounted } from 'vue';
import DataTable from '@/components/DataTable2.vue';
import { router } from '@inertiajs/vue3'
import { reactive, watch } from 'vue'
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { SearchDropdown } from '@/components/ui/searchDropdown';
import { Communities } from '@/types';


const { can } = permissionHelpers();

const props = defineProps({
  communities: Object,
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
    data: props.members.data.map(item => ({
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
{ key: 'added_on', label: 'Added On', sortable: true },
{ key: 'last_updated', label: 'Last Updated', sortable: true },
];

const breadcrumbs = [
  { title: 'Members', href: '/member/index' },
];

function editMember(member : Member) {
  router.get(route('member.edit', member.id));
}

function deleteMember(id : Member['id']) {
  if (confirm('Delete this member?')) {
    router.delete(route('member.destroy', id));
  }
}

const canCreateMember = can('create-member');
const canReadAnyMember = can('read-member');
const canUpdateAnyMember = can('update-member');
const canDeleteAnyMember = can('delete-member');

const communityId = ref(null);
const filters = reactive({
  column: '',
  value: '',
});

const searchColumnsOptions = computed(() => {
  return columns.map(col => ({
    id: col.key,
    name: col.label,
  }));
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Members" />
        <DatatableHeader>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Members</h2>
                <div class="btn-group flex space-x-2">
                    <Button
                        v-if="canCreateMember"
                        as="a"
                        href="/member/create"
                        class="btn btn-secondary"
                    >
                        <component :is="Plus" />
                        <span>Add Member</span>
                    </Button>
                </div>
            </div>
            <div class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Community Search Dropdown -->
                    <div>
                        <Label for="community" class="block text-sm font-medium text-gray-700">Community</Label>
                        <SearchDropdown
                            id="community_id"
                            v-model="communityId"
                            :options="props.communities"
                            class="mt-1 block w-full"
                            placeholder="Select Community"
                        />
                    </div>

                    <!-- Search by Column Name -->
                    <div>
                        <Label for="column" class="block text-sm font-medium text-gray-700">Search By Column</Label>
                        <SearchDropdown
                            id="search_by_column_key"
                            v-model="filters.column"
                            :options="searchColumnsOptions"
                            class="mt-1 block w-full"
                            placeholder="Search By Column"
                        />

                    </div>

                    <!-- Search by Column Value -->
                    <div>
                        <Label for="value" class="block text-sm font-medium text-gray-700">Search Value</Label>
                        <Input
                            id="search_by_column_value"
                            v-model="filters.value"
                            class="mt-1 block w-full"
                            placeholder="Enter value"

                        />
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
        </DataTable>
        <div v-else class="text-center text-gray-500 py-10">
            You do not have permission to view members.
        </div>
    </AppLayout>
</template>
