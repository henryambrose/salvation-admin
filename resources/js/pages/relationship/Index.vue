<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import DataTable2 from '@/components/DataTable2.vue';

const props = defineProps({
  relationships: {
    type: Object,
    default: () => ({ data: [] }),
  },
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Relationship Name', sortable: true },
  { key: 'description', label: 'Description', sortable: false },
];

const breadcrumbs = [{ title: 'Relationships', href: '/relationship/index' }];

function editRelationship(relationship) {
  router.get(route('relationship.edit', relationship.id));
}

function deleteRelationship(id) {
  if (confirm('Delete this Relationship?')) {
    router.delete(route('relationship.destroy', id));
  }
}

import { permissionHelpers } from '@/composables/permissionHelpers';
const { can } = permissionHelpers();

const canCreateRelationship = can('create-relationship');
const canReadAnyRelationship = can('read-relationship');
const canUpdateAnyRelationship = can('update-relationship');
const canDeleteAnyRelationship = can('delete-relationship');
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Relationships" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Relationships</h2>
        <Button v-if="canCreateRelationship" as="a" href="/relationship/create" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
          <component :is="Plus" />
          <span>Add Relationship</span>
        </Button>
      </div>
    </DatatableHeader>

    <div v-if="canReadAnyRelationship" class="mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
      <DataTable2
        :data="props.relationships"
        :columns="columns"
        :filters="props.filters"
        :fetch-url="props.fetchUrl"
        :has-actions="canUpdateAnyRelationship || canDeleteAnyRelationship"
      >
        <template #actions="{ row }">
          <div class="flex gap-2">
            <Button
              v-if="canUpdateAnyRelationship"
              @click="editRelationship(row)"
              class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition"
            >
              <component :is="Pencil" />
              <span>Edit</span>
            </Button>
            <Button
              v-if="canDeleteAnyRelationship"
              @click="deleteRelationship(row.id)"
              variant="destructive"
              class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition"
            >
              <component :is="Trash" />
              <span>Delete</span>
            </Button>
          </div>
        </template>
      </DataTable2>
    </div>
  </AppLayout>
</template>
