<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Community } from '@/types';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';

const props = defineProps({
  communities: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Community Name', sortable: true },
  { key: 'created_at', label: 'Created At', sortable: true },
];

console.log('Communities:', props.communities.data);

const breadcrumbs = [{ title: 'Communities', href: '/community/index' }];

function editCommunity(community: Community) {
  router.get(route('community.edit', community.id));
}

function deleteCommunity(id: Community['id']) {
  if (confirm('Delete this Community?')) {
    router.delete(route('community.destroy', id));
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Communities" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Communities</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/community/create">
            <component :is="Plus" />
            <span>Add Community</span>
          </Button>
        </div>
      </div>
    </DatatableHeader>

    <DataTable :data="communities" :columns="columns" :filters="filters" :fetch-url="fetchUrl" :has-actions="true">
      <template #actions="{ row }">
        <div class="flex flex-wrap gap-3">
          <Button @click="editCommunity(row)">
            <component :is="Pencil" />
            <span>Edit</span>
          </Button>
          <Button @click="deleteCommunity(row.id)" variant="destructive">
            <component :is="Trash" />
            <span>Delete</span>
          </Button>
        </div>
      </template>
    </DataTable>
  </AppLayout>
</template>
