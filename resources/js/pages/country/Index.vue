<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from '@/components/DataTable2.vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import DatatableHeader from '@/components/DatatableHeader.vue';

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

const breadcrumbs = [
  { title: 'Countries', href: '/country/index' },
];

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
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Countries</h2>
        <Button as="a" href="/country/create" class="btn btn-secondary">
          <component :is="Plus" />
          <span>Add Country</span>
        </Button>
      </div>
    </DatatableHeader>

    <DataTable
      :data="countries"
      :columns="columns"
      :filters="filters"
      :fetch-url="fetchUrl"
      :has-actions="true"
    >
      <template #actions="{ row }">
        <Button class="btn btn-secondary mr-2" @click="editCountry(row.id)">
          <component :is="Pencil" />
          <span>Edit</span>
        </Button>
        <Button class="btn btn-secondary mr-2" @click="deleteCountry(row.id)">
          <component :is="Trash" />
          <span>Delete</span>
        </Button>
      </template>
    </DataTable>
  </AppLayout>
</template>
