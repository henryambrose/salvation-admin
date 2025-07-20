<script setup lang="ts">
import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { RotateCcw } from 'lucide-vue-next';

const props = defineProps({
  countries: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Country Name', sortable: true },
  { key: 'deleted_at', label: 'Deleted Date', sortable: true },
];

const breadcrumbs = [{ title: 'Deleted Countries', href: '/country/deleted' }];

function restoreCountry(countryId: number) {
  router.post(route('country.restore', countryId));
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Deleted Countries" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Deleted Countries</h2>
      </div>
    </DatatableHeader>

    <DataTable :data="countries" :columns="columns" :filters="filters" :fetch-url="fetchUrl" :has-actions="true">
      <template #actions="{ row }">
        <Button class="mr-2" @click="restoreCountry(row.id)">
          <component :is="RotateCcw" />
          <span>Restore</span>
        </Button>
      </template>
    </DataTable>
  </AppLayout>
</template>
