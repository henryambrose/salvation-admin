<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { TextareaInput } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Pencil, Plus, Trash } from 'lucide-vue-next';

declare global {
  interface Window {
    app?: any;
  }
}

const props = defineProps({
  zones: {
    type: Object,
    default: () => ({ data: [] }),
  },
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

const page = usePage<{ auth: { roles: string[] } }>();
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
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-2xl font-bold text-blue-700">Zones</h2>
      <Button @click="showModal = true" class="flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
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

    <!-- Modal -->
    <transition name="fade">
      <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Create Zone</h3>
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="form.name" :type="'text'" />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
              </div>
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Description</label>
                <TextareaInput
                  name="description"
                  id="description"
                  v-model="form.description"
                  placeholder="Enter description..."
                  :rows="3"
                  :error="form.errors.description ?? ''"
                />
                <div v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</div>
              </div>
              <div class="flex justify-end space-x-2">
                <Button
                  variant="destructive"
                  type="button"
                  @click="showModal = false"
                  class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition px-6 py-2"
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  :disabled="form.processing"
                  class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2 flex items-center gap-2"
                >
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
