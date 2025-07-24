<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import { onMounted, reactive } from 'vue';

interface Filters {
  search?: string;
  // add other filter fields if needed
}

interface Props {
  mustVerifyEmail: boolean;
  status?: string;
  zones: { data: any[] };
  filters: Filters;
}

const props = defineProps<Props>();

const filters = reactive({
  search: props.filters.search || '',
});

// let timeout: ReturnType<typeof setTimeout> | null = null;
// function searchZones() {
//   clearTimeout(timeout ?? undefined);
//   timeout = setTimeout(() => {
//     router.get('/zones', { search: filters.search }, { preserveState: true, replace: true });
//   }, 300);
// }

// function goToPage(url) {
//   if (url) router.get(url, {}, { preserveState: true });
// }

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Zone Management',
    href: '/zone/index',
  },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const columns = [
  { key: 'id', label: 'Id' },
  { key: 'name', label: 'Name' },
];

const handleEdit = (zone: any) => {
  router.get(route('zone.edit', { zone: zone.id }));
};

const handleDelete = (zone: any) => {
  if (confirm('Are you sure you want to delete this zone?')) {
    router.delete(route('zone.destroy', { zone: zone.id }), {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Zones Management" />
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-2xl font-bold text-blue-700">Zones</h2>
      <Button as="a" href="/zones/create" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
        <component :is="Plus" />
        <span>Add Zone</span>
      </Button>
    </div>
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
            <tr v-for="row in props.zones.data" :key="row.id" class="even:bg-gray-50 hover:bg-blue-50 transition">
              <td class="p-2">
                <div class="flex gap-2">
                  <Button @click="handleEdit(row)" class="rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                    <component :is="Pencil" />
                    <span>Edit</span>
                  </Button>
                  <Button @click="handleDelete(row)" variant="destructive" class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition">
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
