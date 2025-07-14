<template>
  <AppLayout>
    <Head title="Edit Family Income Range" />
    <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded shadow">
      <h2 class="text-2xl font-bold mb-4">Edit Family Income Range</h2>
      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Range</label>
          <Input v-model="form.name" type="text" />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>
        <div class="flex justify-end space-x-2">
          <Button type="button" variant="secondary" @click="cancel">Cancel</Button>
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Saving...' : 'Save' }}
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  familyIncomeRange: { id: number; name: string }
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
