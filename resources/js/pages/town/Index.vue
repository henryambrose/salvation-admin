<script setup lang="ts">
import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';

const props = defineProps({
  towns: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Town Name', sortable: true },
  { key: 'pincode', label: 'Pincode', sortable: true },
  { key: 'state_name', label: 'State Name', sortable: true },
  { key: 'created_at', label: 'Created Date', sortable: true },
];

const breadcrumbs = [{ title: 'Towns', href: '/town/index' }];

function editTown(townId: number) {
  router.get(route('town.edit', townId));
}

function deleteTown(townId: number) {
  if (confirm('Delete this Town?')) {
    router.delete(route('town.destroy', townId));
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Towns" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Towns</h2>
        <div class="flex gap-2">
          <Button as="a" href="/town/create">
            <component :is="Plus" />
            <span>Add Town</span>
          </Button>
          <Button as="a" href="/town/deleted">
            <span>Deleted Towns</span>
          </Button>
        </div>
      </div>
    </DatatableHeader>

    <DataTable :data="towns" :columns="columns" :filters="filters" :fetch-url="fetchUrl" :has-actions="true">
      <template #actions="{ row }">
        <div class="flex flex-wrap gap-3">
          <Button class="mr-2" @click="editTown(row.id)">
            <component :is="Pencil" />
            <span>Edit</span>
          </Button>
          <Button class="mr-2" @click="deleteTown(row.id)" variant="destructive">
            <component :is="Trash" />
            <span>Delete</span>
          </Button>
        </div>
      </template>
    </DataTable>
  </AppLayout>
</template>
