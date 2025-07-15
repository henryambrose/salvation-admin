<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { TextareaInput } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';

const props = defineProps({
  zone: Object,
});

const form = useForm({
  name: props.zone.name,
  description: props.zone.description,
});

function submit() {
  form.put(route('zone.update', { zone: props.zone.id }), {
    preserveScroll: true,
  });
}
</script>

<template>
  <AppLayout>
    <Head title="Edit Zone" />
    <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded shadow">
      <h2 class="text-2xl font-bold mb-4">Edit Zone</h2>
      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Name</label>
          <Input v-model="form.name" type="text" />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Description</label>
          <TextareaInput v-model="form.description" />
          <InputError class="mt-2" :message="form.errors.description" />
        </div>
        <div class="flex justify-end space-x-2">
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Saving...' : 'Save' }}
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
