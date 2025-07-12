<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from '@/components/DataTable2.vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { RotateCcw } from 'lucide-vue-next';
import DatatableHeader from '@/components/DatatableHeader.vue';

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
  { key: 'deleted_at', label: 'Deleted Date', sortable: true },
];

const breadcrumbs = [
  { title: 'Deleted States', href: '/state/deleted' },
];

function restoreState(stateId: number) {
  router.post(route('state.restore', stateId));
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Deleted States" />
    <DatatableHeader>
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Deleted States</h2>
      </div>
    </DatatableHeader>

    <DataTable
      :data="states"
      :columns="columns"
      :filters="filters"
      :fetch-url="fetchUrl"
      :has-actions="true"
    >
      <template #actions="{ row }">
        <Button class="btn btn-secondary mr-2" @click="restoreState(row.id)">
          <component :is="RotateCcw" />
          <span>Restore</span>
        </Button>
      </template>
    </DataTable>
  </AppLayout>
</template>
