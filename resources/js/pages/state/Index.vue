<script setup lang="ts">
import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';

const props = defineProps({
  states: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'State Name', sortable: true },
  { key: 'abbr', label: 'Abbreviation', sortable: true },
  { key: 'country_name', label: 'Country Name', sortable: true },
  { key: 'created_at', label: 'Created Date', sortable: true },
];

const breadcrumbs = [{ title: 'States', href: '/state/index' }];

function editState(stateId: number) {
  router.get(route('state.edit', stateId));
}

function deleteState(stateId: number) {
  if (confirm('Delete this State?')) {
    router.delete(route('state.destroy', stateId));
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="States" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">States</h2>
        <div class="flex gap-2">
          <Button as="a" href="/state/create">
            <component :is="Plus" />
            <span>Add State</span>
          </Button>
          <Button as="a" href="/state/deleted">
            <span>Deleted States</span>
          </Button>
        </div>
      </div>
    </DatatableHeader>

    <DataTable :data="states" :columns="columns" :filters="filters" :fetch-url="fetchUrl" :has-actions="true">
      <template #actions="{ row }">
        <div class="flex flex-wrap gap-3">
          <Button class="mr-2" @click="editState(row.id)">
            <component :is="Pencil" />
            <span>Edit</span>
          </Button>
          <Button class="mr-2" @click="deleteState(row.id)" variant="destructive">
            <component :is="Trash" />
            <span>Delete</span>
          </Button>
        </div>
      </template>
    </DataTable>
  </AppLayout>
</template>
