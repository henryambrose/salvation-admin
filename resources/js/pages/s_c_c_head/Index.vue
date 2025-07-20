<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { SCCHead } from '@/types';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';

const props = defineProps({
  s_c_c_heads: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'member_first_name', label: 'Member Name', sortable: true },
  { key: 'community_name', label: 'Community Name', sortable: true },
];

console.log('SCC Heads:', props.s_c_c_heads.data);

const breadcrumbs = [{ title: 'SCC Heads', href: '/scc-head/index' }];

function editSCCHead(sccHead: SCCHead) {
  // open edit modal logic
  router.get(route('scc-head.edit', sccHead.id));
}

function deleteSCCHead(id: SCCHead['id']) {
  if (confirm('Delete this SCC Head?')) {
    router.delete(route('scc-head.destroy', id));
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="SSC Heads" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">SCC Head</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/scc-head/create">
            <component :is="Plus" />
            <span>Add SCC Head</span>
          </Button>
        </div>
      </div>
    </DatatableHeader>

    <DataTable :data="s_c_c_heads" :columns="columns" :filters="filters" :fetch-url="fetchUrl" :has-actions="true">
      <template #actions="{ row }">
        <div class="flex flex-wrap gap-3">
          <Button @click="editSCCHead(row)">
            <component :is="Pencil" />
            <span>Edit</span>
          </Button>
          <Button @click="deleteSCCHead(row.id)" variant="destructive">
            <component :is="Trash" />
            <span>Delete</span>
          </Button>
        </div>
      </template>
    </DataTable>
  </AppLayout>
</template>
