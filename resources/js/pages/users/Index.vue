<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from '@/components/DataTable2.vue';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import DatatableHeader from '@/components/DatatableHeader.vue';

const props = defineProps({
  users: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'created_at', label: 'Created At', sortable: true },
];

const breadcrumbs = [
  { name: 'Users', href: '/users/index' },
];

const deleteUser = (id: number) => {
  if (confirm('Are you sure you want to delete this user?')) {
    router.delete(`/users/${id}`);
  }
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Users" />

    <DatatableHeader>
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Users</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/users/create" class="btn btn-secondary">
            <component :is="Plus" />
            <span>Add User</span>
          </Button>
        </div>
      </div>
    </DatatableHeader>

    <DataTable
      :data="users"
      :columns="columns"
      :filters="filters"
      :fetch-url="fetchUrl"
      :has-actions="true"
    >
      <template #actions="{ row }">
        <div class="flex items-center space-x-2">
          <Button
            as="a"
            :href="`/users/${row.id}/edit`"
            variant="outline"
            size="icon"
          >
            <component :is="Pencil" class="h-4 w-4" />
          </Button>
          <Button
            variant="outline"
            size="icon"
            @click="deleteUser(row.id)"
          >
            <component :is="Trash" class="h-4 w-4" />
          </Button>
        </div>
      </template>
    </DataTable>
  </AppLayout>
</template>