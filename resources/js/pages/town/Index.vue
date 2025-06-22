<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from '@/components/DataTable2.vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import DatatableHeader from '@/components/DatatableHeader.vue';

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

const breadcrumbs = [
  { title: 'Towns', href: '/town/index' },
];

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
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Towns</h2>
        <Button as="a" href="/town/create" class="btn btn-secondary">
          <component :is="Plus" />
          <span>Add Town</span>
        </Button>
      </div>
    </DatatableHeader>

    <DataTable
      :data="towns"
      :columns="columns"
      :filters="filters"
      :fetch-url="fetchUrl"
      :has-actions="true"
    >
      <template #actions="{ row }">
        <Button class="btn btn-secondary mr-2" @click="editTown(row.id)">
          <component :is="Pencil" />
          <span>Edit</span>
        </Button>
        <Button class="btn btn-secondary mr-2" @click="deleteTown(row.id)">
          <component :is="Trash" />
          <span>Delete</span>
        </Button>
      </template>
    </DataTable>
  </AppLayout>
</template>
