<script setup lang="ts">
import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { RotateCcw } from 'lucide-vue-next';

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

const breadcrumbs = [{ title: 'Deleted States', href: '/state/deleted' }];

function restoreState(stateId: number) {
  router.post(route('state.restore', stateId));
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Deleted States" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Deleted States</h2>
      </div>
    </DatatableHeader>

    <DataTable :data="states" :columns="columns" :filters="filters" :fetch-url="fetchUrl" :has-actions="true">
      <template #actions="{ row }">
        <Button class="mr-2" @click="restoreState(row.id)">
          <component :is="RotateCcw" />
          <span>Restore</span>
        </Button>
      </template>
    </DataTable>
  </AppLayout>
</template>
