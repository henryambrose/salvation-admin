<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import DataTable2 from '@/components/DataTable2.vue';

const props = defineProps({
  parishes: {
    type: Object,
    default: () => ({ data: [] }),
  },
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'deanery', label: 'Deanery', sortable: true },
  { key: 'name', label: 'Parish Name', sortable: true },
  { key: 'code', label: 'Code', sortable: false },
  { key: 'address', label: 'Address', sortable: false },
];

const breadcrumbs = [{ title: 'Parishes', href: '/parish/index' }];

function editParish(parish) {
  router.get(route('parish.edit', parish.id));
}

function deleteParish(id) {
  if (confirm('Delete this Parish?')) {
    router.delete(route('parish.destroy', id));
  }
}

import { permissionHelpers } from '@/composables/permissionHelpers';
const { can } = permissionHelpers();

const canCreateParish = can('create-parish');
const canReadAnyParish = can('read-parish');
const canUpdateAnyParish = can('update-parish');
const canDeleteAnyParish = can('delete-parish');
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Parishes" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Parishes</h2>
        <Button v-if="canCreateParish" as="a" href="/parish/create" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
          <component :is="Plus" />
          <span>Add Parish</span>
        </Button>
      </div>
    </DatatableHeader>

    <div v-if="canReadAnyParish" class="mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
      <DataTable2
        :data="props.parishes"
        :columns="columns"
        :filters="props.filters"
        :fetch-url="props.fetchUrl"
        :has-actions="canUpdateAnyParish || canDeleteAnyParish"
      >
        <template #actions="{ row }">
          <div class="flex gap-2">
            <Button v-if="canUpdateAnyParish" @click="editParish(row)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
              <component :is="Pencil" />
              <span>Edit</span>
            </Button>
            <Button v-if="canDeleteAnyParish" @click="deleteParish(row.id)" variant="destructive" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
              <component :is="Trash" />
              <span>Delete</span>
            </Button>
          </div>
        </template>
      </DataTable2>
    </div>
  </AppLayout>
</template>
