<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { CommunityFund } from '@/types';
import DataTable from '@/components/DataTable2.vue';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import { CommunityFunds } from '@/types';
import { PropType } from 'vue';
import DatatableHeader from '@/components/DatatableHeader.vue';

const props = defineProps({
  communityFunds: {
    type: Object as PropType<CommunityFunds>,
    required: true,
  },
  filters: Object,
  fetchUrl: String,
});

const columns = [
    { key: 'id', label: 'Id', sortable: true },
    { key: 'member_first_name', label: 'Member Name', sortable: true },
    { key: 'community_name', label: 'Community Name', sortable: true },
    { key: 'year', label: 'Year', sortable: true },
    { key: 'amount', label: 'Amount', sortable: true },
    { key: 'created_at', label: 'Created Date', sortable: true },
];

const breadcrumbs = [
  { title: 'Community Funds', href: '/community-fund/index' },
];

function editCommunityFund(communityFund: CommunityFund) {
  router.get(route('community-fund.edit', communityFund.id));
}

function deleteCommunityFund(id: CommunityFund['id']) {
  if (confirm('Delete this Community Fund?')) {
    router.delete(route('community-fund.destroy', id));
  }
}

console.log('Community Funds:', props.communityFunds);

props.communityFunds?.data.forEach((fund) => {
    fund.member_first_name = fund.member?.first_name || '';
    fund.community_name = fund.member?.community?.name || '';
});

console.log('Enhanced Community Funds:', props.communityFunds?.data);

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Community Funds" />
        <DatatableHeader>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Community Funds</h2>
                <div class="btn-group flex space-x-2">
                    <Button as="a" href="/community-fund/create" class="btn btn-secondary">
                        <component :is="Plus" />
                        <span>Add Community Fund</span>
                    </Button>
                </div>
            </div>
        </DatatableHeader>

        <DataTable
            :data="communityFunds"
            :columns="columns"
            :filters="filters"
            :fetch-url="fetchUrl"
            :has-actions="true"
        >
            <template #actions="{ row }">
                <Button class="btn btn-secondary mr-2" @click="editCommunityFund(row)">
                    <component :is="Pencil" />
                    <span>Edit</span>
                </Button>
                <Button class="btn btn-secondary mr-2" @click="deleteCommunityFund(row.id)">
                    <component :is="Trash" />
                    <span>Delete</span>
                </Button>
            </template>
        </DataTable>
    </AppLayout>
</template>
