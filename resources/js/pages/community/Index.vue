<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Community } from '@/types';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';

const props = defineProps({
  communities: {
    type: Object,
    default: () => ({ data: [] }),
  },
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Community Name', sortable: true },
  { key: 'created_at', label: 'Created At', sortable: true },
];

const breadcrumbs = [{ title: 'Communities', href: '/community/index' }];

function editCommunity(community: Community) {
  router.get(route('community.edit', community.id));
}

function deleteCommunity(id: Community['id']) {
  if (confirm('Delete this Community?')) {
    router.delete(route('community.destroy', id));
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Communities" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">Communities</h2>
        <Button as="a" href="/community/create" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
          <component :is="Plus" />
          <span>Add Community</span>
        </Button>
      </div>
    </DatatableHeader>

    <div class="mt-4 rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
      <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="w-full border-collapse text-left">
          <thead>
            <tr class="bg-blue-50">
              <th class="border-b p-3 font-semibold text-gray-700">Actions</th>
              <th v-for="col in columns" :key="col.key" class="border-b p-3 font-semibold text-gray-700">
                {{ col.label }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in props.communities.data" :key="row.id" class="even:bg-gray-50 hover:bg-blue-50 transition">
              <td class="p-2">
                <div class="flex gap-2">
                  <Button @click="editCommunity(row)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                    <component :is="Pencil" />
                    <span>Edit</span>
                  </Button>
                  <Button @click="deleteCommunity(row.id)" variant="destructive" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
                    <component :is="Trash" />
                    <span>Delete</span>
                  </Button>
                </div>
              </td>
              <td v-for="col in columns" :key="col.key" class="p-2">
                {{ row[col.key] }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
