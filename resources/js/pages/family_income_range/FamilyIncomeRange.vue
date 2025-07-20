<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

import DataTable from '@/components/DataTable2.vue';
import DatatableHeader from '@/components/DatatableHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  familyIncomeRange: Object,
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
  //   starting_range: '',
  //   ending_range: '',
});

function submit() {
  form.post('/family-income-range', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showModal.value = false;
    },
  });
}
const breadcrumbs = [{ title: 'Family Income Range', href: '/family-income-range' }];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Family Income Range" />
    <DatatableHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Family Income Range</h2>
        <Button @click="showModal = true" class="mr-2"> ➕ Add Family Income Range </Button>
      </div>
    </DatatableHeader>

    <DataTable :data="familyIncomeRange" :columns="columns" :filters="filters" :fetch-url="fetchUrl" has-actions>
      <template #actions="{ row }">
        <div class="flex flex-wrap gap-3">
          <Button size="sm" @click="$inertia.visit(`/family-income-range/${row.id}/edit`)"> Edit </Button>
          <Button
            size="sm"
            variant="destructive"
            @click="
              () => {
                if (confirm('Are you sure you want to delete this item?')) {
                  $inertia.delete(`/family-income-range/${row.id}`);
                }
              }
            "
          >
            Delete
          </Button>
        </div>
      </template>
    </DataTable>

    <!-- Modal -->
    <transition name="fade">
      <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="from-grey-900 via-grey-800 to-grey-600 w-full max-w-md rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-white p-6">
            <h3 class="mb-4 text-xl font-semibold">Create Family Income Range</h3>
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label class="mb-1 block text-sm font-medium">Range</label>
                <Input v-model="form.name" type="text" />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
              </div>
              <!-- <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Starting Range</label>
                        <Input
                        v-model="form.starting_range"
                        type="text"

                        />
                        <div v-if="form.errors.starting_range" class="text-red-500 text-sm mt-1">{{ form.errors.starting_range }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Ending Range</label>
                        <Input
                        v-model="form.ending_range"
                        type="text"

                        />
                        <div v-if="form.errors.ending_range" class="text-red-500 text-sm mt-1">{{ form.errors.ending_range }}</div>
                    </div> -->

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
