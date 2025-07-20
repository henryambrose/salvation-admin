<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';

import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { TextareaInput } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  zones: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Name', sortable: true },
];

const showModal = ref(false);
const form = useForm({
  name: '',
  description: '',
});

function submit() {
  form.post('/zone', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showModal.value = false;
    },
  });
}
const breadcrumbs = [{ title: 'Zone', href: '/zone' }];

const handleEdit = (zone) => {
  router.get(route('zone.edit', { zone: zone.id }));
};

const handleDelete = (zone) => {
  if (confirm('Are you sure you want to delete this zone?')) {
    router.delete(route('zone.destroy', { zone: zone.id }), {
      preserveScroll: true,
    });
  }
};

const page = usePage();
const roles = page.props.auth?.roles || [];
const isSuperAdmin = roles.includes('superadmin');

function can(permission: string) {
  if (typeof window !== 'undefined' && window?.app?.config?.globalProperties?.$can) {
    return window.app.config.globalProperties.$can(permission);
  }
  return isSuperAdmin;
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Zone" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Zones</h2>
        <Button @click="showModal = true" class="mr-2"> ➕ Add Zone </Button>
      </div>
    </DatatableHeader>

    <DataTable :data="zones" :columns="columns" :filters="filters" :fetch-url="fetchUrl" has-actions @edit="handleEdit" @delete="handleDelete">
      <template #actions="{ row }">
        <Button class="mr-2" @click="handleEdit(row)"> Edit </Button>
        <Button class="mr-2" @click="handleDelete(row)" variant="destructive"> Delete </Button>
      </template>
    </DataTable>

    <!-- Modal -->
    <transition name="fade">
      <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Create Zone</h3>
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="form.name" type="text" />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Description</label>

                <TextareaInput
                  name="description"
                  id="description"
                  v-model="form.description"
                  placeholder="Enter description..."
                  :error="form.errors.description"
                />
                <div v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</div>
              </div>
              <div class="flex justify-end space-x-2">
                <Button variant="destructive" type="button" @click="showModal = false"> Cancel </Button>
                <Button type="submit" :disabled="form.processing">
                  {{ form.processing ? 'Creating...' : 'Create' }}
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>
  </AppLayout>
</template>
<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
