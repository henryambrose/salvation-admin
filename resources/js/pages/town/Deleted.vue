<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from '@/components/DataTable2.vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { RotateCcw } from 'lucide-vue-next';
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
  { key: 'deleted_at', label: 'Deleted Date', sortable: true },
];

const breadcrumbs = [
  { title: 'Deleted Towns', href: '/town/deleted' },
];

function restoreTown(townId: number) {
  router.post(route('town.restore', townId));
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Deleted Towns" />
    <DatatableHeader>
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Deleted Towns</h2>
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
        <Button class="btn btn-secondary mr-2" @click="restoreTown(row.id)">
          <component :is="RotateCcw" />
          <span>Restore</span>
        </Button>
      </template>
    </DataTable>
  </AppLayout>
</template>
