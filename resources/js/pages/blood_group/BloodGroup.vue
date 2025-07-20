<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  bloodGroups: Object,
  filters: Object,
  fetchUrl: String,
});
console.log(props.bloodGroups);
const columns = [
  { key: 'id', label: 'Id', sortable: true },
  { key: 'name', label: 'Name', sortable: true },
];

const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editingBloodGroup = ref<Record<string, any>>();
const deletingBloodGroup = ref<Record<string, any>>();

const form = useForm({
  name: '',
});

const editForm = useForm({
  name: '',
});

function submit() {
  form.post('/blood-group', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showModal.value = false;
    },
  });
}

function openEditModal(row: any) {
  editingBloodGroup.value = row;
  editForm.name = row.name;
  showEditModal.value = true;
}

function submitEdit() {
  editForm.put(`/blood-group/${editingBloodGroup.value?.id || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      editingBloodGroup.value = undefined;
    },
  });
}

function openDeleteModal(row: any) {
  deletingBloodGroup.value = row;
  showDeleteModal.value = true;
}

function confirmDelete() {
  router.delete(`/blood-group/${deletingBloodGroup.value?.id || ''}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      deletingBloodGroup.value = undefined;
    },
  });
}

const breadcrumbs = [{ title: 'Blood Group', href: '/blood-group' }];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Blood Group" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Blood Group</h2>
        <Button @click="showModal = true" class="mr-2"> ➕ Add Blood Group </Button>
      </div>
    </DatatableHeader>

    <DataTable :data="bloodGroups" :columns="columns" :filters="filters" :fetch-url="fetchUrl" :has-actions="true">
      <template #actions="{ row }">
        <div class="flex flex-wrap gap-3">
          <Button size="sm" @click="openEditModal(row)">Edit</Button>
          <Button size="sm" variant="destructive" @click="openDeleteModal(row)">Delete</Button>
        </div>
      </template>
    </DataTable>

    <!-- Create Modal -->
    <transition name="fade">
      <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Create Blood Group</h3>
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="form.name" type="text" />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
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

    <!-- Edit Modal -->
    <transition name="fade">
      <div v-if="showEditModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Edit Blood Group</h3>
            <form @submit.prevent="submitEdit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Name</label>
                <Input v-model="editForm.name" type="text" />
                <div v-if="editForm.errors.name" class="mt-1 text-sm text-red-500">{{ editForm.errors.name }}</div>
              </div>
              <div class="flex justify-end space-x-2">
                <Button variant="destructive" type="button" @click="showEditModal = false"> Cancel </Button>
                <Button type="submit" :disabled="editForm.processing">
                  {{ editForm.processing ? 'Saving...' : 'Save' }}
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>

    <!-- Delete Modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Blood Group</h3>
            <p>
              Are you sure you want to delete <span class="font-bold">{{ deletingBloodGroup?.name }}</span
              >?
            </p>
            <div class="mt-6 flex justify-end space-x-2">
              <Button variant="secondary" type="button" @click="showDeleteModal = false"> Cancel </Button>
              <Button variant="destructive" type="button" :disabled="false" @click="confirmDelete"> Delete </Button>
            </div>
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
