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


const props = defineProps({
  members: Object,
  filters: Object,
  fetchUrl: String,
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
  // open edit modal logic
  router.get(route('member.edit', member.id));
}

function deleteMember(id : Member['id']) {
  if (confirm('Delete this member?')) {
    router.delete(route('member.destroy', id));
  }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Members" />
        <DatatableHeader>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Members</h2>
                <div class="btn-group flex space-x-2">
                    <Button as="a" href="/member/create" class="btn btn-secondary">
                        <component :is="Plus" />
                        <span>Add Member</span>
                    </Button>
                </div>
            </div>
        </DatatableHeader>

        <DataTable
            :data="enhancedMembers"
            :columns="columns"
            :filters="filters"
            :fetch-url="fetchUrl"
            :has-actions="true"
        >
            <template #actions="{ row }">
                <Button class="btn btn-secondary mr-2" @click="editMember(row)">
                    <component :is="Pencil" />
                    <span>Edit</span>
                </Button>
                <Button class="btn btn-secondary mr-2" @click="deleteMember(row.id)">
                    <component :is="Trash" />
                    <span>Delete</span>
                </Button>
            </template>
        </DataTable>


    </AppLayout>
</template>
