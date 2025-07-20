<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { PPCHead } from '@/types';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';

const props = defineProps({
  ppcHeads: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'member_first_name', label: 'Member Name', sortable: true },
  { key: 'community_name', label: 'Community Name', sortable: true },
];

const breadcrumbs = [{ title: 'PPC Heads', href: '/ppc-head/index' }];

function editPPCHead(ppcHead: PPCHead) {
  router.get(route('ppc-head.edit', ppcHead.id));
}

function deletePPCHead(id: PPCHead['id']) {
  if (confirm('Delete this PPC Head?')) {
    router.delete(route('ppc-head.destroy', id));
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="PPC Heads" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">PPC Heads</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/ppc-head/create">
            <component :is="Plus" />
            <span>Add PPC Head</span>
          </Button>
        </div>
      </div>
    </DatatableHeader>

    <DataTable :data="ppcHeads" :columns="columns" :filters="filters" :fetch-url="fetchUrl" :has-actions="true">
      <template #actions="{ row }">
        <div class="flex flex-wrap gap-3">
          <Button @click="editPPCHead(row)">
            <component :is="Pencil" />
            <span>Edit</span>
          </Button>
          <Button @click="deletePPCHead(row.id)" variant="destructive">
            <component :is="Trash" />
            <span>Delete</span>
          </Button>
        </div>
      </template>
    </DataTable>
  </AppLayout>
</template>
