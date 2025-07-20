<script setup lang="ts">
import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { CommunityFund, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';

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

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Community Funds', href: '/community-fund/index' }];

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
  data: props.communityFunds.data.map((fund) => ({
    ...fund,
    member_name: fund.member?.first_name + ' ' + (fund.member?.last_name || ''),
  })),
};

const page = usePage();
const roles = page.props.auth?.roles || [];
const isSuperAdmin = roles.includes('superadmin');

// Helper to check permission (uses $can if available, else fallback)
function can(permission: string) {
  if (typeof window !== 'undefined' && window?.app?.config?.globalProperties?.$can) {
    return window.app.config.globalProperties.$can(permission);
  }
  return isSuperAdmin;
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Community Funds" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Community Funds</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/community-fund/create">
            <component :is="Plus" />
            <span>Add Community Fund</span>
          </Button>
        </div>
      </div>
    </DatatableHeader>
    <DataTable :data="enhancedFunds" :columns="columns" :filters="filters" :fetch-url="fetchUrl" :has-actions="true">
      <template #actions="{ row }">
        <div class="flex flex-wrap gap-3">
          <template v-if="isSuperAdmin || can('community-fund.edit')">
            <Button @click="editCommunityFund(row)">
              <component :is="Pencil" />
              <span>Edit</span>
            </Button>
          </template>
          <template v-if="isSuperAdmin || can('community-fund.delete')">
            <Button @click="deleteCommunityFund(row.id)" variant="destructive">
              <component :is="Trash" />
              <span>Delete</span>
            </Button>
          </template>
        </div>
      </template>
    </DataTable>
  </AppLayout>
</template>
