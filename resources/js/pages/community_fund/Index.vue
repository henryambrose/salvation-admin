<script setup lang="ts">
import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { CommunityFund, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import { ref, watch } from 'vue';

const props = defineProps({
  communityFunds: {
    type: Object,
    default: () => ({ data: [] }),
  },
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

const isArchived = ref(false);

function editCommunityFund(fund: any) {
  router.get(route('community-fund.edit', fund.id));
}

function deleteCommunityFund(id: any) {
  if (confirm('Delete this community fund?')) {
    router.delete(route('community-fund.destroy', id), {
      onSuccess: () => {
        router.reload({ only: ['communityFunds'] });
      },
    });
  }
}

function fetch() {
  if (props.fetchUrl) {
    router.get(props.fetchUrl, { isArchived: isArchived.value }, { preserveState: true, replace: true });
  }
}
watch(isArchived, fetch);

const enhancedFunds = {
  ...props.communityFunds,
  data: props.communityFunds.data.map((fund: any) => ({
    ...fund,
    member_name: fund.member?.first_name + ' ' + (fund.member?.last_name || ''),
  })),
};

const page = usePage();
const roles = (page as any).props.auth?.roles || [];
const isSuperAdmin = roles.includes('superadmin');

// Helper to check permission (uses $can if available, else fallback)
function can(permission: string) {
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
      <div class="flex items-center gap-4 mt-2">
        <label class="flex items-center gap-2 cursor-pointer select-none">
          <Checkbox v-model="isArchived" class="switch-checkbox" />
          <span class="text-sm font-medium">Show Archived</span>
        </label>
      </div>
    </DatatableHeader>
    <div class="mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
      <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="w-full border-collapse text-left">
          <thead>
            <tr class="bg-blue-50">
              <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
              <th v-for="col in columns" :key="col.key" class="border-b p-3 font-semibold text-gray-700">
                {{ col.label }}
              </th>
              <th class="border-b p-3 font-semibold text-gray-700">Delete</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in enhancedFunds.data" :key="row.id" class="even:bg-gray-50 hover:bg-blue-50 transition">
              <td class="p-2">
                <Button @click="editCommunityFund(row)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                  Edit
                </Button>
              </td>
              <td v-for="col in columns" :key="col.key" class="p-2">
                {{ row[col.key] }}
              </td>
              <td class="p-2">
                <Button @click="deleteCommunityFund(row.id)" variant="destructive" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
                  Delete
                </Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<style>
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
