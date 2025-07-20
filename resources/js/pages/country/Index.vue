<script setup lang="ts">
import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';

const props = defineProps({
  countries: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Country Name', sortable: true },
  { key: 'created_at', label: 'Created Date', sortable: true },
];

const breadcrumbs = [{ title: 'Countries', href: '/country/index' }];

function editCountry(countryId: number) {
  router.get(route('country.edit', countryId));
}

function deleteCountry(countryId: number) {
  if (confirm('Delete this Country?')) {
    router.delete(route('country.destroy', countryId));
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Countries" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Countries</h2>
        <div class="flex gap-2">
          <Button as="a" href="/country/create">
            <component :is="Plus" />
            <span>Add Country</span>
          </Button>
          <Button as="a" href="/country/deleted">
            <span>Deleted Countries</span>
          </Button>
        </div>
      </div>
    </DatatableHeader>

    <DataTable :data="countries" :columns="columns" :filters="filters" :fetch-url="fetchUrl" :has-actions="true">
      <template #actions="{ row }">
        <div class="flex flex-wrap gap-3">
          <Button @click="editCountry(row.id)">
            <component :is="Pencil" />
            <span>Edit</span>
          </Button>
          <Button @click="deleteCountry(row.id)" variant="destructive">
            <component :is="Trash" />
            <span>Delete</span>
          </Button>
        </div>
      </template>
    </DataTable>
  </AppLayout>
</template>
