<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { CommunityFund, Member, type BreadcrumbItem } from '@/types';
import DataTable from '@/components/DataTable2.vue';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import DatatableHeader from '@/components/DatatableHeader.vue';

const props = defineProps({
  communityFunds: Object,
  filters: Object,
  fetchUrl: String,
  members: Array,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'member_name', label: 'Member Name', sortable: true },
  { key: 'amount', label: 'Amount', sortable: true },
  { key: 'fund_date', label: 'Fund Date', sortable: true },
  { key: 'description', label: 'Description', sortable: false },
  { key: 'created_at', label: 'Created Date', sortable: true },
];

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Community Funds', href: '/community-fund/index' },
];

function editCommunityFund(fund: CommunityFund) {
  router.get(route('community-fund.edit', fund.id));
}

function deleteCommunityFund(id: CommunityFund['id']) {
  if (confirm('Delete this community fund?')) {
    router.delete(route('community-fund.destroy', id), {
      onSuccess: () => {
        router.reload({ only: ['communityFunds'] });
      },
    });
  }
}

const enhancedFunds = {
  ...props.communityFunds,
  data: props.communityFunds.data.map(fund => ({
    ...fund,
    member_name: fund.member?.first_name + ' ' + (fund.member?.last_name || ''),
  })),
};
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
      :data="enhancedFunds"
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
