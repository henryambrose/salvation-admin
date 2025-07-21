<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  country: Object,
});
</script>

<template>
  <AppLayout>
    <Head :title="`Country: ${props.country.name}`" />
    <div class="p-6">
      <h2 class="mb-4 text-2xl font-bold">Country Details</h2>
      <div>
        <div><strong>ID:</strong> {{ props.country.id }}</div>
        <div><strong>Name:</strong> {{ props.country.name }}</div>
        <div><strong>Created At:</strong> {{ props.country.created_at }}</div>
        <div v-if="props.country.deleted_at"><strong>Deleted At:</strong> {{ props.country.deleted_at }}</div>
      </div>
    </div>
  </AppLayout>
</template>
