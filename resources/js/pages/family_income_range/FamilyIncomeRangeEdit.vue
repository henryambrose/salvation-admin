<template>
  <AppLayout>
    <Head title="Edit Family Income Range" />
    <div class="mx-auto mt-10 max-w-lg rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
      <h2 class="mb-4 text-2xl font-bold text-blue-700">Edit Family Income Range</h2>
      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="mb-1 block text-sm font-medium">Range</label>
          <Input v-model="form.name" type="text" />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>
        <div class="flex justify-end space-x-2">
          <Button
            type="button"
            variant="destructive"
            @click="cancel"
            class="rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition px-6 py-2"
          >
            Cancel
          </Button>
          <Button
            type="submit"
            :disabled="form.processing"
            class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2 flex items-center gap-2"
          >
            {{ form.processing ? 'Saving...' : 'Save' }}
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
  familyIncomeRange: { id: number; name: string };
}>();

const form = useForm({
  name: props.familyIncomeRange.name,
});

function submit() {
  form.put(`/family-income-range/${props.familyIncomeRange.id}`, {
    preserveScroll: true,
  });
}

function cancel() {
  window.location.href = '/family-income-range/index';
}
</script>
